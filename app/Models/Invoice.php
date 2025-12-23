<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Invoice (Tagihan SPP/Infaq).
 * 
 * @property int $id
 * @property string $invoice_number
 * @property int $student_id
 * @property int $year
 * @property int $month
 * @property float $total_amount
 * @property float $paid_amount
 * @property float $balance
 * @property string $status
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'invoice_number',
        'student_id',
        'year',
        'month',
        'invoice_date',
        'due_date',
        'subtotal',
        'discount',
        'total_amount',
        'paid_amount',
        'balance',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'year' => 'integer',
        'month' => 'integer',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'reference');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['sent', 'partial', 'overdue']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['sent', 'partial'])
                    ->where('due_date', '<', now());
            });
    }

    public function scopeForPeriod($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getPeriodLabelAttribute(): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        return $months[$this->month] . ' ' . $this->year;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'sent' => 'Terkirim',
            'partial' => 'Sebagian',
            'paid' => 'Lunas',
            'overdue' => 'Jatuh Tempo',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'sent' => 'blue',
            'partial' => 'yellow',
            'paid' => 'green',
            'overdue' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && $this->balance > 0;
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Generate nomor invoice.
     * Format: INV-YYYYMM-XXXXX
     */
    public static function generateNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';

        $lastNumber = static::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->value('invoice_number');

        if ($lastNumber) {
            $sequence = (int) substr($lastNumber, -5) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Add item ke invoice.
     */
    public function addItem(string $type, string $description, float $unitPrice, int $quantity = 1): InvoiceItem
    {
        return $this->items()->create([
            'item_type' => $type,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'amount' => $unitPrice * $quantity,
        ]);
    }

    /**
     * Recalculate totals dari items.
     */
    public function recalculate(): void
    {
        $this->subtotal = $this->items()->sum('amount');
        $this->total_amount = $this->subtotal - $this->discount;
        $this->balance = $this->total_amount - $this->paid_amount;

        // Update status
        if ($this->balance <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->is_overdue) {
            $this->status = 'overdue';
        }

        $this->save();
    }

    /**
     * Record pembayaran.
     */
    public function recordPayment(float $amount, string $method, int $accountId, ?string $reference = null): Payment
    {
        $payment = $this->payments()->create([
            'payment_number' => Payment::generateNumber(),
            'payment_date' => now(),
            'amount' => $amount,
            'payment_method' => $method,
            'reference' => $reference,
            'account_id' => $accountId,
            'status' => 'confirmed',
            'created_by' => auth()->id(),
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $this->paid_amount += $amount;
        $this->recalculate();

        return $payment;
    }
}
