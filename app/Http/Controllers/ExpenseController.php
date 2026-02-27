<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $colocations = auth()->user()
            ->memberships()
            ->active()
            ->with('colocation')
            ->get()
            ->pluck('colocation')
            ->filter()
            ->values();

        $colocationIds = $colocations->pluck('id');

        $expenses = Expense::query()
            ->with(['payer', 'category'])
            ->whereIn('colocation_id', $colocationIds)
            ->when(request('colocation'), function ($query, $colocationId) use ($colocationIds) {
                if ($colocationIds->contains((int) $colocationId)) {
                    $query->where('colocation_id', $colocationId);
                }
            })
            ->when(request('date_from'), fn ($query, $dateFrom) => $query->whereDate('created_at', '>=', $dateFrom))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = collect();

        return view('expenses.index', compact('expenses', 'colocations', 'categories'));
    }

    public function create()
    {
        $colocations = auth()->user()
            ->memberships()
            ->active()
            ->with('colocation')
            ->get()
            ->pluck('colocation')
            ->filter()
            ->values();

        $categories = collect();

        return view('expenses.create', compact('colocations', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colocation_id' => 'required|exists:colocations,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $isMember = auth()->user()
            ->memberships()
            ->active()
            ->where('colocation_id', $data['colocation_id'])
            ->exists();

        abort_unless($isMember, 403);

        Expense::create([
            'colocation_id' => $data['colocation_id'],
            'paid_by' => auth()->id(),
            'description' => $data['description'],
            'amount' => $data['amount'],
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense added!');
    }

    public function show(string $id)
    {
        return redirect()->route('expenses.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('expenses.index');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('expenses.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('expenses.index');
    }
}
