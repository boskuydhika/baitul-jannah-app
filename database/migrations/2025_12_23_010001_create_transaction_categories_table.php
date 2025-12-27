<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Kategori Transaksi.
 * 
 * Kategori dinamis untuk buku kas sederhana.
 * Contoh: Bayar SPP, Uang Pangkal, Beli ATK, Listrik & Air, dll.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();

            // Nama kategori
            $table->string('name', 100);

            // Tipe: income (pemasukan) atau expense (pengeluaran)
            $table->enum('type', ['income', 'expense']);

            // Deskripsi opsional
            $table->string('description')->nullable();

            // Status aktif
            $table->boolean('is_active')->default(true);

            // Urutan tampilan
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // Index
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_categories');
    }
};
