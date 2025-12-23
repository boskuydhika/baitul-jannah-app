# 🔧 Troubleshooting Guide

> Panduan mengatasi masalah umum pada Baitul Jannah Super App

---

## 📋 Daftar Isi

- [Environment Issues](#environment-issues)
- [Database Issues](#database-issues)
- [Authentication Issues](#authentication-issues)
- [API Issues](#api-issues)
- [Performance Issues](#performance-issues)

---

## Environment Issues

### PHP Not Found

**Gejala:**
```
zsh: command not found: php
```

**Solusi:**
```bash
# Ubuntu/Debian
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath
```

---

### Composer Not Found

**Gejala:**
```
zsh: command not found: composer
```

**Solusi:**
Gunakan composer.phar lokal yang sudah ada di root project:
```bash
php composer.phar install
php composer.phar update
```

---

### Permission Denied pada Storage

**Gejala:**
```
The stream or file "storage/logs/laravel.log" could not be opened
```

**Solusi:**
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

---

## Database Issues

### MySQL/MariaDB Not Found

**Gejala:**
```
zsh: command not found: mysql
```

**Penyebab:**
MariaDB/MySQL belum terinstall di sistem.

**Solusi:**
```bash
# Ubuntu/Debian - Install MariaDB
sudo apt update
sudo apt install -y mariadb-server mariadb-client

# Start service
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Secure installation (opsional, recommended untuk production)
sudo mysql_secure_installation
```

**Verifikasi:**
```bash
# Cek versi
mysql --version
# Output: mysql  Ver 15.1 Distrib 10.x.x-MariaDB

# Test koneksi
sudo mysql -u root
# Jika berhasil, akan masuk ke MariaDB prompt
# Ketik 'exit' untuk keluar
```

**Buat Database:**
```bash
# Login ke MariaDB
sudo mysql -u root

# Di dalam MariaDB prompt:
CREATE DATABASE baitul_jannah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE baitul_jannah_logs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Catatan:** Di Ubuntu/Debian, MariaDB menggunakan `sudo mysql` tanpa password untuk root user secara default.

---

### Access Denied for 'root'@'localhost' (Error 1698)

**Gejala:**
```
SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'
```

**Penyebab:**
MariaDB di Ubuntu menggunakan `unix_socket` authentication untuk root user. Artinya hanya bisa login via `sudo mysql`, bukan dengan password biasa yang dipakai Laravel.

**Solusi - Buat User Khusus untuk Laravel:**

```bash
# 1. Login ke MariaDB sebagai root
sudo mysql -u root

# 2. Buat user baru dengan password (di dalam MariaDB prompt)
CREATE USER 'baitul_admin'@'localhost' IDENTIFIED BY 'password123';

# 3. Berikan akses ke database
GRANT ALL PRIVILEGES ON baitul_jannah.* TO 'baitul_admin'@'localhost';
GRANT ALL PRIVILEGES ON baitul_jannah_logs.* TO 'baitul_admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Update .env:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=baitul_jannah
DB_USERNAME=baitul_admin
DB_PASSWORD=password123

DB_LOGS_DATABASE=baitul_jannah_logs
DB_LOGS_USERNAME=baitul_admin
DB_LOGS_PASSWORD=password123
```

**Jalankan Ulang:**
```bash
php artisan config:clear
php artisan migrate
php artisan db:seed
```

---

### SQLSTATE Connection Refused

**Gejala:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solusi:**
1. Pastikan MariaDB/MySQL berjalan:
```bash
sudo systemctl start mariadb
# atau
sudo systemctl start mysql
```

2. Cek konfigurasi di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=baitul_jannah
DB_USERNAME=root
DB_PASSWORD=
```

---

### Database Does Not Exist

**Gejala:**
```
SQLSTATE[HY000] [1049] Unknown database 'baitul_jannah'
```

**Solusi:**
```bash
# Buat database utama
mysql -u root -p -e "CREATE DATABASE baitul_jannah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Buat database logs
mysql -u root -p -e "CREATE DATABASE baitul_jannah_logs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan migration
php artisan migrate
```

---

### Migration Failed - Table Already Exists

**Gejala:**
```
SQLSTATE[42S01]: Base table or view already exists
```

**Solusi:**
```bash
# Reset semua migration (HATI-HATI: menghapus semua data!)
php artisan migrate:fresh

# Atau rollback lalu migrate ulang
php artisan migrate:rollback
php artisan migrate
```

---

## Authentication Issues

### Token Invalid atau Expired

**Gejala:**
```json
{
    "message": "Unauthenticated."
}
```

**Solusi:**
1. Login ulang untuk mendapatkan token baru
2. Pastikan token dikirim dengan format:
```
Authorization: Bearer {your-token}
```

---

### Rate Limited (Too Many Attempts)

**Gejala:**
```json
{
    "success": false,
    "message": "Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit."
}
```

**Solusi:**
1. Tunggu 15 menit
2. Atau clear rate limiter (development only):
```bash
php artisan cache:clear
```

---

### Magic Password Tidak Berfungsi

**Gejala:**
Login dengan master password gagal.

**Solusi:**
1. Pastikan `MASTER_PASSWORD` di-set di `.env`:
```env
MASTER_PASSWORD="Rahasia=123"
```

2. Clear config cache:
```bash
php artisan config:clear
```

3. Cek apakah user exists dengan phone number tersebut

---

## API Issues

### 404 Not Found pada API

**Gejala:**
```json
{
    "message": "Not Found"
}
```

**Solusi:**
1. Pastikan prefix URL benar: `/api/v1/...`
2. Cek route tersedia:
```bash
php artisan route:list --name=api
```

---

### CORS Error

**Gejala:**
```
Access to XMLHttpRequest has been blocked by CORS policy
```

**Solusi:**
Edit `config/cors.php`:
```php
'allowed_origins' => ['http://localhost:3000', 'http://localhost:5173'],
'supports_credentials' => true,
```

Lalu clear config:
```bash
php artisan config:clear
```

---

### Validation Error Format

**Gejala:**
Response error tidak sesuai format yang diharapkan.

**Solusi:**
Pastikan request menggunakan header:
```
Accept: application/json
Content-Type: application/json
```

---

## Performance Issues

### Slow Response Time

**Solusi:**
1. Enable query caching:
```bash
php artisan config:cache
php artisan route:cache
```

2. Check slow queries:
```bash
php artisan telescope:install  # Install Telescope
php artisan migrate
```

---

### Memory Limit Exceeded

**Gejala:**
```
Allowed memory size of X bytes exhausted
```

**Solusi:**
Edit `php.ini`:
```ini
memory_limit = 512M
```

Atau set di `.htaccess`:
```
php_value memory_limit 512M
```

---

## Logging & Debugging

### Melihat Log Aplikasi

```bash
# Tail log file
tail -f storage/logs/laravel.log

# Atau gunakan Laravel Pail
php artisan pail
```

### Debug Mode

Di `.env`:
```env
APP_DEBUG=true
APP_ENV=local
```

> ⚠️ **JANGAN** aktifkan debug mode di production!

---

### Melihat Audit Log

```bash
# Via Tinker
php artisan tinker

>>> App\Models\Logs\AuditLog::latest()->take(10)->get()
```

---

## Reset Everything (Development)

Jika ingin reset semua ke kondisi awal:

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reset database
php artisan migrate:fresh --seed

# Regenerate key
php artisan key:generate
```

---

## Kontak Support

Jika masalah tidak teratasi:

1. Cek dokumentasi: `docs/`
2. Cek API reference: `docs/API_REFERENCE.md`
3. Hubungi tim development

---

*Dokumen ini diupdate berdasarkan issues yang ditemukan.*
