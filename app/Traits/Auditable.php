<?php

namespace App\Traits;

use App\Models\Logs\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait untuk menambahkan audit logging otomatis ke model.
 * 
 * Trait ini akan mencatat setiap perubahan (create, update, delete) pada model
 * ke database terpisah untuk audit trail yang lengkap.
 * 
 * Cara penggunaan:
 * ```php
 * class Student extends Model
 * {
 *     use Auditable;
 * }
 * ```
 */
trait Auditable
{
    /**
     * Boot trait dan register event listeners.
     */
    public static function bootAuditable(): void
    {
        // Log saat record dibuat
        static::created(function (Model $model) {
            self::logAction($model, 'create', null, $model->getAttributes());
        });

        // Log saat record diupdate
        static::updated(function (Model $model) {
            $oldValues = $model->getOriginal();
            $newValues = $model->getChanges();

            // Hanya log jika ada perubahan
            if (!empty($newValues)) {
                self::logAction($model, 'update', $oldValues, $newValues);
            }
        });

        // Log saat record dihapus
        static::deleted(function (Model $model) {
            self::logAction($model, 'delete', $model->getAttributes(), null);
        });
    }

    /**
     * Mencatat aksi ke audit log.
     */
    protected static function logAction(
        Model $model,
        string $action,
        ?array $oldValues,
        ?array $newValues
    ): void {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_type' => auth()->user() ? get_class(auth()->user()) : null,
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'old_values' => $oldValues ? self::filterSensitiveData($oldValues) : null,
                'new_values' => $newValues ? self::filterSensitiveData($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method(),
            ]);
        } catch (\Exception $e) {
            // Jangan sampai error logging mengganggu operasi utama
            report($e);
        }
    }

    /**
     * Filter data sensitif sebelum disimpan ke log.
     */
    protected static function filterSensitiveData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'remember_token',
            'api_token',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }

        return $data;
    }

    /**
     * Mendapatkan history audit untuk model ini.
     */
    public function auditLogs()
    {
        return AuditLog::where('model_type', get_class($this))
            ->where('model_id', $this->getKey())
            ->orderBy('created_at', 'desc');
    }
}
