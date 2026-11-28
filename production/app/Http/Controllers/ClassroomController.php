<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Tampilkan direktori 29 Master Rombongan Belajar dengan filter dan relasi Wali Kelas
     */
    public function index(Request $request)
    {
        $selectedGrade = $request->get('grade', 'all');

        $query = Classroom::with('homeroomTeacher')
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('section');

        if ($selectedGrade !== 'all') {
            $query->where('grade_level', $selectedGrade);
        }

        $classrooms = $query->get();

        $stats = [
            'total_classes'          => Classroom::count(),
            'total_students'         => Classroom::sum('total_students'),
            'total_capacity'         => Classroom::sum('capacity'),
            'assigned_teachers_count'=> Classroom::whereNotNull('homeroom_teacher_id')->count(),
        ];

        return view('operator.classrooms.index', compact('classrooms', 'stats', 'selectedGrade'));
    }

    /**
     * Tampilkan form edit rombel dan penetapan / rotasi Wali Kelas
     */
    public function edit(Classroom $classroom)
    {
        $classroom->load('homeroomTeacher', 'students');

        // Daftar seluruh guru ber-NIP aktif
        $teachers = User::where('role', 'guru/walikelas')
            ->orderBy('name')
            ->get();

        // Cari rombel yang sedang dipegang oleh masing-masing guru (untuk info anti-bentrok)
        $assignedClasses = Classroom::whereNotNull('homeroom_teacher_id')
            ->where('id', '!=', $classroom->id)
            ->pluck('code', 'homeroom_teacher_id')
            ->toArray();

        return view('operator.classrooms.edit', compact('classroom', 'teachers', 'assignedClasses'));
    }

    /**
     * Simpan perubahan teknis rombel & penugasan SK Wali Kelas
     */
    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'capacity'            => 'required|integer|min:15|max:40',
            'room_name'           => 'nullable|string|max:100',
            'academic_year'       => 'required|string|max:20',
            'status'              => 'required|in:active,inactive',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ], [
            'capacity.required'   => 'Daya tampung kapasitas kelas wajib diisi.',
            'capacity.min'        => 'Kapasitas minimal 15 siswa.',
            'capacity.max'        => 'Kapasitas maksimal 40 siswa.',
            'homeroom_teacher_id.exists' => 'Data guru terpilih tidak valid.',
        ]);

        $notice = '';

        // Proses penugasan wali kelas
        if (!empty($validated['homeroom_teacher_id'])) {
            $teacherId = (int)$validated['homeroom_teacher_id'];

            // Cek apakah guru ini sebelumnya memegang rombel lain (rotasi penugasan)
            $oldClass = Classroom::where('homeroom_teacher_id', $teacherId)
                ->where('id', '!=', $classroom->id)
                ->first();

            if ($oldClass) {
                // Lepaskan penugasan di rombel lama
                $oldClass->update([
                    'homeroom_teacher_id' => null,
                    'homeroom_teacher'    => null,
                    'homeroom_nip'        => null,
                ]);
                $notice = " (Catatan: Guru ini sebelumnya dipindahkan dari Kelas {$oldClass->code})";
            }

            $teacher = User::find($teacherId);
            $classroom->homeroom_teacher_id = $teacher->id;
            $classroom->homeroom_teacher    = $teacher->name;
            $classroom->homeroom_nip        = $teacher->nip_nuptk;

            // Update info di profil guru
            $info = $teacher->additional_info ?? [];
            $info['classroom'] = $classroom->code;
            $info['jabatan'] = 'Wali Kelas ' . $classroom->code;
            $teacher->additional_info = $info;
            $teacher->save();
        } else {
            // Kosongkan wali kelas jika dipilih "Belum Ditugaskan"
            $classroom->homeroom_teacher_id = null;
            $classroom->homeroom_teacher    = null;
            $classroom->homeroom_nip        = null;
        }

        $classroom->capacity      = $validated['capacity'];
        $classroom->room_name     = $validated['room_name'];
        $classroom->academic_year = $validated['academic_year'];
        $classroom->status        = $validated['status'];
        $classroom->save();

        ActivityLog::record(
            'ROTASI_ROMBEL',
            'Master Rombel',
            "Memperbarui data Kelas {$classroom->code} (Kapasitas: {$classroom->capacity}, Wali Kelas: " . ($classroom->homeroom_teacher ?? 'Kosong') . "){$notice}"
        );

        return redirect()->route('classrooms.index')
            ->with('success', "Pengaturan Kelas {$classroom->code} dan penetapan Wali Kelas berhasil disimpan!{$notice}");
    }
}
