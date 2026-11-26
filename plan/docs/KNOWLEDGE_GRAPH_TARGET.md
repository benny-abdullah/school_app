# Knowledge Graph: Target Architecture (Arsitektur Target Produksi)
**Status**: Perencanaan Target (Enterprise Production Architecture)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Penyimpanan**: Basis Data Relasional PostgreSQL 16 + Redis 7  
**Framework Backend**: Laravel 11/12 (PHP 8.3+) + Livewire 3 / Alpine.js  
**Kepatuhan Standar**: ISO/IEC 27001:2022 & UU Pelindungan Data Pribadi

Dokumen ini memetakan seluruh entitas, komponen layanan, lapisan keamanan, dan relasi data pada arsitektur target yang dirancang untuk mendukung operasional sekolah nyata skala penuh.

---

## 1. Entitas Inti Perencanaan (Nodes)

| Node ID | Kategori | Entitas Arsitektural | Peran & Tanggung Jawab Sistem |
| :--- | :--- | :--- | :--- |
| `GateScannerStation` | Hardware & UI| Pos Pemindai Gerbang | Perangkat laser barcode scanner USB (keyboard wedge) & antarmuka live feedback $\le 200\text{ ms}$. |
| `AttendanceScanApi` | API Endpoint | `POST /api/attendance/gate-scan`| Endpoint pemrosesan scan berlatensi ultra-rendah dengan verifikasi cooldown 5 menit. |
| `AttendanceRuleEngine`| Service Logic| `AttendanceRuleEngineService` | Mesin evaluasi keterlambatan berjenjang (Hadir, T-1, T-2, T-3) & kalkulator demerit points. |
| `AutoAlpaScheduler` | Cron Daemon | `AttendanceAutoAlpaJob` | Penjadwal otomatis pukul 08:00 WIB untuk menetapkan status Alpa (-15 Poin) & trigger alert WA. |
| `SubjectAttendanceSync`| Service Logic| `SubjectAttendanceService` | Layanan sinkronisasi presensi gerbang dengan presensi kelas KBM guna mendeteksi siswa membolos. |
| `DigitalGatePassEngine`| Service Logic| `DigitalGatePassService` | Layanan penerbitan tiket izin keluar gerbang & verifikasi kode QR oleh satpam pos gerbang. |
| `LeaveApprovalWorkflow`| Service Logic| `ParentLeaveRequestService` | Alur kerja persetujuan surat izin sakit mandiri orang tua oleh wali kelas (pembebasan Auto-Alpa). |
| `BarcodeSecurityCrypto`| Cryptography | `BarcodeSecurityCryptoService`| Generator & verifikator tanda tangan digital HMAC-SHA256 untuk payload QR Code kartu CR-80. |
| `WarningLetterEngine` | Service Logic| `WarningLetterPdfService` | Generator otomatis dokumen resmi Surat Peringatan (SP 1, SP 2, SP 3) ber-QR verification. |
| `WhistleblowerCrypto` | Cryptography | `FieldEncryptionService` | Enkripsi end-to-end kolom sensitif aduan (AES-256-GCM) & ruang chat investigasi dua arah. |
| `WhatsAppDispatcher` | Async Queue | `WhatsAppNotificationJob` | Antrean asinkron Redis Queue untuk pengiriman pesan WhatsApp instan tanpa membebani server utama. |
| `ServerDataScopeEngine`| Security | `RoleDataIsolationScope` | Global Scope Eloquent yang memaksakan isolasi data di level query database PostgreSQL. |
| `ImmutableAuditEngine` | Security | `ImmutableAuditLogService` | Pencatat audit trail kekal ISO 27001 (A.12.4) yang dilindungi trigger database PostgreSQL. |
| `Backup321Manager` | Infrastructure| `BackupRedundancyS3Service` | Mesin backup harian otomatis terenkripsi AES-256 dengan replikasi offsite ke AWS S3 Vault. |
| `PostgreSqlDb` | Persistence | Basis Data PostgreSQL 16 | Relational Data Store ACID compliance dengan 21 tabel, UUIDv7, dan CHECK constraints. |
| `RedisCacheServer` | In-Memory | Redis 7 Instance | Cache sesi otentikasi, rate limiter endpoint login, dan antrean pengiriman pesan WhatsApp. |

