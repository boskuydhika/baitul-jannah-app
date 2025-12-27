/**
 * Utility functions untuk format tanggal dengan bahasa Indonesia.
 */

const HARI_INDONESIA = [
    'Minggu',
    'Senin',
    'Selasa',
    'Rabu',
    'Kamis',
    'Jumat',
    'Sabtu',
];

const BULAN_INDONESIA = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

/**
 * Format tanggal ke format Indonesia: "Jumat, 2025-12-27"
 * @param date - Date object atau string ISO date
 * @returns Formatted string "dddd, yyyy-mm-dd"
 */
export function formatDateIndo(date: string | Date): string {
    const d = new Date(date);
    const dayName = HARI_INDONESIA[d.getDay()];
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${dayName}, ${year}-${month}-${day}`;
}

/**
 * Format tanggal ke format Indonesia lengkap: "Jumat, 27 Desember 2025"
 */
export function formatDateIndoFull(date: string | Date): string {
    const d = new Date(date);
    const dayName = HARI_INDONESIA[d.getDay()];
    const monthName = BULAN_INDONESIA[d.getMonth()];

    return `${dayName}, ${d.getDate()} ${monthName} ${d.getFullYear()}`;
}

/**
 * Format datetime ke format Indonesia: "Jumat, 2025-12-27 14:30:00"
 * Menggunakan 24-jam format
 */
export function formatDateTimeIndo(date: string | Date): string {
    const d = new Date(date);
    const dateStr = formatDateIndo(d);
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');

    return `${dateStr} ${hours}:${minutes}:${seconds}`;
}

/**
 * Format ke ISO date string saja (yyyy-mm-dd)
 */
export function toISODateString(date: string | Date): string {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

/**
 * Get nama hari dalam bahasa Indonesia
 */
export function getDayName(date: string | Date): string {
    return HARI_INDONESIA[new Date(date).getDay()];
}

/**
 * Get nama bulan dalam bahasa Indonesia
 */
export function getMonthName(date: string | Date): string {
    return BULAN_INDONESIA[new Date(date).getMonth()];
}
