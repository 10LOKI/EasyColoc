<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'colocations' => 0,
            'total_expenses' => 0,
            'balance' => 0,
            'pending_settlements' => 0,
        ];

        $colocations = collect();
        $recentExpenses = collect();

        return view('dashboard', compact('stats', 'colocations', 'recentExpenses'));
    }
}
