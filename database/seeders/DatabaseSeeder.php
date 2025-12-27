<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main database seeder untuk Baitul Jannah Super App.
 * 
 * Urutan seeding:
 * 1. RoleSeeder - Buat roles dan permissions
 * 2. AdminSeeder - Buat user admin default
 * 3. TransactionCategorySeeder - Kategori transaksi
 * 4. StudentSeeder - Data santri dummy
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            TransactionCategorySeeder::class,
            StudentSeeder::class,
        ]);
    }
}
