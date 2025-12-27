<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Model untuk data Santri TPQ dan TAUD.
 * 
 * NIS Format:
 * - TPQA250012 = TPQ Kelas Pagi, tahun 2025, urutan 12
 * - TPQB250012 = TPQ Kelas Sore, tahun 2025, urutan 12
 * - TAUD250012 = TAUD, tahun 2025, urutan 12
 */
class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'nickname',
        'type',
        'class_time',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'father_name',
        'father_occupation',
        'father_phone',
        'father_wa',
        'mother_name',
        'mother_occupation',
        'mother_phone',
        'mother_wa',
        'registration_date',
        'entry_year',
        'monthly_fee',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'registration_date' => 'date',
        'entry_year' => 'integer',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'type_label',
        'class_label',
        'formatted_monthly_fee',
        'age',
        'primary_whatsapp',
        'primary_whatsapp_link',
    ];

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

    public function getClassLabelAttribute(): string
    {
        if ($this->type === 'taud') {
            return 'TAUD';
        }

        return match ($this->class_time) {
            'pagi' => 'TPQ Pagi',
            'sore' => 'TPQ Sore',
            default => 'TPQ',
        };
    }

    public function getFormattedMonthlyFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->monthly_fee ?? 0, 0, ',', '.');
    }

    /**
     * Hitung usia dari tanggal lahir
     * @return array|null [years, months, days]
     */
    public function getAgeAttribute(): ?array
    {
        if (!$this->birth_date) {
            return null;
        }

        $birthDate = Carbon::parse($this->birth_date);
        $now = Carbon::now();

        // Use diff() for accurate integer calculation
        $diff = $birthDate->diff($now);

        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        return [
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'formatted' => $years . ' tahun ' . $months . ' bulan ' . $days . ' hari',
            'short' => $years . ' tahun ' . $months . ' bulan',
        ];
    }

    /**
     * Get nomor WA utama (prioritas: father_wa, father_phone, mother_wa, mother_phone)
     */
    public function getPrimaryWhatsappAttribute(): ?string
    {
        return $this->father_wa
            ?: $this->father_phone
            ?: $this->mother_wa
            ?: $this->mother_phone;
    }

    /**
     * Get link WhatsApp utama (format 628xxx)
     */
    public function getPrimaryWhatsappLinkAttribute(): ?string
    {
        $number = $this->primary_whatsapp;
        if (!$number) {
            return null;
        }

        // Convert 08xxx to 628xxx
        $formatted = preg_replace('/^0/', '62', $number);
        return 'https://wa.me/' . $formatted;
    }

    /**
     * Get WA link for father
     */
    public function getFatherWhatsappLinkAttribute(): ?string
    {
        $number = $this->father_wa ?: $this->father_phone;
        if (!$number)
            return null;
        $formatted = preg_replace('/^0/', '62', $number);
        return 'https://wa.me/' . $formatted;
    }

    /**
     * Get WA link for mother
     */
    public function getMotherWhatsappLinkAttribute(): ?string
    {
        $number = $this->mother_wa ?: $this->mother_phone;
        if (!$number)
            return null;
        $formatted = preg_replace('/^0/', '62', $number);
        return 'https://wa.me/' . $formatted;
    }

    /**
     * Get NIS prefix berdasarkan type dan class_time
     */
    public function getNisPrefixAttribute(): string
    {
        if ($this->type === 'taud') {
            return 'TAUD';
        }

        return $this->class_time === 'sore' ? 'TPQB' : 'TPQA';
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

    public function scopePagi($query)
    {
        return $query->where('class_time', 'pagi');
    }

    public function scopeSore($query)
    {
        return $query->where('class_time', 'sore');
    }

    public function scopeByEntryYear($query, int $year)
    {
        return $query->where('entry_year', $year);
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Generate NIS baru
     * Format: TPQA250012, TPQB250012, TAUD250012
     * 
     * @param string $type tpq atau taud
     * @param string $classTime pagi atau sore
     * @param int $entryYear tahun masuk (2025)
     */
    public static function generateNIS(string $type, string $classTime, int $entryYear): string
    {
        // Tentukan prefix
        if ($type === 'taud') {
            $prefix = 'TAUD';
        } else {
            $prefix = $classTime === 'sore' ? 'TPQB' : 'TPQA';
        }

        $yearShort = substr((string) $entryYear, -2); // 2025 -> 25

        // Cari urutan terakhir untuk prefix + tahun ini
        $lastStudent = static::where('nis', 'like', $prefix . $yearShort . '%')
            ->orderByDesc('nis')
            ->first();

        if ($lastStudent) {
            // Ambil 4 digit terakhir sebagai urutan
            $lastNumber = (int) substr($lastStudent->nis, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $yearShort . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
