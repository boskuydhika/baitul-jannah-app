<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

/**
 * Controller untuk Payment (Pembayaran).
 */
class PaymentController extends Controller
{
    /**
     * List semua pembayaran.
     */
    #[OA\Get(
        path: '/finance/payments',
        summary: 'List semua pembayaran',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', description: 'Filter by date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'invoice_id', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['invoice.student:id,nis,name', 'account:id,code,name'])
            ->confirmed();

        if ($request->has('date')) {
            $query->whereDate('payment_date', $request->date);
        }

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        return $this->paginated($payments, 'Daftar pembayaran');
    }

    /**
     * Input pembayaran baru.
     */
    #[OA\Post(
        path: '/finance/payments',
        summary: 'Input pembayaran',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['invoice_id', 'amount', 'payment_method', 'account_id'],
                properties: [
                    new OA\Property(property: 'invoice_id', type: 'integer'),
                    new OA\Property(property: 'amount', type: 'number'),
                    new OA\Property(property: 'payment_method', type: 'string', enum: ['cash', 'transfer', 'qris']),
                    new OA\Property(property: 'account_id', type: 'integer', description: 'ID akun kas/bank'),
                    new OA\Property(property: 'payment_date', type: 'string', format: 'date'),
                    new OA\Property(property: 'reference', type: 'string'),
                    new OA\Property(property: 'notes', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Pembayaran berhasil'),
            new OA\Response(response: 400, description: 'Error'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,transfer,qris',
            'account_id' => 'required|exists:accounts,id',
            'payment_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'invoice_id.required' => 'Invoice wajib dipilih',
            'amount.required' => 'Jumlah pembayaran wajib diisi',
            'amount.min' => 'Jumlah pembayaran minimal Rp 1',
            'payment_method.required' => 'Metode pembayaran wajib dipilih',
            'account_id.required' => 'Akun kas/bank wajib dipilih',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $account = Account::findOrFail($validated['account_id']);

        // Validasi amount tidak melebihi sisa tagihan
        if ($validated['amount'] > $invoice->balance) {
            return $this->error(
                'Jumlah pembayaran melebihi sisa tagihan (Rp ' . number_format($invoice->balance, 0, ',', '.') . ')',
                400
            );
        }

        // Validasi akun harus postable dan tipe asset (kas/bank)
        if (!$account->is_postable || $account->type !== 'asset') {
            return $this->error('Akun yang dipilih bukan akun kas/bank yang valid', 400);
        }

        $payment = DB::transaction(function () use ($validated, $invoice, $account) {
            // Create payment
            $payment = Payment::create([
                'payment_number' => Payment::generateNumber(),
                'invoice_id' => $validated['invoice_id'],
                'payment_date' => $validated['payment_date'] ?? now(),
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'] ?? null,
                'account_id' => $validated['account_id'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'confirmed',
                'created_by' => auth()->id(),
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            // Create journal entry
            $transaction = $this->createJournalEntry($payment, $invoice, $account);
            $payment->update(['transaction_id' => $transaction->id]);

            // Update invoice
            $invoice->paid_amount += $validated['amount'];
            $invoice->recalculate();

            return $payment;
        });

        return $this->success(
            $payment->load(['invoice', 'account']),
            'Pembayaran berhasil dicatat',
            201
        );
    }

    /**
     * Detail pembayaran.
     */
    #[OA\Get(
        path: '/finance/payments/{id}',
        summary: 'Detail pembayaran',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['invoice.student', 'account', 'transaction.details.account']);

        return $this->success([
            'payment' => $payment,
            'formatted_amount' => $payment->formatted_amount,
        ]);
    }

    /**
     * Batalkan pembayaran.
     */
    #[OA\Delete(
        path: '/finance/payments/{id}',
        summary: 'Batalkan pembayaran',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Pembayaran dibatalkan')]
    )]
    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->status === 'cancelled') {
            return $this->error('Pembayaran sudah dibatalkan sebelumnya', 400);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($payment, $validated) {
            // Void journal entry if exists
            if ($payment->transaction) {
                $payment->transaction->void($validated['reason']);
            }

            // Update payment status
            $payment->update(['status' => 'cancelled']);

            // Update invoice
            $invoice = $payment->invoice;
            $invoice->paid_amount -= $payment->amount;
            $invoice->recalculate();
        });

        return $this->success(null, 'Pembayaran berhasil dibatalkan');
    }

    /**
     * Create journal entry untuk pembayaran.
     */
    private function createJournalEntry(Payment $payment, Invoice $invoice, Account $cashAccount): Transaction
    {
        // Cari akun piutang (1.2.01 - Piutang SPP)
        $receivableAccount = Account::where('code', '1.2.01')->first();

        // Cari akun pendapatan berdasarkan program
        $incomeAccountCode = $invoice->student->program_type === 'taud' ? '4.1.01' : '4.1.02';
        $incomeAccount = Account::where('code', $incomeAccountCode)->first();

        $transaction = Transaction::create([
            'transaction_number' => Transaction::generateNumber(),
            'transaction_date' => $payment->payment_date,
            'type' => 'receipt',
            'reference_type' => Invoice::class,
            'reference_id' => $invoice->id,
            'description' => "Pembayaran {$invoice->invoice_number} - {$invoice->student->name}",
            'amount' => $payment->amount,
            'status' => 'posted',
            'created_by' => auth()->id(),
            'posted_at' => now(),
            'posted_by' => auth()->id(),
        ]);

        // Debit Kas/Bank
        $transaction->addDetail($cashAccount->id, $payment->amount, 0, 'Terima pembayaran');

        // Credit Piutang atau Pendapatan
        if ($receivableAccount) {
            $transaction->addDetail($receivableAccount->id, 0, $payment->amount, 'Pelunasan piutang');
        } elseif ($incomeAccount) {
            $transaction->addDetail($incomeAccount->id, 0, $payment->amount, 'Pendapatan SPP/Infaq');
        }

        // Update saldo akun
        $cashAccount->updateBalance($payment->amount, 0);
        if ($receivableAccount) {
            $receivableAccount->updateBalance(0, $payment->amount);
        } elseif ($incomeAccount) {
            $incomeAccount->updateBalance(0, $payment->amount);
        }

        return $transaction;
    }
}
