<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk kategori transaksi default.
 */
class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Pemasukan (Income)
            ['name' => 'SPP Bulanan', 'type' => 'income', 'description' => 'Pembayaran SPP bulanan santri', 'sort_order' => 1],
            ['name' => 'Uang Pangkal', 'type' => 'income', 'description' => 'Uang pendaftaran santri baru', 'sort_order' => 2],
            ['name' => 'Uang Kegiatan', 'type' => 'income', 'description' => 'Iuran untuk kegiatan khusus', 'sort_order' => 3],
            ['name' => 'Infaq', 'type' => 'income', 'description' => 'Infaq dari santri atau donatur', 'sort_order' => 4],
            ['name' => 'Donasi', 'type' => 'income', 'description' => 'Donasi dari pihak luar', 'sort_order' => 5],
            ['name' => 'Pemasukan Lainnya', 'type' => 'income', 'description' => 'Pemasukan lain-lain', 'sort_order' => 99],

            // Pengeluaran (Expense)  
            ['name' => 'Beli ATK', 'type' => 'expense', 'description' => 'Pembelian alat tulis kantor', 'sort_order' => 1],
            ['name' => 'Beli Konsumsi', 'type' => 'expense', 'description' => 'Pembelian makanan/snack', 'sort_order' => 2],
            ['name' => 'Beli Perlengkapan', 'type' => 'expense', 'description' => 'Pembelian perlengkapan lainnya', 'sort_order' => 3],
            ['name' => 'Transport/Bensin', 'type' => 'expense', 'description' => 'Biaya transportasi atau bensin', 'sort_order' => 4],
            ['name' => 'Listrik & Air', 'type' => 'expense', 'description' => 'Pembayaran tagihan listrik dan air', 'sort_order' => 5],
            ['name' => 'Honor Pengajar', 'type' => 'expense', 'description' => 'Pembayaran honor guru/ustadz', 'sort_order' => 6],
            ['name' => 'Perawatan Gedung', 'type' => 'expense', 'description' => 'Biaya perawatan dan kebersihan', 'sort_order' => 7],
            ['name' => 'Pengeluaran Lainnya', 'type' => 'expense', 'description' => 'Pengeluaran lain-lain', 'sort_order' => 99],
        ];

        foreach ($categories as $category) {
            TransactionCategory::firstOrCreate(
                ['name' => $category['name'], 'type' => $category['type']],
                $category
            );
        }
    }
}
