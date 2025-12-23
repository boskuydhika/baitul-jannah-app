# 📝 Changelog - Baitul Jannah Super App

Semua perubahan penting pada project ini akan didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id-ID/1.0.0/).

---

## [Unreleased]

### Added
- **`useIsDark` Hook** - Custom hook untuk dark mode tanpa flash putih
- **Theme Dropdown** - Pilihan Mode Terang, Mode Gelap, Ikuti Sistem
- **Premium Backgrounds** - Animated blobs (light), gradient orbs (dark)
- **Glassmorphism Effects** - Backdrop blur pada cards dan navbar

### Changed
- **Theme Switching** - Dari CSS `dark:` classes ke React state-based
- **Background Layers** - Separate layers dengan opacity switching
- **Login Page** - Redesign dengan colorful gradient (light) vs deep dark (dark)
- **Dashboard** - Full dark mode support dengan proper contrast

### Fixed
- **White Flash** - Fix flash putih saat navigasi antar halaman dalam dark mode
- **Text Contrast** - "Baitul Jannah" dan header sekarang visible di dark mode
- **Background Persistence** - Background tetap dark saat pindah halaman
- **Theme Toggle Styling** - Button dan dropdown sekarang menyesuaikan tema aktif (tidak selalu gelap)

---

## [1.1.0] - 2025-12-24

### Phase B.2: Premium Dark Mode Implementation

#### New Files
| File | Description |
|------|-------------|
| `hooks/use-is-dark.ts` | Custom hook untuk dark mode synchronous initialization |
| `hooks/index.ts` | Export file untuk hooks |

#### Modified Files
| File | Changes |
|------|---------|
| `Components/theme-provider.tsx` | Context provider untuk tema |
| `Components/theme-toggle.tsx` | Dropdown dengan ☀️/🌙 icon |
| `Pages/Auth/Login.tsx` | Premium design dengan 2 background layers |
| `Pages/Dashboard.tsx` | Full dark mode dengan useIsDark hook |
| `Pages/Finance/Accounts/Index.tsx` | Consistent dark mode theming |

#### Technical Changes
1. **Synchronous Initialization**: `useIsDark` menggunakan `useMemo` untuk initialize sebelum render
2. **Opacity Switching**: Background layers toggle dengan opacity (bukan hidden/block)
3. **Pointer Events**: Layer yang tidak aktif mendapat `pointer-events-none`

---

## [1.0.0] - 2025-12-23

### Phase A: Backend Foundation

#### Added
- Authentication dengan phone + password
- Magic Password untuk debugging
- Financial Management (COA, Transactions, Invoices)
- Academic Management (Students, Guardians, Classes)
- Spatie Permission untuk RBAC

### Phase B.1: Frontend Foundation

#### Added
- Inertia.js + React + TypeScript
- Shadcn/UI components
- Ziggy untuk Laravel route names
- Basic dark mode (CSS-based)

#### Issues Identified
- Login tidak berfungsi → Fixed: Install Ziggy
- UI polosan → Fixed: Rewrite CSS tanpa @theme directive
- White flash saat navigasi → Fixed: useIsDark hook

---

## Migration Notes

### Untuk Halaman Baru

Setiap halaman baru HARUS:

1. Import hook:
```tsx
import { useIsDark } from '@/hooks/use-is-dark';
```

2. Gunakan dalam component:
```tsx
const isDark = useIsDark();
```

3. Setup background layers dengan opacity switching

4. Apply conditional styling ke semua elements

---

## API Endpoints (Unchanged)

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/login` | GET/POST | Login page |
| `/dashboard` | GET | Dashboard |
| `/finance/accounts` | GET | Chart of Accounts |
| `/api/auth/login` | POST | API Login |
| `/api/finance/accounts` | GET | API Accounts |

---

## Environment Requirements

- PHP 8.2+
- Node.js 18+
- MariaDB 10.6+
- Composer 2.x

---

## Known Issues

1. ~~White flash saat navigasi dalam dark mode~~ ✅ FIXED
2. Dashboard stats masih hardcoded
3. Belum ada fitur CRUD untuk accounts

---

## Contributors

- Development: AI Assistant (Gemini/Antigravity)
- Project Owner: Dhika
