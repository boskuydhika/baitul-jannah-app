<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Chart of Accounts (COA).
 * 
 * COA adalah bagan akun yang menjadi fondasi sistem akuntansi.
 * Menggunakan struktur hierarki (parent-child) untuk grouping.
 * 
 * Contoh struktur:
 * 1. ASET
 *    1.1 Kas & Bank
 *        1.1.01 Kas Besar
 *        1.1.02 Bank BRI
 *    1.2 Piutang
 * 2. KEWAJIBAN
 * 3. EKUITAS
 * 4. PENDAPATAN
 *    4.1 SPP/Infaq
 *    4.2 Donasi
 * 5. BEBAN
 *    5.1 Gaji
 *    5.2 Operasional
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // Kode akun (contoh: 1.1.01)
            $table->string('code', 20)->unique();

            // Nama akun
            $table->string('name');

            // Tipe akun (asset, liability, equity, income, expense)
            $table->string('type', 20);

            // Parent akun untuk hierarki (null = akun utama/header)
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();

            // Level dalam hierarki (1 = root, 2 = child, dst)
            $table->unsignedTinyInteger('level')->default(1);

            // Apakah akun ini bisa diisi transaksi (false = header/group only)
            $table->boolean('is_postable')->default(true);

            // Saldo normal (debit/credit)
            $table->string('normal_balance', 10)->default('debit');

            // Saldo saat ini (untuk quick lookup)
            $table->decimal('current_balance', 15, 2)->default(0);

            // Deskripsi/keterangan
            $table->text('description')->nullable();

            // Status aktif
            $table->boolean('is_active')->default(true);

            // Urutan tampilan
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Index untuk query yang sering
            $table->index(['type', 'is_active']);
            $table->index(['parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
