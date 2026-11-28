<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar direktori master siswa dengan pencarian dan filter
     */
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('section')->get();

        $filters = [
            'search'       => $request->get('search'),
            'classroom_id' => $request->get('classroom_id', 'all'),
            'status'       => $request->get('status', 'active'),
            'gender'       => $request->get('gender', 'all'),
        ];

        $students = Student::with('classroom')
            ->filter($filters)
            ->orderBy('classroom_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_active' => Student::where('status', 'active')->count(),
            'total_male'   => Student::where('status', 'active')->where('gender', 'L')->count(),
            'total_female' => Student::where('status', 'active')->where('gender', 'P')->count(),
            'total_classes'=> $classrooms->count(),
        ];

        return view('operator.students.index', compact('students', 'classrooms', 'filters', 'stats'));
    }

    /**
     * Tampilkan form penambahan siswa baru
     */
    public function create()
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('section')->get();
        return view('operator.students.create', compact('classrooms'));
    }

    /**
     * Simpan data siswa baru beserta upload pas foto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn'              => 'required|numeric|digits:10|unique:students,nisn',
            'nis'               => 'nullable|string|max:20',
            'name'              => 'required|string|max:100',
            'gender'            => 'required|in:L,P',
            'classroom_id'      => 'required|exists:classrooms,id',
            'birth_place'       => 'nullable|string|max:60',
            'birth_date'        => 'nullable|date',
            'address'           => 'nullable|string',
            'guardian_name'     => 'nullable|string|max:100',
            'guardian_relation' => 'nullable|string|max:40',
            'guardian_phone'    => 'nullable|string|max:30',
            'status'            => 'required|in:active,graduated,moved,inactive',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nisn.required'       => 'NISN 10 digit wajib diisi.',
            'nisn.digits'         => 'NISN harus tepat 10 digit angka resmi Kemdikbud.',
            'nisn.unique'         => 'NISN tersebut sudah terdaftar di sistem.',
            'name.required'       => 'Nama lengkap siswa wajib diisi.',
            'classroom_id.exists' => 'Rombel kelas tidak valid.',
            'photo.image'         => 'File pas foto harus berupa gambar.',
            'photo.mimes'         => 'Format foto harus berupa JPG, JPEG, PNG, atau WebP.',
            'photo.max'           => 'Ukuran berkas foto maksimal 2 MB.',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create($validated);

        // Update jumlah siswa di kelas bersangkutan
        $this->updateClassroomStudentCount($student->classroom_id);

        ActivityLog::record(
            'CREATE_STUDENT',
            'Master Siswa',
            "Menambahkan siswa baru: {$student->name} (NISN: {$student->nisn}, Kelas: {$student->classroom->code})"
        );

        return redirect()->route('operator.students.index')
            ->with('success', "Data siswa {$student->name} (NISN: {$student->nisn}) berhasil ditambahkan ke rombel {$student->classroom->code}!");
    }

    /**
     * Tampilkan form edit siswa & mutasi rombel
     */
    public function edit(Student $student)
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('section')->get();
        return view('operator.students.edit', compact('student', 'classrooms'));
    }

    /**
     * Perbarui data siswa, proses mutasi rombel, dan upload foto baru
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nisn'              => ['required', 'numeric', 'digits:10', Rule::unique('students')->ignore($student->id)],
            'nis'               => 'nullable|string|max:20',
            'name'              => 'required|string|max:100',
            'gender'            => 'required|in:L,P',
            'classroom_id'      => 'required|exists:classrooms,id',
            'birth_place'       => 'nullable|string|max:60',
            'birth_date'        => 'nullable|date',
            'address'           => 'nullable|string',
            'guardian_name'     => 'nullable|string|max:100',
            'guardian_relation' => 'nullable|string|max:40',
            'guardian_phone'    => 'nullable|string|max:30',
            'status'            => 'required|in:active,graduated,moved,inactive',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'photo.image'       => 'File pas foto harus berupa gambar.',
            'photo.mimes'       => 'Format foto harus berupa JPG, JPEG, PNG, atau WebP.',
            'photo.max'         => 'Ukuran berkas foto maksimal 2 MB.',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus pas foto lama jika ada
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $oldClassroomId = $student->classroom_id;
        $newClassroomId = (int)$validated['classroom_id'];
        $isMutated = ($oldClassroomId !== $newClassroomId);

        $student->update($validated);

        // Jika terjadi mutasi rombel kelas, sinkronkan kedua rombel
        if ($isMutated) {
            $this->updateClassroomStudentCount($oldClassroomId);
            $this->updateClassroomStudentCount($newClassroomId);
            $newClass = Classroom::find($newClassroomId);
            $message = "Data siswa {$student->name} berhasil diperbarui dan dimutasi ke rombel {$newClass->code}!";
        } else {
            $this->updateClassroomStudentCount($newClassroomId);
            $message = "Perubahan data siswa {$student->name} berhasil disimpan!";
        }

        ActivityLog::record(
            $isMutated ? 'MUTASI_ROMBEL' : 'UPDATE_STUDENT',
            'Master Siswa',
            $isMutated
                ? "Memutasikan siswa {$student->name} (NISN: {$student->nisn}) ke Kelas {$student->classroom->code}"
                : "Memperbarui data siswa {$student->name} (NISN: {$student->nisn})"
        );

        return redirect()->route('operator.students.index')->with('success', $message);
    }

    /**
     * Hapus data siswa dan fotonya jika ada
     */
    public function destroy(Student $student)
    {
        $name = $student->name;
        $nisn = $student->nisn;
        $classroomId = $student->classroom_id;

        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        $this->updateClassroomStudentCount($classroomId);

        ActivityLog::record(
            'DELETE_STUDENT',
            'Master Siswa',
            "Menghapus data siswa: {$name} (NISN: {$nisn})"
        );

        return redirect()->route('operator.students.index')
            ->with('info', "Data siswa {$name} telah berhasil dihapus dari sistem.");
    }

    /**
     * Helper sinkronisasi total siswa pada rombel kelas
     */
    private function updateClassroomStudentCount(int $classroomId): void
    {
        $count = Student::where('classroom_id', $classroomId)
            ->where('status', 'active')
            ->count();

        Classroom::where('id', $classroomId)->update(['total_students' => $count]);
    }
}
