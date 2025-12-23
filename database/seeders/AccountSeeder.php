<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk Chart of Accounts (COA) default.
 * 
 * Struktur akun standar untuk lembaga pendidikan Islam.
 */
class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ===============================================================
            // 1. ASET
            // ===============================================================
            [
                'code' => '1',
                'name' => 'ASET',
                'type' => 'asset',
                'is_postable' => false,
                'children' => [
                    [
                        'code' => '1.1',
                        'name' => 'Kas & Bank',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '1.1.01', 'name' => 'Kas Besar'],
                            ['code' => '1.1.02', 'name' => 'Kas Kecil'],
                            ['code' => '1.1.03', 'name' => 'Bank BRI'],
                            ['code' => '1.1.04', 'name' => 'Bank BSI'],
                        ],
                    ],
                    [
                        'code' => '1.2',
                        'name' => 'Piutang',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '1.2.01', 'name' => 'Piutang SPP'],
                            ['code' => '1.2.02', 'name' => 'Piutang Lain-lain'],
                        ],
                    ],
                    [
                        'code' => '1.3',
                        'name' => 'Aset Tetap',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '1.3.01', 'name' => 'Tanah'],
                            ['code' => '1.3.02', 'name' => 'Bangunan'],
                            ['code' => '1.3.03', 'name' => 'Peralatan'],
                            ['code' => '1.3.04', 'name' => 'Kendaraan'],
                        ],
                    ],
                ],
            ],

            // ===============================================================
            // 2. KEWAJIBAN
            // ===============================================================
            [
                'code' => '2',
                'name' => 'KEWAJIBAN',
                'type' => 'liability',
                'is_postable' => false,
                'normal_balance' => 'credit',
                'children' => [
                    [
                        'code' => '2.1',
                        'name' => 'Hutang Jangka Pendek',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '2.1.01', 'name' => 'Hutang Usaha'],
                            ['code' => '2.1.02', 'name' => 'Hutang Gaji'],
                            ['code' => '2.1.03', 'name' => 'Uang Muka Santri'],
                        ],
                    ],
                ],
            ],

            // ===============================================================
            // 3. EKUITAS
            // ===============================================================
            [
                'code' => '3',
                'name' => 'EKUITAS',
                'type' => 'equity',
                'is_postable' => false,
                'normal_balance' => 'credit',
                'children' => [
                    ['code' => '3.1', 'name' => 'Modal Yayasan', 'is_postable' => true],
                    ['code' => '3.2', 'name' => 'Laba Ditahan', 'is_postable' => true],
                    ['code' => '3.3', 'name' => 'Laba Tahun Berjalan', 'is_postable' => true],
                ],
            ],

            // ===============================================================
            // 4. PENDAPATAN
            // ===============================================================
            [
                'code' => '4',
                'name' => 'PENDAPATAN',
                'type' => 'income',
                'is_postable' => false,
                'normal_balance' => 'credit',
                'children' => [
                    [
                        'code' => '4.1',
                        'name' => 'Pendapatan SPP & Infaq',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '4.1.01', 'name' => 'SPP TAUD'],
                            ['code' => '4.1.02', 'name' => 'Infaq TPQ'],
                            ['code' => '4.1.03', 'name' => 'Uang Pendaftaran'],
                            ['code' => '4.1.04', 'name' => 'Uang Gedung'],
                        ],
                    ],
                    [
                        'code' => '4.2',
                        'name' => 'Pendapatan Donasi',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '4.2.01', 'name' => 'Donasi Umum'],
                            ['code' => '4.2.02', 'name' => 'Zakat'],
                            ['code' => '4.2.03', 'name' => 'Infaq Masjid'],
                        ],
                    ],
                    [
                        'code' => '4.3',
                        'name' => 'Pendapatan Lain-lain',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '4.3.01', 'name' => 'Pendapatan Bunga Bank'],
                            ['code' => '4.3.02', 'name' => 'Pendapatan Lain-lain'],
                        ],
                    ],
                ],
            ],

            // ===============================================================
            // 5. BEBAN
            // ===============================================================
            [
                'code' => '5',
                'name' => 'BEBAN',
                'type' => 'expense',
                'is_postable' => false,
                'children' => [
                    [
                        'code' => '5.1',
                        'name' => 'Beban Pegawai',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '5.1.01', 'name' => 'Gaji Guru'],
                            ['code' => '5.1.02', 'name' => 'Gaji Staff'],
                            ['code' => '5.1.03', 'name' => 'Honor Mengajar'],
                            ['code' => '5.1.04', 'name' => 'THR'],
                        ],
                    ],
                    [
                        'code' => '5.2',
                        'name' => 'Beban Operasional',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '5.2.01', 'name' => 'Listrik'],
                            ['code' => '5.2.02', 'name' => 'Air PDAM'],
                            ['code' => '5.2.03', 'name' => 'Internet'],
                            ['code' => '5.2.04', 'name' => 'ATK'],
                            ['code' => '5.2.05', 'name' => 'Kebersihan'],
                            ['code' => '5.2.06', 'name' => 'Konsumsi'],
                        ],
                    ],
                    [
                        'code' => '5.3',
                        'name' => 'Beban Pemeliharaan',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '5.3.01', 'name' => 'Pemeliharaan Gedung'],
                            ['code' => '5.3.02', 'name' => 'Pemeliharaan Peralatan'],
                            ['code' => '5.3.03', 'name' => 'Pemeliharaan Kendaraan'],
                        ],
                    ],
                    [
                        'code' => '5.4',
                        'name' => 'Beban Lain-lain',
                        'is_postable' => false,
                        'children' => [
                            ['code' => '5.4.01', 'name' => 'Beban Administrasi Bank'],
                            ['code' => '5.4.02', 'name' => 'Beban Lain-lain'],
                        ],
                    ],
                ],
            ],
        ];

        $this->createAccounts($accounts);

        $this->command->info('Chart of Accounts berhasil dibuat!');
    }

    /**
     * Create accounts recursively.
     */
    private function createAccounts(array $accounts, ?int $parentId = null, int $level = 1): void
    {
        $sortOrder = 0;

        foreach ($accounts as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $account = Account::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'type' => $data['type'] ?? ($parentId ? Account::find($parentId)->type : 'asset'),
                'parent_id' => $parentId,
                'level' => $level,
                'is_postable' => $data['is_postable'] ?? true,
                'normal_balance' => $data['normal_balance'] ?? 'debit',
                'sort_order' => $sortOrder++,
            ]);

            if (!empty($children)) {
                $this->createAccounts($children, $account->id, $level + 1);
            }
        }
    }
}
