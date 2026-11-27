# Manajemen Proyek, WBS & Peta Jalan (Project Management) - SSOT
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Teknologi Pelaksanaan**: Full-Stack Laravel 13 (PHP 8.4) + Laravel Livewire 3 + Alpine.js + PostgreSQL 16 + Redis 7 + Laravel Reverb  
**Metodologi**: Agile Iteratif / Sprint-Based (5 Paket Kerja)  
**Target Rilis**: Sistem Produksi Terintegrasi (Enterprise Grade)

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk rencana kerja, pemecahan tugas (*Work Breakdown Structure* / WBS), manajemen risiko (*Risk Register*), dan kriteria penyelesaian (*Definition of Done* / DoD) pada ekosistem **Laravel 13**.

---

## 1. Peta Jalan Pengembangan (Milestone Roadmap)

```mermaid
gantt
    title Peta Jalan Implementasi Laravel 13 Produksi EduTrack Pro
    dateFormat  YYYY-MM-DD
    section Paket Kerja 1: Fondasi & Database
    WBS 1.1: Inisialisasi Proyek Laravel 13 & Livewire 3        :active, p1_core, 2026-11-26, 3d
    WBS 1.2: Migrasi Eloquent 21 Tabel PostgreSQL 16 & UUIDv7  :p1_eloquent, after p1_core, 2d
    WBS 1.3: Seeder Master GTK, Siswa, Ortu & RBAC Policies    :p1_seed, after p1_eloquent, 2d

    section Paket Kerja 2: Layanan Inti & Scanner 3-Mode
    WBS 2.1: AttendanceRuleEngine 3-Mode (PHP 8.4 Match)       :p2_rule, 2026-12-03, 3d
    WBS 2.2: Keadilan Checkout Fallback & Izin Sakit Fleksibel :p2_fair, after p2_rule, 2d
    WBS 2.3: HMAC Barcode, Redis Queue WA & Reverb WebSocket   :p2_sec, after p2_fair, 3d

    section Paket Kerja 3: Modul Antarmuka & Kesiswaan
    WBS 3.1: Meja Scanner Livewire 3 & Web Audio Chime         :p3_scan, 2026-12-11, 3d
    WBS 3.2: Generator Kartu Pelajar PVC ISO CR-80 & Revoke    :p3_card, after p3_scan, 2d
    WBS 3.3: Presensi Mapel KBM, Deteksi Membolos & Cetak SP   :p3_tatib, after p3_card, 3d
    WBS 3.4: Kanal Whistleblower AES-256 & Master GTK Admin    :p3_whistle, after p3_tatib, 2d

    section Paket Kerja 4: QA Assault & Audit Kepatuhan
    WBS 4.1: Stress Test 500 Scan Gerbang Pagi (Latensi < 50ms):p4_load, 2026-12-21, 2d
    WBS 4.2: Pen-Test IDOR Scoping & Audit OWASP Top 10        :p4_sec, after p4_load, 2d
    WBS 4.3: Pengujian Pemulihan Bencana Backup 3-2-1 S3 Vault :p4_dr, after p4_sec, 1d

    section Paket Kerja 5: Deployment aaPanel & Bare VPS Docker
    WBS 5.1: Konfigurasi PHP-FPM, Supervisor & Docker Compose  :p5_dep, 2026-12-26, 2d
    WBS 5.2: Uji Coba Hardware Scanner USB di Pos Satpam       :p5_field, after p5_dep, 2d
```

---

## 2. Struktur Pemecahan Kerja (Work Breakdown Structure - WBS)

