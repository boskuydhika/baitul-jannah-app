<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Buku Kas (Transaksi Sederhana).
 * 
 * Implementasi single-entry untuk cash book:
 * - Setiap transaksi adalah income atau expense
 * - Tidak ada double-entry bookkeeping
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Nomor transaksi (auto-generated)
            $table->string('transaction_number', 30)->unique();

            // Tanggal & waktu transaksi
            $table->datetime('transaction_datetime');

            // Tipe: income atau expense
            $table->enum('type', ['income', 'expense']);

            // Kategori transaksi
            $table->foreignId('category_id')->nullable()
                ->constrained('transaction_categories')->nullOnDelete();

            // Deskripsi transaksi
            $table->text('description');

            // Nominal
            $table->decimal('amount', 15, 2)->default(0);

            // User yang membuat
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();

            // Status transaksi
            $table->enum('status', ['draft', 'posted'])->default('posted');

            $table->timestamps();
            $table->softDeletes(); // Soft delete untuk keamanan data

            // Indexes
            $table->index(['transaction_datetime', 'status']);
            $table->index(['type', 'status']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
