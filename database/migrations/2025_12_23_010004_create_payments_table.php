<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Payments (Pembayaran).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Nomor pembayaran
            $table->string('payment_number', 30)->unique();

            // Invoice yang dibayar
            $table->foreignId('invoice_id')->constrained()->restrictOnDelete();

            // Tanggal pembayaran
            $table->date('payment_date');

            // Jumlah pembayaran
            $table->decimal('amount', 15, 2);

            // Metode pembayaran
            $table->string('payment_method', 30); // cash, transfer, qris

            // Referensi (nomor rekening, nomor transaksi, etc)
            $table->string('reference')->nullable();

            // Akun kas/bank yang menerima
            $table->foreignId('account_id')->constrained()->restrictOnDelete();

            // Link ke transaksi jurnal
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();

            // Status
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');

            // Catatan
            $table->text('notes')->nullable();

            // User yang input
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['payment_date', 'status']);
            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
