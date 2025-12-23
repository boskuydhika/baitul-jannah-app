<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Seeder untuk membuat roles dan permissions default.
 * 
 * Roles:
 * - super_admin: Full access ke semua fitur
 * - ketua_yayasan: Dashboard, laporan, approval
 * - kepala_sekolah: Akademik, guru, santri
 * - bendahara: Keuangan, tagihan, pembayaran
 * - guru: Nilai, absensi, progress santri
 * - wali_santri: Tagihan, raport anak
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ================================================================
        // PERMISSIONS
        // ================================================================

        // User Management
        $permissions = [
            // Users
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Finance
            'finance.view',
            'finance.create',
            'finance.update',
            'finance.delete',
            'finance.reports',
            'finance.export',

            // Invoices
            'invoices.view',
            'invoices.create',
            'invoices.update',
            'invoices.delete',
            'invoices.generate',

            // Payments
            'payments.view',
            'payments.create',
            'payments.update',
            'payments.delete',

            // Students
            'students.view',
            'students.create',
            'students.update',
            'students.delete',

            // Classes
            'classes.view',
            'classes.create',
            'classes.update',
            'classes.delete',

            // Academic Records
            'records.view',
            'records.create',
            'records.update',
            'records.delete',

            // PPDB
            'ppdb.view',
            'ppdb.create',
            'ppdb.update',
            'ppdb.approve',

            // Reports
            'reports.view',
            'reports.export',

            // Audit Logs
            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ================================================================
        // ROLES
        // ================================================================

        // Super Admin - Full access
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Ketua Yayasan - Dashboard, reports, approval
        $ketuaYayasan = Role::firstOrCreate(['name' => 'ketua_yayasan']);
        $ketuaYayasan->givePermissionTo([
            'users.view',
            'finance.view',
            'finance.reports',
            'invoices.view',
            'payments.view',
            'students.view',
            'classes.view',
            'records.view',
            'ppdb.view',
            'ppdb.approve',
            'reports.view',
            'reports.export',
            'audit.view',
        ]);

        // Kepala Sekolah - Academic management
        $kepalaSekolah = Role::firstOrCreate(['name' => 'kepala_sekolah']);
        $kepalaSekolah->givePermissionTo([
            'users.view',
            'students.view',
            'students.create',
            'students.update',
            'classes.view',
            'classes.create',
            'classes.update',
            'records.view',
            'records.create',
            'records.update',
            'ppdb.view',
            'ppdb.create',
            'ppdb.update',
            'ppdb.approve',
            'reports.view',
        ]);

        // Bendahara - Finance management
        $bendahara = Role::firstOrCreate(['name' => 'bendahara']);
        $bendahara->givePermissionTo([
            'finance.view',
            'finance.create',
            'finance.update',
            'finance.reports',
            'finance.export',
            'invoices.view',
            'invoices.create',
            'invoices.update',
            'invoices.generate',
            'payments.view',
            'payments.create',
            'payments.update',
            'students.view',
            'reports.view',
            'reports.export',
        ]);

        // Guru - Teaching and grading
        $guru = Role::firstOrCreate(['name' => 'guru']);
        $guru->givePermissionTo([
            'students.view',
            'classes.view',
            'records.view',
            'records.create',
            'records.update',
        ]);

        // Wali Santri - View own child's data
        $waliSantri = Role::firstOrCreate(['name' => 'wali_santri']);
        $waliSantri->givePermissionTo([
            'students.view',
            'invoices.view',
            'payments.view',
            'records.view',
        ]);

        $this->command->info('Roles dan permissions berhasil dibuat!');
    }
}
