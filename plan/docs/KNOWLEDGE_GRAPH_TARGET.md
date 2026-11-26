# Knowledge Graph: Target Architecture (Arsitektur Target Produksi)
**Status**: Perencanaan Target (Enterprise Production Architecture)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Penyimpanan**: Basis Data Relasional PostgreSQL 16 Enterprise + Redis 7  
**Framework Stack**: Full-stack JavaScript / TypeScript (Nuxt 3 Vue 3 / Fastify + Drizzle ORM + BullMQ)  
**Tema Antarmuka**: Dominan Putih Minimalis Modern Profesional (Bootstrap 5 Modern + Chart.js)  
**Kepatuhan Standar**: ISO/IEC 27001:2022 & UU Pelindungan Data Pribadi

Dokumen ini memetakan seluruh entitas, komponen layanan, lapisan keamanan, dan relasi data pada arsitektur target berbasis ekosistem JavaScript yang dirancang untuk mendukung operasional sekolah nyata skala penuh.

---

## 1. Entitas Inti Perencanaan (Nodes)

| Node ID | Kategori | Entitas Arsitektural | Peran & Tanggung Jawab Sistem |
| :--- | :--- | :--- | :--- |
| `GateScannerStation` | Hardware & UI| Pos Pemindai Gerbang 3-Mode | Perangkat laser barcode scanner USB & antarmuka live feedback auto-switch 3 jendela waktu ($\le 50\text{ ms}$). |
| `AttendanceScanApi` | API Endpoint | `POST /api/attendance/gate-scan`| Endpoint Fastify pemrosesan scan ultra-cepat dengan evaluasi jam masuk, gate pass, dan kepulangan sore. |
| `AttendanceRuleEngine`| Service Logic| `AttendanceRuleEngineService` | Mesin evaluasi 3 mode: Masuk (Hadir, T1-T3), Siaga Gate Pass, dan Pulang (Chime 3-nada C-E-G). |
| `CheckoutJusticeEngine`| Service Logic| `CheckoutJusticeService` | Penjamin keadilan: Siswa yang hadir di KBM terakhir namun lupa tap pulang tidak dihukum Alpa (status tetap Hadir). |
| `AutoAlpaScheduler` | Cron Daemon | `AttendanceAutoAlpaJob` | Penjadwal otomatis pukul 08:00 WIB untuk menetapkan status Alpa (-15 Poin) & trigger alert WA darurat. |
| `FlexibleLeaveWorkflow`| Service Logic| `ParentLeaveRequestService` | Alur izin sakit fleksibel: Sakit 1 hari surat ortu (maks 2x) vs Sakit >1 hari wajib surat dokter. |
| `SubjectAttendanceSync`| Service Logic| `SubjectAttendanceService` | Sinkronisasi presensi gerbang dengan presensi kelas KBM per jam pelajaran guna mendeteksi siswa membolos. |
| `DigitalGatePassEngine`| Service Logic| `DigitalGatePassService` | Penerbitan tiket izin keluar, verifikasi penjemput, pencatatan `early_leave_time`, dan alert WA seketika. |
| `BarcodeSecurityCrypto`| Cryptography | `BarcodeSecurityCryptoService`| Generator & verifikator tanda tangan digital HMAC-SHA256 untuk payload QR Code kartu CR-80. |
| `WarningLetterEngine` | Service Logic| `WarningLetterPdfService` | Generator otomatis dokumen resmi Surat Peringatan (SP 1, SP 2, SP 3) ber-QR verification. |
| `WhistleblowerCrypto` | Cryptography | `FieldEncryptionService` | Enkripsi end-to-end kolom sensitif aduan (AES-256-GCM) & ruang chat investigasi dua arah. |
| `WhatsAppDispatcher` | Async Queue | `WhatsAppNotificationJob` | Antrean asinkron BullMQ + Redis untuk pengiriman pesan WhatsApp masuk, pulang, dan alert darurat. |
| `ServerDataScopeEngine`| Security | `RoleDataIsolationMiddleware` | Penegakan isolasi data di level query database Drizzle ORM sisi server (Zero IDOR). |
| `ImmutableAuditEngine` | Security | `ImmutableAuditLogService` | Pencatat audit trail kekal ISO 27001 (A.12.4) yang dilindungi trigger database PostgreSQL. |
| `WhiteMinimalistUI` | UI Engine | Nuxt 3 / Vue 3 Component Suite | Antarmuka putih bersih, efek Bootstrap 5 modern, pagination, searching, multi-filter, dan grafik Chart.js. |
| `PostgreSqlDb` | Persistence | Basis Data PostgreSQL 16 | Relational Data Store ACID compliance dengan 21 tabel, UUIDv7, Drizzle ORM, dan CHECK constraints. |
| `RedisCacheServer` | In-Memory | Redis 7 Instance | Cache sesi otentikasi, rate limiter endpoint login, dan antrean pengiriman pesan WhatsApp BullMQ. |

