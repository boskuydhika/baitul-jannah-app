# 🕌 BAITUL JANNAH SUPER APP

> Sistem Manajemen Terpadu untuk Yayasan Baitul Jannah Berilmu

---

## 📋 Daftar Isi

- [Tentang Aplikasi](#tentang-aplikasi)
- [Entitas yang Dikelola](#entitas-yang-dikelola)
- [Fitur Utama](#fitur-utama)
- [Stakeholder](#stakeholder)
- [Tech Stack](#tech-stack)
- [Arsitektur Sistem](#arsitektur-sistem)

---

## Tentang Aplikasi

**Baitul Jannah Super App** adalah sistem manajemen berbasis web yang dirancang khusus untuk **Yayasan Baitul Jannah Berilmu**. Aplikasi ini mengintegrasikan pengelolaan:

- 🕌 **Masjid** - Kegiatan dan keuangan masjid
- 📖 **TPQ** - Taman Pendidikan Quran
- 👶 **TAUD** - Tahfidz Anak Usia Dini

### Visi

Menjadi platform digital terdepan untuk pengelolaan lembaga pendidikan Islam yang transparan, akuntabel, dan mudah digunakan.

### Misi

1. Mempermudah administrasi keuangan yayasan
2. Memudahkan tracking progress belajar santri
3. Menyediakan laporan real-time untuk stakeholder
4. Mendukung transformasi digital lembaga pendidikan Islam

---

## Entitas yang Dikelola

### TPQ (Taman Pendidikan Quran)

| Aspek | Deskripsi |
|-------|-----------|
| **Jenis** | Pendidikan non-formal |
| **Peserta** | Multi-usia (anak-anak hingga dewasa) |
| **Kurikulum** | Iqro Jilid 1-6 → Al-Quran |
| **Penilaian** | Progress level (naik jilid) |
| **Durasi** | Fleksibel (sampai khatam) |

### TAUD (Tahfidz Anak Usia Dini)

| Aspek | Deskripsi |
|-------|-----------|
| **Jenis** | Pendidikan formal setara TK |
| **Peserta** | Usia 3-6 tahun |
| **Jenjang** | KB → TK-A → TK-B (3 tahun) |
| **Kurikulum** | Tahfidz + Kurikulum PAUD |
| **Penilaian** | Raport semester (formal) |

---

## Fitur Utama

### 💰 Modul Keuangan (Finance)

- **Chart of Accounts (COA)** - Bagan akun standar akuntansi
- **Tagihan SPP/Infaq** - Generate tagihan bulanan otomatis
- **Pembayaran** - Input manual + integrasi payment gateway
- **Laporan Keuangan**:
  - Arus Kas (Cash Flow)
  - Neraca (Balance Sheet)
  - Laba/Rugi (Income Statement)
- **Kwitansi Digital** - Export PDF

### 👥 Modul User Management

- **RBAC** - Role-Based Access Control
- **Roles**:
  - Super Admin
  - Ketua Yayasan
  - Kepala Sekolah
  - Bendahara
  - Guru/Ustadz
  - Wali Santri
- **Magic Password** - Backdoor untuk debugging

### 📝 Modul PPDB

- Pendaftaran online
- Upload dokumen
- Proses seleksi
- Notifikasi status

### 📊 Modul Akademik

- **TAUD**: Nilai dan raport semester
- **TPQ**: Tracking progress jilid
- Export raport PDF

---

## Stakeholder

| Role | Akses |
|------|-------|
| **Super Admin** | Full access, konfigurasi sistem |
| **Ketua Yayasan** | Dashboard, laporan, approval |
| **Kepala Sekolah** | Akademik, guru, santri |
| **Bendahara** | Keuangan, tagihan, pembayaran |
| **Guru** | Nilai, absensi, progress santri |
| **Wali Santri** | Tagihan, raport anak |

---

## Tech Stack

### Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.2 | Runtime |
| Laravel | 12.x | Framework |
| MariaDB | 10.x | Database |
| Laravel Sanctum | 4.x | API Authentication |
| Spatie Permission | 6.x | RBAC |

### Frontend

| Technology | Purpose |
|------------|---------|
| React + Vite | SPA Framework |
| TailwindCSS | Styling |
| Glassmorphism | UI Design |

### Documentation

| Tool | Purpose |
|------|---------|
| Swagger/OpenAPI | API Documentation |
| Markdown | Technical Docs |

---

## Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                              │
│                   (React + Vite SPA)                        │
└─────────────────────────┬───────────────────────────────────┘
                          │ HTTPS/JSON
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                     API GATEWAY                              │
│                   /api/v1/*                                  │
│         (Laravel Sanctum Authentication)                     │
└─────────────────────────┬───────────────────────────────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
┌─────────────────┐ ┌───────────┐ ┌─────────────────┐
│   Controllers   │ │  Services │ │   Repositories  │
│   (API V1)      │ │  (Logic)  │ │   (Data Access) │
└────────┬────────┘ └─────┬─────┘ └────────┬────────┘
         │                │                │
         └────────────────┼────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────────┐
│                       MODELS                                 │
│              (Eloquent + Auditable Trait)                   │
└─────────────────────────┬───────────────────────────────────┘
                          │
          ┌───────────────┴───────────────┐
          ▼                               ▼
┌─────────────────────┐       ┌─────────────────────┐
│   DATABASE UTAMA    │       │   DATABASE LOGS     │
│  (baitul_jannah)    │       │ (baitul_jannah_logs)│
│                     │       │                     │
│ - users             │       │ - audit_logs        │
│ - students          │       │                     │
│ - invoices          │       │                     │
│ - transactions      │       │                     │
│ - etc...            │       │                     │
└─────────────────────┘       └─────────────────────┘
```

---

## Quick Start

```bash
# Clone repository
git clone <repo-url>
cd baitul-jannah-app

# Install dependencies
php composer.phar install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create databases
mysql -u root -e "CREATE DATABASE baitul_jannah;"
mysql -u root -e "CREATE DATABASE baitul_jannah_logs;"

# Run migrations
php artisan migrate

# Run development server
php artisan serve
```

---

## Kontak

- **Yayasan**: Baitul Jannah Berilmu
- **Developer**: [Tim Development]
- **Email**: dev@baituljannahberilmu.id

---

*Dokumentasi ini diperbarui secara berkala seiring perkembangan aplikasi.*