---

## 2. Relasi Terarah Antar Komponen (Directed Architecture Graph)

```text
GateScannerStation     --[transmits_burst_keystrokes_to]-> AttendanceScanApi
AttendanceScanApi      --[delegates_evaluation_to]-------> AttendanceRuleEngine
AttendanceRuleEngine   --[checks_cooldown_in]------------> PostgreSqlDb (attendances_daily)
AttendanceRuleEngine   --[updates_attendance_in]---------> PostgreSqlDb (attendances_daily)
AttendanceRuleEngine   --[deducts_demerit_in]------------> PostgreSqlDb (students.demerit_points)
AttendanceRuleEngine   --[dispatches_async_event_to]-----> WhatsAppDispatcher

WhatsAppDispatcher     --[pushes_job_to]-----------------> RedisCacheServer
RedisCacheServer       --[executes_daemon_worker_to]-----> External WhatsApp Gateway

AutoAlpaScheduler      --[queries_unscanned_students]----> PostgreSqlDb
AutoAlpaScheduler      --[locks_status_as_alpa_in]-------> PostgreSqlDb (attendances_daily)
AutoAlpaScheduler      --[triggers_emergency_alert_to]---> WhatsAppDispatcher

SubjectAttendanceSync  --[cross_checks_gate_status_in]---> PostgreSqlDb (attendances_daily)
SubjectAttendanceSync  --[records_skipping_violation_in]-> PostgreSqlDb (student_violations)

DigitalGatePassEngine  --[validates_qr_token_via]--------> BarcodeSecurityCrypto
DigitalGatePassEngine  --[records_exit_timestamp_in]-----> PostgreSqlDb (gate_passes)

LeaveApprovalWorkflow  --[updates_parent_request_in]-----> PostgreSqlDb (leave_requests)
LeaveApprovalWorkflow  --[exempts_auto_alpa_in]----------> PostgreSqlDb (attendances_daily)

BarcodeSecurityCrypto  --[signs_cr80_card_payload_for]---> PostgreSqlDb (student_cards)
WarningLetterEngine    --[fetches_accumulated_points_from] PostgreSqlDb (student_violations)
WarningLetterEngine    --[generates_signed_pdf_to]-------> PostgreSqlDb (warning_letters)

WhistleblowerCrypto    --[encrypts_aes256_content_into]--> PostgreSqlDb (complaints.description_encrypted)
WhistleblowerCrypto    --[encrypts_messages_into]--------> PostgreSqlDb (complaint_messages)

ServerDataScopeEngine  --[intercepts_all_queries_of]-----> PostgreSqlDb
ImmutableAuditEngine   --[inserts_append_only_records_to]-> PostgreSqlDb (audit_logs)
Backup321Manager       --[executes_pg_dump_encrypted]----> PostgreSqlDb
Backup321Manager       --[replicates_vault_snapshot_to]--> AWS S3 Vault SG
```

---

## 3. Peningkatan Kualitas & Nilai Tambah Arsitektur Target

1. **Integritas Transaksional Penuh (ACID)**: Seluruh mutasi kehadiran, penalti pelanggaran, dan pengurangan poin dieksekusi dalam transaksi basis data terisolasi (*atomic transactions*).
2. **Kinerja Ultra-Cepat Meja Gerbang**: Dengan pemisahan antrean pesan ke Redis Queue, pemrosesan pemindaian kartu di pos satpam terjamin tuntas dalam waktu $\le 200\text{ ms}$, meniadakan antrean siswa di pagi hari.
3. **Keamanan Tanpa Kompromi (Zero-Trust Data Scoping)**: Hak akses orang tua dan siswa terkunci di level basis data, sehingga manipulasi request API tetap akan menghasilkan respon `403 Forbidden`.
4. **Kepatuhan Terhadap Regulasi Nasional**: Menerapkan standar enkripsi data sensitif (UU PDP) dan jejak audit yang tidak dapat dimanipulasi (ISO/IEC 27001).