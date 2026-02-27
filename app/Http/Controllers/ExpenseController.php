<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
            ->when(request('category'), fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when(request('date_from'), fn ($query, $dateFrom) => $query->whereDate('created_at', '>=', $dateFrom))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::query()
            ->whereNull('colocation_id')
            ->orWhereIn('colocation_id', $colocationIds)
            ->orderBy('name')
            ->get();

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

        $selectedColocationId = (int) (old('colocation_id', request('colocation', $colocations->first()?->id)));

        $categoriesByColocation = [];
        foreach ($colocations as $colocation) {
            $categoriesByColocation[$colocation->id] = Category::query()
                ->whereNull('colocation_id')
                ->orWhere('colocation_id', $colocation->id)
                ->orderBy('name')
                ->get(['id', 'name', 'colocation_id']);
        }

        $categories = collect($categoriesByColocation[$selectedColocationId] ?? []);

        return view('expenses.create', compact('colocations', 'categories', 'categoriesByColocation', 'selectedColocationId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colocation_id' => 'required|exists:colocations,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $isMember = auth()->user()
            ->memberships()
            ->active()
            ->where('colocation_id', $data['colocation_id'])
            ->exists();

        abort_unless($isMember, 403);

        $categoryIsAllowed = Category::query()
            ->where('id', $data['category_id'])
            ->where(function ($query) use ($data) {
                $query->whereNull('colocation_id')
                    ->orWhere('colocation_id', $data['colocation_id']);
            })
            ->exists();

        abort_unless($categoryIsAllowed, 422);

        Expense::create([
            'colocation_id' => $data['colocation_id'],
            'paid_by' => auth()->id(),
            'category_id' => $data['category_id'],
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
