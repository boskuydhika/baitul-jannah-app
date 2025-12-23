<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main database seeder untuk Baitul Jannah Super App.
 * 
 * Urutan seeding:
 * 1. RoleSeeder - Buat roles dan permissions
 * 2. AdminSeeder - Buat user admin default
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
            AccountSeeder::class,
            SampleDataSeeder::class,
        ]);
    }
}
