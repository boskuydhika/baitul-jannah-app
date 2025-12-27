<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Transaction untuk buku kas sederhana.
 * 
 * Setiap transaksi adalah satu entri: pemasukan ATAU pengeluaran.
 * Tidak menggunakan double-entry bookkeeping.
 * 
 * @property int $id
 * @property string $transaction_number
 * @property \Carbon\Carbon $transaction_datetime
 * @property int|null $category_id
 * @property string $type (income|expense)
 * @property string $description
 * @property float $amount
 * @property string $status (draft|posted)
 * @property int|null $created_by
 */
class Transaction extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'transaction_number',
        'transaction_datetime',
        'category_id',
        'type',
        'description',
        'amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'transaction_datetime' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_amount',
        'formatted_datetime',
        'status_label',
        'type_label',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeOfCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_datetime', [$startDate, $endDate]);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedDatetimeAttribute(): string
    {
        if (!$this->transaction_datetime)
            return '-';

        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $dt = $this->transaction_datetime;
        $dayName = $days[$dt->dayOfWeek];

        return $dayName . ', ' . $dt->format('Y-m-d H:i:s');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'posted' => 'Posted',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'yellow',
            'posted' => 'green',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'income' => 'green',
            'expense' => 'red',
            default => 'gray',
        };
    }

    // =========================================================================
    // STATIC METHODS
    // =========================================================================

    /**
     * Generate nomor transaksi baru.
     * Format: TRX-YYYYMM-XXXXX (contoh: TRX-202412-00001)
     */
    public static function generateNumber(): string
    {
        $prefix = 'TRX-' . now()->format('Ym') . '-';

        $lastNumber = static::where('transaction_number', 'like', $prefix . '%')
            ->orderBy('transaction_number', 'desc')
            ->value('transaction_number');

        if ($lastNumber) {
            $sequence = (int) substr($lastNumber, -5) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung saldo saat ini (total pemasukan - total pengeluaran).
     */
    public static function getCurrentBalance(): float
    {
        $income = static::posted()->income()->sum('amount');
        $expense = static::posted()->expense()->sum('amount');

        return $income - $expense;
    }

    /**
     * Hitung saldo sampai tanggal tertentu.
     */
    public static function getBalanceUntil(\DateTime $date): float
    {
        $income = static::posted()->income()
            ->where('transaction_datetime', '<=', $date)
            ->sum('amount');
        $expense = static::posted()->expense()
            ->where('transaction_datetime', '<=', $date)
            ->sum('amount');

        return $income - $expense;
    }

    /**
     * Jumlah transaksi draft.
     */
    public static function getDraftCount(): int
    {
        return static::draft()->count();
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Post transaksi (ubah status dari draft ke posted).
     */
    public function post(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->status = 'posted';
        return $this->save();
    }

    /**
     * Unpost transaksi (ubah status dari posted ke draft).
     * Hanya untuk super admin.
     */
    public function unpost(): bool
    {
        if ($this->status !== 'posted') {
            return false;
        }

        $this->status = 'draft';
        return $this->save();
    }
}
