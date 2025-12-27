<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Baitul Jannah Super App
|--------------------------------------------------------------------------
|
| Dokumentasi lengkap: /api/documentation (Swagger)
|
| Structure:
| - /auth/* - Autentikasi (login, logout, me)
| - /users/* - User Management (RBAC)
| - /finance/* - Keuangan (Transaksi, Tagihan)
| - /academic/* - Akademik (Santri, Kelas, Nilai)
| - /ppdb/* - Penerimaan Santri Baru
|
*/

// ----------------------------------------------------------------------
// Authentication Routes (Public)
// ----------------------------------------------------------------------
Route::prefix('auth')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\Auth\AuthController::class, 'login'])
        ->name('api.auth.login');
});

// ----------------------------------------------------------------------
// Protected Routes (Requires Authentication)
// ----------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Auth routes yang perlu login
    Route::prefix('auth')->group(function () {
        Route::post('logout', [App\Http\Controllers\Api\Auth\AuthController::class, 'logout'])
            ->name('api.auth.logout');
        Route::get('me', [App\Http\Controllers\Api\Auth\AuthController::class, 'me'])
            ->name('api.auth.me');
    });

    // ------------------------------------------------------------------
    // User Management
    // ------------------------------------------------------------------
    Route::prefix('users')->group(function () {
        // TODO: Implement UserController
    });

    // ------------------------------------------------------------------
    // Finance Module (Transactions, Invoices, Payments)
    // ------------------------------------------------------------------
    Route::prefix('finance')->group(function () {
        // Transactions (Buku Kas)
        Route::post('transactions/{transaction}/post', [App\Http\Controllers\Api\Finance\TransactionController::class, 'post']);
        Route::apiResource('transactions', App\Http\Controllers\Api\Finance\TransactionController::class);
    });

    // ------------------------------------------------------------------
    // Academic Module (Students, Classes, Records)
    // ------------------------------------------------------------------
    Route::prefix('academic')->group(function () {
        // Students - akan diimplementasikan
    });

    // ------------------------------------------------------------------
    // PPDB Module (Student Admission)
    // ------------------------------------------------------------------
    Route::prefix('ppdb')->group(function () {
        // TODO: Implement PPDB Controllers
    });
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
