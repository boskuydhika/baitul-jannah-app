<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\Finance\AccountController;
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
        return Inertia::render('Dashboard'); // Placeholder Dashboard
    })->name('dashboard');

    // Finance Module
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    });
});
