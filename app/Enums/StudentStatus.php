<?php

namespace App\Enums;

/**
 * Enum untuk status santri.
 */
enum StudentStatus: string
{
    case ACTIVE = 'active';         // Santri aktif
    case GRADUATED = 'graduated';   // Lulus/Khatam
    case DROPPED = 'dropped';       // Keluar/Berhenti
    case PENDING = 'pending';       // Menunggu konfirmasi (PPDB)

    /**
     * Mendapatkan label bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::GRADUATED => 'Lulus',
            self::DROPPED => 'Keluar',
            self::PENDING => 'Menunggu Konfirmasi',
        };
    }

    /**
     * Mendapatkan warna badge untuk UI.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::GRADUATED => 'blue',
            self::DROPPED => 'red',
            self::PENDING => 'yellow',
        };
    }
}
