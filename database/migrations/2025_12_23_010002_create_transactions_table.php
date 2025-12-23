<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Transactions (Jurnal Umum).
 * 
 * Implementasi double-entry bookkeeping:
 * - Setiap transaksi HARUS balance (total debit = total credit)
 * - Satu transaksi bisa memiliki banyak detail (multi-line journal)
 */
return new class extends Migration {
    public function up(): void
    {
        // Tabel header transaksi
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Nomor transaksi (auto-generated)
            $table->string('transaction_number', 30)->unique();

            // Tanggal transaksi
            $table->date('transaction_date');

            // Tipe transaksi untuk kategorisasi
            $table->string('type', 30); // payment, receipt, journal, invoice, expense

            // Referensi ke dokumen terkait (polymorphic)
            $table->nullableMorphs('reference'); // invoice_id, payment_id, etc

            // Deskripsi transaksi
            $table->text('description');

            // Total amount (untuk display, seharusnya = total debit = total credit)
            $table->decimal('amount', 15, 2)->default(0);

            // User yang membuat
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Status transaksi
            $table->enum('status', ['draft', 'posted', 'void'])->default('draft');

            // Tanggal posting (jika sudah diposting)
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();

            // Tanggal void (jika dibatalkan)
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['transaction_date', 'status']);
            $table->index(['type', 'status']);
        });

        // Tabel detail transaksi (debit/credit lines)
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();

            // Debit dan Credit (salah satu harus 0)
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);

            // Keterangan per line
            $table->string('description')->nullable();

            // Urutan baris
            $table->unsignedSmallInteger('line_order')->default(0);

            $table->timestamps();

            // Index
            $table->index(['account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
        Schema::dropIfExists('transactions');
    }
};
