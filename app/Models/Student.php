<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk data Santri TPQ dan TAUD.
 * 
 * Simplified version untuk Buku Kas / SPP management.
 */
class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'type',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'parent_name',
        'parent_phone',
        'academic_year',
        'entry_date',
        'monthly_fee',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'entry_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Appended attributes
     */
    protected $appends = ['type_label', 'formatted_monthly_fee'];

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'tpq' => 'TPQ',
            'taud' => 'TAUD',
            default => $this->type ?? '-',
        };
    }

    public function getFormattedMonthlyFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->monthly_fee ?? 0, 0, ',', '.');
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTpq($query)
    {
        return $query->where('type', 'tpq');
    }

    public function scopeTaud($query)
    {
        return $query->where('type', 'taud');
    }

    public function scopeByAcademicYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Generate NIS baru
     * Format: TPQ25001 atau TAU25001
     */
    public static function generateNIS(string $type, string $academicYear): string
    {
        $prefix = $type === 'tpq' ? 'TPQ' : 'TAU';
        $yearShort = substr($academicYear, 2, 2); // 2025/2026 -> 25

        $lastStudent = static::where('type', $type)
            ->where('academic_year', $academicYear)
            ->orderByDesc('nis')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->nis, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $yearShort . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
