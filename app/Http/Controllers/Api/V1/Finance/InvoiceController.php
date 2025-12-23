<?php

namespace App\Http\Controllers\Api\V1\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

/**
 * Controller untuk Invoice (Tagihan SPP/Infaq).
 */
class InvoiceController extends Controller
{
    /**
     * List semua invoice.
     */
    #[OA\Get(
        path: '/finance/invoices',
        summary: 'List semua invoice',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'year', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'month', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['student:id,nis,name,program_type', 'items']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('month')) {
            $query->where('month', $request->month);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        return $this->paginated($invoices, 'Daftar invoice');
    }

    /**
     * Create invoice baru.
     */
    #[OA\Post(
        path: '/finance/invoices',
        summary: 'Buat invoice baru',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 201, description: 'Invoice berhasil dibuat')]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|integer|min:1|max:12',
            'due_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string|max:50',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Check duplicate
        $exists = Invoice::where('student_id', $validated['student_id'])
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->exists();

        if ($exists) {
            return $this->error('Invoice untuk periode ini sudah ada', 422);
        }

        $invoice = DB::transaction(function () use ($validated) {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'student_id' => $validated['student_id'],
                'year' => $validated['year'],
                'month' => $validated['month'],
                'invoice_date' => now(),
                'due_date' => $validated['due_date'],
                'discount' => $validated['discount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $invoice->addItem(
                    $item['item_type'],
                    $item['description'],
                    $item['unit_price'],
                    $item['quantity'] ?? 1
                );
            }

            $invoice->recalculate();

            return $invoice;
        });

        return $this->success($invoice->load('items'), 'Invoice berhasil dibuat', 201);
    }

    /**
     * Detail invoice.
     */
    #[OA\Get(
        path: '/finance/invoices/{id}',
        summary: 'Detail invoice',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['student.guardian', 'items', 'payments.account']);

        return $this->success([
            'invoice' => $invoice,
            'period_label' => $invoice->period_label,
            'formatted_total' => $invoice->formatted_total,
            'formatted_balance' => $invoice->formatted_balance,
        ]);
    }

    /**
     * Update invoice (hanya draft).
     */
    #[OA\Put(
        path: '/finance/invoices/{id}',
        summary: 'Update invoice',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Invoice berhasil diupdate')]
    )]
    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->status !== 'draft') {
            return $this->error('Hanya invoice draft yang bisa diedit', 400);
        }

        $validated = $request->validate([
            'due_date' => 'date',
            'discount' => 'numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);
        $invoice->recalculate();

        return $this->success($invoice, 'Invoice berhasil diupdate');
    }

    /**
     * Kirim invoice ke wali santri.
     */
    #[OA\Post(
        path: '/finance/invoices/{id}/send',
        summary: 'Kirim invoice',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Invoice berhasil dikirim')]
    )]
    public function send(Invoice $invoice): JsonResponse
    {
        if ($invoice->status !== 'draft') {
            return $this->error('Invoice sudah dikirim sebelumnya', 400);
        }

        $invoice->update(['status' => 'sent']);

        // TODO: Send notification to guardian (WhatsApp/Email)

        return $this->success($invoice, 'Invoice berhasil dikirim');
    }

    /**
     * Generate invoice bulanan batch.
     */
    #[OA\Post(
        path: '/finance/invoices/generate',
        summary: 'Generate invoice bulanan',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 200, description: 'Invoice berhasil digenerate')]
    )]
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|integer|min:1|max:12',
            'program_type' => 'nullable|in:tpq,taud',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $query = Student::active();

        if ($validated['program_type'] ?? null) {
            $query->where('program_type', $validated['program_type']);
        }

        if ($validated['class_id'] ?? null) {
            $query->where('class_id', $validated['class_id']);
        }

        $students = $query->get();
        $created = 0;
        $skipped = 0;

        $dueDate = now()->setYear($validated['year'])
            ->setMonth($validated['month'])
            ->endOfMonth();

        foreach ($students as $student) {
            // Skip jika sudah ada
            $exists = Invoice::where('student_id', $student->id)
                ->where('year', $validated['year'])
                ->where('month', $validated['month'])
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'student_id' => $student->id,
                'year' => $validated['year'],
                'month' => $validated['month'],
                'invoice_date' => now(),
                'due_date' => $dueDate,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Add default item berdasarkan program
            $itemType = $student->program_type === 'taud' ? 'spp' : 'infaq';
            $description = $student->program_type === 'taud' ? 'SPP Bulanan' : 'Infaq TPQ Bulanan';

            $invoice->addItem($itemType, $description, $student->getMonthlyFeeAmount());
            $invoice->recalculate();

            $created++;
        }

        return $this->success([
            'created' => $created,
            'skipped' => $skipped,
            'total_students' => $students->count(),
        ], "Berhasil generate {$created} invoice");
    }

    /**
     * Hapus invoice (hanya draft).
     */
    #[OA\Delete(
        path: '/finance/invoices/{id}',
        summary: 'Hapus invoice',
        tags: ['Finance'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Invoice berhasil dihapus')]
    )]
    public function destroy(Invoice $invoice): JsonResponse
    {
        if ($invoice->status !== 'draft') {
            return $this->error('Hanya invoice draft yang bisa dihapus', 400);
        }

        $invoice->items()->delete();
        $invoice->delete();

        return $this->success(null, 'Invoice berhasil dihapus');
    }
}
