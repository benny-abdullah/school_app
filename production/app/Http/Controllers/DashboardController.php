<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
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
        $totalTeachers = 32;

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
     * Dashboard Guru & Wali Kelas (Kelas 5A Binaan)
     */
    public function guru()
    {
        $user = Auth::user();
        $classroomCode = $user->additional_info['classroom'] ?? '5A';
        $classroom = Classroom::where('code', $classroomCode)->first();

        // Data realistis siswa rombel 5A
        $students5A = [
            ['nisn' => '0081234567', 'name' => 'Ahmad Rifai', 'gender' => 'L', 'status' => 'Hadir', 'time' => '06:42 WIB', 'notes' => 'Tepat Waktu'],
            ['nisn' => '0081234568', 'name' => 'Aisyah Putri Maharani', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:35 WIB', 'notes' => 'Tepat Waktu'],
            ['nisn' => '0081234569', 'name' => 'Bima Sakti Pratama', 'gender' => 'L', 'status' => 'Hadir', 'time' => '06:48 WIB', 'notes' => 'Tepat Waktu'],
            ['nisn' => '0081234570', 'name' => 'Citra Dewi Lestari', 'gender' => 'P', 'status' => 'Izin', 'time' => '-', 'notes' => 'Lomba Pramuka Kota'],
            ['nisn' => '0081234571', 'name' => 'Dewa Raditya Ananta', 'gender' => 'L', 'status' => 'Hadir', 'time' => '06:55 WIB', 'notes' => 'Tepat Waktu'],
            ['nisn' => '0081234572', 'name' => 'Eka Nur Fadilah', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:40 WIB', 'notes' => 'Tepat Waktu'],
            ['nisn' => '0081234573', 'name' => 'Fathan Al Farisi', 'gender' => 'L', 'status' => 'Terlambat', 'time' => '07:05 WIB', 'notes' => 'Ban Sepeda Bocor (+5m)'],
            ['nisn' => '0081234574', 'name' => 'Gita Kirana Salsabila', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:38 WIB', 'notes' => 'Tepat Waktu'],
            ['nisn' => '0081234575', 'name' => 'Haikal Zaidan', 'gender' => 'L', 'status' => 'Sakit', 'time' => '-', 'notes' => 'Surat Dokter Terlampir'],
            ['nisn' => '0081234576', 'name' => 'Indah Kusuma Wardani', 'gender' => 'P', 'status' => 'Hadir', 'time' => '06:44 WIB', 'notes' => 'Tepat Waktu'],
        ];

        return view('guru.dashboard', compact('classroom', 'students5A'));
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
