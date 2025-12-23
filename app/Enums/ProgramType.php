<?php

namespace App\Enums;

/**
 * Enum untuk tipe program pendidikan.
 * 
 * TPQ = Taman Pendidikan Quran (informal, multi-age)
 * TAUD = Tahfidz Anak Usia Dini (formal 3 tahun: KB, TK-A, TK-B)
 */
enum ProgramType: string
{
    case TPQ = 'tpq';
    case TAUD = 'taud';

    /**
     * Mendapatkan label bahasa Indonesia untuk tipe program.
     */
    public function label(): string
    {
        return match ($this) {
            self::TPQ => 'Taman Pendidikan Quran',
            self::TAUD => 'Tahfidz Anak Usia Dini',
        };
    }

    /**
     * Mendapatkan deskripsi singkat program.
     */
    public function description(): string
    {
        return match ($this) {
            self::TPQ => 'Program belajar Al-Quran dari Iqro sampai Al-Quran',
            self::TAUD => 'Program formal 3 tahun setara TK dengan fokus Tahfidz',
        };
    }
}
