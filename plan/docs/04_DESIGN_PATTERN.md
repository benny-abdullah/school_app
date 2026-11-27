# Pola Desain Perangkat Lunak (Design Patterns) - SSOT
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Bahasa & Runtime**: PHP 8.4 / Laravel 13 (Livewire 3 + Eloquent + Reverb + Redis)  
**Cakupan**: Pola Desain Backend, Keamanan Kriptografi, 3-Mode Scanner & Keadilan Absensi

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh pola desain arsitektur perangkat lunak (*software design patterns*) yang diterapkan dalam sistem EduTrack Pro menggunakan ekosistem **Laravel 13 (PHP 8.4)**.

---

## 1. Pola Mesin Pemindai 3 Mode Otomatis (*3-Mode Auto-Switch Scanner Pattern*)

### Masalah Lapangan
Petugas satpam di pos gerbang tidak boleh dibebani memilih tombol masuk atau pulang secara manual saat ratusan siswa berdatangan di pagi hari atau berhamburan pulang di sore hari.

### Penerapan Pola Mesin Aturan Waktu Nyata
Mesin backend mengevaluasi jam server (*wall-clock time*) secara otomatis untuk menentukan mode operasional menggunakan PHP 8.4 Match Expression dan DTO:

```php
<?php

namespace App\Services;

use Carbon\Carbon;

enum ScanMode: string {
    case CheckIn = 'check_in';
    case GatePass = 'gate_pass';
    case CheckOut = 'check_out';
}

enum AudioCue: string {
    case Beep880 = 'beep_880';
    case Buzz180 = 'buzz_180';
    case Chime3Tone = 'chime_3tone';
    case ErrorDoubleBuzz = 'error_double_buzz';
}

readonly class ScanEvaluationResult
{
    public function __construct(
        public ScanMode $mode,
        public string $status,
        public int $pointsDeducted,
        public AudioCue $audioCue,
        public string $instruction,
        public ?string $lateCategory = null,
        public ?array $waNotificationPayload = null,
    ) {}
}

class AttendanceRuleEngineService
{
    public static function evaluateScan(Carbon $currentTime, string $studentNisn): ScanEvaluationResult
    {
        $timeString = $currentTime->format('H:i');

        // JENDELA 1: PRESENSI MASUK PAGI (06:00 - 08:00 WIB)
        if ($timeString >= '06:00' && $timeString <= '08:00') {
            return match (true) {
                $timeString <= '07:00' => new ScanEvaluationResult(
                    mode: ScanMode::CheckIn,
                    status: 'Hadir',
                    pointsDeducted: 0,
                    audioCue: AudioCue::Beep880,
                    instruction: 'Siswa hadir tepat waktu. Silakan masuk kelas untuk literasi pagi.'
                ),
                $timeString <= '07:15' => new ScanEvaluationResult(
                    mode: ScanMode::CheckIn,
                    status: 'Terlambat',
                    pointsDeducted: 2,
                    audioCue: AudioCue::Buzz180,
                    instruction: 'Terlambat Ringan T-1: Tercatat -2 poin tatib. Diberikan dispensasi masuk kelas.',
                    lateCategory: 'T-1'
                ),
                $timeString <= '07:30' => new ScanEvaluationResult(
                    mode: ScanMode::CheckIn,
                    status: 'Terlambat',
                    pointsDeducted: 5,
                    audioCue: AudioCue::Buzz180,
                    instruction: 'Terlambat Sedang T-2: Tercatat -5 poin tatib. Pembinaan di pos piket/literasi.',
                    lateCategory: 'T-2'
                ),
                default => new ScanEvaluationResult(
                    mode: ScanMode::CheckIn,
                    status: 'Terlambat',
                    pointsDeducted: 10,
                    audioCue: AudioCue::Buzz180,
                    instruction: 'Terlambat Berat T-3: Tercatat -10 poin tatib. Diserahkan ke Guru BK & panggilan orang tua.',
                    lateCategory: 'T-3'
                ),
            };
        }

        // JENDELA 2: SIAGA DIGITAL GATE PASS (08:01 - 13:00 WIB)
        if ($timeString > '08:00' && $timeString <= '13:00') {
            return new ScanEvaluationResult(
                mode: ScanMode::GatePass,
                status: 'REJECTED',
                pointsDeducted: 0,
                audioCue: AudioCue::ErrorDoubleBuzz,
                instruction: 'Gerbang terkunci. Jam KBM aktif. Pemindaian kartu ditolak! Gunakan QR Tiket Digital Gate Pass.'
            );
        }

        // JENDELA 3: PRESENSI PULANG SORE (13:01 - 17:00 WIB)
        return new ScanEvaluationResult(
            mode: ScanMode::CheckOut,
            status: 'Pulang_Recorded',
            pointsDeducted: 0,
            audioCue: AudioCue::Chime3Tone,
            instruction: 'Presensi kepulangan berhasil dicatat. Selamat pulang dan hati-hati di jalan!'
        );
    }
}
```

