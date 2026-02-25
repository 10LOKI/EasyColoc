<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = collect();
        $colocations = collect();
        $categories = collect();
        return view('expenses.index', compact('expenses', 'colocations', 'categories'));
    }

    public function create()
    {
        $colocations = collect();
        $categories = collect();
        return view('expenses.create', compact('colocations', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'colocation_id' => 'required',
            'category_id' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
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
