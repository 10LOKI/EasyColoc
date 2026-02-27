<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Reputation;
use App\Models\User;
use Illuminate\Http\Request;

class ReputationController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        $reputations = Reputation::query()
            ->with(['user', 'colocation'])
            ->where('user_id', $authUser->id)
            ->latest()
            ->paginate(15);

        $totalScore = Reputation::getTotalScore($authUser);

        $colocations = $authUser->memberships()
            ->active()
            ->with('colocation.users')
            ->get()
            ->pluck('colocation')
            ->filter()
            ->values();

        $memberOptions = $colocations
            ->flatMap(function ($colocation) use ($authUser) {
                return $colocation->users
                    ->where('id', '!=', $authUser->id)
                    ->map(function ($member) use ($colocation) {
                        return [
                            'colocation_id' => $colocation->id,
                            'colocation_name' => $colocation->name,
                            'user_id' => $member->id,
                            'user_name' => $member->name,
                        ];
                    });
            })
            ->unique(fn ($item) => $item['colocation_id'] . '-' . $item['user_id'])
            ->values();

        return view('reputations.index', compact('reputations', 'totalScore', 'colocations', 'memberOptions'));
    }

    public function storePositive(Request $request)
    {
        return $this->storeEntry($request, true);
    }

    public function storeNegative(Request $request)
    {
        return $this->storeEntry($request, false);
    }

    private function storeEntry(Request $request, bool $positive)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'colocation_id' => 'nullable|exists:colocations,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $targetUser = User::findOrFail($data['user_id']);
        $colocation = isset($data['colocation_id']) ? Colocation::findOrFail($data['colocation_id']) : null;

        abort_if($targetUser->id === auth()->id(), 422, 'You cannot rate yourself.');

        if ($colocation) {
            abort_unless($colocation->isMember(auth()->user()), 403);
            abort_unless($colocation->isMember($targetUser), 403);
        }

        if ($positive) {
            Reputation::addPositive($targetUser, $colocation, $data['reason'], $data['description'] ?? null);
        } else {
            Reputation::addNegative($targetUser, $colocation, $data['reason'], $data['description'] ?? null);
        }

        return redirect()
            ->route('reputations.index')
            ->with('success', 'Reputation entry created successfully.');
    }
}
