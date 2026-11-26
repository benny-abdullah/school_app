# Manajemen Proyek, WBS & Peta Jalan (Project Management) - SSOT
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Metodologi**: Agile Iteratif / Sprint-Based (5 Paket Kerja)  
**Target Rilis**: Sistem Produksi Terintegrasi (Enterprise Grade)

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk rencana kerja, pemecahan tugas (*Work Breakdown Structure* / WBS), manajemen risiko (*Risk Register*), dan kriteria penyelesaian (*Definition of Done* / DoD).

---

## 1. Peta Jalan Pengembangan (Milestone Roadmap)

```mermaid
gantt
    title Peta Jalan Implementasi Produksi EduTrack Pro
    dateFormat  YYYY-MM-DD
    section Paket Kerja 1: Fondasi & Database
    WBS 1.1: Scaffolding Laravel 11 & Skema DDL 21 Tabel :active, p1_db, 2026-11-26, 3d
    WBS 1.2: Seeder Master GTK, Siswa, Ortu & Tatib       :p1_seed, after p1_db, 2d
    WBS 1.3: Konfigurasi RBAC Spatie & Middleware Scope   :p1_rbac, after p1_seed, 2d

    section Paket Kerja 2: Layanan Inti & Scanner Gerbang
    WBS 2.1: AttendanceRuleEngine & Auto-Alpa Scheduler   :p2_rule, 2026-12-03, 3d
    WBS 2.2: Kriptografi Barcode HMAC-SHA256 & Gate Pass  :p2_sec, after p2_rule, 3d
    WBS 2.3: Redis Queue & WhatsApp Dispatcher Worker     :p2_wa, after p2_sec, 2d

    section Paket Kerja 3: Modul Antarmuka & Kesiswaan
    WBS 3.1: Meja Scanner Gerbang & Web Audio Feedback   :p3_scan, 2026-12-11, 3d
    WBS 3.2: Generator Kartu Pelajar PVC ISO CR-80        :p3_card, after p3_scan, 2d
    WBS 3.3: Buku Tatib, Otomatisasi SP & Bank Prestasi   :p3_tatib, after p3_card, 3d
    WBS 3.4: Kanal Whistleblower Terenkripsi AES-256      :p3_whistle, after p3_tatib, 2d

    section Paket Kerja 4: QA Assault & Audit Kepatuhan
    WBS 4.1: Uji Beban Konkurensi 500 Scan Gerbang Pagi  :p4_load, 2026-12-21, 2d
    WBS 4.2: Audit Keamanan OWASP Top 10 & IDOR Scoping  :p4_sec, after p4_load, 2d
    WBS 4.3: Pengujian Disaster Recovery Backup 3-2-1     :p4_dr, after p4_sec, 1d

    section Paket Kerja 5: Server Hardening & Go-Live
    WBS 5.1: Deployment aaPanel Nginx PHP-FPM & VPS Docker:p5_dep, 2026-12-26, 2d
    WBS 5.2: Uji Lapangan Scanner Gerbang di Pos Satpam   :p5_field, after p5_dep, 2d
```

---

## 2. Struktur Pemecahan Kerja (Work Breakdown Structure - WBS)

### Paket Kerja 1: Fondasi Sistem, Database & Akses Kontrol (WP-1)
* **WBS 1.0**: Inisialisasi proyek Laravel 11/12 dengan PHP 8.3, integrasi Livewire 3, Tailwind CSS, dan Octicons.
* **WBS 1.1**: Penulisan migrasi DDL PostgreSQL 16 untuk 21 tabel relasional (lengkap dengan UUIDv7, indeks komposit, dan CHECK constraints).
* **WBS 1.2**: Pembuatan skrip *database seeder* realistis SDN Kebonagung Kota Pasuruan (Master GTK, rombel kelas, akun pengguna, preset tatib, data siswa, data orang tua).
* **WBS 1.3**: Konfigurasi Spatie Laravel Permission dan pembuatan Middleware `EnforceServerDataScope` untuk isolasi query database di sisi backend.

### Paket Kerja 2: Mesin Bisnis Presensi, Kriptografi & WhatsApp Gateway (WP-2)
* **WBS 2.1**: Implementasi kelas layanan `AttendanceRuleEngineService` (evaluasi scan 06:00-07:00 Hadir, 07:01-07:15 T-1, 07:16-07:30 T-2, >07:30 T-3, dan anti-double tap cooldown 5 menit).
* **WBS 2.2**: Implementasi Cron Job scheduler pukul 08:00 WIB untuk deteksi siswa yang belum scan dan penandaan otomatis status Auto-Alpa (-15 Poin).
* **WBS 2.3**: Pembuatan modul tanda tangan digital HMAC-SHA256 untuk payload QR Code kartu pelajar dan token Digital Gate Pass.
* **WBS 2.4**: Integrasi antrean asinkron Redis Queue untuk pengiriman pesan alert WhatsApp ke nomor orang tua siswa.

