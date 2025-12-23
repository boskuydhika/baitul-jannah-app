<?php

namespace App\Enums;

/**
 * Enum untuk tipe akun dalam Chart of Accounts (COA).
 * 
 * Mengikuti standar akuntansi double-entry bookkeeping.
 */
enum AccountType: string
{
    case ASSET = 'asset';           // Aset (Kas, Bank, Piutang, dll)
    case LIABILITY = 'liability';   // Kewajiban (Hutang, dll)
    case EQUITY = 'equity';         // Modal/Ekuitas
    case INCOME = 'income';         // Pendapatan (SPP, Infaq, Donasi)
    case EXPENSE = 'expense';       // Beban (Gaji, Operasional, dll)

    /**
     * Mendapatkan label bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::ASSET => 'Aset',
            self::LIABILITY => 'Kewajiban',
            self::EQUITY => 'Ekuitas',
            self::INCOME => 'Pendapatan',
            self::EXPENSE => 'Beban',
        };
    }

    /**
     * Mendapatkan saldo normal (debit/credit).
     */
    public function normalBalance(): string
    {
        return match ($this) {
            self::ASSET, self::EXPENSE => 'debit',
            self::LIABILITY, self::EQUITY, self::INCOME => 'credit',
        };
    }

    /**
     * Apakah tipe ini menambah saldo saat debit?
     */
    public function increasesOnDebit(): bool
    {
        return in_array($this, [self::ASSET, self::EXPENSE]);
    }
}
