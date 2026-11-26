# Manajemen Proyek, WBS & Peta Jalan (Project Management) - SSOT
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Teknologi Pelaksanaan**: Full-stack JavaScript / TypeScript (Nuxt 3 Vue 3 / Node.js Fastify + PostgreSQL 16 + Redis)  
**Metodologi**: Agile Iteratif / Sprint-Based (5 Paket Kerja)  
**Target Rilis**: Sistem Produksi Terintegrasi (Enterprise Grade)

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk rencana kerja, pemecahan tugas (*Work Breakdown Structure* / WBS), manajemen risiko (*Risk Register*), dan kriteria penyelesaian (*Definition of Done* / DoD) pada ekosistem JavaScript.

---

## 1. Peta Jalan Pengembangan (Milestone Roadmap)

```mermaid
gantt
    title Peta Jalan Implementasi JavaScript Produksi EduTrack Pro
    dateFormat  YYYY-MM-DD
    section Paket Kerja 1: Fondasi & Database
    WBS 1.1: Inisialisasi Nuxt 3 / Fastify TypeScript Monorepo :active, p1_db, 2026-11-26, 3d
    WBS 1.2: Skema DDL 21 Tabel PostgreSQL 16 & Drizzle ORM   :p1_drizzle, after p1_db, 2d
    WBS 1.3: Seeder Master GTK, Siswa, Ortu & RBAC            :p1_seed, after p1_drizzle, 2d

    section Paket Kerja 2: Layanan Inti & Scanner 3-Mode
    WBS 2.1: AttendanceRuleEngine 3-Mode (Masuk/GatePass/Pulang):p2_rule, 2026-12-03, 3d
    WBS 2.2: Keadilan Checkout Fallback & Izin Sakit Fleksibel  :p2_fair, after p2_rule, 2d
    WBS 2.3: Kriptografi Barcode HMAC & BullMQ Redis WA Queue   :p2_sec, after p2_fair, 3d

    section Paket Kerja 3: Modul Antarmuka & Kesiswaan
    WBS 3.1: Meja Scanner Gerbang Live & Web Audio Chime 3-Tone :p3_scan, 2026-12-11, 3d
    WBS 3.2: Generator Kartu Pelajar PVC ISO CR-80 & Revocation :p3_card, after p3_scan, 2d
    WBS 3.3: Presensi Mapel KBM, Deteksi Membolos & Cetak SP    :p3_tatib, after p3_card, 3d
    WBS 3.4: Kanal Whistleblower AES-256 & Master GTK Admin     :p3_whistle, after p3_tatib, 2d

    section Paket Kerja 4: QA Assault & Audit Kepatuhan
    WBS 4.1: Stress Test 500 Scan Gerbang Pagi (Latensi < 50ms) :p4_load, 2026-12-21, 2d
    WBS 4.2: Pen-Test IDOR Scoping & Audit OWASP Top 10         :p4_sec, after p4_load, 2d
    WBS 4.3: Pengujian Pemulihan Bencana Backup 3-2-1 S3 Vault  :p4_dr, after p4_sec, 1d

    section Paket Kerja 5: Deployment aaPanel & Bare VPS Docker
    WBS 5.1: Konfigurasi PM2 Cluster aaPanel & Docker Compose   :p5_dep, 2026-12-26, 2d
    WBS 5.2: Uji Coba Hardware Scanner USB di Pos Satpam        :p5_field, after p5_dep, 2d
```

---

## 2. Struktur Pemecahan Kerja (Work Breakdown Structure - WBS)

### Paket Kerja 1: Fondasi Sistem JavaScript, Drizzle ORM & Database (WP-1)
* **WBS 1.0**: Inisialisasi proyek Nuxt 3 / Node.js Fastify TypeScript monorepo dengan dukungan Tailwind CSS v4 dan Bootstrap 5 modern effects.
* **WBS 1.1**: Penulisan skema Drizzle ORM dan migrasi DDL 21 tabel relasional PostgreSQL 16 (lengkap dengan UUIDv7, indeks komposit, dan CHECK constraints).
* **WBS 1.2**: Pembuatan skrip seeder realistis SDN Kebonagung Kota Pasuruan (Master GTK, rombel kelas, preset tatib, siswa, orang tua).
* **WBS 1.3**: Konfigurasi otentikasi JWT/Sesi, 2FA TOTP, dan middleware `dataScope.ts` untuk isolasi query data di sisi server.

### Paket Kerja 2: Mesin Bisnis Presensi 3-Mode, BullMQ & Kriptografi (WP-2)
* **WBS 2.1**: Implementasi kelas layanan `AttendanceRuleEngineService.ts` untuk pemindai 3 mode otomatis (Mode 1 Masuk 06:00-08:00, Mode 2 Siaga Gate Pass 08:01-13:00, Mode 3 Pulang Sore 13:01-17:00).
* **WBS 2.2**: Implementasi `CheckoutJusticeService.ts` untuk menjamin keadilan siswa yang lupa tap kartu pulang tetap berstatus Hadir jika hadir di KBM jam terakhir.
* **WBS 2.3**: Implementasi alur pengajuan izin fleksibel (Sakit 1 hari tanpa surat dokter maks 2x/semester vs Sakit >1 hari wajib surat dokter).
* **WBS 2.4**: Implementasi tanda tangan digital HMAC-SHA256 untuk kartu CR-80 dan antrean asinkron BullMQ + Redis untuk dispatch notifikasi WhatsApp masuk dan pulang.

