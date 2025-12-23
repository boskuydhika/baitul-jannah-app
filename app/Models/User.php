<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Model User untuk autentikasi dan manajemen pengguna.
 * 
 * User types (via userable polymorphic):
 * - Teacher: Guru/Ustadz
 * - Guardian: Wali Santri
 * - Staff: Staff Yayasan (Ketua, Bendahara, dll)
 * 
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string $password
 * @property string|null $userable_type
 * @property int|null $userable_id
 * @property bool $is_active
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes, Auditable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'userable_type',
        'userable_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Polymorphic relationship ke profile (Teacher, Guardian, Staff).
     */
    public function userable()
    {
        return $this->morphTo();
    }

    /**
     * Check apakah user adalah Guru.
     */
    public function isTeacher(): bool
    {
        return $this->userable_type === 'App\Models\Teacher';
    }

    /**
     * Check apakah user adalah Wali Santri.
     */
    public function isGuardian(): bool
    {
        return $this->userable_type === 'App\Models\Guardian';
    }

    /**
     * Scope untuk user aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Format nomor HP untuk display.
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;

        // Format: 0812-3456-7890
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
        }

        return $phone;
    }
}
