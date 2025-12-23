<?php

namespace App\Enums;

/**
 * Enum untuk level TAUD (Tahfidz Anak Usia Dini).
 * Program formal 3 tahun setara dengan TK.
 */
enum TaudLevel: string
{
    case KB = 'kb';       // Kelompok Bermain (usia 3-4 tahun)
    case TK_A = 'tk_a';   // TK A (usia 4-5 tahun)
    case TK_B = 'tk_b';   // TK B (usia 5-6 tahun)

    /**
     * Mendapatkan label bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::KB => 'Kelompok Bermain',
            self::TK_A => 'TK A',
            self::TK_B => 'TK B',
        };
    }

    /**
     * Mendapatkan rentang usia yang sesuai.
     */
    public function ageRange(): string
    {
        return match ($this) {
            self::KB => '3-4 tahun',
            self::TK_A => '4-5 tahun',
            self::TK_B => '5-6 tahun',
        };
    }

    /**
     * Mendapatkan urutan level (untuk sorting).
     */
    public function order(): int
    {
        return match ($this) {
            self::KB => 1,
            self::TK_A => 2,
            self::TK_B => 3,
        };
    }
}
