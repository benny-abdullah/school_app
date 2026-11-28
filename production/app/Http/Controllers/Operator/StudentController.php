<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
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
     * Simpan data siswa baru ke database
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
        ], [
            'nisn.required'       => 'NISN 10 digit wajib diisi.',
            'nisn.digits'         => 'NISN harus tepat 10 digit angka resmi Kemdikbud.',
            'nisn.unique'         => 'NISN tersebut sudah terdaftar di sistem.',
            'name.required'       => 'Nama lengkap siswa wajib diisi.',
            'classroom_id.exists' => 'Rombel kelas tidak valid.',
        ]);

        $student = Student::create($validated);

        // Update jumlah siswa di kelas bersangkutan
        $this->updateClassroomStudentCount($student->classroom_id);

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
     * Perbarui data siswa & proses mutasi rombel jika ada perubahan kelas
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
        ]);

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

        return redirect()->route('operator.students.index')->with('success', $message);
    }

    /**
     * Hapus data siswa
     */
    public function destroy(Student $student)
    {
        $name = $student->name;
        $classroomId = $student->classroom_id;
        $student->delete();

        $this->updateClassroomStudentCount($classroomId);

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
