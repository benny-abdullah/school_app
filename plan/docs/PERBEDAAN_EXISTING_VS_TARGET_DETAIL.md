# Perbandingan Mendalam: Sistem Eksisting vs. Arsitektur Target (Technical Detail)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Status**: Analisis Komparatif Mendalam (Baseline Mockup vs. Target Production)

Dokumen ini memuat perbandingan teknis, arsitektural, operasional, dan keamanan secara komprehensif baris-demi-baris antara sistem prototipe saat ini (*existing in-memory SPA*) pada [index.html](file:///c:/scholl_app/index.html) dan arsitektur target yang direncanakan (*target production Laravel + PostgreSQL*).

---

## 1. Matriks Komparasi Parameter Teknis

| Dimensi Arsitektur | Sistem Eksisting (Mockup `index.html`) | Arsitektur Target (Planned Production) | Dampak & Alasan Transformasi |
| :--- | :--- | :--- | :--- |
| **Penyimpanan Data (Persistence)** | Browser RAM Memory (`ref([])` di JavaScript Vue). Data hilang seketika saat halaman di-refresh (*F5*). | Basis Data Relasional **PostgreSQL 16 Enterprise** dengan garansi ACID, UUIDv7, dan enkripsi *at-rest*. | Memberikan persistensi data permanen, kapasitas rekaman kesiswaan tak terbatas, dan keandalan integritas data antar semester. |
| **Konkurensi & Multi-Device** | **Zero Concurrency**: Hanya berjalan di 1 tab browser pada 1 komputer pengembang lokal. | **Multi-Device Concurrent Engine**: Pos Satpam, Ruang Guru, Meja BK, dan Ponsel Ortu terhubung serentak via Jaringan/Internet. | Memungkinkan satpam memindai kartu di gerbang sementara wali kelas memantau absensi kelas dan orang tua menerima pesan WA serentak. |
| **Presensi Gerbang & Latensi** | Simulasi dropdown jam (`06:45`, `07:10`, dsb.) dan pencarian array JS lokal. | **High-Throughput Endpoint (`POST /api/attendance/gate-scan`)** dengan latensi $\le 200\text{ ms}$ & koneksi scanner laser USB. | Menjamin pemindaian 500 siswa di pos gerbang tuntas dalam 20 menit tanpa hambatan teknis atau antrean mengular. |
| **Anti-Double Tap Cooldown** | Tidak ada validasi jeda waktu fisik antar pemindaian kartu yang sama. | **Atomic Database Cooldown Check (< 5 Menit)** pada baris kartu aktif di tabel `attendances_daily`. | Mencegah kartu yang tidak sengaja tertempel dua kali mencatat duplikasi kehadiran atau penalti ganda. |
| **Deteksi Siswa Membolos KBM** | Tombol simulasi JS yang menambahkan item ke array pelanggaran lokal. | **Relational Cross-Checking Engine**: Membandingkan log gerbang pagi dengan log presensi KBM per jam pelajaran. | Otomatis mendeteksi siswa yang hadir di gerbang tetapi membolos di jam pelajaran siang (+10 Poin & notifikasi darurat BK). |
| **Keamanan Kartu Pelajar** | Teks alfanumerik biasa tanpa tanda tangan digital. Rentan difotokopi/titip absen. | **Dual-Symbology ISO CR-80**: Barcode 1D Code 128 + QR Code 2D bertanda tangan digital **HMAC-SHA256**. | Mengeliminasi kecurangan titip absen; sistem dapat memverifikasi keabsahan kartu fisik secara kriptografis. |
| **Kebijakan Kartu Pelajar** | Array statis siswa; tidak ada mekanisme pencabutan kartu fisik yang hilang. | **One-Card One-Active Policy**: Fitur *Revoke & Regenerate* seketika menonaktifkan kartu lama di seluruh scanner. | Mencegah penyalahgunaan kartu siswa yang tercecer atau dicuri oleh pihak lain. |
| **Dokumen Surat Peringatan (SP)**| Modal popup HTML statis dengan pratinjau teks biasa. | **Engine Dokumen Resmi PDF (DomPDF/Snappy)** berkop resmi SDN Kebonagung, stempel kedinasan & QR Code verifikasi keabsahan. | Menghasilkan dokumen resmi yang sah secara hukum pendidikan untuk diserahkan kepada orang tua siswa. |
| **Kanal Aduan & Whistleblower** | Data teks aduan disimpan dalam array JavaScript biasa (*plain text*). | **Enkripsi End-to-End Kolom Sensitif (AES-256-GCM)** via Eloquent Casts; identitas anonim terisolasi penuh. | Menjamin kerahasiaan identitas pelapor kasus perundungan (*bullying*) sesuai amanat UU Perlindungan Data Pribadi (UU PDP). |
| **Pengiriman Notifikasi WhatsApp**| Jendela modal sembulan simulasi teks pesan di layar pengembang. | **Asynchronous Redis Queue Worker** yang menembakkan pesan resmi via WhatsApp Business API Gateway. | Orang tua langsung menerima notifikasi status kehadiran anak secara *real-time* di ponsel masing-masing. |
| **Master Guru & GTK (Admin)** | Array statis 7 orang guru/staf di memori JavaScript. | **Tabel Relasional `teachers`**: Validasi NIP/NUPTK resmi Kemendikbudristek, hak akses eksklusif Admin, dan audit trail ISO 27001. | Memfasilitasi tata kelola kepegawaian sekolah secara profesional, tertib administrasi Dapodik, dan aman dari modifikasi liar. |
| **Isolasi Data (Anti-IDOR)** | Filter computed property `roleDataScope` di JavaScript (dapat dibypass via F12 console). | **Server-Side Global Scopes & Laravel Policies**: Isolasi query dipaksakan di level basis data. | Mencegah kebocoran data privasi siswa atau orang tua lain (*Zero IDOR Vulnerability*). |
| **Jejak Audit Sistem** | Array `auditLogs` lokal yang terhapus saat refresh. | **Tabel Kekal `audit_logs` (ISO 27001 A.12.4)** dilindungi *PostgreSQL Database Trigger* dilarang UPDATE & DELETE. | Menjamin jejak mutasi data (*Who, What, When, IP Address*) tidak dapat dimanipulasi bahkan oleh administrator sistem. |
| **Pencadangan Data (Backup)** | Simulasi timeout JS (1.2 detik) yang menambahkan item palsu ke tabel. | **Automated 3-2-1 Encrypted Backup**: Dump database terenkripsi AES-256-GCM otomatis direplikasi ke AWS S3 Vault SG. | Menjamin sekolah memiliki pemulihan bencana (*disaster recovery*) jika server lokal mengalami kerusakan perangkat keras. |
| **Lingkungan Deployment** | Berkas statis tunggal dibuka via `file:///` atau `php -S 127.0.0.1:8000`. | **Dual-Target Production**: aaPanel Nginx + PHP-FPM 8.3 & Bare VPS Ubuntu / Docker Compose. | Siap di-deploy pada server produksi hosting sekolah dengan SSL HTTPS Let's Encrypt dan auto-restart daemon. |

---

## 2. Perbandingan Alur Bisnis Kritis (Workflow Deep-Dive)

### A. Alur Presensi Gerbang Pagi (Morning Gate Attendance)

```text
ALUR MOCKUP EKSISTING:
1. Operator memilih jam simulasi di dropdown (misal: 07:10).
2. Operator mengetik NISN '0081234567' atau klik tombol 'Tap Scan'.
3. Fungsi JavaScript 'executeBarcodeScan()' mencari siswa di array in-memory.
4. Nilai 'demeritPoints' siswa di memori ditambah 2 poin.
5. Record baru ditambahkan ke array 'attendanceList' di memori browser.
6. Notifikasi Toast muncul di layar laptop pengembang.
MASALAH: Data tidak tersimpan di server; orang tua tidak menerima pesan WA asli; tidak ada proteksi duplikasi tap.

ALUR TARGET PRODUKSI:
1. Siswa men-tap kartu pelajar PVC pada Laser Gun Scanner di pos gerbang sekolah.
2. Scanner memuntahkan karakter barcode seketika ke endpoint 'POST /api/attendance/gate-scan' (< 200ms).
3. AttendanceRuleEngineService memvalidasi cooldown (< 5 menit) dan mengevaluasi jam scan nyata server.
4. Database PostgreSQL mengunci baris dalam transaksi atomik, mencatat status Terlambat T-1 (-2 Poin Tatib).
5. Layanan Audio membunyikan nada triangle (warning_t1) pada speaker pos gerbang.
6. Layanan memicu 'AttendanceRecordedEvent' yang melempar payload notifikasi ke Redis Queue.
7. Worker Redis memproses pengiriman pesan WhatsApp resmi ke nomor wali murid tanpa membebani pos gerbang.
8. Mutasi tercatat otomatis di tabel kekal 'audit_logs'.
```

### B. Alur Presensi KBM Mapel & Deteksi Membolos (Classroom Attendance & Skipping Detection)

```text
ALUR TARGET PRODUKSI:
1. Guru mata pelajaran membuka antarmuka 'Presensi KBM Jam Mapel' di ruang kelas.
2. Sistem secara otomatis menampilkan status kehadiran gerbang pagi sebagai acuan dasar.
3. Guru melakukan pengecekan fisik di kelas. Jika siswa hadir di gerbang pagi namun kursi kosong di jam KBM:
   -> Guru menandai status 'Membolos' dan menekan tombol '🚨 Lapor Membolos ke BK'.
4. Sistem memproses transaksi ganda:
   - Status presensi sesi mapel tercatat 'Membolos' (is_skipping = true).
   - Record pelanggaran baru otomatis dibuat di tabel 'student_violations' (+10 Poin Tatib).
5. Layanan WhatsApp mengirimkan peringatan darurat ke orang tua:
   "Ananda tercatat hadir di gerbang pagi, namun tidak mengikuti jam pelajaran [Nama Mapel]. Mohon konfirmasi."
6. Dashboard Guru BK dan Wali Kelas memunculkan indikator waspada merah untuk pembinaan seketika.
```

### C. Alur Pengaduan Whistleblower Anti-Perundungan (Bullying Whistleblower)

```text
ALUR TARGET PRODUKSI:
1. Siswa/Orang Tua membuka modul aduan dan mencentang opsi 'Kirim sebagai Anonim (Whistleblower)'.
2. Pengguna mengisi judul dan rincian intimidasi perundungan di lorong sekolah.
3. FieldEncryptionService mengenkripsi seluruh teks deskripsi menggunakan cipher AES-256-GCM sebelum masuk ke PostgreSQL.
4. Kolom pelapor diisi 'Whistleblower Anonim' tanpa menautkan 'user_id' atau 'student_id' (Zero Identity Trace).
5. Tim Satgas Sekolah dan Guru BK menerima tiket baru dan membuka 'Ruang Obrolan Investigasi Terenkripsi'.
6. Komunikasi dua arah berlangsung aman melalui pesan terenkripsi baris-demi-baris di tabel 'complaint_messages'.
7. Korban perundungan terlindungi sepenuhnya dari intimidasi pelaku atau kebocoran data identitas.
```