### Paket Kerja 1: Fondasi Sistem Laravel 13, Eloquent & PostgreSQL 16 (WP-1)
* **WBS 1.0**: Inisialisasi proyek Laravel 13 (PHP 8.4) dengan integrasi Livewire 3, Tailwind CSS v4, Alpine.js, Plus Jakarta Sans, dan gaya Enterprise Clean White Modern Minimalist.
* **WBS 1.1**: Penulisan migrasi Laravel 13 untuk 21 tabel relasional PostgreSQL 16 (menggunakan UUIDv7 via `HasUuids`, indeks komposit teroptimasi, dan CHECK constraints).
* **WBS 1.2**: Pembuatan seeder data realistis SDN Kebonagung Kota Pasuruan (`DatabaseSeeder`, GTK seeder, siswa, rombel, orang tua, preset tatib).
* **WBS 1.3**: Konfigurasi autentikasi (Argon2id, 2FA TOTP), RBAC multi-role, dan middleware `EnforceServerDataScope` untuk proteksi IDOR di sisi server.

### Paket Kerja 2: Mesin Bisnis Presensi 3-Mode, Redis Queue & Reverb (WP-2)
* **WBS 2.1**: Implementasi `AttendanceRuleEngineService.php` (PHP 8.4) untuk pemindai 3 mode otomatis (Mode 1 Masuk 06:00-08:00, Mode 2 Siaga Gate Pass 08:01-13:00, Mode 3 Pulang Sore 13:01-17:00).
* **WBS 2.2**: Implementasi `CheckoutJusticeService.php` untuk menjamin keadilan siswa yang lupa tap kartu pulang tetap Hadir jika tercatat di KBM jam terakhir.
* **WBS 2.3**: Implementasi validasi pengajuan izin sakit fleksibel (Sakit 1 hari tanpa surat dokter maks 2x/semester vs Sakit >1 hari wajib surat dokter).
* **WBS 2.4**: Implementasi tanda tangan digital HMAC-SHA256 untuk kartu CR-80, broadcasting WebSocket via **Laravel Reverb**, dan worker antrean asinkron Redis untuk WhatsApp API.

### Paket Kerja 3: Modul Antarmuka Livewire 3, Meja Scanner & Kesiswaan (WP-3)
* **WBS 3.1**: Pembangunan antarmuka Livewire 3 Meja Scanner Gerbang (`/scanner`) dengan input burst laser gun scanner, indikator visual 3 mode dinamis, dan Web Audio API (Beep 880Hz, Buzz 180Hz, Chime 3 nada C-E-G).
* **WBS 3.2**: Pembangunan modul Cetak Kartu Pelajar PVC ISO CR-80 dengan fitur *revocation* dan preview dua sisi standar perbankan.
* **WBS 3.3**: Pembangunan modul Presensi KBM Mapel dengan deteksi siswa membolos (+10 Poin Tatib) dan generator dokumen resmi Surat Peringatan (SP 1, SP 2, SP 3) PDF ber-QR verification.
* **WBS 3.4**: Pembangunan modul Master GTK (khusus Super Admin) dan kanal aduan whistleblower terenkripsi AES-256 via Laravel Crypt.
* **WBS 3.5**: Integrasi komponen visual Enterprise Clean White: Pagination dinamis, debounced search reaktif, multi-criteria filter, grafik Chart.js (Rose/Emerald), kartu statistik KPI rounded-2xl, dan AI Copilot floating drawer.

### Paket Kerja 4: Penjaminan Mutu (*QA Assault*) & Audit Kepatuhan (WP-4)
* **WBS 4.1**: *Stress Testing* pemindaian gerbang serentak (50 request/detik) dengan Pest PHP / k6 memastikan respon endpoint Laravel $\le 50\text{ ms}$ (didukung OPcache JIT).
* **WBS 4.2**: Audit keamanan penetrasi (Pen-Test) IDOR antar role wali murid/siswa dan verifikasi Laravel Eloquent Model Policies.
* **WBS 4.3**: Pengujian pemulihan bencana (*Disaster Recovery*) dari snapshot database terenkripsi di AWS S3 Vault Singapore.

