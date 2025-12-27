<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\Finance\TransactionController;
use App\Http\Controllers\Web\Finance\TransactionCategoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Modern Monolith Routes (Inertia.js)
|
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Finance Module
    Route::prefix('finance')->name('finance.')->group(function () {
        // Transaction Categories (CRUD) - Super Admin & Finance only
        Route::resource('categories', TransactionCategoryController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Transactions (Buku Kas)
        Route::resource('transactions', TransactionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::post('transactions/{transaction}/post', [TransactionController::class, 'post'])
            ->name('transactions.post');
        Route::post('transactions/{transaction}/unpost', [TransactionController::class, 'unpost'])
            ->name('transactions.unpost');
    });
});
