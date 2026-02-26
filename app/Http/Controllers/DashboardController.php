<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // nakhdo les colocs dial dak l user li mconecti 
        $colocations = auth()->user()->memberships()->with(['colocation.memberships.user']) // Eager loading pour éviter N+1
            ->active() // Seulement les memberships actifs
            ->get()
            ->pluck('colocation') // Extraire les colocations
            ->filter();
        // n7essbo les stats
        $stats = [
            'colocations' => $colocations->count(),
            'total_expenses' => 0, // gha nbedelha men be3d mli newssel l depenses
            'balance' => 0,
            'pending_settlements' => 0,
        ];

        // $colocations = collect();
        $recentExpenses = collect();

        return view('dashboard', compact('stats', 'colocations', 'recentExpenses'));
    }
}