### Paket Kerja 3: Modul Antarmuka, Meja Scanner & Kesiswaan (WP-3)
* **WBS 3.1**: Pembangunan antarmuka Livewire/Vue 3 Meja Scanner Gerbang (`/scanner`) dengan input burst laser gun scanner, indikator visual 3 mode, dan Web Audio API (Beep 880Hz, Buzz 180Hz, Chime 3 nada C-E-G).
* **WBS 3.2**: Pembangunan modul Cetak Kartu Pelajar PVC ISO CR-80 dengan fitur *revocation* dan cetak printer kartu fisik.
* **WBS 3.3**: Pembangunan modul Presensi KBM Mapel dengan deteksi siswa membolos KBM (+10 Poin Tatib) dan penerbitan Surat Peringatan (SP 1, SP 2, SP 3) PDF ber-QR verification.
* **WBS 3.4**: Pembangunan modul Master GTK (khusus Super Admin) dan kanal aduan whistleblower terenkripsi AES-256-GCM.
* **WBS 3.5**: Integrasi komponen wajib: Pagination dinamis, debounced searching, multi-criteria filter, grafik Chart.js, kartu statistik KPI, dan info icons.

### Paket Kerja 4: Penjaminan Mutu (*QA Assault*) & Audit Kepatuhan (WP-4)
* **WBS 4.1**: *Stress Testing* pemindaian gerbang serentak (50 request/detik) memastikan respon server Fastify $\le 50\text{ ms}$ tanpa deadlock basis data.
* **WBS 4.2**: Audit keamanan penetrasi (Pen-Test) IDOR antar role orang tua/siswa dan audit injeksi SQL / XSS.
* **WBS 4.3**: Pengujian pemulihan data (*Disaster Recovery*) dari berkas snapshot terenkripsi di AWS S3 Vault.

### Paket Kerja 5: Pengemasan Infrastruktur & Deployment Ganda (WP-5)
* **WBS 5.1**: Penyiapan konfigurasi PM2 Cluster Mode (`ecosystem.config.js`) dan Nginx Reverse Proxy untuk aaPanel Linux Hosting.
* **WBS 5.2**: Pembuatan berkas mandiri `docker-compose.yml` multi-container untuk Bare VPS Linux (Nginx, Node.js App, Postgres, Redis, Cron).
* **WBS 5.3**: Uji coba lapangan menggunakan perangkat scanner laser USB di pos satpam sekolah dan printer kartu PVC.

---

## 3. Matriks Manajemen Risiko (Risk Register)

| ID Risiko | Deskripsi Risiko | Dampak | Probabilitas | Rencana Mitigasi & Kontingensi |
| :--- | :--- | :---: | :---: | :--- |
| **R-01** | **Penumpukan Antrean Gerbang Pagi**: Scanner lambat membaca kartu kotor atau koneksi drop. | **Tinggi** | Rendah | Menggunakan USB Laser Gun Scanner dengan Fastify backend (< 50ms); antrean asinkron BullMQ mencegah beban server. |
| **R-02** | **Kecurangan Titip Absen**: Siswa memfotokopi barcode atau mengirimkan foto barcode via WA. | **Tinggi** | Sedang | Menerapkan QR Code bertanda tangan digital HMAC-SHA256 yang divalidasi keaktifan kartu (`One-Card One-Active Policy`). |
| **R-03** | **Siswa Berebut Pulang Lupa Tap**: Ratusan siswa lari keluar gerbang sore tanpa men-tap kartu. | **Tinggi** | Tinggi | Diterapkan **Pola Keadilan Checkout Fallback**: Siswa tetap Hadir jika tercatat di KBM jam terakhir. |
| **R-04** | **Kekakuan Izin Sakit Demam 1 Hari**: Orang tua komplain karena dipaksa melampirkan surat dokter. | **Sedang** | Tinggi | Disediakan kategori **Sakit 1 Hari Mandiri** (cukup surat pernyataan orang tua, kuota maks 2x per semester). |
| **R-05** | **Kebocoran Identitas Whistleblower**: Data korban perundungan bocor ke pelaku/publik. | **Kritis** | Rendah | Enkripsi tingkat kolom AES-256-GCM; data anonim tidak menyimpan relasi ke ID siswa di basis data; audit log mencatat akses modul. |

---

## 4. Kriteria Penyelesaian (Definition of Done - DoD)

1. **Fungsionalitas 3 Mode Presensi**: Pemindai gerbang otomatis beralih mode (Masuk 06:00-08:00, Gate Pass 08:01-13:00, Pulang 13:01-17:00) dan mengirim notifikasi WhatsApp yang sesuai.
2. **Kinerja Scanner Gerbang**: Endpoint `POST /api/attendance/gate-scan` terbukti merespons dalam $\le 50\text{ ms}$ pada Node.js Fastify/Nitro.
3. **Keadilan Kepulangan Terverifikasi**: Siswa yang lupa tap pulang terbukti tidak dihukum Alpa di database.
4. **Isolasi Data Sisi Server**: Akun orang tua dan siswa terbukti secara teknis tidak dapat mengakses data di luar cakupannya (*Zero IDOR Leakage*).
5. **Kepatuhan Kriptografi ISO 27001**: Data aduan tersimpan dalam bentuk *cipher text* AES-256, dan log audit terlindungi secara kekal (*immutable*).
6. **Kesiapan UI Putih Minimalis**: Seluruh tabel dilengkapi pagination dinamis, searching reaktif, multi-filter, grafik Chart.js, dan info cards.