---

## 2. Relasi Terarah Antar Komponen (Directed Architecture Graph)

```text
GateScannerStation     --[transmits_burst_keystrokes_to]-> AttendanceScanApi
AttendanceScanApi      --[evaluates_3modes_via]----------> AttendanceRuleEngine
AttendanceRuleEngine   --[checks_cooldown_in]------------> PostgreSqlDb (attendances_daily)
AttendanceRuleEngine   --[updates_check_in_time_in]------> PostgreSqlDb (attendances_daily)
AttendanceRuleEngine   --[updates_check_out_time_in]-----> PostgreSqlDb (attendances_daily)
AttendanceRuleEngine   --[deducts_demerit_in]------------> PostgreSqlDb (students.demerit_points)
AttendanceRuleEngine   --[dispatches_async_event_to]-----> WhatsAppDispatcher

CheckoutJusticeEngine  --[reconciles_missed_checkout]----> PostgreSqlDb (attendances_daily & attendances_subject)
CheckoutJusticeEngine  --[preserves_hadir_status_in]-----> PostgreSqlDb (attendances_daily.checkout_status)

AutoAlpaScheduler      --[queries_unscanned_students]----> PostgreSqlDb
AutoAlpaScheduler      --[locks_status_as_alpa_in]-------> PostgreSqlDb (attendances_daily)
AutoAlpaScheduler      --[triggers_emergency_alert_to]---> WhatsAppDispatcher

WhatsAppDispatcher     --[pushes_job_to]-----------------> RedisCacheServer (BullMQ)
RedisCacheServer       --[executes_daemon_worker_to]-----> External WhatsApp Gateway (Masuk, Pulang, Alert)

DigitalGatePassEngine  --[records_early_leave_in]--------> PostgreSqlDb (gate_passes & attendances_daily)
DigitalGatePassEngine  --[dispatches_early_alert_to]-----> WhatsAppDispatcher

FlexibleLeaveWorkflow  --[validates_quota_and_doctor_cert] PostgreSqlDb (leave_requests)
FlexibleLeaveWorkflow  --[exempts_auto_alpa_in]----------> PostgreSqlDb (attendances_daily)

SubjectAttendanceSync  --[cross_checks_gate_status_in]---> PostgreSqlDb (attendances_daily)
SubjectAttendanceSync  --[records_skipping_violation_in]-> PostgreSqlDb (student_violations)

BarcodeSecurityCrypto  --[signs_cr80_card_payload_for]---> PostgreSqlDb (student_cards)
WarningLetterEngine    --[fetches_accumulated_points_from] PostgreSqlDb (student_violations)
WarningLetterEngine    --[generates_signed_pdf_to]-------> PostgreSqlDb (warning_letters)

WhistleblowerCrypto    --[encrypts_aes256_content_into]--> PostgreSqlDb (complaints.description_encrypted)
WhistleblowerCrypto    --[encrypts_messages_into]--------> PostgreSqlDb (complaint_messages)

ServerDataScopeEngine  --[intercepts_all_queries_of]-----> PostgreSqlDb
ImmutableAuditEngine   --[inserts_append_only_records_to]-> PostgreSqlDb (audit_logs)
WhiteMinimalistUI      --[renders_charts_from]-----------> Chart.js (Kehadiran & Disiplin)
WhiteMinimalistUI      --[paginates_and_filters_via]-----> ServerDataScopeEngine
```

---

## 3. Peningkatan Kualitas Arsitektur JavaScript

1. **Migrasi Alami dari [index.html](file:///c:/scholl_app/index.html)**: Kode logika reaktif Vue 3 (Composition API) dari prototipe langsung diintegrasikan ke Nuxt 3 / Vue 3 tanpa perlu menulis ulang template tampilan dari nol.
2. **Kinerja Scanner Ultra-Cepat**: Runtime Node.js (Fastify/Nitro) mampu memproses request pemindaian barcode gerbang dalam waktu $\le 50\text{ milidetik}$.
3. **Keadilan dan Humanis**: Menghilangkan kekakuan administratif (anak demam 1 hari bisa izin surat ortu, dan anak yang lupa tap pulang tidak dihakimi Alpa).
4. **Kepastian Pengawasan Orang Tua**: Orang tua menerima dua notifikasi penting setiap hari: saat anak tiba di sekolah pagi hari dan saat anak melangkah keluar gerbang sore hari.