<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Web Controller untuk manajemen Santri TPQ dan TAUD.
 */
class StudentController extends Controller
{
    /**
     * List semua santri.
     */
    public function index(Request $request): Response
    {
        $query = Student::query()->latest();

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by class_time
        if ($request->filled('class_time') && $request->class_time !== 'all') {
            $query->where('class_time', $request->class_time);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by entry year
        if ($request->filled('entry_year')) {
            $query->where('entry_year', $request->entry_year);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('father_name', 'like', "%{$search}%")
                    ->orWhere('mother_name', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20)->withQueryString();

        // Get distinct entry years for filter
        $entryYears = Student::select('entry_year')
            ->distinct()
            ->orderByDesc('entry_year')
            ->pluck('entry_year');

        return Inertia::render('Students/Index', [
            'students' => $students,
            'entryYears' => $entryYears,
            'filters' => [
                'type' => $request->type ?? 'all',
                'class_time' => $request->class_time ?? 'all',
                'status' => $request->status ?? 'active',
                'entry_year' => $request->entry_year,
                'search' => $request->search,
            ],
            'stats' => [
                'total' => Student::count(),
                'active' => Student::active()->count(),
                'tpq_pagi' => Student::active()->tpq()->pagi()->count(),
                'tpq_sore' => Student::active()->tpq()->sore()->count(),
                'taud' => Student::active()->taud()->count(),
            ],
        ]);
    }

    /**
     * Form tambah santri.
     */
    public function create(): Response
    {
        return Inertia::render('Students/Create');
    }

    /**
     * Simpan santri baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:50',
            'type' => 'required|in:tpq,taud',
            'class_time' => 'required|in:pagi,sore',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'father_name' => 'nullable|string|max:100',
            'father_occupation' => 'nullable|string|max:100',
            'father_phone' => 'nullable|string|max:20',
            'father_wa' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:100',
            'mother_occupation' => 'nullable|string|max:100',
            'mother_phone' => 'nullable|string|max:20',
            'mother_wa' => 'nullable|string|max:20',
            'registration_date' => 'nullable|date',
            'entry_year' => 'required|integer|min:2020|max:2099',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        // TAUD selalu kelas pagi
        if ($validated['type'] === 'taud') {
            $validated['class_time'] = 'pagi';
        }

        $validated['is_active'] = true;

        // Generate NIS
        $validated['nis'] = Student::generateNIS(
            $validated['type'],
            $validated['class_time'],
            $validated['entry_year']
        );

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Santri berhasil ditambahkan dengan NIS: ' . $validated['nis']);
    }

    /**
     * Detail santri.
     */
    public function show(Student $student): Response
    {
        // Append additional attributes for view
        $student->append(['father_whatsapp_link', 'mother_whatsapp_link']);

        return Inertia::render('Students/Show', [
            'student' => $student,
        ]);
    }

    /**
     * Form edit santri.
     */
    public function edit(Student $student): Response
    {
        return Inertia::render('Students/Edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update santri.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:50',
            'type' => 'required|in:tpq,taud',
            'class_time' => 'required|in:pagi,sore',
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'father_name' => 'nullable|string|max:100',
            'father_occupation' => 'nullable|string|max:100',
            'father_phone' => 'nullable|string|max:20',
            'father_wa' => 'nullable|string|max:20',
            'mother_name' => 'nullable|string|max:100',
            'mother_occupation' => 'nullable|string|max:100',
            'mother_phone' => 'nullable|string|max:20',
            'mother_wa' => 'nullable|string|max:20',
            'registration_date' => 'nullable|date',
            'monthly_fee' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // TAUD selalu kelas pagi
        if ($validated['type'] === 'taud') {
            $validated['class_time'] = 'pagi';
        }

        $student->update($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Data santri berhasil diperbarui');
    }

    /**
     * Hapus santri (soft delete).
     */
    public function destroy(Student $student)
    {
        $student->update(['is_active' => false]);

        return redirect()->route('students.index')
            ->with('success', 'Santri berhasil dinonaktifkan');
    }
}
