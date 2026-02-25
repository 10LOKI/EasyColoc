<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Http\Requests\StoreColocationRequest;
use App\Http\Requests\UpdateColocationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ColocationController extends Controller
{
    /**
     * Display a listing of user's colocations
     */
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

    /**
     * Show the form for creating a new colocation
     */
    public function create()
    {
        return view('colocations.create');
    }

    /**
     * Store a newly created colocation
     */
    public function store(StoreColocationRequest $request)
    {
        try {
            $colocation = DB::transaction(function () use ($request) {
                // Create colocation
                $colocation = Colocation::create($request->validated());
                
                // Add creator as owner
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
                ->with('error', 'Failed to create colocation. Please try again.');
        }
    }

    /**
     * Display the specified colocation
     */
    public function show(Colocation $colocation)
    {
        // Authorization check
        abort_unless($colocation->isMember(auth()->user()), 403);

        $colocation->load([
            'memberships.user',
            'expenses.payer',
            'expenses.category'
        ]);

        return view('colocations.show', compact('colocation'));
    }

    /**
     * Show the form for editing the specified colocation
     */
    public function edit(Colocation $colocation)
    {
        // Only owners can edit
        abort_unless($colocation->isOwner(auth()->user()), 403);

        return view('colocations.edit', compact('colocation'));
    }

    /**
     * Update the specified colocation
     */
    public function update(UpdateColocationRequest $request, Colocation $colocation)
    {
        try {
            $colocation->update($request->validated());

            return redirect()
                ->route('colocations.show', $colocation)
                ->with('success', 'Colocation updated successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update colocation. Please try again.');
        }
    }

    /**
     * Remove the specified colocation (soft delete by changing status)
     */
    public function destroy(Colocation $colocation)
    {
        // Only owners can delete
        abort_unless($colocation->isOwner(auth()->user()), 403);

        try {
            DB::transaction(function () use ($colocation) {
                // Mark as cancelled instead of hard delete
                $colocation->update(['status' => 'cancelled']);
                
                // Mark all memberships as left
                $colocation->memberships()
                    ->whereNull('left_at')
                    ->update(['left_at' => now()]);
            });

            return redirect()
                ->route('colocations.index')
                ->with('success', 'Colocation cancelled successfully!');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to cancel colocation. Please try again.');
        }
    }
}