### Paket Kerja 5: Pengemasan Infrastruktur & Deployment Ganda (WP-5)
* **WBS 5.1**: Penyiapan konfigurasi PHP 8.4-FPM, Supervisor daemon (`artisan queue:work` & `artisan reverb:start`), serta cron tab aaPanel untuk `artisan schedule:run`.
* **WBS 5.2**: Pembuatan berkas mandiri `docker-compose.yml` multi-container untuk Bare VPS Linux (Nginx, PHP 8.4 App, PostgreSQL 16, Redis 7, Queue Worker, Reverb).
* **WBS 5.3**: Uji coba lapangan menggunakan perangkat laser scanner USB di pos satpam sekolah dan printer kartu PVC.

---

## 3. Matriks Manajemen Risiko (Risk Register)

| ID Risiko | Deskripsi Risiko | Dampak | Probabilitas | Rencana Mitigasi & Kontingensi |
| :--- | :--- | :---: | :---: | :--- |
| **R-01** | **Penumpukan Antrean Gerbang Pagi**: Scanner lambat membaca kartu kotor atau koneksi drop. | **Tinggi** | Rendah | Endpoint Controller Laravel teroptimasi dengan OPcache JIT (< 50ms); dispatch notifikasi WhatsApp via Redis Queue mencegah bottleneck IO. |
| **R-02** | **Kecurangan Titip Absen**: Siswa memfotokopi barcode atau mengirimkan foto barcode via WA. | **Tinggi** | Sedang | Menerapkan QR Code bertanda tangan digital HMAC-SHA256 yang divalidasi keaktifan kartu (`One-Card One-Active Policy`). |
| **R-03** | **Siswa Berebut Pulang Lupa Tap**: Ratusan siswa lari keluar gerbang sore tanpa men-tap kartu. | **Tinggi** | Tinggi | Diterapkan **Pola Keadilan Checkout Fallback**: Siswa tetap Hadir jika tercatat di KBM jam terakhir via artisan command sore hari. |
| **R-04** | **Kekakuan Izin Sakit Demam 1 Hari**: Orang tua komplain karena dipaksa melampirkan surat dokter. | **Sedang** | Tinggi | Disediakan kategori **Sakit 1 Hari Mandiri** (cukup surat pernyataan orang tua, kuota maks 2x per semester). |
| **R-05** | **Kebocoran Identitas Whistleblower**: Data korban perundungan bocor ke pelaku/publik. | **Kritis** | Rendah | Enkripsi tingkat kolom AES-256 via Laravel Crypt; data anonim tidak menyimpan foreign key ke ID siswa; audit log mencatat setiap akses. |

---

## 4. Kriteria Penyelesaian (Definition of Done - DoD)

1. **Fungsionalitas 3 Mode Presensi**: Pemindai gerbang otomatis beralih mode (Masuk 06:00-08:00, Gate Pass 08:01-13:00, Pulang 13:01-17:00) dan mengirim notifikasi WhatsApp yang sesuai.
2. **Kinerja Scanner Gerbang**: Endpoint `POST /api/attendance/gate-scan` terbukti merespons dalam $\le 50\text{ ms}$ pada controller Laravel 13.
3. **Keadilan Kepulangan Terverifikasi**: Siswa yang lupa tap pulang terbukti tidak dihukum Alpa di database PostgreSQL.
4. **Isolasi Data Sisi Server**: Akun orang tua dan siswa terbukti secara teknis tidak dapat mengakses data di luar cakupannya (*Zero IDOR Leakage*) melalui Laravel Policies & Global Scopes.
5. **Kepatuhan Kriptografi ISO 27001**: Data aduan tersimpan dalam bentuk *cipher text* AES-256, dan log audit terlindungi secara kekal (*immutable*) via database trigger.
6. **Kesiapan UI Putih Minimalis**: Seluruh tabel Livewire 3 dilengkapi pagination dinamis, searching reaktif, multi-filter, grafik Chart.js, kartu KPI, dan cetak kartu fisik CR-80.
7. **Dual Deployment Ready**: Konfigurasi aaPanel (PHP 8.4-FPM + Supervisor + Nginx) dan Docker Compose multi-container terverifikasi siap melayani domain produksi sekolah.
