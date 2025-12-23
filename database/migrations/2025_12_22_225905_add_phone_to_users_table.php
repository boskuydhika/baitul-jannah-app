<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk menambahkan field phone dan polymorphic ke users table.
 * 
 * Field yang ditambahkan:
 * - phone: Nomor HP untuk login (menggantikan email sebagai identifier utama)
 * - userable_type/userable_id: Polymorphic relation ke Teacher/Guardian/Staff
 * - is_active: Status aktif user
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nomor HP untuk login (wajib, unik)
            $table->string('phone', 20)->unique()->after('name');

            // Email jadi optional
            $table->string('email')->nullable()->change();

            // Polymorphic relation ke profile (Teacher, Guardian, Staff)
            $table->nullableMorphs('userable');

            // Status aktif
            $table->boolean('is_active')->default(true)->after('remember_token');

            // Soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'userable_type', 'userable_id', 'is_active']);
            $table->dropSoftDeletes();
        });
    }
};
