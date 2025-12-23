<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Model Transaction untuk jurnal umum (double-entry bookkeeping).
 * 
 * Setiap transaksi HARUS balance: Total Debit = Total Credit
 * 
 * @property int $id
 * @property string $transaction_number
 * @property \Carbon\Carbon $transaction_date
 * @property string $type
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property string $description
 * @property float $amount
 * @property int|null $created_by
 * @property string $status
 */
class Transaction extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'type',
        'reference_type',
        'reference_id',
        'description',
        'amount',
        'created_by',
        'status',
        'posted_at',
        'posted_by',
        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'posted_at' => 'datetime',
        'voided_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class)->orderBy('line_order');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
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

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getTotalDebitAttribute(): float
    {
        return $this->details->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return $this->details->sum('credit');
    }

    public function getIsBalancedAttribute(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'posted' => 'Posted',
            'void' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'yellow',
            'posted' => 'green',
            'void' => 'red',
            default => 'gray',
        };
    }

    // =========================================================================
    // METHODS
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
     * Posting transaksi (update saldo akun).
     */
    public function post(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        if (!$this->is_balanced) {
            throw new \Exception('Transaksi tidak balance! Debit: ' . $this->total_debit . ', Credit: ' . $this->total_credit);
        }

        return DB::transaction(function () {
            // Update saldo setiap akun
            foreach ($this->details as $detail) {
                $detail->account->updateBalance($detail->debit, $detail->credit);
            }

            // Update status
            $this->status = 'posted';
            $this->posted_at = now();
            $this->posted_by = auth()->id();
            $this->save();

            return true;
        });
    }

    /**
     * Void/batalkan transaksi (reverse saldo akun).
     */
    public function void(string $reason): bool
    {
        if ($this->status !== 'posted') {
            return false;
        }

        return DB::transaction(function () use ($reason) {
            // Reverse saldo setiap akun
            foreach ($this->details as $detail) {
                // Reverse: debit jadi credit dan sebaliknya
                $detail->account->updateBalance($detail->credit, $detail->debit);
            }

            // Update status
            $this->status = 'void';
            $this->voided_at = now();
            $this->voided_by = auth()->id();
            $this->void_reason = $reason;
            $this->save();

            return true;
        });
    }

    /**
     * Add detail line ke transaksi.
     */
    public function addDetail(int $accountId, float $debit = 0, float $credit = 0, ?string $description = null): TransactionDetail
    {
        $lastOrder = $this->details()->max('line_order') ?? 0;

        return $this->details()->create([
            'account_id' => $accountId,
            'debit' => $debit,
            'credit' => $credit,
            'description' => $description,
            'line_order' => $lastOrder + 1,
        ]);
    }

    /**
     * Recalculate amount dari details.
     */
    public function recalculateAmount(): void
    {
        $this->amount = $this->details()->sum('debit');
        $this->save();
    }
}
