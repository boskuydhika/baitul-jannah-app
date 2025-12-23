<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model InvoiceItem (Detail Tagihan).
 */
class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_type',
        'description',
        'quantity',
        'unit_price',
        'amount',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->item_type) {
            'spp' => 'SPP',
            'infaq' => 'Infaq',
            'daftar_ulang' => 'Daftar Ulang',
            'seragam' => 'Seragam',
            'buku' => 'Buku',
            'kegiatan' => 'Kegiatan',
            default => ucfirst($this->item_type),
        };
    }
}
