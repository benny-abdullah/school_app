<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Services\AttendanceRuleEngineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerController extends Controller
{
    /**
     * Tampilkan Terminal Meja Scanner Gerbang 3-Mode & Pemindai Barcode/Kamera
     * Dapat diakses oleh Admin, Operator, dan Guru/Wali Kelas
     */
    public function index(Request $request)
    {
        $activeMode = AttendanceRuleEngineService::resolveCurrentMode($request->get('mode'));
        $currentTimeWib = Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y - H:i:s') . ' WIB';

        $totalActiveStudents = Student::where('status', 'active')->count();
        $todayAttendances = Attendance::today()->with('student.classroom', 'operator')->orderBy('updated_at', 'desc')->get();

        $stats = [
            'total_students' => $totalActiveStudents,
            'today_scanned'  => $todayAttendances->count(),
            'today_ontime'   => $todayAttendances->where('status', 'Hadir')->where('late_level', 'T-0')->count(),
            'today_late'     => $todayAttendances->where('status', 'Terlambat')->count(),
            'today_gatepass' => $todayAttendances->where('gate_pass_active', true)->count(),
        ];

        // Daftar rombel untuk filter pencarian manual jika siswa tidak membawa kartu
        $classrooms = Classroom::orderBy('grade_level')->orderBy('section')->get();

        return view('scanner.index', compact('activeMode', 'currentTimeWib', 'todayAttendances', 'stats', 'classrooms'));
    }

    /**
     * Endpoint API & Form Pemrosesan Tap Barcode / Scan QR / Input Manual NISN
     */
    public function scan(Request $request)
    {
        $request->validate([
            'code'   => 'required|string',
            'method' => 'nullable|in:barcode_usb,camera_qr,manual_nisn',
            'mode'   => 'nullable|in:masuk,gatepass,pulang',
        ]);

        $code = trim($request->input('code'));
        $method = $request->input('method', 'barcode_usb');
        $mode = $request->input('mode');
        $gatePassReason = $request->input('gate_pass_reason');
        $operator = Auth::user();

        $result = AttendanceRuleEngineService::processScan($code, $method, $operator, $mode, $gatePassReason);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('scan_result', $result);
        }

        return back()->withErrors(['code' => $result['message']]);
    }

    /**
     * Endpoint JSON Pencarian Cepat Nama / NISN Siswa (Metode Isi Manual Jika Siswa Lupa Kartu)
     */
    public function searchStudents(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $students = Student::with('classroom')
            ->where('status', 'active')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('nisn', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'nisn', 'name', 'gender', 'classroom_id', 'photo']);

        return response()->json($students);
    }
}
