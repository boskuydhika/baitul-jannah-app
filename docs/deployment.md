# 🚀 Deployment Guide

> Panduan deployment Baitul Jannah Super App

---

## 📋 Daftar Isi

- [Requirements](#requirements)
- [Shared Hosting Deployment](#shared-hosting-deployment)
- [VPS Deployment](#vps-deployment)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Post-Deployment Checklist](#post-deployment-checklist)

---

## Requirements

### Server Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP | 8.2 | 8.3 |
| MySQL/MariaDB | 8.0 / 10.6 | 8.0 / 11.x |
| RAM | 512 MB | 2 GB |
| Storage | 1 GB | 10 GB |
| Node.js | 18.x | 20.x |

### PHP Extensions

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PDO_MySQL
- Tokenizer
- XML
- Zip
```

---

## Shared Hosting Deployment

### Step 1: Upload Files

```bash
# Compress project (exclude vendor dan node_modules)
zip -r baitul-jannah.zip . -x "vendor/*" -x "node_modules/*" -x ".git/*"

# Upload via FTP/SFTP ke folder private (di luar public_html)
```

### Step 2: Install Dependencies

```bash
# SSH ke server
ssh user@your-server.com

# Masuk ke folder project
cd ~/baitul-jannah-app

# Install PHP dependencies
php composer.phar install --optimize-autoloader --no-dev

# Install Node dependencies dan build
npm install
npm run build
```

### Step 3: Setup Public Directory

```bash
# Pindahkan isi folder public ke public_html
cp -r public/* ~/public_html/

# Edit index.php di public_html
nano ~/public_html/index.php
```

Edit path di `index.php`:
```php
// Sebelum
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Sesudah
require __DIR__.'/../baitul-jannah-app/vendor/autoload.php';
$app = require_once __DIR__.'/../baitul-jannah-app/bootstrap/app.php';
```

### Step 4: Setup Environment

```bash
cp .env.example .env
nano .env
```

Edit sesuai konfigurasi hosting:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### Step 5: Generate Key & Migrate

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force  # Jika ada seeder
php artisan storage:link
```

### Step 6: Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## VPS Deployment

### Step 1: Setup Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-zip php8.2-curl php8.2-gd

# Install Nginx
sudo apt install -y nginx

# Install MariaDB
sudo apt install -y mariadb-server
sudo mysql_secure_installation

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Step 2: Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/baitul-jannah
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/baitul-jannah/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/baitul-jannah /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 3: Deploy Code

```bash
# Clone repository
cd /var/www
sudo git clone <repo-url> baitul-jannah
sudo chown -R www-data:www-data baitul-jannah

# Install dependencies
cd baitul-jannah
php composer.phar install --optimize-autoloader --no-dev
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate
```

### Step 4: SSL dengan Certbot

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## Environment Configuration

### Production .env

```env
# Application
APP_NAME="Baitul Jannah Super App"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxx
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Locale
APP_LOCALE=id
APP_TIMEZONE=Asia/Jakarta

# Database Utama
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=baitul_jannah
DB_USERNAME=baitul_user
DB_PASSWORD=strong_password_here

# Database Logs
DB_LOGS_DATABASE=baitul_jannah_logs
DB_LOGS_USERNAME=baitul_user
DB_LOGS_PASSWORD=strong_password_here

# Magic Password (HAPUS di production jika tidak diperlukan)
# MASTER_PASSWORD=

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Gunakan service seperti Mailgun, SES, dll)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Database Setup

### Create Databases

```sql
-- Login ke MySQL/MariaDB
mysql -u root -p

-- Buat database utama
CREATE DATABASE baitul_jannah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat database logs
CREATE DATABASE baitul_jannah_logs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Buat user
CREATE USER 'baitul_user'@'localhost' IDENTIFIED BY 'strong_password_here';

-- Grant permissions
GRANT ALL PRIVILEGES ON baitul_jannah.* TO 'baitul_user'@'localhost';
GRANT ALL PRIVILEGES ON baitul_jannah_logs.* TO 'baitul_user'@'localhost';
FLUSH PRIVILEGES;
```

### Run Migrations

```bash
php artisan migrate --force
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=AdminSeeder --force
```

---

## Post-Deployment Checklist

### Security

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS enabled (SSL certificate)
- [ ] `.env` file secured (chmod 600)
- [ ] `MASTER_PASSWORD` disabled atau diganti password kuat
- [ ] Database password kuat
- [ ] Firewall configured

### Performance

- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] OPcache enabled
- [ ] Redis untuk cache/session

### Monitoring

- [ ] Error logging configured
- [ ] Health check endpoint working
- [ ] Backup database scheduled
- [ ] Uptime monitoring active

### Backup

```bash
# Backup database (cron daily)
0 2 * * * mysqldump -u baitul_user -p'password' baitul_jannah > /backups/db_$(date +\%Y\%m\%d).sql

# Backup logs database (cron weekly)
0 3 * * 0 mysqldump -u baitul_user -p'password' baitul_jannah_logs > /backups/logs_$(date +\%Y\%m\%d).sql
```

---

## Troubleshooting Deployment

Lihat `docs/troubleshooting.md` untuk solusi masalah umum.

---

*Dokumen ini diupdate sesuai best practices terbaru.*
