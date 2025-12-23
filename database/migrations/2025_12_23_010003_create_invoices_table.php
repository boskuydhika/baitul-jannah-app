<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk Invoice (Tagihan SPP/Infaq).
 * 
 * Tagihan bulanan untuk santri TPQ dan TAUD.
 */
return new class extends Migration {
    public function up(): void
    {
        // Master tagihan
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Nomor invoice (auto-generated)
            $table->string('invoice_number', 30)->unique();

            // Santri yang ditagih
            $table->foreignId('student_id')->constrained()->restrictOnDelete();

            // Periode tagihan
            $table->unsignedSmallInteger('year'); // Tahun ajaran
            $table->unsignedTinyInteger('month'); // Bulan (1-12)

            // Tanggal invoice dan jatuh tempo
            $table->date('invoice_date');
            $table->date('due_date');

            // Total tagihan
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            // Pembayaran
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0); // Sisa tagihan

            // Status
            $table->enum('status', ['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'])
                ->default('draft');

            // Catatan
            $table->text('notes')->nullable();

            // User yang membuat
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: 1 santri 1 invoice per bulan
            $table->unique(['student_id', 'year', 'month']);

            // Index
            $table->index(['year', 'month', 'status']);
            $table->index(['due_date', 'status']);
        });

        // Detail item tagihan
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();

            // Jenis tagihan
            $table->string('item_type', 50); // spp, infaq, daftar_ulang, seragam, etc
            $table->string('description');

            // Jumlah dan harga
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('amount', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
