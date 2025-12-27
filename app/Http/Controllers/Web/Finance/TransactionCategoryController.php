<?php

namespace App\Http\Controllers\Web\Finance;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Web Controller untuk Kategori Transaksi.
 * 
 * Permission: super-admin, finance
 */
class TransactionCategoryController extends Controller
{
    /**
     * List semua kategori.
     */
    public function index(): Response
    {
        $categories = TransactionCategory::ordered()->get();

        return Inertia::render('Finance/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store kategori baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string|max:255',
        ]);

        $maxOrder = TransactionCategory::where('type', $validated['type'])->max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        TransactionCategory::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, TransactionCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Delete kategori (soft validation).
     */
    public function destroy(TransactionCategory $category)
    {
        // Check if category is being used
        if ($category->transactions()->exists()) {
            return back()->withErrors(['message' => 'Kategori tidak bisa dihapus karena sudah digunakan']);
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus');
    }
}
