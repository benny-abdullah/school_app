<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    /**
     * Tampilkan direktori Master Rombongan Belajar dengan filter dan relasi Wali Kelas
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
     * Ekspor Data Rombel ke PDF Resmi
     */
    public function exportPdf(Request $request)
    {
        $selectedGrade = $request->get('grade', 'all');

        $query = Classroom::with('homeroomTeacher')->withCount('students')->orderBy('grade_level')->orderBy('section');
        if ($selectedGrade !== 'all') {
            $query->where('grade_level', $selectedGrade);
        }
        $classrooms = $query->get();

        ActivityLog::record('EXPORT_PDF', 'Master Rombel', 'Mengunduh laporan PDF direktori rombongan belajar (' . $classrooms->count() . ' rombel)');

        return ExportService::pdf('Rekap_Rombel_SDN_Kebonagung_' . date('Ymd_His'), 'exports.classrooms_pdf', compact('classrooms', 'selectedGrade'), 'portrait');
    }

    /**
     * Ekspor Data Rombel ke Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $selectedGrade = $request->get('grade', 'all');

        $query = Classroom::with('homeroomTeacher')->withCount('students')->orderBy('grade_level')->orderBy('section');
        if ($selectedGrade !== 'all') {
            $query->where('grade_level', $selectedGrade);
        }
        $classrooms = $query->get();

        $headers = [
            'no'               => 'No',
            'code'             => 'Kode Rombel',
            'name'             => 'Nama Rombel',
            'grade_level'      => 'Tingkat',
            'room_name'        => 'Lokasi Ruangan',
            'homeroom_teacher' => 'Wali Kelas Resmi',
            'homeroom_nip'     => 'NIP Guru',
            'students_count'   => 'Siswa Terdaftar',
            'capacity'         => 'Kapasitas Kuota',
            'academic_year'    => 'Tahun Ajaran',
            'status'           => 'Status',
        ];

        $rows = [];
        foreach ($classrooms as $idx => $cls) {
            $rows[] = [
                'no'               => $idx + 1,
                'code'             => $cls->code,
                'name'             => $cls->name,
                'grade_level'      => 'Kelas ' . $cls->grade_level,
                'room_name'        => $cls->room_name ?? '-',
                'homeroom_teacher' => $cls->homeroomTeacher?->name ?? ($cls->homeroom_teacher ?? 'Belum Ada'),
                'homeroom_nip'     => $cls->homeroomTeacher?->nip_nuptk ?? ($cls->homeroom_nip ?? '-'),
                'students_count'   => $cls->students_count > 0 ? $cls->students_count : $cls->total_students,
                'capacity'         => $cls->capacity,
                'academic_year'    => $cls->academic_year,
                'status'           => ucfirst($cls->status),
            ];
        }

        ActivityLog::record('EXPORT_EXCEL', 'Master Rombel', 'Mengunduh berkas Excel data rombongan belajar (' . $classrooms->count() . ' rombel)');

        return ExportService::excel(
            'Rekap_Rombel_SDN_Kebonagung_' . date('Ymd_His'),
            'Rekapitulasi Rombongan Belajar & SK Wali Kelas',
            'UPTD SDN Kebonagung | Tahun Ajaran 2025/2026 | Total: ' . count($rows) . ' Rombel',
            $headers,
            $rows
        );
    }

    /**
     * Form tambah rombel kelas baru
     */
    public function create()
    {
        $teachers = User::where('role', 'guru/walikelas')
            ->orderBy('name')
            ->get();

        $assignedClasses = Classroom::whereNotNull('homeroom_teacher_id')
            ->pluck('code', 'homeroom_teacher_id')
            ->toArray();

        return view('operator.classrooms.create', compact('teachers', 'assignedClasses'));
    }

    /**
     * Simpan rombel kelas baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level'         => 'required|integer|min:1|max:6',
            'section'             => 'required|string|max:5',
            'capacity'            => 'required|integer|min:15|max:40',
            'room_name'           => 'nullable|string|max:100',
            'academic_year'       => 'required|string|max:20',
            'status'              => 'required|in:active,inactive',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ], [
            'grade_level.required' => 'Tingkat kelas wajib dipilih.',
            'section.required'     => 'Huruf / nama seksi rombel wajib diisi.',
            'capacity.required'    => 'Daya tampung kapasitas kelas wajib diisi.',
            'capacity.min'         => 'Kapasitas minimal 15 siswa.',
            'capacity.max'         => 'Kapasitas maksimal 40 siswa.',
        ]);

        $section = strtoupper(trim($validated['section']));
        $code = $validated['grade_level'] . $section;
        $name = 'Kelas ' . $code;

        // Cek keunikan kode kelas
        if (Classroom::where('code', $code)->exists()) {
            return back()->withInput()->withErrors(['section' => "Rombel dengan kode {$code} sudah ada di dalam sistem."]);
        }

        $notice = '';
        $teacherName = null;
        $teacherNip = null;

        if (!empty($validated['homeroom_teacher_id'])) {
            $teacherId = (int)$validated['homeroom_teacher_id'];

            // Cek anti-bentrok jika guru sedang memegang rombel lain
            $oldClass = Classroom::where('homeroom_teacher_id', $teacherId)->first();
            if ($oldClass) {
                $oldClass->update([
                    'homeroom_teacher_id' => null,
                    'homeroom_teacher'    => null,
                    'homeroom_nip'        => null,
                ]);
                $notice = " (Catatan: Guru ini dimutasi dari Kelas {$oldClass->code})";
            }

            $teacher = User::find($teacherId);
            $teacherName = $teacher->name;
            $teacherNip = $teacher->nip_nuptk;

            // Update info jabatan di profil guru
            $info = $teacher->additional_info ?? [];
            $info['classroom'] = $code;
            $info['jabatan'] = 'Wali Kelas ' . $code;
            $teacher->additional_info = $info;
            $teacher->save();
        }

        $classroom = Classroom::create([
            'code'                => $code,
            'name'                => $name,
            'grade_level'         => $validated['grade_level'],
            'section'             => $section,
            'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?: null,
            'homeroom_teacher'    => $teacherName,
            'homeroom_nip'        => $teacherNip,
            'room_name'           => $validated['room_name'] ?: 'Ruang ' . $name,
            'capacity'            => $validated['capacity'],
            'total_students'      => 0,
            'academic_year'       => $validated['academic_year'],
            'status'              => $validated['status'],
        ]);

        ActivityLog::record(
            'CREATE_CLASSROOM',
            'Master Rombel',
            "Menambahkan rombel baru: {$classroom->name} ({$classroom->code}, Kapasitas: {$classroom->capacity})"
        );

        return redirect()->route('classrooms.index')
            ->with('success', "Rombel {$classroom->name} berhasil ditambahkan ke direktori akademik!{$notice}");
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

    /**
     * Hapus rombel kelas secara aman (Proteksi: Hanya jika 0 siswa)
     */
    public function destroy(Classroom $classroom)
    {
        $studentCount = $classroom->students()->count();

        // Proteksi: Cegah penghapusan jika masih ada siswa di dalam rombel
        if ($studentCount > 0) {
            return back()->with('error', "Gagal menghapus! Kelas {$classroom->code} masih memiliki {$studentCount} siswa aktif. Harap mutasikan seluruh siswa ke rombel lain terlebih dahulu sebelum menghapus kelas ini.");
        }

        $className = $classroom->name;
        $classCode = $classroom->code;

        // Jika ada guru wali kelas terkait, lepaskan penugasan di profil guru
        if ($classroom->homeroom_teacher_id) {
            $teacher = User::find($classroom->homeroom_teacher_id);
            if ($teacher) {
                $info = $teacher->additional_info ?? [];
                unset($info['classroom']);
                $info['jabatan'] = 'Guru Mapel / Non-Wali';
                $teacher->additional_info = $info;
                $teacher->save();
            }
        }

        $classroom->delete();

        ActivityLog::record(
            'DELETE_CLASSROOM',
            'Master Rombel',
            "Menghapus rombel kelas: {$className} ({$classCode})"
        );

        return redirect()->route('classrooms.index')
            ->with('success', "Rombel {$className} berhasil dihapus dari direktori sekolah!");
    }
}
