# 🕌 Baitul Jannah Super App

> Sistem Manajemen Terpadu untuk Yayasan Baitul Jannah Berilmu

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-19-61DAFB?style=flat&logo=react&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=flat&logo=typescript&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-38B2AC?style=flat&logo=tailwind-css&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)

---

## 📋 Tentang Aplikasi

**Baitul Jannah Super App** adalah aplikasi manajemen berbasis web untuk **Yayasan Baitul Jannah Berilmu** yang mengintegrasikan:

- 🕌 **Masjid** - Kegiatan dan keuangan masjid
- 📖 **TPQ** - Taman Pendidikan Quran (pagi & sore)
- 👶 **TAUD** - Tahfidz Anak Usia Dini

---

## ✨ Fitur Utama

### 🏛️ Keuangan (Buku Kas)
- ✅ Pencatatan pemasukan & pengeluaran
- ✅ Kategori transaksi terstruktur
- ✅ Status draft/completed
- ✅ Filter berdasarkan tanggal, kategori, status
- ✅ Export laporan (coming soon)

### 👥 Manajemen Santri
- ✅ Data santri TPQ & TAUD
- ✅ NIS otomatis (TPQA/TPQB/TAUD + tahun + urutan)
- ✅ Data orangtua terpisah (Ayah & Ibu)
- ✅ Tracking usia real-time
- ✅ Pencarian berdasarkan nama & nama panggilan
- ✅ WhatsApp quick action untuk orangtua

### 🎨 UI/UX Modern
- ✅ Dark mode & light mode
- ✅ Glassmorphism design
- ✅ Mobile-first responsive
- ✅ Indonesian locale (date, currency)
- ✅ Custom DatePicker (YYYY-MM-DD format)

### 🔐 Multi-Role Access
- Super Admin
- Ketua Yayasan
- Kepala Sekolah
- Bendahara
- Guru/Ustadz

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 12, PHP 8.2+ |
| **Frontend** | React 19, TypeScript, Inertia.js |
| **Styling** | TailwindCSS 4, Shadcn/UI |
| **Database** | MariaDB 10.6+ |
| **Auth** | Laravel Sanctum, Spatie Permission |
| **Build** | Vite |

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MariaDB/MySQL 10.6+

### Installation

```bash
# Clone repository
git clone <repo-url>
cd baitul-jannah-app

# Install PHP dependencies
php composer.phar install

# Install Node dependencies
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_DATABASE=baitul_jannah
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations & seeders
php artisan migrate:fresh --seed

# Start development servers
php artisan serve &
npm run dev
```

### Default Login
| Role | Phone | Password |
|------|-------|----------|
| Super Admin | 08123456789 | admin123 |
| Bendahara | 08123456003 | bendahara123 |

> 🔑 **Master Password:** `Rahasia=123` (dapat login ke akun manapun)

---

## 📁 Project Structure

```
baitul-jannah-app/
├── app/
│   ├── Http/Controllers/Web/    # Web controllers (Inertia)
│   └── Models/                  # Eloquent models
├── database/
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Data seeders
├── resources/js/
│   ├── Components/              # React components (Shadcn UI)
│   ├── Pages/                   # Inertia pages
│   └── hooks/                   # Custom React hooks
├── docs/                        # Documentation
└── routes/
    └── web.php                  # Web routes
```

---

## 📚 Documentation

Dokumentasi lengkap tersedia di folder `/docs`:

| File | Description |
|------|-------------|
| [product-overview.md](docs/product-overview.md) | Gambaran umum produk |
| [frontend-architecture.md](docs/frontend-architecture.md) | Arsitektur frontend |
| [CHANGELOG.md](docs/CHANGELOG.md) | Log perubahan |
| [deployment.md](docs/deployment.md) | Panduan deployment |
| [troubleshooting.md](docs/troubleshooting.md) | Troubleshooting guide |
| [API_REFERENCE.md](docs/API_REFERENCE.md) | Referensi API |

---

## 🎯 Roadmap

- [x] **Phase A** - Backend Foundation (Auth, RBAC, DB)
- [x] **Phase B** - Frontend Foundation (React, Dark Mode)
- [x] **Phase C** - Buku Kas (Keuangan)
- [ ] **Phase D** - Manajemen Santri & SPP
- [ ] **Phase E** - PPDB Online
- [ ] **Phase F** - Akademik & Raport

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

Proprietary - Yayasan Baitul Jannah Berilmu

---

## 📞 Contact

- **Yayasan:** Baitul Jannah Berilmu
- **Developer:** Dhika
- **AI Assistant:** Gemini/Antigravity

---

*Built with ❤️ for Islamic education management*
