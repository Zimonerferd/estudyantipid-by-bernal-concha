<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\OverviewController as AdminOverviewController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Student\AnalyticsController;
use App\Http\Controllers\Student\BudgetController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ExpenseController;
use App\Http\Controllers\Student\MessageController;
use App\Http\Controllers\Student\ReportController;
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

    Route::get('/goals',             [SavingsGoalController::class, 'index'])->name('goals');
    Route::get('/goals/new',         [SavingsGoalController::class, 'create'])->name('goals.create');
    Route::post('/goals',            [SavingsGoalController::class, 'store'])->name('goals.store');
    Route::post('/goals/{goal}/add', [SavingsGoalController::class, 'deposit'])->name('goals.deposit');

    Route::get('/analytics', AnalyticsController::class)->name('analytics');

    Route::get('/reports',        [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/messages',  [MessageController::class, 'index'])->name('messages');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
});

// ---- Admin area ----
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/overview', AdminOverviewController::class)->name('overview');

    Route::get('/students',                  [AdminStudentController::class, 'index'])->name('students');
    Route::post('/students/{student}/toggle', [AdminStudentController::class, 'toggle'])->name('students.toggle');

    Route::get('/categories',                [AdminCategoryController::class, 'index'])->name('categories');
    Route::post('/categories',               [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}',  [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/feedback',                    [AdminFeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback/{message}/reply',   [AdminFeedbackController::class, 'reply'])->name('feedback.reply');
    Route::post('/feedback/{message}/resolve', [AdminFeedbackController::class, 'resolve'])->name('feedback.resolve');
});
