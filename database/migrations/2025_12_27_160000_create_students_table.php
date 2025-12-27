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

            // NIS Format: TPQA250012 (TPQ Pagi), TPQB250012 (TPQ Sore), TAUD250012 (TAUD)
            $table->string('nis', 20)->unique()->comment('Nomor Induk Santri');
            $table->string('name', 100);
            $table->string('nickname', 50)->nullable()->comment('Nama panggilan');

            // Program: tpq atau taud
            $table->enum('type', ['tpq', 'taud'])->comment('Jenis: TPQ atau TAUD');

            // Waktu Kelas: pagi atau sore (TAUD hanya pagi)
            $table->enum('class_time', ['pagi', 'sore'])->default('pagi')
                ->comment('Waktu kelas: pagi (TPQA/TAUD) atau sore (TPQB)');

            $table->enum('gender', ['L', 'P'])->default('L');
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->text('address')->nullable();

            // Data Ayah (Father)
            $table->string('father_name', 100)->nullable();
            $table->string('father_occupation', 100)->nullable()->comment('Pekerjaan Ayah');
            $table->string('father_phone', 20)->nullable()->comment('No HP Ayah');
            $table->string('father_wa', 20)->nullable()->comment('No WA Ayah (jika berbeda)');

            // Data Ibu (Mother)
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_occupation', 100)->nullable()->comment('Pekerjaan Ibu');
            $table->string('mother_phone', 20)->nullable()->comment('No HP Ibu');
            $table->string('mother_wa', 20)->nullable()->comment('No WA Ibu (jika berbeda)');

            // Registration & Entry info
            $table->date('registration_date')->nullable()->comment('Tanggal daftar');
            $table->year('entry_year')->comment('Tahun masuk belajar (YY), untuk generate NIS');

            // Payment info
            $table->decimal('monthly_fee', 12, 2)->default(0)->comment('Iuran/SPP bulanan');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('class_time');
            $table->index('is_active');
            $table->index('entry_year');
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
