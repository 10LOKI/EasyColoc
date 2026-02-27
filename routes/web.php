<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReputationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified', 'check.banned'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('colocations', ColocationController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::get('/reputations', [ReputationController::class, 'index'])->name('reputations.index');
    Route::post('/reputations/positive', [ReputationController::class, 'storePositive'])->name('reputations.positive');
    Route::post('/reputations/negative', [ReputationController::class, 'storeNegative'])->name('reputations.negative');
    
    // Expense routes within colocations
    Route::post('/colocation/{colocation}/expenses',[ColocationController::class,'addExpense'])->name('colocations.expenses');
    Route::get('/colocation/{colocation}/balances',[ColocationController::class,'calculateBalances'])->name('colocations.balances');
    
    Route::get('/join', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/join', [InvitationController::class, 'join'])->name('invitations.join');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'check.banned', 'admin'])->prefix('admin')->group(function () {
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
});

require __DIR__.'/auth.php';
