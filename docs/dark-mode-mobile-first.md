# 🌙 Dark Mode & 📱 Mobile-First Implementation Guide

Panduan komprehensif untuk implementasi Dark Mode dan Mobile-First design di Baitul Jannah Super App.

---

## Overview

Aplikasi ini menggunakan **React state-based theme switching** bukan CSS `dark:` classes untuk menghindari masalah:
1. Flash putih saat navigasi antar halaman
2. Inconsistent rendering antara SSR dan client
3. Delay dalam penerapan tema

---

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        ThemeProvider                            │
│  - Wrap seluruh aplikasi di app.tsx                            │
│  - Manage theme state ('light' | 'dark' | 'system')            │
│  - Persist ke localStorage                                      │
│  - Apply class ke <html> element                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                        useIsDark() Hook                         │
│  - Return boolean isDark secara SINKRON                        │
│  - Initialize dari localStorage/document class SEBELUM render  │
│  - Tidak ada flash karena nilai langsung benar                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                      Page Components                            │
│  - Import { useIsDark } from '@/hooks/use-is-dark'             │
│  - Gunakan isDark untuk conditional styling                    │
│  - Tidak pakai CSS dark: classes untuk backgrounds             │
└─────────────────────────────────────────────────────────────────┘
```

---

## File Structure

```
resources/js/
├── app.tsx                     # Entry point, wrap dengan ThemeProvider
├── Components/
│   ├── theme-provider.tsx      # Context provider untuk tema
│   ├── theme-toggle.tsx        # Dropdown untuk ganti tema
│   └── ui/                     # Shadcn components
├── hooks/
│   ├── index.ts                # Export semua hooks
│   └── use-is-dark.ts          # Hook untuk cek dark mode
└── Pages/
    ├── Auth/Login.tsx          # Menggunakan useIsDark
    ├── Dashboard.tsx           # Menggunakan useIsDark
    └── Finance/Accounts/Index.tsx
```

---

## Core Components

### 1. ThemeProvider (`Components/theme-provider.tsx`)

Context provider yang:
- Menyimpan theme state
- Detect system preference
- Apply class ke `<html>` element
- Persist ke localStorage

```tsx
// Penggunaan di app.tsx
<ThemeProvider defaultTheme="system" storageKey="baitul-jannah-theme">
    <App {...props} />
</ThemeProvider>
```

### 2. useIsDark Hook (`hooks/use-is-dark.ts`)

**PENTING:** Hook ini digunakan di SEMUA halaman untuk menghindari flash putih.

```tsx
import { useIsDark } from '@/hooks/use-is-dark';

function MyPage() {
    const isDark = useIsDark();
    
    return (
        <div className={isDark ? "bg-gray-900" : "bg-white"}>
            Content
        </div>
    );
}
```

**Mengapa bukan CSS `dark:` classes?**

CSS classes bergantung pada class `.dark` di `<html>` element. Saat navigasi dengan Inertia, React component di-mount SEBELUM ThemeProvider sempat apply class, menyebabkan flash putih.

Dengan hook, nilai `isDark` diinisialisasi SINKRON dari localStorage/document class menggunakan `useMemo`.

### 3. ThemeToggle (`Components/theme-toggle.tsx`)

Dropdown button dengan:
- Icon ☀️/🌙 pada tombol
- 3 pilihan: Mode Terang, Mode Gelap, Ikuti Sistem
- Visual checkmark untuk opsi aktif

---

## Implementation Pattern

### Page Template

Setiap halaman HARUS mengikuti pattern ini:

```tsx
import { useIsDark } from '@/hooks/use-is-dark';
import { cn } from '@/lib/utils';

export default function MyPage() {
    const isDark = useIsDark();
    
    return (
        <>
            <Head title="My Page" />
            
            {/* Background Layers */}
            <div className="fixed inset-0">
                {/* Light Mode Background */}
                <div className={cn(
                    "absolute inset-0 bg-gradient-to-br from-slate-50 to-indigo-100",
                    isDark ? "opacity-0 pointer-events-none" : "opacity-100"
                )}></div>
                
                {/* Dark Mode Background */}
                <div className={cn(
                    "absolute inset-0",
                    isDark ? "opacity-100" : "opacity-0 pointer-events-none"
                )}>
                    <div className="absolute inset-0 bg-gradient-to-br from-gray-950 to-gray-900"></div>
                    {/* Decorative elements */}
                </div>
            </div>

            {/* Content */}
            <div className="relative min-h-screen">
                {/* Navbar, Main Content, etc */}
            </div>
        </>
    );
}
```

### Conditional Styling Pattern

Gunakan `cn()` helper untuk conditional classes:

```tsx
// Text
<h1 className={cn(
    "text-2xl font-bold",
    isDark ? "text-white" : "text-gray-900"
)}>Title</h1>

// Cards
<Card className={cn(
    "border backdrop-blur-sm",
    isDark 
        ? "bg-gray-900/50 border-gray-800" 
        : "bg-white border-gray-200"
)}>

// Buttons / Interactive elements
<Button className={cn(
    isDark 
        ? "text-gray-300 hover:bg-gray-800" 
        : "text-gray-600 hover:bg-gray-100"
)}>
```

---

## Color Palette

### Light Mode
| Element | Color | Tailwind |
|---------|-------|----------|
| Background | Light gradient | `from-slate-50 via-blue-50 to-indigo-100` |
| Cards | White | `bg-white` |
| Text Primary | Gray 900 | `text-gray-900` |
| Text Secondary | Gray 500 | `text-gray-500` |
| Borders | Gray 200 | `border-gray-200` |

### Dark Mode
| Element | Color | Tailwind |
|---------|-------|----------|
| Background | Deep gradient | `from-gray-950 via-slate-900 to-gray-900` |
| Cards | Semi-transparent | `bg-gray-900/50` |
| Text Primary | White | `text-white` |
| Text Secondary | Gray 400 | `text-gray-400` |
| Borders | Gray 800 | `border-gray-800` |

---

## Premium Effects

### Animated Blobs (Light Mode - Login)
```css
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
```

### Grid Pattern (Dark Mode)
```tsx
<div className="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:50px_50px]"></div>
```

### Gradient Orbs (Dark Mode)
```tsx
<div className="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-900/20 rounded-full blur-3xl"></div>
```

---

## Checklist untuk Halaman Baru

- [ ] Import `useIsDark` dari `@/hooks/use-is-dark`
- [ ] Gunakan `const isDark = useIsDark()` di component
- [ ] Setup background layers (light + dark) dengan opacity switching
- [ ] Apply conditional styling ke semua elements
- [ ] Test navigasi dari halaman lain (tidak ada flash putih)
- [ ] Test toggle tema (transisi smooth)

---

## Troubleshooting

### Flash Putih saat Navigasi
**Penyebab:** Menggunakan CSS `dark:` classes atau initialize `isDark` sebagai `false`

**Solusi:** Pastikan menggunakan `useIsDark()` hook yang initialize dari localStorage

### Elemen Tidak Berubah saat Toggle
**Penyebab:** Menggunakan hardcoded colors tanpa conditional

**Solusi:** Gunakan pattern `isDark ? "dark-class" : "light-class"`

### Inconsistent Theme antar Halaman
**Penyebab:** Page tidak menggunakan hook yang sama

**Solusi:** Semua page HARUS import dan gunakan `useIsDark()`
