<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiStudentAnalysisService
{
    /**
     * Dapatkan ringkasan metrik kesiswaan dan presensi untuk prompt AI
     */
    public static function getSchoolContextData(): array
    {
        $today = Carbon::today('Asia/Jakarta');
        $totalStudents = Student::where('status', 'active')->count();
        $totalClasses = Classroom::count();

        $todayAttendances = Attendance::today()->with('student.classroom')->get();
        $presentCount = $todayAttendances->where('status', 'Hadir')->count();
        $lateCount = $todayAttendances->where('status', 'Terlambat')->count();
        $gatepassCount = $todayAttendances->where('gate_pass_active', true)->count();

        // Cari siswa yang terlambat hari ini
        $lateStudents = $todayAttendances->where('status', 'Terlambat')->map(function ($att) {
            return [
                'nama'   => $att->student?->name ?? 'Siswa',
                'nisn'   => $att->nisn,
                'kelas'  => $att->student?->classroom?->code ?? '-',
                'level'  => $att->late_level ?? 'T-1',
                'menit'  => $att->late_minutes,
                'alasan' => $att->notes,
            ];
        })->values()->toArray();

        return [
            'total_students' => $totalStudents,
            'total_classes'  => $totalClasses,
            'present_count'  => $presentCount,
            'late_count'     => $lateCount,
            'gatepass_count' => $gatepassCount,
            'attendance_rate'=> $totalStudents > 0 ? round(($presentCount + $lateCount) / $totalStudents * 100, 1) : 0,
            'late_students'  => $lateStudents,
            'tanggal'        => $today->translatedFormat('l, d F Y'),
        ];
    }

    /**
     * Mesin Rekomendasi Pedagogis Rule-Based (Fallback Cerdas Tanpa Kuota / Offline)
     */
    public static function generateRuleBasedAnalysis(string $promptType = 'general', ?array $context = null): string
    {
        $ctx = $context ?? self::getSchoolContextData();
        $rate = $ctx['attendance_rate'];
        $late = $ctx['late_count'];
        $total = $ctx['total_students'];

        switch ($promptType) {
            case 'summary':
                return "Pada hari {$ctx['tanggal']}, tingkat kehadiran siswa SDN Kebonagung mencapai **{$rate}%** dari total {$total} peserta didik. Tercatat sebanyak **{$late} siswa terlambat** dan {$ctx['gatepass_count']} siswa mengajukan Digital Gate Pass. Secara umum iklim kedisiplinan pagi hari terkategori **" . ($rate >= 95 ? "Sangat Baik" : "Perlu Pendampingan") . "**.";

            case 'risk':
                if (empty($ctx['late_students'])) {
                    return "Berdasarkan evaluasi sistem realtime, tidak ditemukan anomali atau siswa dengan keterlambatan berulang hari ini. Seluruh rombel binaan 1A s/d 6E terpantau mematuhi tata tertib jam masuk sekolah.";
                }
                $list = collect($ctx['late_students'])->take(3)->map(fn($s) => "• **{$s['nama']}** (Kelas {$s['kelas']}): Terlambat {$s['level']} (+{$s['menit']} menit)")->implode("\n");
                return "Terdeteksi **{$late} siswa** memerlukan perhatian kedisiplinan hari ini:\n{$list}\n\n**Rekomendasi Pedagogik**: Wali kelas disarankan melakukan dialog empatik saat jam istirahat untuk mengidentifikasi kendala transportasi atau jam tidur siswa di rumah.";

            case 'draft_sp':
                $firstLate = $ctx['late_students'][0] ?? ['nama' => 'Ahmad Rifai', 'kelas' => '5A', 'nisn' => '0081234567'];
                return "📄 **DRAF SURAT PEMBERITAHUAN KEDISIPLINAN (SP-1)**\n\nNomor: 421.2/SP-01/SDN.KBG/" . date('Y') . "\nKepada Yth. Orang Tua / Wali dari: **{$firstLate['nama']}** (NISN: {$firstLate['nisn']})\nKelas: {$firstLate['kelas']} UPTD SDN Kebonagung\n\nDengan hormat, kami menginformasikan catatan keterlambatan tap gerbang ananda. Kami mengharapkan kerja sama Bapak/Ibu untuk mendampingi ananda hadir sebelum pukul 07:00 WIB demi kelancaran kegiatan belajar mengajar.";

            case 'draft_wa':
                $firstLate = $ctx['late_students'][0] ?? ['nama' => 'Ahmad Rifai', 'kelas' => '5A'];
                return "💬 **DRAF PESAN WHATSAPP SANTUN WALI KELAS**\n\n*Assalamu'alaikum Wr. Wb. Selamat siang Bapak/Ibu wali dari Ananda {$firstLate['nama']} (Kelas {$firstLate['kelas']}).*\n\nSemoga Bapak/Ibu sekeluarga senantiasa sehat. Kami dari pihak sekolah izin mengabarkan bahwa ananda hari ini tiba di sekolah dengan catatan keterlambatan gerbang. Mohon bantuan Bapak/Ibu di rumah agar ananda dapat berangkat lebih awal esok pagi ya. Terima kasih banyak atas perhatian dan kerja samanya. 🙏";

            case 'pedagogic':
            default:
                return "💡 **REKOMENDASI PENDEKATAN DISIPLIN POSITIF RAMAH ANAK**\n\n1. **Apresiasi Murid Tepat Waktu**: Berikan stiker bintang atau poin karakter bagi siswa yang konsisten hadir sebelum 06:45 WIB.\n2. **Identifikasi Akar Masalah Keterlambatan**: Pisahkan antara faktor kemalasan dengan faktor eksternal (jarak tempuh, hujan, antar jemput adik).\n3. **Pendekatan Restitusi**: Bimbing siswa memahami pentingnya waktu tanpa rasa malu atau hukuman fisik di depan teman-temannya.";
        }
    }

    /**
     * Panggil Google Gemini 1.5 Flash API untuk analisis cerdas
     */
    public static function askGemini(string $userPrompt, string $promptType = 'custom'): string
    {
        $apiKey = env('GEMINI_API_KEY');
        $ctx = self::getSchoolContextData();

        // Jika API Key belum dikonfigurasi, gunakan mesin rule-based cerdas
        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            return self::generateRuleBasedAnalysis($promptType, $ctx);
        }

        try {
            $systemInstruction = "Anda adalah Asisten Pedagogik & Analisis Kesiswaan AI Resmi untuk UPTD SDN Kebonagung Kota Pasuruan (Jawa Timur). Nada bicara Anda empatik, profesional, ramah pendidik, dan menjunjung tinggi prinsip Disiplin Positif Sekolah Ramah Anak. Konteks sekolah saat ini: Tanggal {$ctx['tanggal']}, Total siswa {$ctx['total_students']}, Tingkat kehadiran {$ctx['attendance_rate']}%, Siswa terlambat {$ctx['late_count']}. Berikan respon ringkas maksimal 3-4 paragraf yang langsung dapat dipraktikkan oleh Kepala Sekolah dan Guru.";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(8)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role'  => 'user',
                        'parts' => [
                            ['text' => "{$systemInstruction}\n\nPermintaan pengguna: {$userPrompt}"],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 600,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!empty($aiText)) {
                    return $aiText;
                }
            }

            Log::warning('Gemini API call returned non-successful status: ' . $response->status());
            return self::generateRuleBasedAnalysis($promptType, $ctx);

        } catch (\Throwable $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return self::generateRuleBasedAnalysis($promptType, $ctx);
        }
    }
}
