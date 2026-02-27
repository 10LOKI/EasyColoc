<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Invitation;
use App\Models\Reputation;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ColocationController extends Controller
{
    public function index()
    {
        $colocations = auth()->user()->colocations;
        return view('colocations.index', compact('colocations'));
    }

    public function create()
    {
        return view('colocations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:30",
        ]);

        $colocation = Colocation::create([
            "name" => $data['name'],
            "invite_code" => \Illuminate\Support\Str::random(10),
        ]);

        $colocation->users()->attach(auth()->id(), ['role' => 'owner', 'left_at' => null]);

        return redirect()->route('colocations.show', $colocation);
    }
    public function show(Colocation $colocation)
    {
        $colocation->load([
            'users',
            'memberships' => function ($query) {
                $query->whereNull('left_at')->with('user');
            },
            'expenses.payer'
        ]);

        return view('colocations.show', compact('colocation'));
    }

    public function edit(Colocation $colocation)
    {
        abort_unless($colocation->isOwner(auth()->user()), 403);

        return view('colocations.edit', compact('colocation'));
    }

    public function update(Request $request, Colocation $colocation)
    {
        abort_unless($colocation->isOwner(auth()->user()), 403);

        $data = $request->validate([
            'name' => 'required|string|max:30',
        ]);

        $colocation->update([
            'name' => $data['name'],
        ]);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('success', 'Colocation updated successfully.');
    }

    public function invit(Request $request, $colocation)
    {



        $invitation = Invitation::create([
            'colocation_id' => $colocation,
            'created_by' => auth()->id(),
            'token' => Str::random(20),
            'email' => $request->email,
            'status' => "pendding"
        ]);

           $url = url('/invitations/' . $invitation->token);

            return redirect()->back()->with('link', $url);

    }

    public function sendinvitation($token){
        $invitation= Invitation::where('token',$token)->firstOrfail();
        
        $colocation = Colocation::findOrfail($invitation->colocation_id);

        return view('colocations.invitation',compact('invitation','colocation'));
    }   
    public function acceptinvitation(Request $request , $token){
        
       

        $invitation= Invitation::where('token',$token)->firstOrfail();

         if(auth()->user()->email != $invitation->email )
            abort(403);
        $invitation->update([
            "status"=> "accepted",
            "accepted_by"=>auth()->id()
        ]);
        
        $colocation = Colocation::findOrfail($invitation->colocation_id);
        $colocation->users()->attach(auth()->id(), ['role' => 'member', 'left_at' => null]);  
        
         return redirect()->route('colocations.show', $colocation);


    }
    public function addExpense(Request $request, Colocation $colocation)
{
    $data = $request->validate([
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0.01'
    ]);

    $colocation->expenses()->create([
        'paid_by' => auth()->id(),
        'description' => $data['description'],
        'amount' => $data['amount']
    ]);

    return redirect()->route('colocations.show', $colocation);
}

public function calculateBalances(Colocation $colocation)
{
    abort_unless($colocation->isMember(auth()->user()), 403);

    $members = $colocation->users;
    $expenses = $colocation->expenses()->get();
    $paidSettlements = $colocation->settlements()->where('status', 'paid')->get();
    $pendingSettlements = $colocation->settlements()
        ->with(['sender', 'receiver'])
        ->where('status', 'pending')
        ->latest()
        ->get();

    $totalExpenses = $expenses->sum('amount');
    $memberCount = $members->count();
    $perPerson = $memberCount > 0 ? $totalExpenses / $memberCount : 0;

    $balances = [];
    foreach ($members as $member) {
        $paidExpenses = $expenses->where('paid_by', $member->id)->sum('amount');
        $sentSettlements = $paidSettlements->where('sender_id', $member->id)->sum('amount');
        $receivedSettlements = $paidSettlements->where('receiver_id', $member->id)->sum('amount');
        $paid = $paidExpenses - $sentSettlements + $receivedSettlements;

        $balances[$member->id] = [
            'id' => $member->id,
            'name' => $member->name,
            'paid' => $paid,
            'owes' => $perPerson - $paid
        ];
    }

    $suggestedSettlements = $this->buildSuggestedSettlements($balances);

    return view('colocations.balances', compact(
        'colocation',
        'balances',
        'perPerson',
        'totalExpenses',
        'suggestedSettlements',
        'pendingSettlements'
    ));
}

public function leave(Colocation $colocation)
{
    $user = auth()->user();

    abort_unless($colocation->isMember($user), 403);
    if ($colocation->isOwner($user)) {
        return back()->with('error', 'Owner cannot leave the colocation. Transfer ownership first or cancel it.');
    }

    $userOwes = $this->calculateUserOwesAmount($colocation, $user->id);

    DB::transaction(function () use ($colocation, $user, $userOwes) {
        $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->update(['left_at' => now()]);

        if ($userOwes > 0) {
            Reputation::addNegative(
                $user,
                $colocation,
                'Left colocation with unpaid debt',
                'Outstanding amount at leaving time: $' . number_format($userOwes, 2)
            );
        } else {
            Reputation::addPositive(
                $user,
                $colocation,
                'Left colocation with no debt',
                'No outstanding debt at leaving time.'
            );
        }
    });

    return redirect()
        ->route('colocations.index')
        ->with('success', $userOwes > 0
            ? 'You left the colocation. Negative reputation added because you still had debt.'
            : 'You left the colocation. Positive reputation added because you had no debt.');
}

