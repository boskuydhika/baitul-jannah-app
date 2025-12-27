<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk data dummy santri.
 */
class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'nis' => 'TPQ25001',
                'name' => 'Ahmad Fauzi',
                'type' => 'tpq',
                'gender' => 'L',
                'birth_date' => '2017-03-15',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Contoh No. 1, Bandung',
                'parent_name' => 'Budi Santoso',
                'parent_phone' => '081234567001',
                'academic_year' => '2025/2026',
                'entry_date' => '2025-07-14',
                'monthly_fee' => 50000,
                'is_active' => true,
            ],
            [
                'nis' => 'TPQ25002',
                'name' => 'Aisyah Putri',
                'type' => 'tpq',
                'gender' => 'P',
                'birth_date' => '2018-06-20',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Melati No. 5, Bandung',
                'parent_name' => 'Agus Setiawan',
                'parent_phone' => '081234567002',
                'academic_year' => '2025/2026',
                'entry_date' => '2025-07-14',
                'monthly_fee' => 50000,
                'is_active' => true,
            ],
            [
                'nis' => 'TPQ25003',
                'name' => 'Muhammad Rizki',
                'type' => 'tpq',
                'gender' => 'L',
                'birth_date' => '2016-11-08',
                'birth_place' => 'Cimahi',
                'address' => 'Jl. Anggrek No. 10, Cimahi',
                'parent_name' => 'Dedi Kurniawan',
                'parent_phone' => '081234567003',
                'academic_year' => '2025/2026',
                'entry_date' => '2025-07-14',
                'monthly_fee' => 75000,
                'is_active' => true,
            ],
            [
                'nis' => 'TAU25001',
                'name' => 'Fatimah Zahra',
                'type' => 'taud',
                'gender' => 'P',
                'birth_date' => '2020-01-25',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Mawar No. 3, Bandung',
                'parent_name' => 'Eko Prasetyo',
                'parent_phone' => '081234567004',
                'academic_year' => '2025/2026',
                'entry_date' => '2025-07-14',
                'monthly_fee' => 100000,
                'is_active' => true,
            ],
            [
                'nis' => 'TAU25002',
                'name' => 'Abdullah Rasyid',
                'type' => 'taud',
                'gender' => 'L',
                'birth_date' => '2019-09-12',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Dahlia No. 7, Bandung',
                'parent_name' => 'Faisal Rahman',
                'parent_phone' => '081234567005',
                'academic_year' => '2025/2026',
                'entry_date' => '2025-07-14',
                'monthly_fee' => 100000,
                'is_active' => true,
            ],
        ];

        foreach ($students as $studentData) {
            Student::firstOrCreate(
                ['nis' => $studentData['nis']],
                $studentData
            );
        }

        $this->command->info('5 santri dummy berhasil dibuat!');
        $this->command->table(
            ['NIS', 'Nama', 'Jenis', 'SPP/bulan'],
            collect($students)->map(fn($s) => [
                $s['nis'],
                $s['name'],
                strtoupper($s['type']),
                'Rp ' . number_format($s['monthly_fee'], 0, ',', '.'),
            ])->toArray()
        );
    }
}
