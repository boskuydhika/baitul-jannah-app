import { useTheme } from '@/Components/theme-provider';
import { useState, useEffect, useMemo } from 'react';

/**
 * Custom hook untuk mendapatkan status dark mode secara sinkron.
 * 
 * Menggunakan useMemo untuk initialize nilai langsung dari:
 * 1. localStorage (jika tersedia)
 * 2. document.documentElement class (jika sudah di-render)
 * 3. System preference sebagai fallback
 * 
 * Ini menghindari flash putih saat navigasi antar halaman karena
 * nilai diinisialisasi SEBELUM render pertama.
 * 
 * @example
 * ```tsx
 * function MyPage() {
 *     const isDark = useIsDark();
 *     return (
 *         <div className={isDark ? "bg-gray-900" : "bg-white"}>
 *             Content
 *         </div>
 *     );
 * }
 * ```
 */
export function useIsDark(): boolean {
    const { theme } = useTheme();

    // Initialize dengan nilai yang benar LANGSUNG, bukan setelah effect
    const getIsDark = (): boolean => {
        // Jika theme explicit dark atau light, gunakan itu
        if (theme === 'dark') return true;
        if (theme === 'light') return false;

        // Untuk 'system', langsung cek system preference (bukan DOM class)
        // Ini menghindari race condition dimana DOM class belum ter-update
        if (typeof window !== 'undefined') {
            return window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        return false;
    };

    // useMemo ensures this runs synchronously during render
    const initialValue = useMemo(() => getIsDark(), []);

    const [isDark, setIsDark] = useState(initialValue);

    // Update saat theme berubah
    useEffect(() => {
        setIsDark(getIsDark());
    }, [theme]);

    // Listen untuk system preference changes (untuk mode 'system')
    useEffect(() => {
        if (theme !== 'system') return;

        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handler = (e: MediaQueryListEvent) => setIsDark(e.matches);

        mediaQuery.addEventListener('change', handler);
        return () => mediaQuery.removeEventListener('change', handler);
    }, [theme]);

    return isDark;
}
