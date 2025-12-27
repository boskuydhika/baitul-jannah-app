# Fitur Pembayaran Infaq Bulanan (SPP) - Implementation Plan

## Overview
Fitur untuk Bendahara mengelola pembayaran infaq bulanan (SPP) santri TPQ dan TAUD. Bendahara dapat mencari santri, melihat status tagihan per bulan, dan mencatat pembayaran.

## Requirements (Confirmed)
- **Nominal SPP**: Dinamis per santri (ditentukan saat registrasi)
- **Tahun Ajaran**: Juli - Juni (contoh: 2025/2026)
- **Student CRUD**: Form lengkap dengan field iuran bulanan

---

## Proposed Changes

### Database Schema (DONE ✅)

#### `students` table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| nis | varchar(20) | Nomor Induk Santri (TPQ25001) |
| name | varchar(100) | Nama santri |
| type | enum('tpq','taud') | Jenis program |
| gender | enum('L','P') | |
| birth_date | date | |
| parent_name | varchar(100) | |
| parent_phone | varchar(20) | |
| academic_year | varchar(9) | Format: 2025/2026 |
| entry_date | date | Tanggal masuk |
| **monthly_fee** | decimal(12,2) | **Iuran SPP bulanan** |
| is_active | boolean | |

#### `student_payments` table (TODO)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| student_id | FK | |
| month | date | YYYY-MM-01 |
| amount_due | decimal | Total tagihan |
| amount_paid | decimal | Sudah dibayar |
| status | enum | unpaid/partial/paid |
| transaction_id | FK nullable | Link ke Buku Kas |

---

## Progress

### ✅ Completed
- [x] Cleanup file COA (Account model, controller, migration, seeder)
- [x] Cleanup routes (web.php, api.php)
- [x] Create `students` table migration
- [x] Simplified `Student` model
- [x] Create `StudentSeeder` (5 dummy students)
- [x] Update `DatabaseSeeder`

### 📋 Next Steps
1. [ ] Create Student CRUD pages (Index, Create, Edit)
2. [ ] Create `student_payments` table migration
3. [ ] Create `StudentPayment` model
4. [ ] Create Payment recording UI
5. [ ] Generate monthly bills automatically

---

## UI Flow - Pembayaran

```
┌─────────────────────────────────────────────────┐
│ 💳 Pembayaran Infaq Bulanan                     │
├─────────────────────────────────────────────────┤
│ 🔍 Cari Santri: [Ahmad - TPQ        ▼]         │
├─────────────────────────────────────────────────┤
│ Tahun Ajaran: 2025/2026                         │
│ ┌────────┬────────┬────────┬────────┐          │
│ │ Jul ✅ │ Agt ✅ │ Sep ⚠️ │ Okt ❌ │          │
│ │ Lunas  │ Lunas  │ Parsial│ Belum  │          │
│ └────────┴────────┴────────┴────────┘          │
│                                                 │
│ ▶ Bayar Bulan: September 2025                  │
│   SPP Bulan ini: Rp 50.000                     │
│   Terbayar: Rp 25.000                          │
│   Kurang: Rp 25.000                            │
│                                                 │
│   Nominal Bayar: [Rp _______________]          │
│   [💰 Lunaskan Rp 25.000] [💵 Bayar Sebagian]  │
└─────────────────────────────────────────────────┘
```

## Verification Plan

### Manual Testing
1. ✅ `php artisan migrate:fresh --seed` berjalan tanpa error
2. [ ] CRUD santri berfungsi
3. [ ] Pembayaran tercatat dengan benar
4. [ ] Status (unpaid/partial/paid) sesuai
