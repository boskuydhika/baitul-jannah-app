# 📚 API Reference - Baitul Jannah Super App

> Dokumentasi lengkap semua endpoint API

**Base URL:** `http://localhost:8000/api/v1`

**Authentication:** Bearer Token (Laravel Sanctum)

---

## 📋 Daftar Isi

- [Authentication](#authentication)
- [Response Format](#response-format)
- [Error Codes](#error-codes)
- [Endpoints](#endpoints)

---

## Response Format

### Success Response

```json
{
    "success": true,
    "message": "Operasi berhasil",
    "data": { ... }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Deskripsi error",
    "errors": {
        "field": ["Pesan validasi"]
    }
}
```

### Paginated Response

```json
{
    "success": true,
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150
    }
}
```

---

## Error Codes

| HTTP Code | Meaning |
|-----------|---------|
| 200 | OK - Request berhasil |
| 201 | Created - Resource berhasil dibuat |
| 400 | Bad Request - Request tidak valid |
| 401 | Unauthorized - Token tidak valid/expired |
| 403 | Forbidden - Tidak punya akses |
| 404 | Not Found - Resource tidak ditemukan |
| 422 | Validation Error - Data tidak valid |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Server Error - Kesalahan server |

---

## Endpoints

### Authentication

#### POST /auth/login

Login dengan nomor HP dan password.

**Request Body:**

```json
{
    "phone": "081234567890",
    "password": "password123"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Ahmad",
            "phone": "081234567890",
            "email": "ahmad@example.com",
            "roles": ["bendahara"]
        },
        "token": "1|abc123xyz..."
    }
}
```

**Error Response (401):**

```json
{
    "success": false,
    "message": "Nomor HP atau password salah"
}
```

**Error Response (429) - Rate Limited:**

```json
{
    "success": false,
    "message": "Terlalu banyak percobaan login. Silakan coba lagi dalam 15 menit."
}
```

---

#### POST /auth/logout

Logout dan revoke current token.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

---

#### GET /auth/me

Mendapatkan info user yang sedang login.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Ahmad",
        "phone": "081234567890",
        "email": "ahmad@example.com",
        "roles": ["bendahara"],
        "permissions": ["finance.view", "finance.create"]
    }
}
```

---

### Users (Coming Soon)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /users | List semua user |
| POST | /users | Buat user baru |
| GET | /users/{id} | Detail user |
| PUT | /users/{id} | Update user |
| DELETE | /users/{id} | Hapus user |

---

### Finance (Coming Soon)

#### Accounts (COA)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /finance/accounts | List COA |
| POST | /finance/accounts | Buat akun baru |
| GET | /finance/accounts/{id} | Detail akun |
| PUT | /finance/accounts/{id} | Update akun |
| DELETE | /finance/accounts/{id} | Hapus akun |

#### Transactions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /finance/transactions | List transaksi |
| POST | /finance/transactions | Buat transaksi |
| GET | /finance/transactions/{id} | Detail transaksi |

#### Invoices

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /finance/invoices | List tagihan |
| POST | /finance/invoices | Buat tagihan |
| POST | /finance/invoices/generate | Generate tagihan bulanan |

#### Payments

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /finance/payments | List pembayaran |
| POST | /finance/payments | Input pembayaran |

#### Reports

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /finance/reports/cash-flow | Laporan arus kas |
| GET | /finance/reports/balance-sheet | Neraca |
| GET | /finance/reports/income-statement | Laba rugi |

---

### Academic (Coming Soon)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /academic/students | List santri |
| GET | /academic/classes | List kelas |
| GET | /academic/records | List nilai |
| GET | /academic/tpq-progress | Progress TPQ |

---

### PPDB (Coming Soon)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /ppdb/registrations | Daftar baru |
| GET | /ppdb/registrations/{id}/status | Cek status |

---

## Health Check

#### GET /health

Cek status API server.

**Response:**

```json
{
    "status": "ok",
    "timestamp": "2024-01-15T10:30:00+07:00",
    "app": "Baitul Jannah Super App",
    "version": "1.0.0"
}
```

---

## Rate Limiting

- **Login endpoint**: 5 attempts per 15 minutes per IP
- **Other endpoints**: 60 requests per minute per user

---

## Swagger Documentation

Interactive API documentation tersedia di:

```
GET /api/documentation
```

---

*Dokumentasi ini diupdate seiring perkembangan API.*