---

## 2. Pola Keadilan Absensi Kepulangan (*Justice-Preserving Checkout Fallback Pattern*)

### Masalah Lapangan
Siswa usia sekolah dasar (SD) seringkali berebut keluar gerbang saat bel pulang berbunyi dan lupa men-tap kartu pada scanner. Siswa **TIDAK BOLEH** dihukum Alpa jika mereka nyata-nyata hadir belajar sepanjang hari di kelas.

### Penerapan Pola Fallback Relasional Eloquent
Sistem menjalankan scheduler harian Laravel 13 pukul 17:00 WIB via artisan command `attendance:reconcile-missed-checkouts` yang membandingkan presensi KBM jam pelajaran terakhir:

```php
<?php

namespace App\Services;

use App\Models\AttendanceDaily;
use App\Models\AttendanceSubject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckoutJusticeService
{
    public static function reconcileMissedCheckouts(string $todayDate): int
    {
        // 1. Ambil seluruh record presensi siswa yang check-in pagi tapi belum tap pulang
        $unclosedAttendances = AttendanceDaily::where('date', $todayDate)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time')
            ->get();

        $reconciledCount = 0;

        foreach ($unclosedAttendances as $attendance) {
            // 2. Periksa apakah siswa hadir pada presensi KBM jam mapel terakhir
            $lastSubjectSession = AttendanceSubject::where('student_id', $attendance->student_id)
                ->where('date', $todayDate)
                ->where('status', 'Hadir')
                ->latest('id')
                ->first();

            if ($lastSubjectSession) {
                // 3. KEADILAN: Tetap Hadir, tidak dihukum Alpa!
                $attendance->update([
                    'checkout_status' => 'Check-out Default (Selesai KBM)',
                    'is_checkout_missed' => true,
                    'notes' => 'Siswa hadir di KBM sesi terakhir; tidak tap kartu saat pulang.',
                ]);

                $reconciledCount++;
            }
        }

        Log::info("Checkout Justice: Rekonsiliasi selesai untuk {$reconciledCount} siswa pada tanggal {$todayDate}");
        return $reconciledCount;
    }
}
```

---

## 3. Pola Alur Izin Sakit Fleksibel (*Flexible Leave Workflow Pattern*)

### Masalah Lapangan
Mewajibkan surat dokter untuk sakit demam 1 hari sangat memberatkan orang tua dan memicu sanksi Auto-Alpa keliru.

### Penerapan Pola Validasi & Kuota
Laravel Form Request & Domain Service memvalidasi tipe pengajuan izin:

