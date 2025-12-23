<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model TransactionDetail untuk detail jurnal (debit/credit lines).
 * 
 * Setiap line adalah satu entry debit ATAU credit ke satu akun.
 * 
 * @property int $id
 * @property int $transaction_id
 * @property int $account_id
 * @property float $debit
 * @property float $credit
 * @property string|null $description
 * @property int $line_order
 */
class TransactionDetail extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'transaction_id',
        'account_id',
        'debit',
        'credit',
        'description',
        'line_order',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'line_order' => 'integer',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get amount (debit atau credit, mana yang > 0).
     */
    public function getAmountAttribute(): float
    {
        return max($this->debit, $this->credit);
    }

    /**
     * Get type (debit atau credit).
     */
    public function getTypeAttribute(): string
    {
        return $this->debit > 0 ? 'debit' : 'credit';
    }

    public function getFormattedDebitAttribute(): string
    {
        return $this->debit > 0 ? 'Rp ' . number_format($this->debit, 0, ',', '.') : '-';
    }

    public function getFormattedCreditAttribute(): string
    {
        return $this->credit > 0 ? 'Rp ' . number_format($this->credit, 0, ',', '.') : '-';
    }

    // =========================================================================
    // VALIDATION
    // =========================================================================

    /**
     * Validate that only one of debit/credit is filled.
     */
    public function isValid(): bool
    {
        // Tidak boleh keduanya 0
        if ($this->debit == 0 && $this->credit == 0) {
            return false;
        }

        // Tidak boleh keduanya > 0
        if ($this->debit > 0 && $this->credit > 0) {
            return false;
        }

        return true;
    }
}
