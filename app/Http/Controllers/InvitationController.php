<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    public function show()
    {
        return view('invitations.join');
    }

    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|size:8',
        ]);

        $colocation = Colocation::where('invite_code', strtoupper($request->invite_code))
            ->where('status', 'active')
            ->first();

        if (!$colocation) {
            return back()->with('error', 'Invalid or expired invitation code.');
        }

        if ($colocation->isMember(auth()->user())) {
            return redirect()
                ->route('colocations.show', $colocation)
                ->with('info', 'You are already a member of this colocation.');
        }

        try {
            DB::transaction(function () use ($colocation) {
                $colocation->memberships()->create([
                    'user_id' => auth()->id(),
                    'role' => 'member',
                    'joined_at' => now(),
                ]);
            });

            return redirect()
                ->route('colocations.show', $colocation)
                ->with('success', 'Successfully joined the colocation!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to join colocation. Please try again.');
        }
    }
}
