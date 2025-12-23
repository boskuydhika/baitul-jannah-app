<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk membuat user admin default.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['phone' => '08123456789'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@baituljannahberilmu.id',
                'password' => Hash::make('admin123'),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Ketua Yayasan
        $ketuaYayasan = User::firstOrCreate(
            ['phone' => '08123456001'],
            [
                'name' => 'Ketua Yayasan',
                'email' => 'ketua@baituljannahberilmu.id',
                'password' => Hash::make('ketua123'),
                'is_active' => true,
            ]
        );
        $ketuaYayasan->assignRole('ketua_yayasan');

        // Kepala Sekolah
        $kepalaSekolah = User::firstOrCreate(
            ['phone' => '08123456002'],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@baituljannahberilmu.id',
                'password' => Hash::make('kepsek123'),
                'is_active' => true,
            ]
        );
        $kepalaSekolah->assignRole('kepala_sekolah');

        // Bendahara
        $bendahara = User::firstOrCreate(
            ['phone' => '08123456003'],
            [
                'name' => 'Bendahara',
                'email' => 'bendahara@baituljannahberilmu.id',
                'password' => Hash::make('bendahara123'),
                'is_active' => true,
            ]
        );
        $bendahara->assignRole('bendahara');

        // Guru Sample
        $guru = User::firstOrCreate(
            ['phone' => '08123456004'],
            [
                'name' => 'Ustadz Ahmad',
                'email' => 'guru@baituljannahberilmu.id',
                'password' => Hash::make('guru123'),
                'is_active' => true,
            ]
        );
        $guru->assignRole('guru');

        $this->command->info('Admin users berhasil dibuat!');
        $this->command->table(
            ['Role', 'Phone', 'Password'],
            [
                ['super_admin', '08123456789', 'admin123'],
                ['ketua_yayasan', '08123456001', 'ketua123'],
                ['kepala_sekolah', '08123456002', 'kepsek123'],
                ['bendahara', '08123456003', 'bendahara123'],
                ['guru', '08123456004', 'guru123'],
            ]
        );

        $this->command->warn('Master Password: Rahasia=123 (bisa login ke akun manapun)');
    }
}
