# Pola Desain Perangkat Lunak (Design Patterns) - SSOT
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Cakupan**: Pola Desain Backend, Keamanan Kriptografi, Aliran Event Asinkron & Isolasi Data

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh pola desain arsitektur perangkat lunak (*software design patterns*) yang diterapkan dalam sistem EduTrack Pro guna menjamin performa tinggi, keandalan presensi gerbang, dan kepatuhan ISO/IEC 27001.

---

## 1. Pola Mesin Aturan Keterlambatan Berjenjang (*Attendance Rule Engine Pattern*)

### Masalah Lapangan
Pencatatan presensi sekolah tidak sekadar binary (Hadir vs Tidak Hadir). Terdapat aturan keterlambatan berjenjang yang memiliki konsekuensi pembinaan dan poin pelanggaran berbeda, serta jendela waktu cutoff otomatis.

### Penerapan Pola Mesin Aturan (Strategy & Chain of Responsibility)
Logika evaluasi waktu didekapsulasi ke dalam kelas layanan mandiri `AttendanceRuleEngineService`:

```php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Student;
use App\Models\DailyAttendance;

class AttendanceRuleEngineService
{
    public function evaluateScan(Student $student, Carbon $scanTime): AttendanceEvaluationResult
    {
        // 1. Anti-Double Tap Check (< 5 menit)
        $existing = DailyAttendance::where('student_id', $student->id)
            ->whereDate('date', $scanTime->toDateString())
            ->first();

        if ($existing && $existing->check_in_time) {
            $lastScan = Carbon::parse($existing->date->format('Y-m-d') . ' ' . $existing->check_in_time);
            if ($scanTime->diffInMinutes($lastScan) < 5) {
                return new AttendanceEvaluationResult(
                    status: 'REJECTED_COOLDOWN',
                    message: "Siswa sudah tercatat hadir pada {$existing->check_in_time}"
                );
            }
        }

        // 2. Evaluasi Jendela Waktu
        $hour = $scanTime->format('H:i');

        if ($hour <= '07:00') {
            return new AttendanceEvaluationResult(
                status: 'Hadir',
                category: null,
                points: 0,
                instruction: 'Siswa langsung menuju kelas untuk literasi pagi.',
                audioCue: 'success_bell'
            );
        } elseif ($hour <= '07:15') {
            return new AttendanceEvaluationResult(
                status: 'Terlambat',
                category: 'T-1',
                points: -2,
                instruction: 'Terlambat Ringan T-1: Dispensasi masuk kelas, tercatat -2 poin tatib.',
                audioCue: 'warning_t1'
            );
        } elseif ($hour <= '07:30') {
            return new AttendanceEvaluationResult(
                status: 'Terlambat',
                category: 'T-2',
                points: -5,
                instruction: 'Terlambat Sedang T-2: Pembinaan di pos piket / literasi.',
                audioCue: 'warning_t2'
            );
        } else {
            return new AttendanceEvaluationResult(
                status: 'Terlambat',
                category: 'T-3',
                points: -10,
                instruction: 'Terlambat Berat T-3: Penyerahan ke Guru BK & pemanggilan orang tua.',
                audioCue: 'danger_buzz'
            );
        }
    }
}
```

---

## 2. Pola Event-Driven & Antrean Asinkron (*Observer & Queue Pattern*)

### Masalah Lapangan
Operator gerbang harus dapat memindai kartu siswa berikutnya dalam $\le 300\text{ ms}$. Jika pengiriman pesan WhatsApp ke orang tua (yang membutuhkan waktu $1 - 2\text{ detik}$) dieksekusi secara sinkron, antrean ratusan siswa di gerbang akan macet parah.

### Penerapan Pola
Setiap scan berhasil langsung menghasilkan event `AttendanceRecordedEvent`. Listener `QueueWhatsAppNotification` mendorong payload ke **Redis Queue Worker** untuk diproses di latar belakang secara asinkron tanpa memblokir respon HTTP pemindai gerbang.

```
+------------------+         +-------------------------------+
|  SCANNER GERBANG | ------> |  POST /api/attendance/gate-scan|
+------------------+         +-------------------------------+
                                            |
                                            v (Simpan DB < 50ms)
                               +-----------------------------+
                               |  Event: AttendanceRecorded  |
                               +-----------------------------+
                                            |
                     +----------------------+----------------------+
                     | (Respon Cepat 200 OK)                       | (Push Job Asinkron)
                     v                                             v
           +--------------------+                       +--------------------+
           |  Layari Monitor POS|                       | Redis Queue Worker |
           |  (Umpan Balik Audio)|                      +--------------------+
           +--------------------+                                  |
                                                                   v
                                                        +--------------------+
                                                        | WhatsApp API Gateway|
                                                        | (Kirim WA ke Ortu) |
                                                        +--------------------+
```

---

## 3. Pola Tanda Tangan Kriptografis Barcode Kartu Pelajar (*HMAC Token Pattern*)

### Masalah Lapangan
Siswa memfotokopi barcode atau mengirimkan foto barcode via smartphone kepada temannya agar diabsenkan (praktik titip absen).

### Penerapan Pola (ISO 27001 Kontrol Kriptografi A.10)
Sistem menggunakan tanda tangan digital **HMAC-SHA256** yang menggabungkan kunci rahasia server (*server secret*), NISN, dan UUID fisik kartu:

