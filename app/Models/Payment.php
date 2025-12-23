<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Payment (Pembayaran).
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference',
        'account_id',
        'transaction_id',
        'status',
        'notes',
        'created_by',
        'confirmed_by',
        'confirmed_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('payment_date', $date);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            default => ucfirst($this->payment_method),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Generate nomor pembayaran.
     * Format: PAY-YYYYMMDD-XXXXX
     */
    public static function generateNumber(): string
    {
        $prefix = 'PAY-' . now()->format('Ymd') . '-';

        $lastNumber = static::where('payment_number', 'like', $prefix . '%')
            ->orderBy('payment_number', 'desc')
            ->value('payment_number');

        if ($lastNumber) {
            $sequence = (int) substr($lastNumber, -5) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}
