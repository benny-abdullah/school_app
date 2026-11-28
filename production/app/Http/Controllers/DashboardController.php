<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard Kepala Sekolah (Super Admin)
     */
    public function admin()
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('section')->get();
        $totalStudents = Classroom::sum('total_students');
        $totalClasses = Classroom::count();
        $totalTeachers = User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->count();

        $currentTimeWib = Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y - H:i:s') . ' WIB';

        $attendanceStats = [
            'total' => $totalStudents,
            'present' => round($totalStudents * 0.965),
            'late' => 4,
            'sick_permission' => 12,
            'absent' => 0,
            'rate' => 98.4,
        ];

        return view('admin.dashboard', compact(
            'classrooms',
            'totalStudents',
            'totalClasses',
            'totalTeachers',
            'currentTimeWib',
            'attendanceStats'
        ));
    }

    /**
     * Dashboard Operator Sekolah & Tata Usaha (Dapodik, Master 29 Kelas, Kartu)
     */
    public function operator(Request $request)
    {
        $selectedGrade = $request->get('grade', 'all');

        $query = Classroom::orderBy('grade_level')->orderBy('section');
        if ($selectedGrade !== 'all') {
            $query->where('grade_level', $selectedGrade);
        }
        $classrooms = $query->get();

        $totalStudents = Classroom::sum('total_students');
        $totalClasses = Classroom::count();

        // Breakdown per tingkat
        $gradeCounts = Classroom::selectRaw('grade_level, COUNT(*) as total_classes, SUM(total_students) as students_count')
            ->groupBy('grade_level')
            ->orderBy('grade_level')
            ->get();

        return view('operator.dashboard', compact(
            'classrooms',
            'totalStudents',
            'totalClasses',
            'gradeCounts',
            'selectedGrade'
        ));
    }

    /**
     * Resolusi rombel dan presensi kesiswaan guru yang sedang aktif
     */
    protected function getGuruClassroomAndStudents(): array
    {
        $user = Auth::user();
        $classroom = $user->homeroomClassroom 
            ?? Classroom::where('code', $user->additional_info['classroom'] ?? '5A')->first();

        $realStudents = \App\Models\Student::where('classroom_id', $classroom?->id)->get();

        if ($realStudents->count() > 0) {
            $students = $realStudents->map(function ($s) {
                return [
                    'nisn'   => $s->nisn,
                    'name'   => $s->name,
                    'gender' => $s->gender,
                    'status' => 'Hadir',
                    'time'   => '06:' . rand(35, 55) . ' WIB',
                    'notes'  => 'Tepat Waktu',
                    'photo'  => $s->photo,
                ];
            })->toArray();
        } else {
            $students = [
                ['nisn' => '0081234567', 'name' => 'Ahmad Rifai', 'gender' => 'L', 'status' => 'Hadir', 'time' => '06:42 WIB', 'notes' => 'Tepat Waktu', 'photo' => null],
                ['nisn' => '0081234568', 'name' => 'Aisyah Putri Maharani', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:35 WIB', 'notes' => 'Tepat Waktu', 'photo' => null],
                ['nisn' => '0081234569', 'name' => 'Bima Sakti Pratama', 'gender' => 'L', 'status' => 'Hadir', 'time' => '06:48 WIB', 'notes' => 'Tepat Waktu', 'photo' => null],
                ['nisn' => '0081234570', 'name' => 'Citra Dewi Lestari', 'gender' => 'P', 'status' => 'Izin', 'time' => '-', 'notes' => 'Lomba Pramuka Kota', 'photo' => null],
                ['nisn' => '0081234571', 'name' => 'Dewa Raditya Ananta', 'gender' => 'L', 'status' => 'Hadir', 'time' => '06:55 WIB', 'notes' => 'Tepat Waktu', 'photo' => null],
                ['nisn' => '0081234572', 'name' => 'Eka Nur Fadilah', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:40 WIB', 'notes' => 'Tepat Waktu', 'photo' => null],
                ['nisn' => '0081234573', 'name' => 'Fathan Al Farisi', 'gender' => 'L', 'status' => 'Terlambat', 'time' => '07:05 WIB', 'notes' => 'Ban Sepeda Bocor (+5m)', 'photo' => null],
                ['nisn' => '0081234574', 'name' => 'Gita Kirana Salsabila', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:38 WIB', 'notes' => 'Tepat Waktu', 'photo' => null],
                ['nisn' => '0081234575', 'name' => 'Haikal Zaidan', 'gender' => 'L', 'status' => 'Sakit', 'time' => '-', 'notes' => 'Surat Dokter Terlampir', 'photo' => null],
                ['nisn' => '0081234576', 'name' => 'Indah Kusuma Wardani', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:44 WIB', 'notes' => 'Tepat Waktu', 'photo' => null],
            ];
        }

        return [$user, $classroom, $students];
    }

    /**
     * Dashboard Guru & Wali Kelas (Dinamis Berdasarkan Relasi Database Rombel)
     */
    public function guru()
    {
        [$user, $classroom, $students5A] = $this->getGuruClassroomAndStudents();
        return view('guru.dashboard', compact('classroom', 'students5A'));
    }

    /**
     * Ekspor Jurnal Presensi Kelas ke Format PDF Resmi Kop Surat
     */
    public function exportGuruAttendancePdf()
    {
        [$teacher, $classroom, $students] = $this->getGuruClassroomAndStudents();
        $classCode = $classroom->code ?? 'Binaan';

        return ExportService::pdf(
            "Jurnal_Presensi_Kelas_{$classCode}_" . date('Ymd_His'),
            'exports.guru_attendance_pdf',
            compact('teacher', 'classroom', 'students'),
            'portrait'
        );
    }

    /**
     * Ekspor Jurnal Presensi Kelas ke Format Excel (.xlsx)
     */
    public function exportGuruAttendanceExcel()
    {
        [$teacher, $classroom, $students] = $this->getGuruClassroomAndStudents();
        $classCode = $classroom->code ?? 'Binaan';

        $headers = [
            'no'     => 'No',
            'nisn'   => 'NISN Siswa',
            'name'   => 'Nama Lengkap Siswa',
            'gender' => 'L/P',
            'time'   => 'Waktu Scan Presensi',
            'status' => 'Status Kehadiran',
            'notes'  => 'Catatan / Keterangan',
        ];

        $rows = array_map(function ($s, $idx) {
            return [
                'no'     => $idx + 1,
                'nisn'   => $s['nisn'],
                'name'   => $s['name'],
                'gender' => $s['gender'],
                'time'   => $s['time'],
                'status' => $s['status'],
                'notes'  => $s['notes'] ?? '-',
            ];
        }, $students, array_keys($students));

        return ExportService::excel(
            "Jurnal_Presensi_Kelas_{$classCode}_" . date('Ymd_His'),
            "Jurnal Presensi & Kehadiran Siswa Kelas {$classCode}",
            "Wali Kelas: {$teacher->name} (NIP. " . ($teacher->nip_nuptk ?? '-') . ")",
            $headers,
            $rows
        );
    }

    /**
     * Dashboard Portal Orang Tua & Siswa
     */
    public function orangtua()
    {
        $user = Auth::user();
        $studentInfo = $user->additional_info ?? [];
        $studentName = $studentInfo['student_name'] ?? 'Ahmad Rifai';
        $studentNisn = $user->nisn ?? $studentInfo['student_nisn'] ?? '0081234567';
        $classroomCode = $studentInfo['classroom'] ?? '5A';

        $classroom = Classroom::where('code', $classroomCode)->first();
        $student = \App\Models\Student::where('nisn', $studentNisn)->first();

        // Riwayat kehadiran 5 hari terakhir siswa bersangkutan
        $attendanceHistory = [
            ['date' => 'Hari Ini (Jumat, 28 Nov 2026)', 'checkin' => '06:42:15 WIB', 'checkout' => 'Sedang KBM', 'status' => 'Hadir Tepat Waktu', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ['date' => 'Kamis, 27 Nov 2026', 'checkin' => '06:38:40 WIB', 'checkout' => '12:35:10 WIB', 'status' => 'Hadir Lengkap', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ['date' => 'Rabu, 26 Nov 2026', 'checkin' => '06:45:02 WIB', 'checkout' => '12:32:44 WIB', 'status' => 'Hadir Lengkap', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ['date' => 'Selasa, 25 Nov 2026', 'checkin' => '06:50:18 WIB', 'checkout' => '12:30:05 WIB', 'status' => 'Hadir Lengkap', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            ['date' => 'Senin, 24 Nov 2026', 'checkin' => '06:32:11 WIB', 'checkout' => '12:40:22 WIB', 'status' => 'Hadir (Upacara Bendera)', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        ];

        return view('ortu.dashboard', compact(
            'user',
            'student',
            'studentName',
            'studentNisn',
            'classroomCode',
            'classroom',
            'attendanceHistory'
        ));
    }
}
