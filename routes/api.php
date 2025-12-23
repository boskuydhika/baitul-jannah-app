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
| - /finance/* - Keuangan (COA, Transaksi, Tagihan)
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
        // Route::apiResource('/', UserController::class);
    });

    // ------------------------------------------------------------------
    // Finance Module (COA, Transactions, Invoices, Payments)
    // ------------------------------------------------------------------
    Route::prefix('finance')->group(function () {
        // Chart of Accounts
        Route::get('accounts/next-code', [App\Http\Controllers\Api\Finance\AccountController::class, 'nextCode']);
        Route::apiResource('accounts', App\Http\Controllers\Api\Finance\AccountController::class);

        // Transactions (Jurnal Umum)
        Route::post('transactions/{transaction}/post', [App\Http\Controllers\Api\Finance\TransactionController::class, 'post']);
        Route::post('transactions/{transaction}/void', [App\Http\Controllers\Api\Finance\TransactionController::class, 'void']);
        Route::apiResource('transactions', App\Http\Controllers\Api\Finance\TransactionController::class);

        // Invoices (Tagihan)
        Route::post('invoices/generate', [App\Http\Controllers\Api\Finance\InvoiceController::class, 'generate']);
        Route::post('invoices/{invoice}/send', [App\Http\Controllers\Api\Finance\InvoiceController::class, 'send']);
        Route::apiResource('invoices', App\Http\Controllers\Api\Finance\InvoiceController::class);

        // Payments (Pembayaran)
        Route::apiResource('payments', App\Http\Controllers\Api\Finance\PaymentController::class)->except(['update']);
    });

    // ------------------------------------------------------------------
    // Academic Module (Students, Classes, Records)
    // ------------------------------------------------------------------
    Route::prefix('academic')->group(function () {
        // Students
        Route::apiResource('students', App\Http\Controllers\Api\Academic\StudentController::class);
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
