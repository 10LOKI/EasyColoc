<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'colocation_id' => 'required|exists:colocations,id',
            'name' => 'required|string|max:100',
        ]);

        $colocation = Colocation::findOrFail($data['colocation_id']);
        abort_unless($colocation->isMember(auth()->user()), 403);

        $exists = Category::query()
            ->where('colocation_id', $colocation->id)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($data['name']))])
            ->exists();

        if ($exists) {
            return back()->with('error', 'This category already exists in the selected colocation.');
        }

        Category::create([
            'colocation_id' => $colocation->id,
            'name' => trim($data['name']),
        ]);

        return redirect()
            ->route('expenses.create', ['colocation' => $colocation->id])
            ->with('success', 'Category created successfully.');
    }
}
