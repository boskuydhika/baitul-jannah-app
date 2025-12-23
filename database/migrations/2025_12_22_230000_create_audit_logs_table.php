<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel audit_logs di database terpisah.
 * 
 * Tabel ini mencatat SEMUA perubahan data untuk audit trail yang lengkap.
 * Menggunakan koneksi 'logs' yang terpisah dari database utama.
 * 
 * Best Practices yang diimplementasikan:
 * 1. Database terpisah untuk tidak membebani DB utama
 * 2. Tidak ada updated_at (log tidak boleh diubah)
 * 3. JSON untuk menyimpan old/new values (flexible schema)
 * 4. Index untuk query yang sering digunakan
 */
return new class extends Migration {
    /**
     * Koneksi yang digunakan untuk migration ini.
     */
    protected $connection = 'logs';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop dulu jika ada (karena migrate:fresh tidak drop tabel di DB terpisah)
        Schema::connection('logs')->dropIfExists('audit_logs');

        Schema::connection('logs')->create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Siapa yang melakukan aksi
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_type')->nullable(); // Class name user

            // Aksi yang dilakukan
            $table->string('action', 50)->index(); // create, update, delete, login, logout, export

            // Model/data yang diubah
            $table->string('model_type')->index(); // Class name model
            $table->unsignedBigInteger('model_id')->index(); // ID record

            // Data sebelum dan sesudah perubahan (JSON)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Request information
            $table->string('ip_address', 45)->nullable(); // IPv4 atau IPv6
            $table->text('user_agent')->nullable();
            $table->string('request_url', 500)->nullable();
            $table->string('request_method', 10)->nullable();

            // Timestamp (hanya created, tidak ada updated)
            $table->timestamp('created_at')->useCurrent()->index();

            // Composite index untuk query yang sering digunakan
            $table->index(['model_type', 'model_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('logs')->dropIfExists('audit_logs');
    }
};
