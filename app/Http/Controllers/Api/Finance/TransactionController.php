<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

/**
 * Controller untuk Transaction (Jurnal Umum).
 */
class TransactionController extends Controller
{
    /**
     * List semua transaksi.
     */
    #[OA\Get(
        path: '/finance/transactions',
        summary: 'List semua transaksi',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'type', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::with(['details.account:id,code,name', 'creator:id,name']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return $this->paginated($transactions, 'Daftar transaksi');
    }

    /**
     * Create transaksi manual (jurnal umum).
     */
    #[OA\Post(
        path: '/finance/transactions',
        summary: 'Buat transaksi jurnal',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['transaction_date', 'description', 'details'],
                properties: [
                    new OA\Property(property: 'transaction_date', type: 'string', format: 'date'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'type', type: 'string', example: 'journal'),
                    new OA\Property(
                        property: 'details',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'account_id', type: 'integer'),
                                new OA\Property(property: 'debit', type: 'number'),
                                new OA\Property(property: 'credit', type: 'number'),
                                new OA\Property(property: 'description', type: 'string'),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Transaksi berhasil'),
            new OA\Response(response: 400, description: 'Error'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:500',
            'type' => 'nullable|string|max:30',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.credit' => 'required|numeric|min:0',
            'details.*.description' => 'nullable|string|max:255',
        ]);

        // Validate balance
        $totalDebit = collect($validated['details'])->sum('debit');
        $totalCredit = collect($validated['details'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return $this->error(
                "Transaksi tidak balance! Debit: Rp " . number_format($totalDebit, 0, ',', '.') .
                ", Credit: Rp " . number_format($totalCredit, 0, ',', '.'),
                400
            );
        }

        // Validate each detail has either debit or credit
        foreach ($validated['details'] as $index => $detail) {
            if ($detail['debit'] == 0 && $detail['credit'] == 0) {
                return $this->error("Baris " . ($index + 1) . ": Debit atau Credit harus diisi", 400);
            }
            if ($detail['debit'] > 0 && $detail['credit'] > 0) {
                return $this->error("Baris " . ($index + 1) . ": Tidak boleh isi Debit dan Credit sekaligus", 400);
            }
        }

        $transaction = DB::transaction(function () use ($validated, $totalDebit) {
            $transaction = Transaction::create([
                'transaction_number' => Transaction::generateNumber(),
                'transaction_date' => $validated['transaction_date'],
                'type' => $validated['type'] ?? 'journal',
                'description' => $validated['description'],
                'amount' => $totalDebit,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['details'] as $detail) {
                $transaction->addDetail(
                    $detail['account_id'],
                    $detail['debit'],
                    $detail['credit'],
                    $detail['description'] ?? null
                );
            }

            return $transaction;
        });

        return $this->success(
            $transaction->load('details.account'),
            'Transaksi berhasil dibuat',
            201
        );
    }

    /**
     * Detail transaksi.
     */
    #[OA\Get(
        path: '/finance/transactions/{id}',
        summary: 'Detail transaksi',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['details.account', 'creator', 'poster', 'reference']);

        return $this->success([
            'transaction' => $transaction,
            'is_balanced' => $transaction->is_balanced,
            'total_debit' => $transaction->total_debit,
            'total_credit' => $transaction->total_credit,
        ]);
    }

    /**
     * Posting transaksi.
     */
    #[OA\Post(
        path: '/finance/transactions/{id}/post',
        summary: 'Posting transaksi',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Transaksi berhasil diposting'),
            new OA\Response(response: 400, description: 'Error'),
        ]
    )]
    public function post(Transaction $transaction): JsonResponse
    {
        if ($transaction->status !== 'draft') {
            return $this->error('Transaksi sudah diposting atau dibatalkan', 400);
        }

        try {
            $transaction->post();
            return $this->success($transaction, 'Transaksi berhasil diposting');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Void/batalkan transaksi.
     */
    #[OA\Post(
        path: '/finance/transactions/{id}/void',
        summary: 'Batalkan transaksi',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Transaksi dibatalkan')]
    )]
    public function void(Request $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        if ($transaction->status !== 'posted') {
            return $this->error('Hanya transaksi posted yang bisa dibatalkan', 400);
        }

        try {
            $transaction->void($validated['reason']);
            return $this->success($transaction, 'Transaksi berhasil dibatalkan');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Hapus transaksi draft.
     */
    #[OA\Delete(
        path: '/finance/transactions/{id}',
        summary: 'Hapus transaksi',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Transaksi dihapus')]
    )]
    public function destroy(Transaction $transaction): JsonResponse
    {
        if ($transaction->status !== 'draft') {
            return $this->error('Hanya transaksi draft yang bisa dihapus', 400);
        }

        $transaction->details()->delete();
        $transaction->delete();

        return $this->success(null, 'Transaksi berhasil dihapus');
    }
}
