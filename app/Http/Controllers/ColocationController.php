<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ColocationController extends Controller
{
    public function index()
    {
        $colocations = collect();
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
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return redirect()->route('colocations.index')->with('success', 'Colocation created!');
    }

    public function show(string $id)
    {
        return redirect()->route('colocations.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('colocations.index');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('colocations.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('colocations.index');
    }
}
