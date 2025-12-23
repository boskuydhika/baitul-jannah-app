<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk sample data santri dan kelas untuk testing.
 */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create classes
        $classes = [
            // TAUD Classes
            ['name' => 'KB Khadijah', 'program_type' => 'taud', 'taud_level' => 'kb', 'academic_year' => '2024/2025'],
            ['name' => 'TK-A Umar', 'program_type' => 'taud', 'taud_level' => 'tk_a', 'academic_year' => '2024/2025'],
            ['name' => 'TK-B Abu Bakar', 'program_type' => 'taud', 'taud_level' => 'tk_b', 'academic_year' => '2024/2025'],
            // TPQ Classes
            ['name' => 'Iqro Jilid 1-2 Pagi', 'program_type' => 'tpq', 'jilid_level' => 1, 'academic_year' => '2024/2025'],
            ['name' => 'Iqro Jilid 3-4 Sore', 'program_type' => 'tpq', 'jilid_level' => 3, 'academic_year' => '2024/2025'],
            ['name' => 'Al-Quran', 'program_type' => 'tpq', 'jilid_level' => 7, 'academic_year' => '2024/2025'],
        ];

        foreach ($classes as $data) {
            ClassRoom::firstOrCreate(
                ['name' => $data['name'], 'academic_year' => $data['academic_year']],
                $data
            );
        }

        // Create sample guardians and students
        $sampleData = [
            [
                'guardian' => ['name' => 'Bapak Ahmad', 'phone' => '081234567001', 'occupation' => 'Wiraswasta'],
                'students' => [
                    ['name' => 'Muhammad Farhan', 'gender' => 'L', 'birth_date' => '2019-05-15', 'program_type' => 'taud', 'taud_level' => 'tk_a'],
                    ['name' => 'Aisyah Zahra', 'gender' => 'P', 'birth_date' => '2018-03-20', 'program_type' => 'tpq', 'current_jilid' => 3],
                ],
            ],
            [
                'guardian' => ['name' => 'Bapak Ridwan', 'phone' => '081234567002', 'occupation' => 'PNS'],
                'students' => [
                    ['name' => 'Umar Hadi', 'gender' => 'L', 'birth_date' => '2020-01-10', 'program_type' => 'taud', 'taud_level' => 'kb'],
                ],
            ],
            [
                'guardian' => ['name' => 'Ibu Fatimah', 'phone' => '081234567003', 'occupation' => 'Guru'],
                'students' => [
                    ['name' => 'Khadijah Putri', 'gender' => 'P', 'birth_date' => '2017-08-25', 'program_type' => 'taud', 'taud_level' => 'tk_b'],
                    ['name' => 'Ali Akbar', 'gender' => 'L', 'birth_date' => '2015-12-01', 'program_type' => 'tpq', 'current_jilid' => 5],
                ],
            ],
            [
                'guardian' => ['name' => 'Bapak Yusuf', 'phone' => '081234567004', 'occupation' => 'Pedagang'],
                'students' => [
                    ['name' => 'Bilal Rahman', 'gender' => 'L', 'birth_date' => '2016-06-18', 'program_type' => 'tpq', 'current_jilid' => 4],
                    ['name' => 'Hafidz Maulana', 'gender' => 'L', 'birth_date' => '2014-02-28', 'program_type' => 'tpq', 'current_jilid' => 7],
                ],
            ],
            [
                'guardian' => ['name' => 'Ibu Aminah', 'phone' => '081234567005', 'occupation' => 'Ibu Rumah Tangga'],
                'students' => [
                    ['name' => 'Siti Maryam', 'gender' => 'P', 'birth_date' => '2019-09-12', 'program_type' => 'taud', 'taud_level' => 'tk_a'],
                ],
            ],
        ];

        $nisCountTpq = 1;
        $nisCountTaud = 1;

        foreach ($sampleData as $data) {
            $guardian = Guardian::firstOrCreate(
                ['phone' => $data['guardian']['phone']],
                $data['guardian']
            );

            foreach ($data['students'] as $studentData) {
                $nis = $studentData['program_type'] === 'taud'
                    ? 'TAUD2024' . str_pad($nisCountTaud++, 3, '0', STR_PAD_LEFT)
                    : 'TPQ2024' . str_pad($nisCountTpq++, 3, '0', STR_PAD_LEFT);

                Student::firstOrCreate(
                    ['nis' => $nis],
                    array_merge($studentData, [
                        'nis' => $nis,
                        'guardian_id' => $guardian->id,
                        'status' => 'active',
                        'entry_date' => now()->subMonths(rand(1, 12)),
                    ])
                );
            }
        }

        $this->command->info('Sample data berhasil dibuat!');
        $this->command->table(
            ['Program', 'Jumlah Santri'],
            [
                ['TAUD', Student::taud()->count()],
                ['TPQ', Student::tpq()->count()],
            ]
        );
    }
}
