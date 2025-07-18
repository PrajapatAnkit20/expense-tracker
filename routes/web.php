<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PersonalExpenseController;
use App\Http\Controllers\AnalyticsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Personal Expenses
Route::get('/personal', [PersonalExpenseController::class, 'index'])->name('personal.index');
Route::post('/personal', [PersonalExpenseController::class, 'store'])->name('personal.store');
Route::delete('/personal/{expense}', [PersonalExpenseController::class, 'destroy'])->name('personal.destroy');

// API routes for AJAX requests
Route::get('/api/personal/expenses', [PersonalExpenseController::class, 'getExpenses'])->name('api.personal.expenses');

// Group routes
Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('/', [GroupController::class, 'index'])->name('index');
    Route::get('/create', [GroupController::class, 'create'])->name('create');
    Route::post('/', [GroupController::class, 'store'])->name('store');
    Route::get('/{group}', [GroupController::class, 'show'])->name('show');
    Route::get('/{group}/edit', [GroupController::class, 'edit'])->name('edit');
    Route::put('/{group}', [GroupController::class, 'update'])->name('update');
    Route::delete('/{group}', [GroupController::class, 'destroy'])->name('destroy');

    // Member management
    Route::post('/{group}/members', [GroupController::class, 'addMember'])->name('add-member');
    Route::delete('/{group}/members/{member}', [GroupController::class, 'removeMember'])->name('remove-member');

    // Expense management
    Route::post('/{group}/expenses', [GroupController::class, 'storeExpense'])->name('store-expense');
    Route::get('/{group}/expenses/{expense}', [GroupController::class, 'showExpense'])->name('show-expense');
    Route::delete('/{group}/expenses/{expense}', [GroupController::class, 'deleteExpense'])->name('delete-expense');

    // Split settlement
    Route::post('/{group}/splits/{split}/settle', [GroupController::class, 'settleSplit'])->name('settle-split');

    // Statistics and export
    Route::get('/{group}/statistics', [GroupController::class, 'statistics'])->name('statistics');
    Route::get('/{group}/export', [GroupController::class, 'export'])->name('export');
});

// Analytics
Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
