<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Students (Santri), Guardians (Wali), dan Classes (Kelas).
 * 
 * Urutan pembuatan:
 * 1. guardians - tidak ada dependency
 * 2. classes - tidak ada dependency 
 * 3. students - depends on guardians dan classes
 */
return new class extends Migration {
    public function up(): void
    {
        // 1. Wali Santri (tidak ada foreign key ke tabel lain)
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();

            // Link ke user account (opsional)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Data wali
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            // Pekerjaan (untuk data ekonomi)
            $table->string('occupation')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Kelas (tidak ada foreign key selain ke users)
        Schema::create('classes', function (Blueprint $table) {
            $table->id();

            // Nama kelas
            $table->string('name'); // TK-A Umar, Jilid 3 Pagi, etc

            // Program
            $table->enum('program_type', ['tpq', 'taud']);

            // Khusus TAUD
            $table->enum('taud_level', ['kb', 'tk_a', 'tk_b'])->nullable();

            // Khusus TPQ (jilid yang diajar)
            $table->unsignedTinyInteger('jilid_level')->nullable();

            // Tahun ajaran
            $table->string('academic_year', 9); // 2024/2025

            // Guru wali kelas
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();

            // Kapasitas
            $table->unsignedSmallInteger('capacity')->default(30);

            // Jadwal
            $table->string('schedule')->nullable(); // Senin-Jumat 08:00-10:00

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['program_type', 'academic_year', 'is_active']);
        });

        // 3. Santri (setelah guardians dan classes dibuat)
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Nomor Induk Santri
            $table->string('nis', 20)->unique();

            // Data santri
            $table->string('name');
            $table->string('nickname')->nullable(); // Nama panggilan
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date');
            $table->string('birth_place')->nullable();

            // Alamat
            $table->text('address')->nullable();

            // Wali santri
            $table->foreignId('guardian_id')->constrained()->restrictOnDelete();

            // Program (TPQ atau TAUD)
            $table->enum('program_type', ['tpq', 'taud']);

            // Khusus TAUD
            $table->enum('taud_level', ['kb', 'tk_a', 'tk_b'])->nullable();

            // Khusus TPQ
            $table->unsignedTinyInteger('current_jilid')->nullable(); // 1-6, 7=Quran

            // Kelas saat ini (sudah ada karena classes dibuat duluan)
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();

            // Status
            $table->enum('status', ['pending', 'active', 'graduated', 'dropped'])->default('pending');
            $table->date('entry_date')->nullable();
            $table->date('graduation_date')->nullable();

            // Foto
            $table->string('photo')->nullable();

            // Biaya khusus (override dari default)
            $table->decimal('monthly_fee', 15, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index(['program_type', 'status']);
            $table->index(['class_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('guardians');
    }
};
