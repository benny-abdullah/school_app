# Pola Desain Perangkat Lunak (Design Patterns) - SSOT
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Bahasa & Runtime**: JavaScript / TypeScript (Node.js 20+ LTS / Nuxt 3 Engine)  
**Cakupan**: Pola Desain Backend, Keamanan Kriptografi, 3-Mode Scanner & Keadilan Absensi

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh pola desain arsitektur perangkat lunak (*software design patterns*) yang diterapkan dalam sistem EduTrack Pro menggunakan ekosistem JavaScript/TypeScript.

---

## 1. Pola Mesin Pemindai 3 Mode Otomatis (*3-Mode Auto-Switch Scanner Pattern*)

### Masalah Lapangan
Petugas satpam di pos gerbang tidak boleh dibebani memilih tombol masuk atau pulang secara manual saat ratusan siswa berdatangan di pagi hari atau berhamburan pulang di sore hari.

### Penerapan Pola Mesin Aturan Waktu Nyata
Mesin backend mengevaluasi jam server (*wall-clock time*) secara otomatis untuk menentukan mode operasional:

```typescript
// server/services/AttendanceRuleEngineService.ts
export interface ScanEvaluationResult {
  mode: 'check_in' | 'gate_pass' | 'check_out';
  status: 'Hadir' | 'Terlambat' | 'GatePass_Verified' | 'Pulang_Recorded' | 'REJECTED';
  lateCategory?: 'T-1' | 'T-2' | 'T-3';
  pointsDeducted: number;
  audioCue: 'beep_880' | 'buzz_180' | 'chime_3tone' | 'error_double_buzz';
  instruction: string;
  waNotificationPayload?: object;
}

export class AttendanceRuleEngineService {
  static evaluateScan(currentTime: Date, studentNisn: string): ScanEvaluationResult {
    const hoursMinutes = currentTime.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });

    // JENDELA 1: PRESENSI MASUK (06:00 - 08:00 WIB)
    if (hoursMinutes >= '06:00' && hoursMinutes <= '08:00') {
      if (hoursMinutes <= '07:00') {
        return {
          mode: 'check_in',
          status: 'Hadir',
          pointsDeducted: 0,
          audioCue: 'beep_880',
          instruction: 'Siswa hadir tepat waktu. Silakan masuk kelas untuk literasi pagi.'
        };
      } else if (hoursMinutes <= '07:15') {
        return {
          mode: 'check_in',
          status: 'Terlambat',
          lateCategory: 'T-1',
          pointsDeducted: 2,
          audioCue: 'buzz_180',
          instruction: 'Terlambat Ringan T-1: Tercatat -2 poin tatib. Diberikan dispensasi masuk kelas.'
        };
      } else if (hoursMinutes <= '07:30') {
        return {
          mode: 'check_in',
          status: 'Terlambat',
          lateCategory: 'T-2',
          pointsDeducted: 5,
          audioCue: 'buzz_180',
          instruction: 'Terlambat Sedang T-2: Tercatat -5 poin tatib. Pembinaan di pos piket/literasi.'
        };
      } else {
        return {
          mode: 'check_in',
          status: 'Terlambat',
          lateCategory: 'T-3',
          pointsDeducted: 10,
          audioCue: 'buzz_180',
          instruction: 'Terlambat Berat T-3: Tercatat -10 poin tatib. Diserahkan ke Guru BK & panggilan orang tua.'
        };
      }
    }

    // JENDELA 2: SIAGA DIGITAL GATE PASS (08:01 - 13:00 WIB)
    if (hoursMinutes > '08:00' && hoursMinutes <= '13:00') {
      return {
        mode: 'gate_pass',
        status: 'REJECTED',
        pointsDeducted: 0,
        audioCue: 'error_double_buzz',
        instruction: 'Gerbang terkunci. Jam KBM aktif. Pemindaian hanya menerima QR Tiket Digital Gate Pass!'
      };
    }

    // JENDELA 3: PRESENSI PULANG SORE (13:01 - 17:00 WIB)
    return {
      mode: 'check_out',
      status: 'Pulang_Recorded',
      pointsDeducted: 0,
      audioCue: 'chime_3tone',
      instruction: 'Presensi kepulangan berhasil dicatat. Selamat pulang dan hati-hati di jalan!'
    };
  }
}
```

---

## 2. Pola Keadilan Absensi Kepulangan (*Justice-Preserving Checkout Fallback Pattern*)

### Masalah Lapangan
Siswa usia sekolah dasar (SD) seringkali berebut keluar gerbang saat bel pulang berbunyi dan lupa men-tap kartu pada scanner. Siswa **TIDAK BOLEH** dihukum Alpa jika mereka nyata-nyata hadir belajar sepanjang hari di kelas.

### Penerapan Pola Fallback Relasional
Sistem menjalankan fungsi rekonsiliasi sore hari (Cron Job jam 17:00 WIB) yang membandingkan presensi KBM jam pelajaran terakhir:

