# 🏗️ Frontend Architecture - Baitul Jannah Super App

> **Tech Stack:** Laravel 12 + Inertia.js + React 18 + TypeScript + Tailwind CSS v4 + Shadcn/UI

---

## 1. Architecture Overview

```
┌─────────────────────────────────────────────────────┐
│                    Browser                          │
├─────────────────────────────────────────────────────┤
│  React 18 + TypeScript + Tailwind CSS + Shadcn/UI   │
│  ├── Pages/           # Inertia Pages               │
│  ├── Components/      # UI Components               │
│  ├── hooks/           # Custom React Hooks          │
│  └── lib/             # Utilities                   │
├─────────────────────────────────────────────────────┤
│        Inertia.js (Server-Side Adapter)             │
├─────────────────────────────────────────────────────┤
│            Laravel 12 (PHP 8.2)                     │
└─────────────────────────────────────────────────────┘
```

---

## 2. Directory Structure

```
resources/
├── js/
│   ├── app.tsx                     # Entry point
│   ├── bootstrap.js                # Axios setup
│   │
│   ├── Components/
│   │   ├── ui/                     # Shadcn components
│   │   │   ├── button.tsx
│   │   │   ├── card.tsx
│   │   │   ├── input.tsx
│   │   │   ├── label.tsx
│   │   │   └── table.tsx
│   │   ├── theme-provider.tsx      # Theme context
│   │   └── theme-toggle.tsx        # Theme dropdown
│   │
│   ├── hooks/
│   │   ├── index.ts                # Export all hooks
│   │   └── use-is-dark.ts          # Dark mode hook
│   │
│   ├── lib/
│   │   └── utils.ts                # cn() helper
│   │
│   └── Pages/
│       ├── Auth/
│       │   └── Login.tsx
│       ├── Dashboard.tsx
│       └── Finance/
│           └── Accounts/
│               └── Index.tsx
│
├── css/
│   └── app.css                     # Global styles
│
└── views/
    └── app.blade.php               # Root template
```

---

## 3. Key Technologies

### Inertia.js
- Server-side routing dengan Laravel
- Client-side rendering dengan React
- No API layer needed untuk internal pages
- Automatic page preloading

### Tailwind CSS v4
- Utility-first CSS framework
- CSS custom properties untuk theming
- JIT (Just-In-Time) compilation

### Shadcn/UI
- Headless UI components
- Copy-paste implementation
- Full customization control

---

## 4. Custom Hooks

### `useIsDark()`

**Location:** `hooks/use-is-dark.ts`

**Purpose:** Mendapatkan status dark mode secara SINKRON untuk menghindari flash.

**Implementation:**
```tsx
export function useIsDark(): boolean {
    const { theme } = useTheme();
    
    const getIsDark = (): boolean => {
        if (theme === 'dark') return true;
        if (theme === 'light') return false;
        
        // Check document class (sudah di-set oleh ThemeProvider)
        if (typeof document !== 'undefined') {
            return document.documentElement.classList.contains('dark');
        }
        
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    };
    
    // useMemo untuk synchronous initialization
    const initialValue = useMemo(() => getIsDark(), []);
    const [isDark, setIsDark] = useState(initialValue);
    
    useEffect(() => {
        setIsDark(getIsDark());
    }, [theme]);
    
    return isDark;
}
```

**Usage:**
```tsx
import { useIsDark } from '@/hooks/use-is-dark';

function MyComponent() {
    const isDark = useIsDark();
    return <div className={isDark ? "bg-gray-900" : "bg-white"}>...</div>;
}
```

---

## 5. Theme System

### ThemeProvider

Wrap di `app.tsx`:
```tsx
<ThemeProvider defaultTheme="system" storageKey="baitul-jannah-theme">
    <App {...props} />
</ThemeProvider>
```

### Theme Options
- `light` - Mode terang
- `dark` - Mode gelap  
- `system` - Ikuti preferensi sistem

### Storage
- Key: `baitul-jannah-theme`
- Stored in: `localStorage`

---

## 6. Styling Patterns

### Conditional Classes dengan cn()

```tsx
import { cn } from '@/lib/utils';

<div className={cn(
    "base-classes",
    isDark ? "dark-classes" : "light-classes",
    condition && "conditional-classes"
)}>
```

### Background Layers Pattern

```tsx
{/* Light Mode */}
<div className={cn(
    "absolute inset-0 bg-gradient-light",
    isDark ? "opacity-0" : "opacity-100"
)} />

{/* Dark Mode */}
<div className={cn(
    "absolute inset-0",
    isDark ? "opacity-100" : "opacity-0"
)}>
    <div className="bg-gradient-dark" />
    {/* Decorative elements */}
</div>
```

---

## 7. Component Guidelines

### Page Component Template

```tsx
import { Head } from '@inertiajs/react';
import { useIsDark } from '@/hooks/use-is-dark';
import { cn } from '@/lib/utils';

export default function PageName() {
    const isDark = useIsDark();
    
    return (
        <>
            <Head title="Page Title" />
            
            {/* Background Layers */}
            <div className="fixed inset-0">
                {/* Light + Dark backgrounds */}
            </div>
            
            {/* Content */}
            <div className="relative min-h-screen">
                {/* Navbar */}
                {/* Main Content */}
            </div>
        </>
    );
}
```

### Card Component Guidelines

```tsx
<Card className={cn(
    "border backdrop-blur-sm",
    isDark 
        ? "bg-gray-900/50 border-gray-800" 
        : "bg-white border-gray-200"
)}>
```

---

## 8. Build Commands

```bash
# Development (with hot reload)
npm run dev

# Production build
npm run build

# Type checking
npm run ts-check
```

---

## 9. Path Aliases

Configured in `tsconfig.json` and `vite.config.ts`:

| Alias | Path |
|-------|------|
| `@/Components` | `resources/js/Components` |
| `@/hooks` | `resources/js/hooks` |
| `@/lib` | `resources/js/lib` |
| `@/Pages` | `resources/js/Pages` |

---

## 10. Best Practices

1. **Always use `useIsDark` hook** - Jangan pakai CSS `dark:` untuk backgrounds
2. **Use `cn()` for conditional classes** - Merge classes dengan aman
3. **Separate background layers** - Light dan dark dalam div terpisah
4. **Use `pointer-events-none`** - Pada layer yang tidak aktif
5. **Keep transitions minimal** - 300ms max untuk theme switch
6. **Test navigation in dark mode** - Pastikan tidak ada flash