### Paket Kerja 3: Modul Antarmuka, Meja Scanner & Kesiswaan (WP-3)
* **WBS 3.1**: Pembangunan antarmuka Livewire Meja Scanner Gerbang (`/scanner`) dengan penanganan input USB barcode gun, indikator visual status, dan umpan balik suara Web Audio API (Beep 880Hz / Buzz 180Hz).
* **WBS 3.2**: Pembangunan modul Cetak Kartu Pelajar PVC ISO CR-80 dengan fitur *revocation* dan cetak printer kartu fisik.
* **WBS 3.3**: Pembangunan modul Kedisiplinan & Tatib, pembuatan otomatis Surat Peringatan (SP 1, SP 2, SP 3) format PDF ber-QR Code resmi.
* **WBS 3.4**: Pembangunan modul Kanal Aduan & Whistleblower dengan enkripsi kolom AES-256-GCM dan ruang obrolan investigasi dua arah.
* **WBS 3.5**: Pembangunan modul Master GTK (eksklusif Super Admin) serta sinkronisasi Master Siswa dan Orang Tua.

### Paket Kerja 4: Penjaminan Mutu (*QA Assault*) & Audit Kepatuhan (WP-4)
* **WBS 4.1**: *Stress Testing* pemindaian gerbang serentak (simulasi 50 scan per detik) untuk memverifikasi latensi tetap $\le 200\text{ ms}$ tanpa deadlock basis data.
* **WBS 4.2**: Pengujian penetrasi keamanan (Pen-Test) IDOR antar role orang tua/siswa dan audit injeksi SQL / XSS.
* **WBS 4.3**: Verifikasi pemulihan data (*Disaster Recovery Test*) dari berkas snapshot terenkripsi di AWS S3 Vault.

### Paket Kerja 5: Pengemasan Infrastruktur & Deployment Ganda (WP-5)
* **WBS 5.1**: Penyiapan berkas konfigurasi Nginx Reverse Proxy, socket PHP-FPM 8.3, dan Supervisor Worker untuk aaPanel.
* **WBS 5.2**: Pembuatan berkas mandiri `docker-compose.yml` multi-container untuk Bare VPS Linux (Nginx, App, Postgres, Redis, Cron).
* **WBS 5.3**: Uji coba perangkat keras fisik scanner laser USB di pos satpam sekolah dan verifikasi cetak printer PVC.

---

## 3. Matriks Manajemen Risiko (Risk Register)

| ID Risiko | Deskripsi Risiko | Dampak | Probabilitas | Rencana Mitigasi & Kontingensi |
| :--- | :--- | :---: | :---: | :--- |
| **R-01** | **Penumpukan Antrean Gerbang Pagi**: Scanner lambat membaca kartu kotor atau koneksi drop. | **Tinggi** | Rendah | Menggunakan USB Laser Gun Scanner berkecepatan 0.3s/siswa; menyediakan pos gerbang cadangan (Opsi PWA/Tablet Kiosk offline). |
| **R-02** | **Kecurangan Titip Absen**: Siswa memfotokopi barcode atau mengirimkan foto barcode via WA. | **Tinggi** | Sedang | Menerapkan QR Code ber-tanda tangan digital HMAC-SHA256 yang divalidasi keaktifan kartu (`One-Card One-Active Policy`); monitor menampilkan foto siswa seketika. |
| **R-03** | **Kebocoran Identitas Whistleblower**: Data korban perundungan bocor ke pelaku/publik. | **Kritis** | Rendah | Enkripsi tingkat kolom AES-256-GCM; data anonim tidak menyimpan relasi ke ID siswa di database; audit log mencatat setiap akses ke modul aduan. |
| **R-04** | **Kegagalan Pengiriman WhatsApp**: Provider gateway down atau nomor sekolah terblokir. | **Sedang** | Sedang | Menggunakan Redis Queue dengan retry bertingkat (3x coba); fallback notifikasi ke portal mandiri orang tua dan riwayat audit. |
| **R-05** | **Bypass Otorisasi IDOR**: Orang tua memanipulasi parameter URL/API untuk melihat rapor anak lain. | **Tinggi** | Rendah | Penegakan `RoleDataIsolationScope` di level query Eloquent server dan penggunaan UUIDv7 pada endpoint publik. |

---

## 4. Kriteria Penyelesaian (Definition of Done - DoD)

Setiap modul atau fitur dinyatakan **Selesai (Done)** dan siap rilis ke produksi hanya jika memenuhi kriteria berikut:

1. **Fungsionalitas Penuh**: Seluruh skenario pada [index.html](file:///c:/scholl_app/index.html) dan PRD terimplementasi lengkap tanpa error JavaScript konsol atau PHP exception.
2. **Kinerja Presensi Gerbang**: Endpoint `POST /api/attendance/gate-scan` terbukti merespons dalam $\le 200\text{ ms}$ pada pengujian beban.
3. **Isolasi Data Terverifikasi**: Akun orang tua dan siswa terbukti secara teknis tidak dapat menarik data di luar cakupan anak asuhnya melalui inspeksi jaringan/API.
4. **Kepatuhan Kriptografi ISO 27001**: Data aduan tersimpan dalam bentuk *cipher text* di database PostgreSQL, dan dokumen SP memuat token QR terverifikasi.
5. **Jejak Audit Otomatis**: Setiap aksi tambah, edit, atau hapus menghasilkan baris kekal di tabel `audit_logs`.
6. **Kesiapan Deployment**: Skrip migrasi database berjalan sukses dari nol (*zero-error migration & seeder*), dan worker queue Redis berjalan lancar sebagai daemon.