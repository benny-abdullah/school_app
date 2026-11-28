<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;

class AttendanceRuleEngineService
{
    /**
     * Resolusi mode operasional berdasarkan waktu saat ini atau override manual
     * - Mode 1 (masuk): 06:00 - 08:00 WIB
     * - Mode 2 (gatepass): 08:01 - 13:00 WIB
     * - Mode 3 (pulang): 13:01 - 18:00 WIB
     */
    public static function resolveCurrentMode(?string $overrideMode = null): string
    {
        if ($overrideMode && in_array($overrideMode, ['masuk', 'gatepass', 'pulang'])) {
            return $overrideMode;
        }

        $now = Carbon::now('Asia/Jakarta');
        $hour = (int)$now->format('H');

        if ($hour >= 6 && $hour < 8) {
            return 'masuk';
        } elseif ($hour >= 8 && $hour < 13) {
            return 'gatepass';
        } else {
            return 'pulang';
        }
    }

    /**
     * Ekstraksi NISN dari barcode USB gun, URL QR Code, atau input teks bebas
     */
    public static function extractNisn(string $rawInput): ?string
    {
        $clean = trim($rawInput);

        // Kasus 1: Input berupa URL verifikasi (http://.../card/verify/123)
        if (preg_match('/verify\/([0-9]+)/', $clean, $matches)) {
            $student = Student::find($matches[1]);
            return $student?->nisn;
        }

        // Kasus 2: Input berformat STARKIDS:NISN:TOKEN
        if (str_contains($clean, 'STARKIDS:')) {
            $parts = explode(':', $clean);
            if (isset($parts[1]) && strlen($parts[1]) >= 8) {
                return $parts[1];
            }
        }

        // Kasus 3: Input numerik langsung NISN (10 digit standar Dapodik)
        $digitsOnly = preg_replace('/[^0-9]/', '', $clean);
        if (strlen($digitsOnly) >= 5) {
            return $digitsOnly;
        }

        return null;
    }

