<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Baitul Jannah Super App
|--------------------------------------------------------------------------
|
| Semua routes API dengan prefix /api/v1
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

// ==========================================================================
// API Version 1
// ==========================================================================
Route::prefix('v1')->group(function () {

    // ----------------------------------------------------------------------
    // Authentication Routes (Public)
    // ----------------------------------------------------------------------
    Route::prefix('auth')->group(function () {
        Route::post('login', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login'])
            ->name('api.auth.login');
    });

    // ----------------------------------------------------------------------
    // Protected Routes (Requires Authentication)
    // ----------------------------------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {

        // Auth routes yang perlu login
        Route::prefix('auth')->group(function () {
            Route::post('logout', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'logout'])
                ->name('api.auth.logout');
            Route::get('me', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'me'])
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
            Route::get('accounts/next-code', [App\Http\Controllers\Api\V1\Finance\AccountController::class, 'nextCode']);
            Route::apiResource('accounts', App\Http\Controllers\Api\V1\Finance\AccountController::class);

            // Transactions (Jurnal Umum)
            Route::post('transactions/{transaction}/post', [App\Http\Controllers\Api\V1\Finance\TransactionController::class, 'post']);
            Route::post('transactions/{transaction}/void', [App\Http\Controllers\Api\V1\Finance\TransactionController::class, 'void']);
            Route::apiResource('transactions', App\Http\Controllers\Api\V1\Finance\TransactionController::class);

            // Invoices (Tagihan)
            Route::post('invoices/generate', [App\Http\Controllers\Api\V1\Finance\InvoiceController::class, 'generate']);
            Route::post('invoices/{invoice}/send', [App\Http\Controllers\Api\V1\Finance\InvoiceController::class, 'send']);
            Route::apiResource('invoices', App\Http\Controllers\Api\V1\Finance\InvoiceController::class);

            // Payments (Pembayaran)
            Route::apiResource('payments', App\Http\Controllers\Api\V1\Finance\PaymentController::class)->except(['update']);

            // Reports (TODO)
            // Route::get('reports/cash-flow', [ReportController::class, 'cashFlow']);
            // Route::get('reports/balance-sheet', [ReportController::class, 'balanceSheet']);
            // Route::get('reports/income-statement', [ReportController::class, 'incomeStatement']);
        });

        // ------------------------------------------------------------------
        // Academic Module (Students, Classes, Records)
        // ------------------------------------------------------------------
        Route::prefix('academic')->group(function () {
            // Students
            Route::apiResource('students', App\Http\Controllers\Api\V1\Academic\StudentController::class);

            // TODO: Implement remaining Academic Controllers
            // Route::apiResource('classes', ClassController::class);
            // Route::apiResource('records', AcademicRecordController::class);
        });

        // ------------------------------------------------------------------
        // PPDB Module (Student Admission)
        // ------------------------------------------------------------------
        Route::prefix('ppdb')->group(function () {
            // TODO: Implement PPDB Controllers
            // Route::apiResource('registrations', RegistrationController::class);
            // Route::apiResource('selections', SelectionController::class);
        });
    });

});

// ==========================================================================
// Health Check (untuk monitoring)
// ==========================================================================
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'app' => config('app.name'),
        'version' => '1.0.0',
    ]);
})->name('api.health');
