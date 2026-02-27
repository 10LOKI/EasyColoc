<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        $colocations =   $colocation->load('users','expenses.paidBy');

        $colocation->load(['memberships.user', 'expenses.payer']);

        return view('colocations.show', compact('colocations'));
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
    $members = $colocation->users;
    $expenses = $colocation->expenses;

    $totalExpenses = $expenses->sum('amount');
    $memberCount = $members->count();
    $perPerson = $memberCount > 0 ? $totalExpenses / $memberCount : 0;

    $balances = [];
    foreach ($members as $member) {
        $paid = $expenses->where('paid_by', $member->id)->sum('amount');
        $balances[$member->id] = [
            'name' => $member->name,
            'paid' => $paid,
            'owes' => $perPerson - $paid
        ];
    }

    return view('colocations.balances', compact('colocation', 'balances', 'perPerson', 'totalExpenses'));
}

}