    /**
     * Mesin Eksekusi Pemindaian Presensi Siswa di Gerbang
     *
     * @param string $rawInput NISN, Barcode string, atau QR URL
     * @param string $method 'barcode_usb', 'camera_qr', atau 'manual_nisn'
     * @param User $operator User petugas (Admin, Operator, atau Guru)
     * @param string|null $mode 'masuk', 'gatepass', 'pulang'
     * @param string|null $gatePassReason Keterangan jika mode Gate Pass
     * @return array Respon terstruktur untuk UI & audio synth
     */
    public static function processScan(
        string $rawInput,
        string $method,
        User $operator,
        ?string $mode = null,
        ?string $gatePassReason = null
    ): array {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s') . ' WIB';
        $activeMode = self::resolveCurrentMode($mode);

        $nisn = self::extractNisn($rawInput);

        // Jika NISN tidak terdeteksi via angka, coba cari berdasarkan nama siswa
        $student = null;
        if ($nisn) {
            $student = Student::with('classroom')->where('nisn', $nisn)->first();
        }

        if (!$student) {
            $student = Student::with('classroom')
                ->where('name', 'like', "%{$rawInput}%")
                ->first();
        }

        if (!$student) {
            return [
                'success' => false,
                'type'    => 'not_found',
                'sound'   => 'buzz_error',
                'message' => "Data siswa dengan kode/nama '{$rawInput}' tidak ditemukan dalam basis data.",
                'student' => null,
            ];
        }

        // Cari atau buat record absensi hari ini
        $attendance = Attendance::firstOrNew([
            'student_id' => $student->id,
            'date'       => $today,
        ]);

        $attendance->nisn = $student->nisn;
        $attendance->operator_id = $operator->id;
        $attendance->method = in_array($method, ['barcode_usb', 'camera_qr', 'manual_nisn']) ? $method : 'barcode_usb';

        // -------------------------------------------------------------
        // EVALUASI MODE 1: MASUK PAGI
        // -------------------------------------------------------------
        if ($activeMode === 'masuk') {
            if ($attendance->exists && !empty($attendance->checkin_time)) {
                return [
                    'success'    => true,
                    'type'       => 'already_checked_in',
                    'sound'      => 'info_chime',
                    'message'    => "Siswa {$student->name} SUDAH TERCATAT HADIR sebelumnya pukul {$attendance->checkin_time}.",
                    'student'    => $student,
                    'attendance' => $attendance,
                    'time'       => $currentTime,
                ];
            }

            $attendance->checkin_time = $currentTime;

            // Evaluasi Jam Masuk (Batas Toleransi Tepat Waktu: 07:00:00 WIB)
            $cutoff = Carbon::today('Asia/Jakarta')->setTime(7, 0, 0);

            if ($now->lessThanOrEqualTo($cutoff)) {
                $attendance->status = 'Hadir';
                $attendance->late_level = 'T-0';
                $attendance->late_minutes = 0;
                $attendance->notes = 'Hadir Tepat Waktu';
                $sound = 'chime_success';
                $statusLabel = 'Hadir Tepat Waktu';
                $statusColor = 'emerald';
            } else {
                $diffMinutes = $cutoff->diffInMinutes($now);
                $attendance->status = 'Terlambat';
                $attendance->late_minutes = $diffMinutes;

                if ($diffMinutes <= 15) {
                    $attendance->late_level = 'T-1';
                    $attendance->notes = "Terlambat T-1 (+{$diffMinutes} menit)";
                } elseif ($diffMinutes <= 30) {
                    $attendance->late_level = 'T-2';
                    $attendance->notes = "Terlambat T-2 (+{$diffMinutes} menit)";
                } else {
                    $attendance->late_level = 'T-3';
                    $attendance->notes = "Terlambat Berat T-3 (+{$diffMinutes} menit)";
                }

                $sound = 'beep_warning';
                $statusLabel = "Terlambat ({$attendance->late_level})";
                $statusColor = 'amber';
            }

            $attendance->save();

            // Catat log aktifitas
            ActivityLog::record(
                'SCAN_GATE_IN',
                'Presensi Gerbang',
                "Presensi Masuk Siswa: {$student->name} (Kelas {$student->classroom?->code}) - Status: {$statusLabel} ({$method})",
                ['student_id' => $student->id, 'nisn' => $student->nisn, 'method' => $method],
                $operator
            );

            return [
                'success'      => true,
                'type'         => 'checkin_success',
                'sound'        => $sound,
                'message'      => "Presensi masuk berhasil dicatat: {$student->name} ({$statusLabel})",
                'student'      => $student,
                'attendance'   => $attendance,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'time'         => $currentTime,
            ];
        }

        // -------------------------------------------------------------
        // EVALUASI MODE 2: SIAGA DIGITAL GATE PASS
        // -------------------------------------------------------------
        if ($activeMode === 'gatepass') {
            $attendance->gate_pass_active = true;
            $attendance->gate_pass_time = $currentTime;
            $attendance->gate_pass_reason = $gatePassReason ?: 'Izin keluar darurat / dijemput orang tua';

            if (empty($attendance->checkin_time)) {
                $attendance->checkin_time = '07:00:00 WIB (Otomatis)';
                $attendance->status = 'Izin';
            }

            $attendance->save();

            ActivityLog::record(
                'SCAN_GATE_PASS',
                'Presensi Gerbang',
                "Penerbitan Digital Gate Pass Keluar: {$student->name} (Alasan: {$attendance->gate_pass_reason})",
                ['student_id' => $student->id, 'nisn' => $student->nisn],
                $operator
            );

            return [
                'success'      => true,
                'type'         => 'gatepass_success',
                'sound'        => 'chime_pass',
                'message'      => "Digital Gate Pass Diterbitkan: {$student->name} diizinkan keluar sekolah.",
                'student'      => $student,
                'attendance'   => $attendance,
                'status_label' => 'Gate Pass Aktif',
                'status_color' => 'blue',
                'time'         => $currentTime,
            ];
        }

        // -------------------------------------------------------------
        // EVALUASI MODE 3: PULANG SORE
        // -------------------------------------------------------------
        if ($activeMode === 'pulang') {
            $attendance->checkout_time = $currentTime;

            // Jika siswa belum tap pagi, tetap dicatat hadir pulang agar tidak alpa sepihak
            if (empty($attendance->checkin_time)) {
                $attendance->checkin_time = '07:00:00 WIB (KBM)';
                $attendance->status = 'Hadir';
                $attendance->notes = 'Tap Pulang (Hadir KBM)';
            }

            $attendance->save();

            ActivityLog::record(
                'SCAN_GATE_OUT',
                'Presensi Gerbang',
                "Presensi Pulang Siswa: {$student->name} (Kelas {$student->classroom?->code}) - Waktu: {$currentTime}",
                ['student_id' => $student->id, 'nisn' => $student->nisn],
                $operator
            );

            return [
                'success'      => true,
                'type'         => 'checkout_success',
                'sound'        => 'chime_bye',
                'message'      => "Presensi pulang berhasil: {$student->name} tercatat check-out pukul {$currentTime}.",
                'student'      => $student,
                'attendance'   => $attendance,
                'status_label' => 'Pulang Sekolah',
                'status_color' => 'purple',
                'time'         => $currentTime,
            ];
        }

        return [
            'success' => false,
            'type'    => 'unknown_mode',
            'sound'   => 'buzz_error',
            'message' => 'Mode waktu presensi tidak valid.',
            'student' => null,
        ];
    }
}