```php
<?php

namespace App\Services;

use InvalidArgumentException;

class LeaveRequestValidatorService
{
    public static function validate(string $type, int $semesterQuotaUsed, bool $hasDoctorCert): array
    {
        if ($type === 'Sakit 1 Hari (Surat Izin Ortu)') {
            if ($semesterQuotaUsed >= 2) {
                throw new InvalidArgumentException(
                    'Batas kuota izin sakit mandiri tanpa surat dokter (maks 2x per semester) telah habis. Wajib melampirkan surat dokter.'
                );
            }
            return ['valid' => true, 'requires_doctor_cert' => false];
        }

        if ($type === 'Sakit >1 Hari (Surat Dokter Terlampir)') {
            if (!$hasDoctorCert) {
                throw new InvalidArgumentException(
                    'Sakit lebih dari 1 hari wajib melampirkan foto surat dokter / Puskesmas / klinik resmi.'
                );
            }
            return ['valid' => true, 'requires_doctor_cert' => true];
        }

        return ['valid' => true, 'requires_doctor_cert' => false];
    }
}
```

---

## 4. Pola Tanda Tangan Kriptografis Barcode Kartu Pelajar (*HMAC-SHA256 Token Pattern*)

Mencegah pemalsuan barcode dan titip absen menggunakan PHP Native Hash Cryptography:

```php
<?php

namespace App\Services;

class BarcodeCryptoService
{
    private static function getSecret(): string
    {
        return config('app.card_secret_key', config('app.key'));
    }

    public static function signPayload(string $nisn, string $cardUid): string
    {
        $raw = "EDUTRACK:{$nisn}:{$cardUid}";
        $hash = substr(hash_hmac('sha256', $raw, self::getSecret()), 0, 16);
        return "{$raw}:{$hash}";
    }

    public static function verifyPayload(string $payload): bool
    {
        $parts = explode(':', $payload);
        if (count($parts) !== 4 || $parts[0] !== 'EDUTRACK') {
            return false;
        }

        $expectedRaw = "EDUTRACK:{$parts[1]}:{$parts[2]}";
        $expectedHash = substr(hash_hmac('sha256', $expectedRaw, self::getSecret()), 0, 16);

        return hash_equals($expectedHash, $parts[3]);
    }
}
```

---

## 5. Pola Enkripsi Kolom Sensitif (*Field-Level AES-256 Pattern*)

Melindungi privasi aduan korban perundungan (*whistleblower*) di database PostgreSQL dengan memanfaatkan `Illuminate\Support\Facades\Crypt` atau Eloquent Encrypted Casts:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Complaint extends Model
{
    use HasUuids;

    protected $fillable = [
        'ticket_number',
        'student_id',
        'parent_id',
        'category',
        'title',
        'description_encrypted',
        'is_anonymous',
        'reporter_display_name',
        'status',
        'assigned_to',
    ];

    /**
     * Eloquent Automatic Attribute Encryption (AES-256-CBC / AES-256-GCM)
     */
    protected function casts(): array
    {
        return [
            'description_encrypted' => 'encrypted',
            'is_anonymous' => 'boolean',
        ];
    }
}
```

Dan untuk pesan investigasi chat dua arah:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'complaint_id',
        'sender_type',
        'sender_name',
        'message_encrypted',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'message_encrypted' => 'encrypted',
            'sent_at' => 'datetime',
        ];
    }
}
```

---

## 6. Pola Jejak Audit Kekal ISO 27001 (*Append-Only Immutable Audit Log Pattern*)

Tabel `audit_logs` dilindungi di level trigger PostgreSQL untuk memblokir aksi `UPDATE` atau `DELETE` bahkan dari akun superuser sekalipun:

```sql
CREATE OR REPLACE FUNCTION block_audit_log_alteration()
RETURNS TRIGGER AS $$
BEGIN
    RAISE EXCEPTION 'Pelanggaran ISO/IEC 27001 (A.12.4): Log Audit bersifat IMMUTABLE dan dilarang dimodifikasi atau dihapus!';
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_protect_audit_logs
BEFORE UPDATE OR DELETE ON audit_logs
FOR EACH ROW EXECUTE FUNCTION block_audit_log_alteration();
```
