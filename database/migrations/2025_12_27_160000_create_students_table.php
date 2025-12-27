<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique()->comment('Nomor Induk Santri');
            $table->string('name', 100);
            $table->enum('type', ['tpq', 'taud'])->comment('Jenis: TPQ atau TAUD');
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->text('address')->nullable();

            // Parent info
            $table->string('parent_name', 100)->nullable();
            $table->string('parent_phone', 20)->nullable();

            // Academic info
            $table->string('academic_year', 9)->comment('Tahun ajaran masuk, format: 2025/2026');
            $table->date('entry_date')->nullable()->comment('Tanggal masuk');

            // Payment info
            $table->decimal('monthly_fee', 12, 2)->default(0)->comment('Iuran/SPP bulanan');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
