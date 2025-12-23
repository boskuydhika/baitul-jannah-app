<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Account untuk Chart of Accounts (COA).
 * 
 * Struktur hierarki akun keuangan dengan parent-child relationship.
 * Mendukung double-entry bookkeeping.
 * 
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property int|null $parent_id
 * @property int $level
 * @property bool $is_postable
 * @property string $normal_balance
 * @property float $current_balance
 * @property string|null $description
 * @property bool $is_active
 * @property int $sort_order
 */
class Account extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'level',
        'is_postable',
        'normal_balance',
        'current_balance',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_postable' => 'boolean',
        'is_active' => 'boolean',
        'current_balance' => 'decimal:2',
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Parent account (untuk hierarki).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Child accounts.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * All descendants (recursive children).
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Transaction details yang menggunakan akun ini.
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Filter akun aktif saja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter akun yang bisa diposting (bukan header).
     */
    public function scopePostable($query)
    {
        return $query->where('is_postable', true);
    }

    /**
     * Filter berdasarkan tipe.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Root accounts (level 1).
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    // =========================================================================
    // ACCESSORS & MUTATORS
    // =========================================================================

    /**
     * Get enum tipe akun.
     */
    public function getAccountTypeAttribute(): ?AccountType
    {
        return AccountType::tryFrom($this->type);
    }

    /**
     * Get label tipe dalam Bahasa Indonesia.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->accountType?->label() ?? $this->type;
    }

    /**
     * Get full path (1.1 > 1.1.01 > Kas Besar).
     */
    public function getFullPathAttribute(): string
    {
        $path = collect([$this->name]);
        $parent = $this->parent;

        while ($parent) {
            $path->prepend($parent->name);
            $parent = $parent->parent;
        }

        return $path->implode(' > ');
    }

    /**
     * Format saldo untuk display.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->current_balance, 0, ',', '.');
    }

    // =========================================================================
    // METHODS
    // =========================================================================

    /**
     * Apakah saldo bertambah saat debit?
     */
    public function increasesOnDebit(): bool
    {
        return in_array($this->type, ['asset', 'expense']);
    }

    /**
     * Update saldo berdasarkan debit/credit.
     */
    public function updateBalance(float $debit, float $credit): void
    {
        if ($this->increasesOnDebit()) {
            $this->current_balance += ($debit - $credit);
        } else {
            $this->current_balance += ($credit - $debit);
        }
        $this->save();
    }

    /**
     * Get semua descendants IDs (untuk query).
     */
    public function getDescendantIds(): array
    {
        $ids = [];

        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getDescendantIds());
        }

        return $ids;
    }

    /**
     * Generate kode akun berikutnya untuk parent ini.
     */
    public static function generateNextCode(?int $parentId = null): string
    {
        if ($parentId) {
            $parent = static::find($parentId);
            $lastChild = static::where('parent_id', $parentId)
                ->orderByRaw('CAST(SUBSTRING_INDEX(code, ".", -1) AS UNSIGNED) DESC')
                ->first();

            if ($lastChild) {
                $parts = explode('.', $lastChild->code);
                $lastNumber = (int) end($parts);
                return $parent->code . '.' . str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            }

            return $parent->code . '.01';
        }

        // Root level
        $lastRoot = static::whereNull('parent_id')
            ->orderByRaw('CAST(code AS UNSIGNED) DESC')
            ->first();

        return $lastRoot ? (string) ((int) $lastRoot->code + 1) : '1';
    }
}
