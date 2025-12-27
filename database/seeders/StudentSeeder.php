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
            // TPQ Pagi (TPQA)
            [
                'nis' => 'TPQA250001',
                'name' => 'Ahmad Fauzi',
                'nickname' => 'Fauzi',
                'type' => 'tpq',
                'class_time' => 'pagi',
                'gender' => 'L',
                'birth_date' => '2017-03-15',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Contoh No. 1, Bandung',
                'father_name' => 'Budi Santoso',
                'father_occupation' => 'Wiraswasta',
                'father_phone' => '081234567001',
                'father_wa' => null,
                'mother_name' => 'Siti Aminah',
                'mother_occupation' => 'Ibu Rumah Tangga',
                'mother_phone' => '081234567101',
                'mother_wa' => null,
                'registration_date' => '2024-12-01',
                'entry_year' => 2025,
                'monthly_fee' => 50000,
                'is_active' => true,
            ],
            [
                'nis' => 'TPQA250002',
                'name' => 'Aisyah Putri',
                'nickname' => 'Icha',
                'type' => 'tpq',
                'class_time' => 'pagi',
                'gender' => 'P',
                'birth_date' => '2018-06-20',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Melati No. 5, Bandung',
                'father_name' => 'Agus Setiawan',
                'father_occupation' => 'PNS',
                'father_phone' => '081234567002',
                'father_wa' => '089876543002',
                'mother_name' => 'Dewi Lestari',
                'mother_occupation' => 'Guru',
                'mother_phone' => '081234567102',
                'mother_wa' => null,
                'registration_date' => '2024-12-15',
                'entry_year' => 2025,
                'monthly_fee' => 50000,
                'is_active' => true,
            ],
            [
                'nis' => 'TPQB250001',
                'name' => 'Muhammad Rizki',
                'nickname' => 'Iki',
                'type' => 'tpq',
                'class_time' => 'sore',
                'gender' => 'L',
                'birth_date' => '2016-11-08',
                'birth_place' => 'Cimahi',
                'address' => 'Jl. Anggrek No. 10, Cimahi',
                'father_name' => 'Dedi Kurniawan',
                'father_occupation' => 'Karyawan Swasta',
                'father_phone' => '081234567003',
                'father_wa' => null,
                'mother_name' => 'Rina Wati',
                'mother_occupation' => 'Ibu Rumah Tangga',
                'mother_phone' => '081234567103',
                'mother_wa' => null,
                'registration_date' => '2025-01-05',
                'entry_year' => 2025,
                'monthly_fee' => 50000,
                'is_active' => true,
            ],
            [
                'nis' => 'TAUD250001',
                'name' => 'Fatimah Zahra',
                'nickname' => 'Fazah',
                'type' => 'taud',
                'class_time' => 'pagi',
                'gender' => 'P',
                'birth_date' => '2020-01-25',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Mawar No. 3, Bandung',
                'father_name' => 'Eko Prasetyo',
                'father_occupation' => 'Dokter',
                'father_phone' => '081234567004',
                'father_wa' => null,
                'mother_name' => 'Fitri Handayani',
                'mother_occupation' => 'Apoteker',
                'mother_phone' => '081234567104',
                'mother_wa' => '089876543104',
                'registration_date' => '2024-11-20',
                'entry_year' => 2025,
                'monthly_fee' => 100000,
                'is_active' => true,
            ],
            [
                'nis' => 'TAUD250002',
                'name' => 'Abdullah Rasyid',
                'type' => 'taud',
                'class_time' => 'pagi',
                'gender' => 'L',
                'birth_date' => '2019-09-12',
                'birth_place' => 'Bandung',
                'address' => 'Jl. Dahlia No. 7, Bandung',
                'father_name' => 'Faisal Rahman',
                'father_occupation' => 'Pengusaha',
                'father_phone' => '081234567005',
                'father_wa' => '082345678005',
                'mother_name' => 'Nurul Hidayah',
                'mother_occupation' => 'Ibu Rumah Tangga',
                'mother_phone' => '081234567105',
                'mother_wa' => null,
                'registration_date' => '2024-12-20',
                'entry_year' => 2025,
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
            ['NIS', 'Nama', 'Kelas', 'SPP/bulan'],
            collect($students)->map(fn($s) => [
                $s['nis'],
                $s['name'],
                $s['type'] === 'taud' ? 'TAUD' : ($s['class_time'] === 'pagi' ? 'TPQ Pagi' : 'TPQ Sore'),
                'Rp ' . number_format($s['monthly_fee'], 0, ',', '.'),
            ])->toArray()
        );
    }
}
