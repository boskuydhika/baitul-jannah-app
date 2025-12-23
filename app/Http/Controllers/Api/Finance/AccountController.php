<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Controller untuk Chart of Accounts (COA).
 */
class AccountController extends Controller
{
    /**
     * List semua akun.
     */
    #[OA\Get(
        path: '/finance/accounts',
        summary: 'List semua akun',
        description: 'Mendapatkan daftar semua akun keuangan dengan filter',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'type', in: 'query', description: 'Filter by type (asset, liability, equity, income, expense)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'postable', in: 'query', description: 'Filter akun yang bisa diposting', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'tree', in: 'query', description: 'Return sebagai tree structure', schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Account::query()->active();

        // Filter by type
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        // Filter postable only
        if ($request->boolean('postable')) {
            $query->postable();
        }

        // Return as tree structure
        if ($request->boolean('tree')) {
            $accounts = Account::roots()->with('descendants')->get();
            return $this->success($accounts, 'Daftar akun (tree)');
        }

        $accounts = $query->orderBy('code')->get();

        return $this->success($accounts, 'Daftar akun');
    }

    /**
     * Create akun baru.
     */
    #[OA\Post(
        path: '/finance/accounts',
        summary: 'Buat akun baru',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['code', 'name', 'type'],
                properties: [
                    new OA\Property(property: 'code', type: 'string', example: '1.1.05'),
                    new OA\Property(property: 'name', type: 'string', example: 'Bank Mandiri'),
                    new OA\Property(property: 'type', type: 'string', example: 'asset'),
                    new OA\Property(property: 'parent_id', type: 'integer', example: 2),
                    new OA\Property(property: 'is_postable', type: 'boolean', example: true),
                    new OA\Property(property: 'description', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Akun berhasil dibuat'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_postable' => 'boolean',
            'description' => 'nullable|string',
        ], [
            'code.required' => 'Kode akun wajib diisi',
            'code.unique' => 'Kode akun sudah digunakan',
            'name.required' => 'Nama akun wajib diisi',
            'type.required' => 'Tipe akun wajib diisi',
            'type.in' => 'Tipe akun tidak valid',
        ]);

        // Calculate level
        $level = 1;
        if ($validated['parent_id'] ?? null) {
            $parent = Account::find($validated['parent_id']);
            $level = $parent->level + 1;
            $validated['type'] = $parent->type; // Inherit type from parent
        }

        // Set normal balance based on type
        $validated['normal_balance'] = in_array($validated['type'], ['asset', 'expense']) ? 'debit' : 'credit';
        $validated['level'] = $level;

        $account = Account::create($validated);

        return $this->success($account, 'Akun berhasil dibuat', 201);
    }

    /**
     * Detail akun.
     */
    #[OA\Get(
        path: '/finance/accounts/{id}',
        summary: 'Detail akun',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Berhasil'),
            new OA\Response(response: 404, description: 'Tidak ditemukan'),
        ]
    )]
    public function show(Account $account): JsonResponse
    {
        $account->load(['parent', 'children']);

        return $this->success([
            'account' => $account,
            'full_path' => $account->full_path,
            'formatted_balance' => $account->formatted_balance,
        ]);
    }

    /**
     * Update akun.
     */
    #[OA\Put(
        path: '/finance/accounts/{id}',
        summary: 'Update akun',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Akun berhasil diupdate'),
        ]
    )]
    public function update(Request $request, Account $account): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'string|max:20|unique:accounts,code,' . $account->id,
            'name' => 'string|max:255',
            'is_postable' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $account->update($validated);

        return $this->success($account, 'Akun berhasil diupdate');
    }

    /**
     * Hapus akun (soft delete).
     */
    #[OA\Delete(
        path: '/finance/accounts/{id}',
        summary: 'Hapus akun',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Akun berhasil dihapus'),
            new OA\Response(response: 400, description: 'Tidak bisa dihapus'),
        ]
    )]
    public function destroy(Account $account): JsonResponse
    {
        // Check if has children
        if ($account->children()->exists()) {
            return $this->error('Tidak bisa menghapus akun yang memiliki sub-akun', 400);
        }

        // Check if has transactions
        if ($account->transactionDetails()->exists()) {
            return $this->error('Tidak bisa menghapus akun yang sudah memiliki transaksi', 400);
        }

        $account->delete();

        return $this->success(null, 'Akun berhasil dihapus');
    }

    /**
     * Generate kode akun berikutnya.
     */
    #[OA\Get(
        path: '/finance/accounts/next-code',
        summary: 'Generate kode akun berikutnya',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'parent_id', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Kode akun berikutnya'),
        ]
    )]
    public function nextCode(Request $request): JsonResponse
    {
        $parentId = $request->input('parent_id');
        $nextCode = Account::generateNextCode($parentId);

        return $this->success(['next_code' => $nextCode]);
    }
}