public function storeSettlement(Request $request, Colocation $colocation)
{
    abort_unless($colocation->isMember(auth()->user()), 403);

    $data = $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'amount' => 'required|numeric|min:0.01',
    ]);

    abort_if((int) $data['receiver_id'] === auth()->id(), 422, 'You cannot settle with yourself.');

    $receiver = $colocation->users()->where('users.id', $data['receiver_id'])->firstOrFail();

    $maxAmount = $this->maxSuggestedAmountForPair($colocation, auth()->id(), (int) $receiver->id);
    abort_if($maxAmount <= 0, 422, 'No pending debt to this member.');
    abort_if((float) $data['amount'] > $maxAmount, 422, 'Amount exceeds your pending debt to this member.');

    Settlement::create([
        'colocation_id' => $colocation->id,
        'sender_id' => auth()->id(),
        'receiver_id' => $receiver->id,
        'amount' => $data['amount'],
        'status' => 'pending',
    ]);

    return redirect()
        ->route('colocations.balances', $colocation)
        ->with('success', 'Settlement created. Mark it as paid once transferred.');
}

public function markSettlementPaid(Colocation $colocation, Settlement $settlement)
{
    abort_unless($colocation->isMember(auth()->user()), 403);
    abort_unless($settlement->colocation_id === $colocation->id, 404);
    abort_unless($settlement->sender_id === auth()->id(), 403);

    $settlement->markAsPaid();

    return redirect()
        ->route('colocations.balances', $colocation)
        ->with('success', 'Settlement marked as paid.');
}

private function buildSuggestedSettlements(array $balances): array
{
    $debtors = collect($balances)
        ->filter(fn ($balance) => $balance['owes'] > 0)
        ->map(fn ($balance) => ['id' => $balance['id'], 'name' => $balance['name'], 'amount' => (float) $balance['owes']])
        ->values()
        ->all();

    $creditors = collect($balances)
        ->filter(fn ($balance) => $balance['owes'] < 0)
        ->map(fn ($balance) => ['id' => $balance['id'], 'name' => $balance['name'], 'amount' => (float) abs($balance['owes'])])
        ->values()
        ->all();

    $suggestions = [];
    $i = 0;
    $j = 0;

    while ($i < count($debtors) && $j < count($creditors)) {
        $transfer = min($debtors[$i]['amount'], $creditors[$j]['amount']);

        $suggestions[] = [
            'from_id' => $debtors[$i]['id'],
            'from_name' => $debtors[$i]['name'],
            'to_id' => $creditors[$j]['id'],
            'to_name' => $creditors[$j]['name'],
            'amount' => round($transfer, 2),
        ];

        $debtors[$i]['amount'] = round($debtors[$i]['amount'] - $transfer, 2);
        $creditors[$j]['amount'] = round($creditors[$j]['amount'] - $transfer, 2);

        if ($debtors[$i]['amount'] <= 0) {
            $i++;
        }
        if ($creditors[$j]['amount'] <= 0) {
            $j++;
        }
    }

    return $suggestions;
}

private function maxSuggestedAmountForPair(Colocation $colocation, int $senderId, int $receiverId): float
{
    $members = $colocation->users;
    $expenses = $colocation->expenses()->get();
    $paidSettlements = $colocation->settlements()->where('status', 'paid')->get();

    $totalExpenses = $expenses->sum('amount');
    $memberCount = $members->count();
    if ($memberCount === 0) {
        return 0;
    }

    $perPerson = $totalExpenses / $memberCount;
    $balances = [];
    foreach ($members as $member) {
        $paidExpenses = $expenses->where('paid_by', $member->id)->sum('amount');
        $sentSettlements = $paidSettlements->where('sender_id', $member->id)->sum('amount');
        $receivedSettlements = $paidSettlements->where('receiver_id', $member->id)->sum('amount');
        $paid = $paidExpenses - $sentSettlements + $receivedSettlements;

        $balances[$member->id] = [
            'id' => $member->id,
            'name' => $member->name,
            'paid' => $paid,
            'owes' => $perPerson - $paid,
        ];
    }

    $suggested = collect($this->buildSuggestedSettlements($balances))
        ->first(fn ($row) => $row['from_id'] === $senderId && $row['to_id'] === $receiverId);

    return (float) ($suggested['amount'] ?? 0);
}

private function calculateUserOwesAmount(Colocation $colocation, int $userId): float
{
    $members = $colocation->users;
    $expenses = $colocation->expenses()->get();
    $paidSettlements = $colocation->settlements()->where('status', 'paid')->get();

    $totalExpenses = $expenses->sum('amount');
    $memberCount = $members->count();
    if ($memberCount === 0) {
        return 0.0;
    }

    $perPerson = $totalExpenses / $memberCount;

    $member = $members->firstWhere('id', $userId);
    if (!$member) {
        return 0.0;
    }

    $paidExpenses = $expenses->where('paid_by', $member->id)->sum('amount');
    $sentSettlements = $paidSettlements->where('sender_id', $member->id)->sum('amount');
    $receivedSettlements = $paidSettlements->where('receiver_id', $member->id)->sum('amount');
    $paid = $paidExpenses - $sentSettlements + $receivedSettlements;

    return round(max(0, $perPerson - $paid), 2);
}

public function destroy(Colocation $colocation)
{
    abort_unless($colocation->isOwner(auth()->user()), 403);

    DB::transaction(function () use ($colocation) {
        $colocation->update(['status' => 'cancelled']);
        $colocation->memberships()
            ->whereNull('left_at')
            ->update(['left_at' => now()]);
    });

    return redirect()
        ->route('colocations.index')
        ->with('success', 'Colocation cancelled successfully.');
}

}