```typescript
// server/services/CheckoutJusticeService.ts
export async function reconcileMissedCheckouts(todayDate: string) {
  // 1. Ambil siswa yang check-in pagi tapi check_out_time masih NULL
  const unclosedAttendances = await db.query.attendancesDaily.findMany({
    where: and(
      eq(attendancesDaily.date, todayDate),
      isNotNull(attendancesDaily.checkInTime),
      isNull(attendancesDaily.checkOutTime)
    )
  });

  for (const record of unclosedAttendances) {
    // 2. Periksa apakah siswa hadir pada presensi KBM sesi terakhir
    const lastSession = await db.query.attendancesSubject.findFirst({
      where: and(
        eq(attendancesSubject.studentId, record.studentId),
        eq(attendancesSubject.date, todayDate),
        eq(attendancesSubject.status, 'Hadir')
      ),
      orderBy: [desc(attendancesSubject.id)]
    });

    if (lastSession) {
      // 3. KEADILAN: Tetap Hadir, tidak dihukum Alpa!
      await db.update(attendancesDaily)
        .set({
          checkoutStatus: 'Check-out Default (Selesai KBM)',
          isCheckoutMissed: true,
          notes: 'Siswa hadir di KBM sesi terakhir; tidak tap kartu saat pulang.'
        })
        .where(eq(attendancesDaily.id, record.id));
    }
  }
}
```

---

## 3. Pola Alur Izin Sakit Fleksibel (*Flexible Leave Workflow Pattern*)

### Masalah Lapangan
Mewajibkan surat dokter untuk sakit demam 1 hari sangat memberatkan orang tua dan memicu sanksi Auto-Alpa keliru.

### Penerapan Pola
Sistem membedakan alur validasi berdasarkan jenis pengajuan izin:

```typescript
export function validateLeaveRequest(type: string, quotaUsed: number, hasDoctorCert: boolean) {
  if (type === 'Sakit 1 Hari (Surat Izin Ortu)') {
    if (quotaUsed >= 2) {
      throw new Error('Batas kuota izin sakit tanpa surat dokter (maks 2x per semester) telah habis. Wajib melampirkan surat dokter.');
    }
    return { valid: true, autoApproveAllowed: false };
  }

  if (type === 'Sakit >1 Hari (Surat Dokter Terlampir)') {
    if (!hasDoctorCert) {
      throw new Error('Sakit lebih dari 1 hari wajib mengunggah foto surat dokter/Puskesmas/klinik resmi.');
    }
    return { valid: true, autoApproveAllowed: false };
  }

  return { valid: true, autoApproveAllowed: false };
}
```

---

## 4. Pola Tanda Tangan Kriptografis Barcode Kartu Pelajar (*HMAC-SHA256 Token Pattern*)

Mencegah manipulasi barcode dan titip absen:

```typescript
// server/services/BarcodeCryptoService.ts
import crypto from 'node:crypto';

export class BarcodeCryptoService {
  private static readonly SECRET = process.env.CARD_SECRET_KEY || 'edutrack-secret-key-2026';

  static signPayload(nisn: string, cardUid: string): string {
    const raw = `EDUTRACK:${nisn}:${cardUid}`;
    const hash = crypto.createHmac('sha256', this.SECRET).update(raw).digest('hex').substring(0, 16);
    return `${raw}:${hash}`;
  }

  static verifyPayload(payload: string): boolean {
    const parts = payload.split(':');
    if (parts.length !== 4 || parts[0] !== 'EDUTRACK') return false;

    const expectedRaw = `EDUTRACK:${parts[1]}:${parts[2]}`;
    const expectedHash = crypto.createHmac('sha256', this.SECRET).update(expectedRaw).digest('hex').substring(0, 16);

    return crypto.timingSafeEqual(Buffer.from(parts[3]), Buffer.from(expectedHash));
  }
}
```

---

## 5. Pola Enkripsi Kolom Sensitif (*Field-Level AES-256-GCM Pattern*)

Melindungi privasi korban perundungan di database PostgreSQL:

```typescript
// server/services/FieldEncryptionService.ts
import crypto from 'node:crypto';

export class FieldEncryptionService {
  private static readonly KEY = Buffer.from(process.env.ENCRYPTION_KEY_32BYTES!, 'hex');

  static encrypt(plainText: string): string {
    const iv = crypto.randomBytes(12);
    const cipher = crypto.createCipheriv('aes-256-gcm', this.KEY, iv);
    let encrypted = cipher.update(plainText, 'utf8', 'hex');
    encrypted += cipher.final('hex');
    const authTag = cipher.getAuthTag().toString('hex');

    return `${iv.toString('hex')}:${authTag}:${encrypted}`;
  }

  static decrypt(cipherText: string): string {
    const [ivHex, authTagHex, encryptedHex] = cipherText.split(':');
    const decipher = crypto.createDecipheriv('aes-256-gcm', this.KEY, Buffer.from(ivHex, 'hex'));
    decipher.setAuthTag(Buffer.from(authTagHex, 'hex'));

    let decrypted = decipher.update(encryptedHex, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    return decrypted;
  }
}
```

---

## 6. Pola Jejak Audit Kekal ISO 27001 (*Append-Only Immutable Audit Log Pattern*)

Tabel `audit_logs` dilindungi di level trigger PostgreSQL untuk memblokir aksi `UPDATE` atau `DELETE`:

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