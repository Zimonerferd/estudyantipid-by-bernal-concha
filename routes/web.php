<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Student\AnalyticsController;
use App\Http\Controllers\Student\BudgetController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ExpenseController;
use App\Http\Controllers\Student\MessageController;
use App\Http\Controllers\Student\SavingsGoalController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// ---- Guests only ----
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showStudent'])->name('login');
    Route::post('/login', [LoginController::class, 'student']);

    Route::get('/admin/login',  [LoginController::class, 'showAdmin'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'admin']);

    Route::get('/register',  [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ---- Student area ----
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/expenses',  [ExpenseController::class, 'index'])->name('expenses');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    Route::get('/budgets', BudgetController::class)->name('budgets');

    Route::get('/goals',               [SavingsGoalController::class, 'index'])->name('goals');
    Route::get('/goals/new',           [SavingsGoalController::class, 'create'])->name('goals.create');
    Route::post('/goals',              [SavingsGoalController::class, 'store'])->name('goals.store');
    Route::post('/goals/{goal}/add',   [SavingsGoalController::class, 'deposit'])->name('goals.deposit');

    Route::get('/analytics', AnalyticsController::class)->name('analytics');

    Route::get('/messages',  [MessageController::class, 'index'])->name('messages');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
});

// ---- Admin area ----
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Placeholder so admin login has somewhere to land. Replace when we build Overview-Admin.
    Route::view('/overview', 'admin.overview')->name('overview');
});
