<?php

namespace App\Http\Controllers\Api\V1\Academic;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

/**
 * Controller untuk Student (Santri).
 */
class StudentController extends Controller
{
    /**
     * List semua santri.
     */
    #[OA\Get(
        path: '/academic/students',
        summary: 'List semua santri',
        tags: ['Academic'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'program_type', in: 'query', schema: new OA\Schema(type: 'string', enum: ['tpq', 'taud'])),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'class_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['guardian:id,name,phone', 'class:id,name']);

        if ($request->has('program_type')) {
            $query->where('program_type', $request->program_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(15);

        return $this->paginated($students, 'Daftar santri');
    }

    /**
     * Create santri baru.
     */
    #[OA\Post(
        path: '/academic/students',
        summary: 'Daftar santri baru',
        tags: ['Academic'],
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(response: 201, description: 'Santri berhasil didaftarkan')]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'program_type' => 'required|in:tpq,taud',
            'taud_level' => 'required_if:program_type,taud|in:kb,tk_a,tk_b',
            'current_jilid' => 'required_if:program_type,tpq|integer|min:1|max:7',
            'class_id' => 'nullable|exists:classes,id',
            'monthly_fee' => 'nullable|numeric|min:0',
            // Guardian data
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'nullable|email',
            'guardian_address' => 'nullable|string',
            'guardian_occupation' => 'nullable|string|max:100',
        ]);

        $student = DB::transaction(function () use ($validated) {
            // Create or find guardian
            $guardian = Guardian::firstOrCreate(
                ['phone' => $validated['guardian_phone']],
                [
                    'name' => $validated['guardian_name'],
                    'email' => $validated['guardian_email'] ?? null,
                    'address' => $validated['guardian_address'] ?? null,
                    'occupation' => $validated['guardian_occupation'] ?? null,
                ]
            );

            // Create student
            $student = Student::create([
                'nis' => Student::generateNis($validated['program_type']),
                'name' => $validated['name'],
                'nickname' => $validated['nickname'] ?? null,
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'birth_place' => $validated['birth_place'] ?? null,
                'address' => $validated['address'] ?? null,
                'guardian_id' => $guardian->id,
                'program_type' => $validated['program_type'],
                'taud_level' => $validated['taud_level'] ?? null,
                'current_jilid' => $validated['current_jilid'] ?? null,
                'class_id' => $validated['class_id'] ?? null,
                'status' => 'active',
                'entry_date' => now(),
                'monthly_fee' => $validated['monthly_fee'] ?? null,
            ]);

            return $student;
        });

        return $this->success(
            $student->load('guardian'),
            'Santri berhasil didaftarkan',
            201
        );
    }

    /**
     * Detail santri.
     */
    #[OA\Get(
        path: '/academic/students/{id}',
        summary: 'Detail santri',
        tags: ['Academic'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Berhasil')]
    )]
    public function show(Student $student): JsonResponse
    {
        $student->load([
            'guardian',
            'class.teacher',
            'invoices' => function ($q) {
                $q->latest()->limit(12);
            }
        ]);

        return $this->success([
            'student' => $student,
            'age' => $student->age,
            'jilid_label' => $student->jilid_label,
            'total_arrears' => $student->total_arrears,
        ]);
    }

    /**
     * Update santri.
     */
    #[OA\Put(
        path: '/academic/students/{id}',
        summary: 'Update santri',
        tags: ['Academic'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Santri berhasil diupdate')]
    )]
    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'nickname' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'taud_level' => 'in:kb,tk_a,tk_b',
            'current_jilid' => 'integer|min:1|max:7',
            'class_id' => 'nullable|exists:classes,id',
            'status' => 'in:pending,active,graduated,dropped',
            'monthly_fee' => 'nullable|numeric|min:0',
        ]);

        $student->update($validated);

        return $this->success($student, 'Santri berhasil diupdate');
    }

    /**
     * Hapus santri (soft delete).
     */
    #[OA\Delete(
        path: '/academic/students/{id}',
        summary: 'Hapus santri',
        tags: ['Academic'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Santri berhasil dihapus')]
    )]
    public function destroy(Student $student): JsonResponse
    {
        // Check if has unpaid invoices
        if ($student->invoices()->unpaid()->exists()) {
            return $this->error('Tidak bisa menghapus santri yang masih memiliki tagihan belum lunas', 400);
        }

        $student->delete();

        return $this->success(null, 'Santri berhasil dihapus');
    }
}
