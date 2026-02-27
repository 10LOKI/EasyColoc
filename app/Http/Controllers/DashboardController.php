<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $colocations = auth()->user()->colocations;
        
        $stats = [
            'colocations' => $colocations->count(),
            'total_expenses' => 0,
            'balance' => 0,
            'pending_settlements' => 0,
        ];

        $recentExpenses = collect();

        return view('dashboard', compact('stats', 'colocations', 'recentExpenses'));
    }
}
