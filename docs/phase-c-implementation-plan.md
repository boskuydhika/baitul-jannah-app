# Phase C: Transaction UI - CRUD Transaksi Keuangan

## 🎯 Goal

UI untuk mengelola transaksi keuangan (jurnal umum) dengan pendekatan **API-first**.

---

## 📋 User Review Required

> [!IMPORTANT]
> **API-First Approach**: Web Controller hanya thin layer yang merender Inertia page dan meneruskan data dari API. Logic tetap di API Controller.

> [!NOTE]
> **Format Tanggal**: Selalu `dddd, yyyy-mm-dd` dengan hari bahasa Indonesia.  
> Contoh: **Jumat, 2025-12-27**  
> Jika ada waktu: format 24-jam (hh:mm:ss)

---

## 📁 Proposed Changes

### Backend: Web Controller (Thin Layer)

#### [NEW] TransactionController.php
`app/Http/Controllers/Web/Finance/TransactionController.php`

```php
// Konsep: Web controller hanya menampilkan page, data dari API
class TransactionController {
    // GET /finance/transactions
    public function index() {
        // Panggil API internal untuk get data
        $transactions = Transaction::with('details.account', 'creator')
            ->latest('transaction_date')
            ->paginate(15);
        
        return Inertia::render('Finance/Transactions/Index', [
            'transactions' => $transactions,
            'accounts' => Account::active()->get(), // untuk dropdown
        ]);
    }
    
    // GET /finance/transactions/create
    public function create() {
        return Inertia::render('Finance/Transactions/Create', [
            'accounts' => Account::active()->get(),
            'transactionTypes' => ['journal', 'payment', 'receipt', 'expense'],
        ]);
    }
    
    // POST /finance/transactions → redirect ke API
    public function store(Request $request) {
        // Delegate ke API controller
        $response = app(ApiTransactionController::class)->store($request);
        
        if ($response->status() === 201) {
            return redirect()->route('finance.transactions.index')
                ->with('success', 'Transaksi berhasil dibuat');
        }
        return back()->withErrors($response->getData()->errors);
    }
}
```

> [!TIP]
> **Best Practice**: Web controller TIDAK duplikasi logic dari API. Gunakan API controller sebagai single source of truth.

---

### Backend: Routes

#### [MODIFY] routes/web.php

```php
Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    
    // Transactions (Web UI) - calls API internally
    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'create', 'store', 'show']);
    Route::post('transactions/{transaction}/post', [TransactionController::class, 'post'])
        ->name('transactions.post');
    Route::post('transactions/{transaction}/void', [TransactionController::class, 'void'])
        ->name('transactions.void');
});
```

---

### Frontend: Pages

#### [NEW] Finance/Transactions/Index.tsx

| Fitur | Desktop | Mobile |
|-------|---------|--------|
| List | Table dengan sortable columns | Cards |
| Filter | Status, Tipe, Tanggal | Collapsible filter |
| Actions | View, Post, Void buttons | Swipe actions |

#### [NEW] Finance/Transactions/Create.tsx

| Field | Format | Validation |
|-------|--------|------------|
| Tanggal | dddd, yyyy-mm-dd (Indonesia) | Required |
| Tipe | Dropdown | Required |
| Deskripsi | Textarea | Required |
| Line Items | Dynamic rows | Min 2 rows, balance check |

**Balance Indicator:**
```
Total Debit:  Rp 1.000.000
Total Credit: Rp 1.000.000
Status: ✅ Balance (atau ❌ Tidak Balance)
```

#### [NEW] Finance/Transactions/Show.tsx

Detail view dengan header, line items table, dan action buttons.

---

### Frontend: Utils

#### [NEW] lib/date-utils.ts

```typescript
// Format tanggal dengan hari bahasa Indonesia
export function formatDateIndo(date: string | Date): string {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const d = new Date(date);
    const dayName = days[d.getDay()];
    const formatted = d.toISOString().split('T')[0]; // yyyy-mm-dd
    return `${dayName}, ${formatted}`;
}
// Output: "Jumat, 2025-12-27"
```

---

## ✅ Verification Plan

Manual testing setelah implementasi:
1. List transaksi dengan filter
2. Create transaksi dengan balance check
3. Post dan Void transaksi
4. Dark mode consistency
5. Mobile responsiveness
