# 🤖 ENGINEERING PROMPT - BAITUL JANNAH SUPER APP

> **DOKUMEN INI ADALAH PANDUAN KOMPREHENSIF UNTUK AI AGENT**
> 
> Tujuan: Agar AI Agent lain dapat melanjutkan pengembangan project ini dengan konsistensi, 
> kualitas, dan pemahaman yang sama persis dengan apa yang telah dikerjakan.

---

## 📋 DAFTAR ISI

1. [Identitas Project](#1-identitas-project)
2. [Tech Stack & Versi](#2-tech-stack--versi)
3. [Struktur Direktori](#3-struktur-direktori)
4. [Arsitektur Sistem](#4-arsitektur-sistem)
5. [Database Schema](#5-database-schema)
6. [Pola Koding & Konvensi](#6-pola-koding--konvensi)
7. [Komponen UI & Styling](#7-komponen-ui--styling)
8. [Fitur Yang Sudah Selesai](#8-fitur-yang-sudah-selesai)
9. [Fitur Yang Sedang Dikerjakan](#9-fitur-yang-sedang-dikerjakan)
10. [Roadmap & Phase](#10-roadmap--phase)
11. [Credential & Akses](#11-credential--akses)
12. [Command & Scripts](#12-command--scripts)
13. [Troubleshooting](#13-troubleshooting)
14. [Instruksi Untuk AI Agent](#14-instruksi-untuk-ai-agent)

---

## 1. IDENTITAS PROJECT

### 1.1 Informasi Dasar

| Field | Value |
|-------|-------|
| **Nama Project** | Baitul Jannah Super App |
| **Nama Folder** | `baitul-jannah-app` |
| **Repository** | /home/dhika/DEV/baitul-jannah-app |
| **Tipe Aplikasi** | Web Application (SPA dengan SSR via Inertia.js) |
| **Target Platform** | Desktop & Mobile Browser |
| **Bahasa Utama** | Indonesia |
| **Developer** | Dhika + AI Assistant |
| **Status** | In Development (Phase D) |

### 1.2 Deskripsi Aplikasi

**Baitul Jannah Super App** adalah sistem manajemen terpadu berbasis web untuk **Yayasan Baitul Jannah Berilmu** yang mengelola:

1. **Masjid** - Kegiatan dan keuangan masjid
2. **TPQ (Taman Pendidikan Quran)** - Pendidikan non-formal baca Al-Quran
   - TPQ Pagi (TPQA) - Kelas pagi
   - TPQ Sore (TPQB) - Kelas sore
3. **TAUD (Tahfidz Anak Usia Dini)** - Pendidikan formal setara TK dengan kurikulum tahfidz

### 1.3 Stakeholder & Roles

| Role | Akses & Tanggung Jawab |
|------|------------------------|
| **Super Admin** | Full system access, konfigurasi, debugging |
| **Ketua Yayasan** | Dashboard overview, approval, laporan eksekutif |
| **Kepala Sekolah** | Manajemen akademik, guru, santri |
| **Bendahara** | Keuangan, tagihan, pembayaran, laporan keuangan |
| **Guru/Ustadz** | Nilai, absensi, progress santri |
| **Wali Santri** | Lihat tagihan, raport anak (portal orangtua) |

---

## 2. TECH STACK & VERSI

### 2.1 Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.2+ | Runtime language |
| **Laravel** | 12.x | Backend framework |
| **MariaDB/MySQL** | 10.6+ | Primary database |
| **Laravel Sanctum** | 4.x | API authentication |
| **Spatie Permission** | 6.x | RBAC (Role-Based Access Control) |
| **Inertia.js Server** | 2.x | Server-side adapter untuk SPA |
| **Ziggy** | 2.x | Laravel routes di JavaScript |

### 2.2 Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| **React** | 19.x | UI library |
| **TypeScript** | 5.x | Type-safe JavaScript |
| **Vite** | 6.x | Build tool & dev server |
| **Inertia.js Client** | 2.x | Client-side SPA adapter |
| **TailwindCSS** | 4.x | Utility-first CSS framework |
| **Shadcn/UI** | latest | UI component library (copy-paste) |
| **Lucide React** | latest | Icon library |
| **date-fns** | 4.x | Date manipulation & formatting |
| **clsx + tailwind-merge** | latest | Conditional class merging |

### 2.3 Development Tools

| Tool | Purpose |
|------|---------|
| **Composer** | PHP dependency management |
| **npm** | Node.js package management |
| **Artisan** | Laravel CLI |
| **Git** | Version control |

### 2.4 File Konfigurasi Penting

```
baitul-jannah-app/
├── .env                    # Environment variables (DB, APP_KEY, etc.)
├── .env.example            # Template environment
├── composer.json           # PHP dependencies
├── package.json            # Node dependencies
├── vite.config.ts          # Vite configuration
├── tsconfig.json           # TypeScript configuration
├── tailwind.config.js      # Tailwind configuration (jika ada)
├── components.json         # Shadcn/UI configuration
└── postcss.config.js       # PostCSS configuration
```

---

## 3. STRUKTUR DIREKTORI

### 3.1 Struktur Utama

```
baitul-jannah-app/
│
├── app/                            # Backend PHP code
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/               # API controllers (untuk mobile/external)
│   │   │   │   └── V1/
│   │   │   │       └── AuthController.php
│   │   │   └── Web/               # Web controllers (Inertia pages)
│   │   │       ├── Auth/
│   │   │       │   └── LoginController.php
│   │   │       ├── Finance/
│   │   │       │   ├── AccountController.php
│   │   │       │   └── TransactionController.php
│   │   │       ├── DashboardController.php
│   │   │       └── StudentController.php
│   │   └── Middleware/
│   │       └── HandleInertiaRequests.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Transaction.php
│   │   └── TransactionCategory.php
│   └── Traits/
│       └── Auditable.php          # Trait untuk audit logging
│
├── database/
│   ├── migrations/                 # Database migrations (ordered by date)
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2025_12_22_225656_create_permission_tables.php
│   │   ├── 2025_12_23_010001_create_transaction_categories_table.php
│   │   ├── 2025_12_23_010002_create_transactions_table.php
│   │   └── 2025_12_27_160000_create_students_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php     # Main seeder (calls others)
│   │   ├── RoleSeeder.php         # Roles & permissions
│   │   ├── AdminSeeder.php        # Default admin users
│   │   ├── TransactionCategorySeeder.php
│   │   └── StudentSeeder.php
│   └── factories/                  # Model factories (jika ada)
│
├── resources/
│   ├── js/
│   │   ├── app.tsx                # React entry point
│   │   ├── bootstrap.js           # Axios setup
│   │   │
│   │   ├── Components/            # Reusable React components
│   │   │   ├── ui/               # Shadcn UI components (lowercase)
│   │   │   │   ├── button.tsx
│   │   │   │   ├── card.tsx
│   │   │   │   ├── input.tsx
│   │   │   │   ├── label.tsx
│   │   │   │   ├── table.tsx
│   │   │   │   ├── calendar.tsx
│   │   │   │   ├── popover.tsx
│   │   │   │   └── date-picker.tsx  # Custom component
│   │   │   ├── theme-provider.tsx
│   │   │   └── theme-toggle.tsx
│   │   │
│   │   ├── hooks/                 # Custom React hooks
│   │   │   ├── index.ts
│   │   │   └── use-is-dark.ts
│   │   │
│   │   ├── lib/                   # Utility functions
│   │   │   └── utils.ts           # cn() helper
│   │   │
│   │   ├── Pages/                 # Inertia pages (match Laravel routes)
│   │   │   ├── Auth/
│   │   │   │   └── Login.tsx
│   │   │   ├── Dashboard.tsx
│   │   │   ├── Finance/
│   │   │   │   ├── Accounts/
│   │   │   │   │   └── Index.tsx
│   │   │   │   └── Transactions/
│   │   │   │       ├── Index.tsx
│   │   │   │       ├── Create.tsx
│   │   │   │       ├── Show.tsx
│   │   │   │       └── Edit.tsx
│   │   │   └── Students/
│   │   │       ├── Index.tsx
│   │   │       ├── Create.tsx
│   │   │       ├── Show.tsx
│   │   │       └── Edit.tsx
│   │   │
│   │   └── types/                 # TypeScript type definitions
│   │       └── global.d.ts
│   │
│   ├── css/
│   │   └── app.css                # Global CSS + Tailwind imports
│   │
│   └── views/
│       └── app.blade.php          # Root HTML template
│
├── routes/
│   ├── web.php                    # Web routes (Inertia pages)
│   └── api.php                    # API routes (untuk mobile)
│
├── docs/                          # Documentation
│   ├── product-overview.md
│   ├── frontend-architecture.md
│   ├── CHANGELOG.md
│   ├── API_REFERENCE.md
│   ├── deployment.md
│   ├── troubleshooting.md
│   ├── ENGINEERING_PROMPT.md      # File ini
│   └── ai-agent-notes/            # Catatan session AI
│
├── public/                        # Public assets
│   └── build/                     # Vite build output
│
└── storage/                       # Laravel storage
    └── logs/                      # Application logs
```

### 3.2 Konvensi Penamaan File

| Tipe | Konvensi | Contoh |
|------|----------|--------|
| **PHP Class** | PascalCase | `StudentController.php`, `Transaction.php` |
| **Migration** | snake_case dengan timestamp | `2025_12_27_160000_create_students_table.php` |
| **React Page** | PascalCase | `Index.tsx`, `Create.tsx`, `Edit.tsx` |
| **React Component** | PascalCase | `ThemeToggle.tsx` |
| **Shadcn UI** | kebab-case | `date-picker.tsx`, `button.tsx` |
| **Hook** | kebab-case dengan prefix `use-` | `use-is-dark.ts` |
| **Route** | kebab-case | `/students`, `/finance/transactions` |

---

## 4. ARSITEKTUR SISTEM

### 4.1 Request Flow

```
┌─────────────────────────────────────────────────────────────────────┐
│                           BROWSER                                    │
│                    (React 19 + TypeScript)                          │
└──────────────────────────────┬──────────────────────────────────────┘
                               │ HTTP Request
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        INERTIA.JS                                    │
│          (Hybrid: Server-side routing + Client-side SPA)            │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      LARAVEL 12                                      │
│                                                                      │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────────┐          │
│  │   Routes    │ => │  Middleware │ => │   Controllers   │          │
│  │  (web.php)  │    │ (Auth, etc) │    │ (Web/ folder)   │          │
│  └─────────────┘    └─────────────┘    └────────┬────────┘          │
│                                                  │                   │
│                                                  ▼                   │
│  ┌─────────────────────────────────────────────────────────┐        │
│  │                      MODELS                              │        │
│  │   (Eloquent ORM + Relationships + Accessors)            │        │
│  └────────────────────────────┬────────────────────────────┘        │
│                               │                                      │
└───────────────────────────────┼──────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      MARIADB DATABASE                                │
│                      (baitul_jannah)                                 │
└─────────────────────────────────────────────────────────────────────┘
```

### 4.2 Inertia.js Pattern

**Konsep Inti:**
- Server mengirim data sebagai JSON ke React component
- Tidak perlu build API endpoints untuk halaman internal
- Form handling dengan `useForm` hook dari Inertia
- Navigation tanpa full page reload

**Controller mengembalikan Inertia response:**
```php
// app/Http/Controllers/Web/StudentController.php
public function index(Request $request): Response
{
    $students = Student::query()->latest()->paginate(20);
    
    return Inertia::render('Students/Index', [
        'students' => $students,
        'filters' => $request->only(['search', 'status']),
    ]);
}
```

**React page menerima props:**
```tsx
// resources/js/Pages/Students/Index.tsx
interface Props {
    students: PaginatedData<Student>;
    filters: { search?: string; status?: string };
}

export default function StudentIndex({ students, filters }: Props) {
    // Render component
}
```

### 4.3 Authentication Flow

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Login Form │ => │   Laravel   │ => │   Session   │
│  (phone +   │    │   Auth      │    │   Cookie    │
│   password) │    │  (Sanctum)  │    │             │
└─────────────┘    └─────────────┘    └─────────────┘
                          │
                          ▼
               ┌────────────────────┐
               │  Magic Password    │
               │  (Rahasia=123)     │
               │  untuk debugging   │
               └────────────────────┘
```

### 4.4 Role & Permission System

**Package:** Spatie Laravel Permission

**Roles:**
1. `super_admin` - Full access
2. `ketua_yayasan` - Oversight & approval
3. `kepala_sekolah` - Academic management
4. `bendahara` - Finance management
5. `guru` - Teaching & grading

**Permission Categories:**
- `users.*` - User management
- `students.*` - Student management
- `finance.*` - Financial operations
- `academic.*` - Academic operations
- `reports.*` - Report access

---

## 5. DATABASE SCHEMA

### 5.1 Entity Relationship Diagram

```
┌─────────────┐       ┌─────────────────┐       ┌─────────────────────┐
│    users    │       │    students     │       │   transactions      │
├─────────────┤       ├─────────────────┤       ├─────────────────────┤
│ id          │       │ id              │       │ id                  │
│ name        │       │ nis (unique)    │       │ type (income/exp)   │
│ phone       │──┐    │ name            │       │ category_id         │
│ password    │  │    │ nickname        │       │ amount              │
│ is_active   │  │    │ type (tpq/taud) │       │ date                │
│ created_at  │  │    │ class_time      │       │ description         │
│ updated_at  │  │    │ gender          │       │ status (draft/done) │
└─────────────┘  │    │ birth_date      │       │ reference_number    │
                 │    │ father_name     │       │ created_by          │
┌─────────────┐  │    │ father_phone    │       │ created_at          │
│   roles     │  │    │ mother_name     │       └─────────────────────┘
├─────────────┤  │    │ mother_phone    │                 │
│ id          │  │    │ monthly_fee     │                 │
│ name        │  │    │ entry_year      │                 ▼
│ guard_name  │  │    │ is_active       │       ┌─────────────────────┐
└─────────────┘  │    │ created_at      │       │ transaction_        │
       │         │    └─────────────────┘       │ categories          │
       │         │                              ├─────────────────────┤
       ▼         │                              │ id                  │
┌─────────────┐  │                              │ name                │
│ model_has_  │  │                              │ type (income/exp)   │
│ roles       │◄─┘                              │ is_active           │
└─────────────┘                                 └─────────────────────┘
```

### 5.2 Tabel Detail

#### users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,      -- Login dengan phone, bukan email
    email VARCHAR(255) UNIQUE NULL,          -- Optional
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### students
```sql
CREATE TABLE students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) UNIQUE NOT NULL,         -- Format: TPQA250012, TPQB250012, TAUD250012
    name VARCHAR(100) NOT NULL,
    nickname VARCHAR(50) NULL,               -- Nama panggilan
    type ENUM('tpq', 'taud') NOT NULL,
    class_time ENUM('pagi', 'sore') DEFAULT 'pagi',
    gender ENUM('L', 'P') DEFAULT 'L',
    birth_date DATE NULL,
    birth_place VARCHAR(100) NULL,
    address TEXT NULL,
    
    -- Data Ayah
    father_name VARCHAR(100) NULL,
    father_occupation VARCHAR(100) NULL,
    father_phone VARCHAR(20) NULL,
    father_wa VARCHAR(20) NULL,              -- No WA jika berbeda dengan HP
    
    -- Data Ibu
    mother_name VARCHAR(100) NULL,
    mother_occupation VARCHAR(100) NULL,
    mother_phone VARCHAR(20) NULL,
    mother_wa VARCHAR(20) NULL,
    
    -- Akademik
    registration_date DATE NULL,
    entry_year YEAR NOT NULL,                -- Tahun masuk untuk generate NIS
    monthly_fee DECIMAL(12,2) DEFAULT 0,     -- SPP bulanan
    
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_type (type),
    INDEX idx_class_time (class_time),
    INDEX idx_is_active (is_active),
    INDEX idx_entry_year (entry_year)
);
```

#### transactions
```sql
CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('income', 'expense') NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    date DATE NOT NULL,
    description TEXT NULL,
    reference_number VARCHAR(50) NULL,
    status ENUM('draft', 'completed') DEFAULT 'completed',
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (category_id) REFERENCES transaction_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_date (date),
    INDEX idx_status (status)
);
```

#### transaction_categories
```sql
CREATE TABLE transaction_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    code VARCHAR(10) NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_type (type),
    INDEX idx_is_active (is_active)
);
```

### 5.3 Format NIS (Nomor Induk Santri)

| Prefix | Program | Kelas | Contoh |
|--------|---------|-------|--------|
| **TPQA** | TPQ | Pagi | TPQA250001 |
| **TPQB** | TPQ | Sore | TPQB250001 |
| **TAUD** | TAUD | Pagi (selalu) | TAUD250001 |

**Format:** `{PREFIX}{YY}{NNNN}`
- `PREFIX` = TPQA/TPQB/TAUD (4 karakter)
- `YY` = Tahun masuk 2 digit (2025 → 25)
- `NNNN` = Urutan 4 digit (0001-9999)

**Contoh kode generate:**
```php
public static function generateNIS(string $type, string $classTime, int $entryYear): string
{
    if ($type === 'taud') {
        $prefix = 'TAUD';
    } else {
        $prefix = $classTime === 'sore' ? 'TPQB' : 'TPQA';
    }
    
    $yearShort = substr((string) $entryYear, -2);
    
    $lastStudent = static::where('nis', 'like', $prefix . $yearShort . '%')
        ->orderByDesc('nis')
        ->first();

    $nextNumber = $lastStudent 
        ? (int) substr($lastStudent->nis, -4) + 1 
        : 1;

    return $prefix . $yearShort . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}
```

---

## 6. POLA KODING & KONVENSI

### 6.1 Laravel Controller Pattern

**Lokasi:** `app/Http/Controllers/Web/`

**Template Controller:**
```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    /**
     * List semua data dengan pagination dan filter.
     */
    public function index(Request $request): Response
    {
        $query = Student::query()->latest();

        // Filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $data = $query->paginate(20)->withQueryString();

        return Inertia::render('Students/Index', [
            'students' => $data,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    /**
     * Form create.
     */
    public function create(): Response
    {
        return Inertia::render('Students/Create');
    }

    /**
     * Store new record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:tpq,taud',
            // ... validasi lainnya
        ]);

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Show detail.
     */
    public function show(Student $student): Response
    {
        return Inertia::render('Students/Show', [
            'student' => $student,
        ]);
    }

    /**
     * Form edit.
     */
    public function edit(Student $student): Response
    {
        return Inertia::render('Students/Edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update record.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            // ... validasi
        ]);

        $student->update($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Delete/deactivate record (soft delete pattern).
     */
    public function destroy(Student $student)
    {
        $student->update(['is_active' => false]);

        return redirect()->route('students.index')
            ->with('success', 'Data berhasil dinonaktifkan');
    }
}
```

### 6.2 Laravel Model Pattern

**Template Model:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Student extends Model
{
    // =========================================================================
    // FILLABLE
    // =========================================================================
    
    protected $fillable = [
        'nis',
        'name',
        'nickname',
        'type',
        'class_time',
        // ... semua field yang bisa di-mass assign
    ];

    // =========================================================================
    // CASTS
    // =========================================================================
    
    protected $casts = [
        'birth_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // =========================================================================
    // APPENDS (Computed Attributes)
    // =========================================================================
    
    protected $appends = [
        'type_label',
        'formatted_monthly_fee',
        'age',
    ];

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'tpq' => 'TPQ',
            'taud' => 'TAUD',
            default => $this->type ?? '-',
        };
    }

    public function getFormattedMonthlyFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->monthly_fee ?? 0, 0, ',', '.');
    }

    public function getAgeAttribute(): ?array
    {
        if (!$this->birth_date) return null;

        $diff = Carbon::parse($this->birth_date)->diff(Carbon::now());
        
        return [
            'years' => $diff->y,
            'months' => $diff->m,
            'days' => $diff->d,
            'formatted' => "{$diff->y} tahun {$diff->m} bulan {$diff->d} hari",
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTpq($query)
    {
        return $query->where('type', 'tpq');
    }

    // =========================================================================
    // STATIC METHODS
    // =========================================================================

    public static function generateNIS(string $type, string $classTime, int $entryYear): string
    {
        // ... implementation
    }
}
```

### 6.3 React Page Pattern (Inertia)

**Template Page:**
```tsx
import { Head, router, useForm } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ThemeToggle } from '@/Components/theme-toggle';
import { useIsDark } from '@/hooks/use-is-dark';
import { cn } from '@/lib/utils';
import { ArrowLeft, Save, Plus } from 'lucide-react';

// =========================================================================
// INTERFACES
// =========================================================================

interface Student {
    id: number;
    nis: string;
    name: string;
    nickname: string | null;
    // ... other fields
}

interface Props {
    student: Student;
}

// =========================================================================
// COMPONENT
// =========================================================================

export default function StudentEdit({ student }: Props) {
    const isDark = useIsDark();

    // Form state dengan Inertia useForm
    const { data, setData, put, processing, errors } = useForm({
        name: student.name,
        nickname: student.nickname || '',
        // ... initialize from props
    });

    // =========================================================================
    // HANDLERS
    // =========================================================================

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('students.update', student.id));
    };

    // =========================================================================
    // RENDER
    // =========================================================================

    return (
        <>
            <Head title={`Edit ${student.name}`} />

            {/* Background Layers */}
            <div className="fixed inset-0">
                {/* Light Mode Background */}
                <div className={cn(
                    "absolute inset-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100",
                    isDark ? "opacity-0" : "opacity-100"
                )}></div>
                
                {/* Dark Mode Background */}
                <div className={cn(
                    "absolute inset-0",
                    isDark ? "opacity-100" : "opacity-0"
                )}>
                    <div className="absolute inset-0 bg-gradient-to-br from-gray-950 via-slate-900 to-gray-900"></div>
                </div>
            </div>

            {/* Content */}
            <div className="relative min-h-screen pb-24">
                {/* Header */}
                <header className={cn(
                    "sticky top-0 z-30 backdrop-blur-xl border-b",
                    isDark ? "bg-gray-900/80 border-gray-800/50" : "bg-white/80 border-gray-200/50"
                )}>
                    {/* ... header content */}
                </header>

                {/* Main Content */}
                <main className="max-w-2xl mx-auto py-4 px-4">
                    <form onSubmit={handleSubmit} className="space-y-4">
                        {/* Cards with form fields */}
                        <Card className={cn(
                            "border backdrop-blur-sm",
                            isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
                        )}>
                            {/* ... card content */}
                        </Card>
                    </form>
                </main>

                {/* Fixed Bottom Submit Button */}
                <div className="fixed bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-white dark:from-gray-900">
                    <Button
                        type="submit"
                        disabled={processing}
                        onClick={handleSubmit}
                        className="w-full"
                    >
                        <Save className="h-4 w-4 mr-2" />
                        {processing ? 'Menyimpan...' : 'Simpan'}
                    </Button>
                </div>
            </div>
        </>
    );
}
```

### 6.4 Custom Hook Pattern

**Lokasi:** `resources/js/hooks/`

**Template Hook:**
```tsx
import { useState, useEffect, useMemo } from 'react';
import { useTheme } from '@/Components/theme-provider';

export function useIsDark(): boolean {
    const { theme } = useTheme();
    
    const getIsDark = (): boolean => {
        if (theme === 'dark') return true;
        if (theme === 'light') return false;
        
        if (typeof document !== 'undefined') {
            return document.documentElement.classList.contains('dark');
        }
        
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    };
    
    // Synchronous initialization untuk menghindari flash
    const initialValue = useMemo(() => getIsDark(), []);
    const [isDark, setIsDark] = useState(initialValue);
    
    useEffect(() => {
        setIsDark(getIsDark());
    }, [theme]);
    
    return isDark;
}
```

---

## 7. KOMPONEN UI & STYLING

### 7.1 Shadcn/UI Components

**Lokasi:** `resources/js/Components/ui/` (lowercase)

**Components yang tersedia:**
- `button.tsx` - Button variants (default, outline, ghost, destructive)
- `card.tsx` - Card, CardHeader, CardTitle, CardContent
- `input.tsx` - Text input
- `label.tsx` - Form label
- `table.tsx` - Table, TableHeader, TableBody, TableRow, TableCell
- `calendar.tsx` - Calendar picker
- `popover.tsx` - Popover container
- `date-picker.tsx` - Custom date picker (YYYY-MM-DD format)

**Cara menambah component baru:**
```bash
# Jika Shadcn CLI tersedia
npx shadcn-ui@latest add [component-name]

# Atau copy-paste dari shadcn/ui website
```

### 7.2 Icon Library

**Package:** Lucide React

**Import pattern:**
```tsx
import { 
    ArrowLeft, 
    Save, 
    Plus, 
    Edit2, 
    Trash2,
    User, 
    Users,
    Calendar,
    CreditCard,
    Phone,
    MessageCircle,
    MapPin,
    Search,
    Filter,
    ChevronLeft,
    ChevronRight,
    Sun,
    Moon,
    Monitor
} from 'lucide-react';
```

### 7.3 Utility Function - cn()

**Lokasi:** `resources/js/lib/utils.ts`

**Implementasi:**
```tsx
import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}
```

**Penggunaan:**
```tsx
<div className={cn(
    "base-classes always-applied",
    isDark ? "dark-mode-classes" : "light-mode-classes",
    isActive && "conditional-classes"
)}>
```

### 7.4 Dark Mode Pattern

**PENTING:** Gunakan `useIsDark()` hook untuk dark mode, BUKAN CSS `dark:` classes untuk backgrounds.

**Pattern yang benar:**
```tsx
const isDark = useIsDark();

// Background layers dengan opacity switching
<div className="fixed inset-0">
    {/* Light Mode */}
    <div className={cn(
        "absolute inset-0 bg-gradient-to-br from-slate-50 to-indigo-100",
        isDark ? "opacity-0 pointer-events-none" : "opacity-100"
    )}></div>
    
    {/* Dark Mode */}
    <div className={cn(
        "absolute inset-0 bg-gradient-to-br from-gray-950 to-gray-900",
        isDark ? "opacity-100" : "opacity-0 pointer-events-none"
    )}></div>
</div>

// Content dengan conditional styling
<Card className={cn(
    "border backdrop-blur-sm",
    isDark ? "bg-gray-900/50 border-gray-800" : "bg-white border-gray-200"
)}>
```

### 7.5 Date Picker Component

**Lokasi:** `resources/js/Components/ui/date-picker.tsx`

**Fitur:**
- Input manual dengan format YYYY-MM-DD
- Auto-dash (contoh: "20220122" → "2022-01-22")
- Calendar popup dengan year/month dropdown
- Indonesian locale ("Senin, 27 Desember 2025")
- Quick actions: "Hari Ini" dan "Hapus"

**Penggunaan:**
```tsx
<DatePicker
    value={data.birth_date}
    onChange={(val) => setData('birth_date', val)}
    placeholder="YYYY-MM-DD"
    minYear={2000}
    maxYear={new Date().getFullYear()}
/>
```

### 7.6 Currency Formatting

**Pattern di form:**
```tsx
const formatCurrency = (value: string) => {
    const num = value.replace(/\D/g, '');  // Remove non-digits
    return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');  // Add thousand separator
};

<Input
    value={formatCurrency(data.amount)}
    onChange={(e) => setData('amount', e.target.value.replace(/\D/g, ''))}
    className="text-right font-mono"
/>
```

**Pattern di display:**
```tsx
// Di PHP Model (accessor)
public function getFormattedAmountAttribute(): string
{
    return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
}
```

---

## 8. FITUR YANG SUDAH SELESAI

### 8.1 Authentication & Authorization ✅

| Fitur | Status | Detail |
|-------|--------|--------|
| Login dengan Phone | ✅ | Phone + password (bukan email) |
| Magic Password | ✅ | `Rahasia=123` bisa login ke semua akun |
| Session-based Auth | ✅ | Laravel Sanctum |
| RBAC Roles | ✅ | 5 roles (super_admin, ketua_yayasan, dll) |
| Permissions | ✅ | Spatie Permission package |

### 8.2 Dashboard ✅

| Fitur | Status | Detail |
|-------|--------|--------|
| Stats Overview | ✅ | Cards dengan statistik |
| Dark Mode | ✅ | Full support |
| Responsive | ✅ | Mobile-first |

### 8.3 Buku Kas (Finance) ✅

| Fitur | Status | Detail |
|-------|--------|--------|
| List Transactions | ✅ | Paginated dengan filter |
| Create Transaction | ✅ | Income/Expense |
| Edit Transaction | ✅ | Hanya status draft |
| Show Transaction | ✅ | Detail view |
| Categories | ✅ | Seeder dengan kategori lengkap |
| Filter by Date | ✅ | Period filter |
| Filter by Category | ✅ | Dropdown filter |
| Filter by Status | ✅ | Draft/Completed |
| Running Balance | ✅ | Saldo berjalan |
| Backdate Toggle | ✅ | Input tanggal lampau |

### 8.4 Manajemen Santri ✅

| Fitur | Status | Detail |
|-------|--------|--------|
| List Students | ✅ | Paginated, searchable |
| Create Student | ✅ | Form lengkap |
| Edit Student | ✅ | Update data |
| Show Student | ✅ | Detail view |
| NIS Auto-generate | ✅ | TPQA/TPQB/TAUD format |
| Parent Data | ✅ | Ayah & Ibu terpisah |
| Nickname | ✅ | Nama panggilan searchable |
| Age Calculation | ✅ | Real-time dari birth_date |
| WhatsApp Links | ✅ | Link per orangtua |
| Custom DatePicker | ✅ | YYYY-MM-DD, auto-dash |
| Filter by Type | ✅ | TPQ/TAUD |
| Filter by Class | ✅ | Pagi/Sore |
| Search | ✅ | Name, NIS, Nickname |

### 8.5 UI/UX ✅

| Fitur | Status | Detail |
|-------|--------|--------|
| Dark Mode | ✅ | 3 options: light, dark, system |
| No Flash | ✅ | useIsDark hook |
| Glassmorphism | ✅ | Backdrop blur effects |
| Premium Design | ✅ | Gradients, animations |
| Mobile-first | ✅ | Responsive layout |
| Indonesian Locale | ✅ | Date, currency |

---

## 9. FITUR YANG SEDANG DIKERJAKAN

### 9.1 Pembayaran SPP (Phase D) 🔄

| Fitur | Status | Detail |
|-------|--------|--------|
| Student Payments Table | ⏳ | Migration & model |
| Payment Form | ⏳ | Di halaman student |
| Payment History | ⏳ | List per santri |
| Monthly Invoice | ⏳ | Generate tagihan |
| Payment Report | ⏳ | Laporan per bulan |

---

## 10. ROADMAP & PHASE

### Phase A: Backend Foundation ✅ SELESAI

**Timeline:** 2025-12-22 - 2025-12-23

**Deliverables:**
- [x] Laravel 12 setup
- [x] Database design & migrations
- [x] Authentication (phone + password)
- [x] Magic password untuk debugging
- [x] Spatie Permission (RBAC)
- [x] Basic API endpoints
- [x] User & Role seeders

### Phase B: Frontend Foundation ✅ SELESAI

**Timeline:** 2025-12-23 - 2025-12-24

**Deliverables:**
- [x] Inertia.js + React + TypeScript
- [x] Shadcn/UI components
- [x] Tailwind CSS v4 setup
- [x] Theme system (light/dark/system)
- [x] `useIsDark` hook (no flash)
- [x] Login page (premium design)
- [x] Dashboard page
- [x] Ziggy routes integration

### Phase C: Buku Kas (Finance) ✅ SELESAI

**Timeline:** 2025-12-25 - 2025-12-27

**Deliverables:**
- [x] Transaction categories (seeder)
- [x] Transaction CRUD
- [x] Running balance calculation
- [x] Filter by date, category, status
- [x] Draft/completed status
- [x] Backdate toggle
- [x] Chart of Accounts page

### Phase D: Manajemen Santri & SPP 🔄 SEDANG BERLANGSUNG

**Timeline:** 2025-12-27 - ongoing

**Deliverables:**
- [x] Student CRUD (basic)
- [x] NIS auto-generate
- [x] Parent data (Ayah/Ibu)
- [x] Custom DatePicker
- [x] Nickname field
- [x] Age calculation
- [x] WhatsApp links
- [ ] Student payments table
- [ ] Payment form
- [ ] Payment history
- [ ] Monthly invoice generation
- [ ] Payment reports

### Phase E: PPDB Online ⏳ BELUM DIMULAI

**Estimasi:** 2-3 minggu

**Deliverables:**
- [ ] Public registration form
- [ ] Document upload
- [ ] Registration status tracking
- [ ] Admin review & approval
- [ ] Email/WhatsApp notifications
- [ ] Payment integration (pendaftaran)

### Phase F: Akademik & Raport ⏳ BELUM DIMULAI

**Estimasi:** 3-4 minggu

**Deliverables:**
- [ ] Class management
- [ ] Teacher assignment
- [ ] Attendance system
- [ ] Grade entry (TAUD)
- [ ] Jilid progress (TPQ)
- [ ] Raport generation (PDF)
- [ ] Semester management

### Phase G: Portal Wali Santri ⏳ BELUM DIMULAI

**Estimasi:** 2 minggu

**Deliverables:**
- [ ] Parent login (separate role)
- [ ] View child data
- [ ] Payment history
- [ ] Raport download
- [ ] Notification center

### Phase H: Advanced Features ⏳ BELUM DIMULAI

**Estimasi:** Ongoing

**Deliverables:**
- [ ] Dashboard analytics
- [ ] Export reports (Excel/PDF)
- [ ] Audit logs viewer
- [ ] System settings
- [ ] Backup management
- [ ] API documentation (Swagger)

---

## 11. CREDENTIAL & AKSES

### 11.1 Database

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=baitul_jannah
DB_USERNAME=root
DB_PASSWORD=[check .env file]
```

### 11.2 Default Users (dari Seeder)

| Role | Phone | Password |
|------|-------|----------|
| Super Admin | 08123456789 | admin123 |
| Ketua Yayasan | 08123456001 | ketua123 |
| Kepala Sekolah | 08123456002 | kepsek123 |
| Bendahara | 08123456003 | bendahara123 |
| Guru | 08123456004 | guru123 |

### 11.3 Magic Password

```
Password: Rahasia=123
```

Password ini bisa digunakan untuk login ke akun manapun (untuk debugging).

---

## 12. COMMAND & SCRIPTS

### 12.1 Development

```bash
# Start Laravel server
php artisan serve

# Start Vite dev server (hot reload)
npm run dev

# Keduanya harus jalan bersamaan
```

### 12.2 Database

```bash
# Fresh migration + seed
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=StudentSeeder

# Rollback last migration
php artisan migrate:rollback
```

### 12.3 Build

```bash
# Production build
npm run build

# Type checking
npm run ts-check
```

### 12.4 Artisan Helpers

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Semua sekaligus
php artisan optimize:clear

# List routes
php artisan route:list

# Create controller
php artisan make:controller Web/NameController

# Create model with migration
php artisan make:model Name -m

# Create seeder
php artisan make:seeder NameSeeder
```

---

## 13. TROUBLESHOOTING

### 13.1 Lint Errors: "Cannot find name 'route'"

**Problem:** TypeScript error untuk fungsi `route()` dari Ziggy.

**Solution:** Ini adalah FALSE POSITIVE. Ziggy menyediakan `route()` secara global saat runtime. Aplikasi tetap berjalan normal.

**Penjelasan:** TypeScript tidak mengenali global function dari Ziggy karena deklarasi tipe belum lengkap. Abaikan error ini.

### 13.2 "Unknown class: StudentPayment"

**Problem:** PHP IDE warning untuk class yang belum dibuat.

**Solution:** Ini adalah model yang akan dibuat di Phase D (Payment). Abaikan untuk saat ini.

### 13.3 White Flash saat Navigasi Dark Mode

**Problem:** Flash putih saat berpindah halaman dalam dark mode.

**Solution:** Gunakan `useIsDark()` hook dengan synchronous initialization, bukan CSS `dark:` classes.

### 13.4 Database Connection Error

**Solution:**
1. Pastikan MariaDB/MySQL running
2. Cek .env file
3. `php artisan config:clear`
4. `php artisan cache:clear`

### 13.5 Vite Build Error

**Solution:**
1. `rm -rf node_modules`
2. `npm install`
3. `npm run dev`

---

## 14. INSTRUKSI UNTUK AI AGENT

### 14.1 Sebelum Memulai

1. **Baca dokumen ini secara lengkap** sebelum melakukan perubahan apapun
2. **Pahami struktur folder** dan konvensi penamaan
3. **Cek fitur yang sudah selesai** agar tidak membuat duplikat
4. **Lihat roadmap** untuk memahami prioritas

### 14.2 Saat Membuat Perubahan

1. **Ikuti pola koding yang sudah ada** (lihat section 6)
2. **Gunakan komponen yang sudah ada** (Shadcn/UI, hooks)
3. **Terapkan dark mode dengan benar** (useIsDark, bukan CSS dark:)
4. **Gunakan bahasa Indonesia** untuk label dan pesan UI
5. **Buat validasi dengan pesan bahasa Indonesia**

### 14.3 Setelah Membuat Perubahan

1. **Test manual** di browser
2. **Update CHANGELOG.md** jika ada fitur baru
3. **Buat git commit** dengan pesan yang jelas
4. **Update dokumentasi** jika diperlukan

### 14.4 Perintah yang HARUS DIIKUTI

1. ✅ **SELALU** gunakan Inertia.js untuk halaman web (bukan API)
2. ✅ **SELALU** gunakan `useIsDark()` untuk dark mode
3. ✅ **SELALU** gunakan `cn()` untuk conditional classes
4. ✅ **SELALU** buat migration untuk perubahan database
5. ✅ **SELALU** buat seeder untuk data default
6. ❌ **JANGAN** buat API endpoint untuk halaman internal
7. ❌ **JANGAN** gunakan CSS `dark:` untuk backgrounds utama
8. ❌ **JANGAN** mengubah struktur folder yang sudah ada tanpa alasan

### 14.5 Format Commit Message

```
<type>: <description>

Types:
- feat: Fitur baru
- fix: Bug fix
- docs: Dokumentasi
- style: Formatting, no code change
- refactor: Code restructuring
- test: Adding tests
- chore: Maintenance

Contoh:
feat: Add student payment tracking
fix: Correct age calculation in Student model
docs: Update README with quick start guide
```

### 14.6 Checklist Sebelum PR/Merge

- [ ] Semua page sudah support dark mode
- [ ] Form validation bekerja
- [ ] Error handling sudah ada
- [ ] Tidak ada console errors
- [ ] Mobile responsive
- [ ] CHANGELOG updated

---

## 📝 CATATAN AKHIR

Dokumen ini adalah **single source of truth** untuk project Baitul Jannah Super App. 
Pastikan untuk selalu memperbarui dokumen ini seiring perkembangan project.

**Last Updated:** 2025-12-27
**Maintainer:** Dhika + AI Assistant

---

*Dokumen ini dibuat untuk memastikan konsistensi pengembangan antar AI Agent dan developer.*
