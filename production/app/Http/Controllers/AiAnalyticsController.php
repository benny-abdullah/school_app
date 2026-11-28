<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Services\AiStudentAnalysisService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAnalyticsController extends Controller
{
    /**
     * Tampilkan Halaman Khusus Mandiri Analisis Data AI & Kesiswaan
     * Hak akses: Admin (Kepala Sekolah), Operator, dan Guru / Wali Kelas
     */
    public function index()
    {
        $context = AiStudentAnalysisService::getSchoolContextData();

        // Rekap kehadiran per rombel kelas hari ini
        $classrooms = Classroom::withCount(['students as total_students' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('grade_level')->orderBy('section')->get();

        $todayAttendances = Attendance::today()->with('student')->get();

        $rombelStats = $classrooms->map(function ($c) use ($todayAttendances) {
            $classAtts = $todayAttendances->filter(fn($a) => $a->student?->classroom_id === $c->id);
            $present = $classAtts->where('status', 'Hadir')->count();
            $late = $classAtts->where('status', 'Terlambat')->count();
            $total = $c->total_students;
            $percent = $total > 0 ? round(($present + $late) / $total * 100) : 0;

            return [
                'id'       => $c->id,
                'code'     => $c->code,
                'name'     => $c->name,
                'total'    => $total,
                'present'  => $present,
                'late'     => $late,
                'percent'  => $percent,
            ];
        });

        // Daftar siswa berisiko (terlambat / perlu perhatian)
        $riskStudents = Attendance::today()
            ->where('status', 'Terlambat')
            ->with('student.classroom')
            ->orderBy('late_minutes', 'desc')
            ->limit(10)
            ->get();

        // Konfigurasi API Key (Khusus Admin / Operator)
        $rawApiKey = AppSetting::get('gemini_api_key', env('GEMINI_API_KEY', ''));
        $maskedApiKey = '';
        if (!empty($rawApiKey) && $rawApiKey !== 'your_gemini_api_key_here') {
            $maskedApiKey = substr($rawApiKey, 0, 7) . '••••••••••••••••' . substr($rawApiKey, -4);
        }

        $aiConfig = [
            'raw_api_key'    => $rawApiKey,
            'masked_api_key' => $maskedApiKey,
            'has_key'        => !empty($rawApiKey) && $rawApiKey !== 'your_gemini_api_key_here',
            'model'          => AppSetting::get('gemini_model', 'gemini-1.5-flash'),
            'status'         => AppSetting::get('gemini_status', 'standby'),
            'last_tested'    => AppSetting::get('gemini_last_tested_at', null),
        ];

        return view('ai_analytics.index', compact('context', 'rombelStats', 'riskStudents', 'aiConfig'));
    }

    /**
     * Endpoint Tanya Jawab Asisten AI Pedagogik & Konsultasi Kesiswaan
     */
    public function chat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'type'   => 'nullable|string',
        ]);

        $prompt = trim($request->input('prompt'));
        $type = $request->input('type', 'custom');

        $activeKey = AiStudentAnalysisService::getActiveApiKey();
        $model = AiStudentAnalysisService::getActiveModel();

        $reply = AiStudentAnalysisService::askGemini($prompt, $type);

        return response()->json([
            'success'   => true,
            'reply'     => $reply,
            'model'     => !empty($activeKey) ? "Google {$model}" : 'STAR-KIDS Rule Engine',
            'timestamp' => Carbon::now('Asia/Jakarta')->format('H:i:s'),
        ]);
    }

    /**
     * Endpoint Uji Koneksi API Key Google Gemini (1-Klik Test Connection)
     */
    public function testConnection(Request $request)
    {
        // Hanya Admin & Operator yang boleh menguji koneksi API
        if (!Auth::user()->isAdmin() && !Auth::user()->isOperator()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $apiKey = $request->input('api_key');
        $model = $request->input('model');

        $result = AiStudentAnalysisService::testApiConnection($apiKey, $model);

        return response()->json($result);
    }

    /**
     * Endpoint Simpan Konfigurasi API Key Google Gemini
     */
    public function saveSettings(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isOperator()) {
            return back()->with('error', 'Hanya Administrator yang berhak mengubah konfigurasi API AI.');
        }

        $request->validate([
            'gemini_api_key' => 'nullable|string',
            'gemini_model'   => 'required|in:gemini-1.5-flash,gemini-1.5-pro',
        ]);

        $apiKey = trim($request->input('gemini_api_key', ''));
        $model = $request->input('gemini_model', 'gemini-1.5-flash');

        if (!empty($apiKey)) {
            AppSetting::set('gemini_api_key', $apiKey, 'ai', 'Google Gemini API Key');
        }

        AppSetting::set('gemini_model', $model, 'ai', 'Model AI Google Gemini');

        ActivityLog::record(
            'UPDATE_AI_CONFIG',
            'Pengaturan AI',
            'Memperbarui konfigurasi Google Gemini API Key & Model ' . $model,
            null,
            Auth::user()
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi Google Gemini API Key berhasil disimpan!',
            ]);
        }

        return back()->with('success', 'Konfigurasi Google Gemini API Key berhasil disimpan!');
    }
}
