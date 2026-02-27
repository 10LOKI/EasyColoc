<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'total_colocations' => Colocation::count(),
            'total_expenses' => Expense::sum('amount'),
            'banned_users' => User::where('is_banned', true)->count(),
            'active_colocations' => Colocation::where('status', 'active')->count(),
        ];

        return view('admin.statistics', compact('stats'));
    }

    public function users()
    {
        $users = User::withCount('colocations')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function banUser(User $user)
    {
        $user->update(['is_banned' => !$user->is_banned]);
        return back()->with('success', $user->is_banned ? 'User banned successfully' : 'User unbanned successfully');
    }
}