```php
namespace App\Services;

class BarcodeSecurityCryptoService
{
    public function generateSignedQrPayload(string $nisn, string $cardUuid): string
    {
        $serverSecret = config('app.card_signature_key');
        $rawPayload = "EDUTRACK:{$nisn}:{$cardUuid}";
        $signature = hash_hmac('sha256', $rawPayload, $serverSecret);
        
        // Output format: EDUTRACK:0081234567:CRD-88912-X:a3b8f1...
        return "{$rawPayload}:" . substr($signature, 0, 16);
    }

    public function verifyQrPayload(string $qrContent): bool
    {
        $parts = explode(':', $qrContent);
        if (count($parts) !== 4 || $parts[0] !== 'EDUTRACK') {
            return false;
        }

        $expectedPayload = "EDUTRACK:{$parts[1]}:{$parts[2]}";
        $expectedSignature = substr(hash_hmac('sha256', $expectedPayload, config('app.card_signature_key')), 0, 16);

        return hash_equals($expectedSignature, $parts[3]);
    }
}
```

---

## 4. Pola Enkripsi Kolom Sensitif (*Field-Level Encryption Pattern*)

### Masalah Lapangan
Sesuai kode etik konseling BK dan UU Perlindungan Data Pribadi (UU PDP), catatan investigasi perundungan, riwayat pelecehan, dan data keluarga tidak boleh bisa dibaca secara terang (*plain-text*) langsung melalui database dump atau oleh staf IT biasa.

### Penerapan Pola (Eloquent Encrypted Casts)
Menggunakan cipher **AES-256-GCM** yang terintegrasi pada model Eloquent:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $casts = [
        'description_encrypted' => 'encrypted', // Otomatis AES-256-GCM saat save/get
        'is_anonymous'          => 'boolean',
    ];
}

class ComplaintMessage extends Model
{
    protected $casts = [
        'message_encrypted'     => 'encrypted', // Terenkripsi di level baris DB
    ];
}
```

---

## 5. Pola Isolasi Cakupan Data Sisi Server (*Server-Side Data Scoping Policy Pattern*)

### Masalah Lapangan
Pada prototipe mockup, isolasi data hanya terjadi di browser (computed properties Vue). Untuk lingkungan produksi, isolasi data **wajib ditegakkan di sisi Server / Database Query Builder** agar data murid kelas lain atau data anak orang lain tidak bocor melalui manipulasi API.

### Penerapan Pola (Global Query Scopes & Policies)

```php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RoleDataIsolationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        if (!$user) return;

        // 1. Super Admin & Guru BK melihat seluruh data sekolah
        if (in_array($user->role, ['admin', 'bk'])) {
            return;
        }

        // 2. Wali Kelas dibatasi hanya pada kelas binaannya
        if ($user->role === 'wali_kelas') {
            $classroomCode = $user->data_scope_target; // e.g. 'X-RPL-1'
            $builder->whereHas('classroom', function ($q) use ($classroomCode) {
                $q->where('code', $classroomCode);
            });
            return;
        }

        // 3. Orang Tua dibatasi hanya pada anak asuhnya
        if ($user->role === 'orang_tua') {
            $builder->whereHas('parents', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return;
        }

        // 4. Siswa dibatasi hanya pada dirinya sendiri
        if ($user->role === 'siswa') {
            $builder->where('user_id', $user->id);
            return;
        }
    }
}
```

---

## 6. Pola Jejak Audit Kekal ISO 27001 (*Append-Only Immutable Audit Trail Pattern*)

### Masalah Lapangan
Standar ISO/IEC 27001 (Kontrol A.12.4) mewajibkan bahwa log audit mutasi data kesiswaan (*Who, What, When, IP Address*) tidak boleh dapat diedit atau dihapus oleh siapapun, termasuk oleh Administrator sekolah.

### Penerapan Pola
Tabel `audit_logs` hanya mengizinkan operasi `INSERT` dan `SELECT`. Operasi `UPDATE` dan `DELETE` diblokir secara fisik menggunakan trigger PostgreSQL:

```sql
-- Trigger Perlindungan Log Audit Kekal (Immutable)
CREATE OR REPLACE FUNCTION prevent_audit_log_modification()
RETURNS TRIGGER AS $$
BEGIN
    RAISE EXCEPTION 'Pelanggaran Kepatuhan ISO 27001: Log Audit bersifat IMMUTABLE dan dilarang dimodifikasi atau dihapus!';
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_protect_audit_logs
BEFORE UPDATE OR DELETE ON audit_logs
FOR EACH ROW EXECUTE FUNCTION prevent_audit_log_modification();
```

---

## 7. Pola One-Card One-Active (*Single Active Credential Pattern*)

### Masalah Lapangan
Jika kartu pelajar hilang dan ditemukan orang lain, kartu lama berpotensi disalahgunakan untuk presensi liar.

### Penerapan Pola
Setiap siswa hanya boleh memiliki tepat **1 kartu berstatus Aktif**. Ketika tombol *"Revoke & Regenerate"* diklik oleh Guru BK:
1. Kartu lama seketika diberi status `'Dicabut (Revoked)'` dengan `revoked_at = NOW()`.
2. Dibuatkan record kartu baru dengan `card_uid` acak baru.
3. Mesin pemindai gerbang mengecek tabel `student_cards` — jika kartu yang di-tap berstatus Revoked, scanner langsung membunyikan nada buzz error dan menolak presensi.