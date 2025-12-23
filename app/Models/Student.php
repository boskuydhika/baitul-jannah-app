<?php

namespace App\Models;

use App\Enums\ProgramType;
use App\Enums\StudentStatus;
use App\Enums\TaudLevel;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Student (Santri) untuk TPQ dan TAUD.
 * 
 * @property int $id
 * @property string $nis
 * @property string $name
 * @property string $program_type
 * @property string|null $taud_level
 * @property int|null $current_jilid
 * @property string $status
 */
class Student extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'nis',
        'name',
        'nickname',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'guardian_id',
        'program_type',
        'taud_level',
        'current_jilid',
        'class_id',
        'status',
        'entry_date',
        'graduation_date',
        'photo',
        'monthly_fee',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'entry_date' => 'date',
        'graduation_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'current_jilid' => 'integer',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTpq($query)
    {
        return $query->where('program_type', 'tpq');
    }

    public function scopeTaud($query)
    {
        return $query->where('program_type', 'taud');
    }

    public function scopeInClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getProgramTypeEnumAttribute(): ?ProgramType
    {
        return ProgramType::tryFrom($this->program_type);
    }

    public function getStatusEnumAttribute(): ?StudentStatus
    {
        return StudentStatus::tryFrom($this->status);
    }

    public function getTaudLevelEnumAttribute(): ?TaudLevel
    {
        return $this->taud_level ? TaudLevel::tryFrom($this->taud_level) : null;
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date->age;
    }

    public function getJilidLabelAttribute(): string
    {
        if ($this->program_type !== 'tpq') {
            return '-';
        }

        return match ($this->current_jilid) {
            1, 2, 3, 4, 5, 6 => "Iqro' Jilid {$this->current_jilid}",
            7 => "Al-Qur'an",
            default => '-',
        };
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Generate NIS baru.
     * Format: [PROGRAM][TAHUN][SEQUENCE] contoh: TPQ2024001
     */
    public static function generateNis(string $programType): string
    {
        $prefix = strtoupper($programType) . date('Y');

        $lastNis = static::where('nis', 'like', $prefix . '%')
            ->orderBy('nis', 'desc')
            ->value('nis');

        if ($lastNis) {
            $sequence = (int) substr($lastNis, -3) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get biaya bulanan (custom atau default).
     */
    public function getMonthlyFeeAmount(): float
    {
        // Jika ada custom fee, gunakan itu
        if ($this->monthly_fee) {
            return $this->monthly_fee;
        }

        // Default fee berdasarkan program
        // TODO: ambil dari settings
        return $this->program_type === 'taud' ? 350000 : 75000;
    }

    /**
     * Get total tunggakan.
     */
    public function getTotalArrearsAttribute(): float
    {
        return $this->invoices()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->sum('balance');
    }
}
