<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;

/**
 * Model AuditLog untuk mencatat semua perubahan data.
 * 
 * Model ini menggunakan koneksi database terpisah ('logs') untuk:
 * 1. Tidak membebani database utama
 * 2. Memisahkan backup dan retention policy
 * 3. Mencegah manipulasi log oleh user yang punya akses DB utama
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $user_type
 * @property string $action
 * @property string $model_type
 * @property int $model_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $request_url
 * @property string|null $request_method
 * @property \Carbon\Carbon $created_at
 */
class AuditLog extends Model
{
    /**
     * Koneksi database yang digunakan (terpisah dari DB utama).
     */
    protected $connection = 'logs';

    /**
     * Nama tabel.
     */
    protected $table = 'audit_logs';

    /**
     * Tidak ada updated_at karena log tidak boleh diupdate.
     */
    const UPDATED_AT = null;

    /**
     * Field yang bisa diisi mass assignment.
     */
    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'request_url',
        'request_method',
    ];

    /**
     * Cast attributes.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Scope untuk filter berdasarkan user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan model type.
     */
    public function scopeByModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope untuk filter berdasarkan action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan tanggal.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Mendapatkan deskripsi aksi yang mudah dibaca.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Membuat',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'export' => 'Export Data',
            default => ucfirst($this->action),
        };
    }

    /**
     * Mendapatkan nama model yang mudah dibaca.
     */
    public function getModelNameAttribute(): string
    {
        $className = class_basename($this->model_type);

        // Mapping ke bahasa Indonesia
        $mapping = [
            'User' => 'Pengguna',
            'Student' => 'Santri',
            'Guardian' => 'Wali Santri',
            'Teacher' => 'Guru',
            'Invoice' => 'Tagihan',
            'Payment' => 'Pembayaran',
            'Transaction' => 'Transaksi',
            'Account' => 'Akun Keuangan',
        ];

        return $mapping[$className] ?? $className;
    }
}
