# Redesign: Buku Kas Sederhana

## 🎯 Goal
Sistem buku kas sederhana untuk yayasan/sekolah informal.

---

## 📱 UI Specs

### Index Page
```
┌─────────────────────────────────────────────────┐
│  💰 SALDO SAAT INI: Rp 12.500.000              │  ← Sticky top
└─────────────────────────────────────────────────┘

| No | Tanggal & Waktu | Kategori | Uraian | Masuk | Keluar |
```
- **Saldo**: Ditampilkan terpisah di atas (sticky)
- **Uraian**: Truncated dengan tombol "Selengkapnya"
- **Tanggal**: Format `Jumat, 2025-12-27 14:30:00`

### Create Page
- **Tanggal Real Berbeda?**: Toggle checkbox
  - Off: Gunakan waktu sekarang
  - On: Input datetime picker (backdate)
- **Tipe**: Radio [Pemasukan / Pengeluaran]
- **Kategori**: **Dropdown with Search** (filtered by tipe)
- **Jumlah**: **Input dengan separator Indonesia** (`10.000.000`)
- **Uraian**: Textarea multiline

### Category Management (CRUD)
- Route: `/finance/categories`
- Permission: `super-admin`, `finance`
- Fields: nama, tipe (income/expense), is_active

---

## 📁 Database

### `transaction_categories`
```sql
id, name, type ENUM('income','expense'), is_active, created_at
```

### `transactions` (Simplified)
```sql
id, transaction_number, transaction_datetime,
category_id, type, description, amount,
created_by, created_at
```

---

## ✅ Tasks

### Database & Models
- [ ] Migration: `transaction_categories`
- [ ] Migration: Modify `transactions`
- [ ] Model: `TransactionCategory`
- [ ] Seeder: Default categories

### Backend Controllers
- [ ] `TransactionCategoryController` (API + Web)
- [ ] Update `TransactionController`

### Frontend - Transactions
- [ ] Rewrite `Index.tsx` (saldo sticky, table sederhana)
- [ ] Rewrite `Create.tsx` (form sederhana + backdate)
- [ ] Rewrite `Show.tsx` (detail view)

### Frontend - Categories
- [ ] Create `Categories/Index.tsx` (CRUD)

### Components
- [ ] Searchable dropdown
- [ ] Number input dengan separator Indonesia
- [ ] Collapsible text (Uraian)
