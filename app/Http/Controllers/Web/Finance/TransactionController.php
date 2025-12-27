<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Web Controller untuk Buku Kas (Transaksi Sederhana).
 */
class TransactionController extends Controller
{
    /**
     * List semua transaksi dengan saldo.
     */
    public function index(Request $request): Response
    {
        $query = Transaction::with(['category', 'creator'])
            ->latest('transaction_datetime');

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_datetime', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_datetime', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        return Inertia::render('Finance/Transactions/Index', [
            'transactions' => $transactions,
            'currentBalance' => Transaction::getCurrentBalance(),
            'draftCount' => Transaction::getDraftCount(),
            'categories' => TransactionCategory::active()->ordered()->get(),
            'filters' => [
                'type' => $request->type ?? 'all',
                'category_id' => $request->category_id,
                'status' => $request->status ?? 'all',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Form create transaksi baru.
     */
    public function create(): Response
    {
        return Inertia::render('Finance/Transactions/Create', [
            'categories' => TransactionCategory::active()->ordered()->get(),
            'nextNumber' => Transaction::generateNumber(),
        ]);
    }

    /**
     * Simpan transaksi baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:transaction_categories,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:1000',
            'is_backdate' => 'boolean',
            'transaction_datetime' => 'required_if:is_backdate,true|nullable|date',
            'status' => 'in:draft,posted',
        ]);

        $transaction = Transaction::create([
            'transaction_number' => Transaction::generateNumber(),
            'transaction_datetime' => $validated['is_backdate'] ?? false
                ? $validated['transaction_datetime']
                : now(),
            'type' => $validated['type'],
            'category_id' => $validated['category_id'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'status' => $validated['status'] ?? 'posted', // Default posted
            'created_by' => auth()->id(),
        ]);

        $statusMsg = $transaction->status === 'draft' ? ' sebagai draft' : '';

        return redirect()->route('finance.transactions.index')
            ->with('success', "Transaksi berhasil dicatat{$statusMsg}");
    }

    /**
     * Detail transaksi.
     */
    public function show(Transaction $transaction): Response
    {
        $transaction->load(['category', 'creator']);

        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');

        return Inertia::render('Finance/Transactions/Show', [
            'transaction' => $transaction,
            'canEdit' => $isSuperAdmin, // Super admin can edit posted transactions
        ]);
    }

    /**
     * Form edit transaksi (draft atau posted untuk super admin).
     */
    public function edit(Transaction $transaction): Response|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');

        // Non-super admin hanya bisa edit draft
        if ($transaction->status !== 'draft' && !$isSuperAdmin) {
            return redirect()->route('finance.transactions.show', $transaction)
                ->withErrors(['message' => 'Hanya transaksi draft yang bisa diedit']);
        }

        $transaction->load('category');

        return Inertia::render('Finance/Transactions/Edit', [
            'transaction' => $transaction,
            'categories' => TransactionCategory::active()->ordered()->get(),
            'canEdit' => true,
        ]);
    }

    /**
     * Update transaksi (draft atau posted untuk super admin).
     */
    public function update(Request $request, Transaction $transaction)
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');

        // Non-super admin hanya bisa update draft
        if ($transaction->status !== 'draft' && !$isSuperAdmin) {
            return back()->withErrors(['message' => 'Hanya transaksi draft yang bisa diedit']);
        }

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:transaction_categories,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:1000',
            'transaction_datetime' => 'required|date',
        ]);

        $transaction->update($validated);

        return redirect()->route('finance.transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil diperbarui');
    }

    /**
     * Post transaksi draft.
     */
    public function post(Transaction $transaction)
    {
        if (!$transaction->post()) {
            return back()->withErrors(['message' => 'Gagal memposting transaksi']);
        }

        return back()->with('success', 'Transaksi berhasil diposting');
    }

    /**
     * Delete transaksi (hanya draft).
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->status !== 'draft') {
            return back()->withErrors(['message' => 'Hanya transaksi draft yang bisa dihapus']);
        }

        $transaction->delete();

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }

    /**
     * Unpost transaksi (ubah posted ke draft).
     * Hanya untuk super admin.
     */
    public function unpost(Transaction $transaction)
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');

        if (!$isSuperAdmin) {
            return back()->withErrors(['message' => 'Hanya super admin yang bisa mengubah status ke draft']);
        }

        if (!$transaction->unpost()) {
            return back()->withErrors(['message' => 'Gagal mengubah status transaksi']);
        }

        return back()->with('success', 'Transaksi berhasil diubah ke draft');
    }
}
