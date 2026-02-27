<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function index()
    {
        $colocations = auth()->user()
            ->memberships()
            ->with(['colocation.memberships.user'])
            ->active()
            ->get()
            ->pluck('colocation')
            ->filter()
            ->values();

        return view('colocations.index', compact('colocations'));
    }

    public function create()
    {
        return view('colocations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $colocation = DB::transaction(function () use ($request) {
                $colocation = Colocation::create([
                    'name' => $request->name,
                ]);
                
                $colocation->memberships()->create([
                    'user_id' => auth()->id(),
                    'role' => 'owner',
                    'joined_at' => now(),
                ]);
                
                return $colocation;
            });

            return redirect()
                ->route('colocations.show', $colocation)
                ->with('success', 'Colocation created successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create colocation: ' . $e->getMessage());
        }
    }

    public function show(Colocation $colocation)
    {
        abort_unless($colocation->isMember(auth()->user()), 403);

        $colocation->load(['memberships.user', 'expenses.payer']);

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

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $colocation->update(['name' => $request->name]);

            return redirect()
                ->route('colocations.show', $colocation)
                ->with('success', 'Colocation updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update colocation.');
        }
    }

    public function destroy(Colocation $colocation)
    {
        abort_unless($colocation->isOwner(auth()->user()), 403);

        try {
            DB::transaction(function () use ($colocation) {
                $colocation->update(['status' => 'cancelled']);
                $colocation->memberships()->whereNull('left_at')->update(['left_at' => now()]);
            });

            return redirect()
                ->route('colocations.index')
                ->with('success', 'Colocation cancelled successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel colocation.');
        }
    }
}
