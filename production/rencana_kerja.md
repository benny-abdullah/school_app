# 📋 Rencana Kerja Implementasi Produksi (Production Roadmap)
**Aplikasi**: Star Kids SDN Kebonagung (Sistem Monitoring Terpadu Sekolah & Kesiswaan)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Target Arsitektur**: Full-Stack Laravel 13 (PHP 8.4) + Livewire 3 + Alpine.js + Tailwind CSS + PostgreSQL 16 / MySQL + Redis 7  
**Lokasi Direktori Produksi**: `C:\my_project\scholl_app\production`  
**Prinsip Kerja**: Bertahap (*Incremental Step-by-Step*), Terukur, Aman (*Zero-Downtime*), dan Teruji (*Test-Driven*).

---

## 📌 Petunjuk Kontrol Pekerjaan untuk Pemilik Proyek (User Guide)
Dokumen ini berfungsi sebagai **Checklist Kendali Utama** bagi Anda untuk memantau, menyetujui, dan mengontrol setiap tahapan migrasi dari prototype HTML/JS ke sistem produksi Laravel 13.
- `[ ]` : Belum dikerjakan (*Pending / Backlog*)
- `[/]` : Sedang dikerjakan (*In Progress*)
- `[x]` : Selesai dan terverifikasi (*Done / Verified*)

> **Cara Melanjutkan**: Cukup berikan perintah sesuai nomor tahap, misalnya:  
> *"Lanjutkan ke Tahap 1: Setup Proyek & Fondasi"* atau *"Lanjutkan Tahap 2.1 Migrasi Database"*.

---

## 🏗️ Tahap 1: Inisialisasi Proyek & Fondasi Sistem Laravel 13
Fokus: Membangun kerangka dasar aplikasi Laravel 13 bersih di dalam folder `production/`.

- [x] **1.1 Inisialisasi Kerangka Laravel 13 (PHP 8.4)**
  - [x] Generate struktur proyek Laravel 13 di `production/`
  - [x] Konfigurasi file `.env` (Database MySQL `db_starkids`, App Name, Timezone `Asia/Jakarta` WIB, Locale `id`)
  - [x] Konfigurasi Composer dependencies (`livewire/livewire`, `predis/predis`, dsb.)
- [x] **1.2 Setup Aset Antarmuka & Desain Sistem (Tailwind CSS v4 & Alpine.js)**
  - [x] Setup Tailwind CSS dengan palet warna resmi: Primary Rose-600 (`#e11d48`), Emerald-600, Slate-900
  - [x] Integrasi Tipografi Google Font *Plus Jakarta Sans* (Weights 300 - 800)
  - [x] Integrasi Icon Library: *Bootstrap Icons* (v1.11.3) via NPM
  - [x] Konfigurasi Alpine.js & Livewire 3 micro-interactions
  - [x] Integrasi Logo Resmi SDN Kebonagung (`logo_sdnkebonagung.jpeg`) ke aset publik
  - [x] Penyelarasan tema & komponen UI identik dengan `index.html` (HRD Clean White, status badges, laser scanner)
- [x] **1.3 Integrasi Library Pemindai & Visualisasi Data**
  - [x] Integrasi `html5-qrcode` untuk pemindai kamera barcode HP
  - [x] Integrasi Chart.js untuk dasbor statistik tren kehadiran
  - [x] Integrasi Web Audio API Synthesizer (Chime C-E-G, Beep 880Hz, Buzz 180Hz)

---

## 🗄️ Tahap 2: Skema Basis Data, Eloquent Model & Seeder Master Data
Fokus: Membangun struktur database relasional berstandar enterprise dengan UUIDv7, indeks teroptimasi, dan data awal realistis.

- [ ] **2.1 Migrasi Database (21 Tabel Relasional)**
  - [ ] Tabel Kepegawaian & GTK: `teachers` (NIP, NUPTK, nama, jabatan, status, kontak)
  - [ ] Tabel Akademik & Kelas: `academic_years`, `classrooms` (wali kelas, rombel, kuota)
  - [ ] Tabel Kesiswaan & Orang Tua: `students`, `guardians`, `student_guardian_rel`
  - [ ] Tabel Presensi & Scanner: `attendances` (check-in, check-out, status, keadilan checkout), `gate_passes`
  - [ ] Tabel Kesiswaan & Tatib: `discipline_violations`, `student_merits`, `counseling_cases`
  - [ ] Tabel Keamanan & Kartu: `student_card_tokens` (HMAC token, RFID UID, status active/revoked), `audit_logs`, `backups`
  - [ ] Tabel Whistleblower & Aduan: `whistleblower_reports`, `report_messages` (AES-256-GCM encrypted)
- [ ] **2.2 Definisi Model Eloquent & Relasi Data**
  - [ ] Model `Student`, `Teacher`, `Attendance`, `GatePass`, `DisciplineViolation`, `StudentMerit`, `WhistleblowerReport`
  - [ ] Trait `HasUuids` (UUIDv7 berurutan berbasis waktu untuk performa indeks tinggi)
  - [ ] Local Query Scopes per Role (`scopeForUserRole`, `scopeActiveToday`)
- [x] **2.3 Database Seeder Realistis (SDN Kebonagung Kota Pasuruan)**
  - [x] Seeder 4 Akun Role Demo Siap Uji Login:
    - [x] Role `admin`: Login NIP `197103151998021004`, Password NIP `197103151998021004` (Drs. H. Mulyadi M.Pd - Kepala Sekolah)
    - [x] Role `operator`: Login NIP/NUPTK `198504202008011005`, Password NIP `198504202008011005` (Siti Aminah S.Kom - Operator Dapodik/TU)
    - [x] Role `guru/walikelas`: Login NIP/NUPTK `198007152006041012`, Password NIP `198007152006041012` (Bambang S.Pd - Wali Kelas 5A)
    - [x] Role `orangtua`: Login NISN Anak `0081234567`, Password NISN `0081234567` (Bpk. Hendra - Wali Murid Ahmad Rifai Kelas 5A)
  - [ ] Seeder Master GTK (Guru Kelas 1 s/d 6, Guru Mapel Agama/PJOK, Operator TU)
  - [x] Seeder Master 29 Rombel Kelas Lengkap:
    - [x] Tingkat 1 (4 Kelas): `1A`, `1B`, `1C`, `1D`
    - [x] Tingkat 2 (5 Kelas): `2A`, `2B`, `2C`, `2D`, `2E`
    - [x] Tingkat 3 (5 Kelas): `3A`, `3B`, `3C`, `3D`, `3E`
    - [x] Tingkat 4 (5 Kelas): `4A`, `4B`, `4C`, `4D`, `4E`
    - [x] Tingkat 5 (5 Kelas): `5A`, `5B`, `5C`, `5D`, `5E`
    - [x] Tingkat 6 (5 Kelas): `6A`, `6B`, `6C`, `6D`, `6E`
  - [ ] Seeder Siswa & Wali Murid Binaan SDN Kebonagung (Ahmad Rifai NISN `0081234567` dkk)
  - [ ] Seeder Preset Aturan Poin Pelanggaran & Poin Prestasi
- [x] **2.4 Master Data & Manajemen Rombel Kelas (CRUD & Filter 29 Kelas)**
  - [x] Model `Classroom` dengan atribut: `id`, `code`, `name` (1A s/d 6E), `grade_level` (1 s/d 6), `homeroom_teacher`, `homeroom_nip`, `capacity`, `total_students`
  - [x] Antarmuka Manajemen Kelas di Modul Operator & Filter Tab 29 Kelas
- [ ] **2.5 Master CRUD Operator (Siswa, Mutasi Rombel, Orang Tua & Impor Dapodik)**
  - [ ] CRUD Master Siswa: Form tambah siswa, edit biodata, NISN 10 digit, nomor kontak ortu/WA
  - [ ] Fitur Mutasi Rombel Siswa: Pindah kelas antar rombel (misal 1A ke 1B) & riwayat mutasi
  - [ ] Impor Data Siswa Massal: Format Excel / CSV Dapodik dengan validasi duplikasi NISN otomatis
  - [ ] CRUD Relasi Orang Tua / Wali Murid: Pengaturan kontak WhatsApp untuk notifikasi gerbang real-time
  - [ ] Pengaturan Kuota & Kapasitas 29 Rombel Kelas (1A s/d 6E)

---

## 🔐 Tahap 3: Autentikasi, Multi-Role RBAC & Shell Antarmuka
Fokus: Keamanan autentikasi tanpa cela (*Zero IDOR*) dan antarmuka *Enterprise Clean White*.

- [x] **3.1 Sistem Autentikasi Modern (Direct Login Page)**
  - [x] Tampilan halaman login langsung (tanpa halaman landing page) dengan gaya HRD Modern Clean White & logo resmi SDN Kebonagung
  - [x] Logika Login Fleksibel Multi-Identifier:
    - [x] Login Guru, Operator & Admin: Input NIP (18 digit) atau NUPTK (16 digit) & Password = NIP/NUPTK
    - [x] Login Orang Tua: Input NISN Siswa/Anaknya (10 digit) & Password = NISN Siswa
  - [x] Fitur 1-Klik Pemilih Role Demo (Admin, Operator, Guru/Wali Kelas, Orang Tua) untuk pengujian instan
  - [x] Captcha Penjumlahan Matematika Sederhana untuk proteksi bot & brute-force
  - [x] Redirection otomatis sesuai role ke dashboard masing-masing setelah autentikasi sukses
- [x] **3.2 RBAC Multi-Role & Middleware Proteksi Data (Zero IDOR)**
  - [x] Definisi Middleware `RoleMiddleware` untuk 4 Role (`admin`, `operator`, `guru/walikelas`, `orangtua`)
  - [x] Proteksi Rute Terisolasi (User role lain tidak dapat mengakses dashboard role lain)
  - [ ] Audit Trail Logger otomatis saat user login, logout, atau mengubah data sensitif
- [x] **3.3 Shell Antarmuka & Dashboard Khusus Masing-Masing Role**
  - [x] Shell Antarmuka Master `layouts/app.blade.php` dengan Sidebar responsif, Glassmorphism Header, dan Logo Resmi
  - [x] Tampilan 4 Dashboard Khusus Sesuai Hak Akses Role:
    - [x] **Dashboard Admin (Kepsek)**: Monitoring metrik global sekolah, rasio kehadiran, dan ringkasan 29 rombel
    - [x] **Dashboard Operator**: Status 29 kelas lengkap, siswa aktif, shortcut cetak kartu CR-80, scanner gerbang
    - [x] **Dashboard Guru / Wali Kelas**: Rekap kelas binaan (5A), presensi siswa 5A realtime, tombol scanner kamera
    - [x] **Dashboard Orang Tua**: Profil ananda (Ahmad Rifai 5A), jam tap gerbang realtime, pratinjau kartu pelajar CR-80 barcode
  - [x] Notifikasi Alert Flash (Success, Error, Warning, Info)
- [ ] **3.4 Master CRUD Admin / Kepala Sekolah (Manajemen Akun, GTK & Penetapan Wali Kelas)**
  - [ ] CRUD Manajemen Akun Pengguna (*User Management*): Tambah user baru, suspend akun, reset password ke default NIP/NISN
  - [ ] CRUD Master GTK (Guru & Tenaga Kependidikan): Form tambah/edit guru, NIP (18 digit), NUPTK (16 digit), jabatan, mapel
  - [ ] Modul Penetapan & Rotasi Wali Kelas: Pengaturan guru pemegang rombel 1A s/d 6E secara terpusat oleh Kepsek
  - [ ] CRUD Master Aturan Poin Tatib & Prestasi: Konfigurasi ambang batas SP1, SP2, SP3, bobot minus pelanggaran, dan reward merit points
  - [ ] Floating AI Copilot Drawer (kanan bawah)

---

## ⚡ Tahap 4: Modul Presensi Gerbang 3-Mode & Pemindai Barcode HP
Fokus: Pembangunan mesin scanner presensi gerbang, WebRTC kamera HP, dan otomatisasi aturan jam masuk.

- [ ] **4.1 Layanan Bisnis Presensi (`AttendanceRuleEngineService.php`)**
  - [ ] Mode 1 Masuk Pagi (06:00 - 08:00 WIB): Evaluasi Tepat Waktu vs Terlambat T-1 (-2 pt), T-2 (-5 pt), T-3 (-10 pt)
  - [ ] Mode 2 Siaga Digital Gate Pass (08:01 - 13:00 WIB): Verifikasi QR izin keluar siswa & validasi satpam
  - [ ] Mode 3 Pulang Sore (13:01 - 17:00 WIB): Perekaman check-out kepulangan otomatis
  - [ ] Layanan Keadilan Checkout (`CheckoutJusticeService.php`): Siswa lupa tap pulang tidak dihukum Alpa jika hadir di KBM jam terakhir
  - [ ] Scheduled Task Cron Cutoff 08:00 WIB: Auto-Alpa bagi yang belum tap masuk + alert darurat
- [ ] **4.2 Komponen Livewire Meja Scanner Gerbang (`/scanner`)**
  - [ ] Tab Switcher 3 Mode waktu otomatis
  - [ ] Simulasi Jam Gerbang & Tombol Uji Coba Tap 1-Klik
  - [ ] Terminal Input Box (dukungan Barcode Gun USB keyboard-wedge & manual NISN)
  - [ ] Kartu Monitor Real-Time: Foto siswa, NISN, Rombel, Hasil Evaluasi, Status Poin Tatib
  - [ ] Tabel Riwayat Scan Gerbang Hari Ini (live updates via Livewire)
- [ ] **4.3 Pemindai Barcode Kamera HP (Mobile WebRTC Scanner)**
  - [ ] Pemilih Lensa: Kamera Belakang (*Environment*) vs Kamera Depan (*User / Stand*)
  - [ ] Kontrol Kamera: Tombol Buka/Tutup Kamera, Senter (*Torch*), status aktif
  - [ ] Viewfinder Frame dengan sudut target dan animasi garis laser merah (`.scanner-laser`)
  - [ ] Algoritma deteksi instan Barcode 1D Code 128 & QR Code 2D
  - [ ] Getar Haptic HP ganda `[70ms, 40ms, 70ms]` & audio chime feedback
  - [ ] Fitur *Auto-Continuous Scan* dengan jeda anti-duplikasi 2.5 detik
  - [ ] Otomasi hemat baterai (kamera mati saat pindah menu atau logout)

---

## 🪪 Tahap 5: Direktori Siswa & Generator Cetak Kartu Pelajar ISO CR-80
Fokus: Pengelolaan data siswa skala besar dan pencetakan kartu fisik standar perbankan.

- [ ] **5.1 Direktori Siswa Berkapasitas Tinggi (*High-Capacity Directory*)**
  - [ ] 4 Kartu KPI Ringkasan: Total Siswa, Kartu Aktif, Rombel Kelas, Siswa Terpilih
  - [ ] Live Search Bar (pencarian nama, NISN, atau kelas secara real-time)
  - [ ] Toolbar Filter Cerdas: Filter Rombel Kelas, Filter Status Kartu (*Aktif*, *Perlu Cetak Ulang*, *Revoked*)
  - [ ] Pemilih Baris per Halaman (5, 10, 20 data/hal) & Tombol Reset Filter
  - [ ] Tabel Siswa Interaktif dengan highlight baris aktif (`bg-rose-50/70 border-l-4 border-rose-600`)
  - [ ] Kontrol Paginasi Cerdas (*"Menampilkan X - Y dari Z siswa"*)
- [ ] **5.2 Generator Kanvas Fisik Kartu ISO CR-80 (85.60 x 53.98 mm)**
  - [ ] Pratinjau bolak-balik fisik CR-80 skala akurat di samping tabel
  - [ ] Sisi Depan: Kop sekolah, Pas foto 3x4, biodata, Barcode 1D Code 128 (NISN), QR Code bertanda tangan HMAC-SHA256
  - [ ] Sisi Belakang: Ketentuan tata tertib, masa berlaku, kontak darurat, Tanda Tangan Digital QR Kepala Sekolah
- [ ] **5.3 Layanan Keamanan & Pencetakan Kartu**
  - [ ] Tombol Cetak Tunggal (CSS Print khusus printer ID Card PVC)
  - [ ] Tombol Cetak Massal 1 Rombel (Template Grid A4 isi 8 kartu)
  - [ ] Layanan *Revoke & Regenerate*: Mematikan kartu lama yang hilang seketika dan menerbitkan token UID baru

---

## ⚖️ Tahap 6: Kesiswaan, Presensi KBM, Tatib, Prestasi & Whistleblower
Fokus: Penegakan tata tertib humanis, pencegahan membolos, portofolio prestasi, dan perlindungan siswa.

- [ ] **6.1 Presensi KBM Jam Mapel & Deteksi Membolos**
  - [ ] Pemilih mata pelajaran aktif & sinkronisasi data presensi gerbang pagi
  - [ ] Fitur Lapor Membolos ke BK (+10 poin tatib, log audit, notifikasi darurat)
- [ ] **6.2 Modul Kedisiplinan & Generator Surat Peringatan (SP)**
  - [ ] Buku saku poin tatib per siswa (kategori Pelanggaran Ringan, Sedang, Berat)
  - [ ] Evaluasi kumulatif poin tatib otomatis (Ambang batas SP-1, SP-2, SP-3 / Panggilan Orang Tua)
  - [ ] Generator dokumen resmi cetak Surat Panggilan Orang Tua & Rekomendasi Home Visit BK
- [ ] **6.3 Modul Portofolio & Bank Prestasi (*Merit Points*)**
  - [ ] Input prestasi akademik & non-akademik (Juara Lomba, Pramuka, Karakter Positif)
  - [ ] Penambahan Merit Points yang dapat mereduksi (*offset*) poin pelanggaran ringan
- [ ] **6.4 Pengajuan Izin / Sakit Fleksibel Orang Tua & Digital Gate Pass**
  - [ ] Formulir izin sakit mandiri 1 hari (kuota maks 2x/semester tanpa surat dokter)
  - [ ] Formulir izin sakit >1 hari (wajib unggah surat dokter)
  - [ ] Verifikasi izin 1-klik oleh Wali Kelas
  - [ ] Penerbitan Digital Gate Pass QR saat siswa dijemput pulang cepat
- [ ] **6.5 Pusat Aduan & Whistleblower Terenkripsi AES-256 (Anti-Bullying)**
  - [ ] Formulir laporan anonim vs terbuka
  - [ ] Enkripsi end-to-end kolom sensitif basis data via `Laravel Crypt`
  - [ ] Ruang obrolan rahasia dua arah antara pelapor anonim dan Satgas Anti-Kekerasan Sekolah

---

## 🤖 Tahap 7: Dasbor Analisis AI (Rule-Based & Google Gemini 1.5 Flash)
Fokus: Deteksi dini siswa bermasalah dan rekomendasi tindakan pedagogik empatik.

- [ ] **7.1 Layanan Deteksi Dini Rule-Based (`AiStudentAnalysisService.php`)**
  - [ ] Logika filter penurunan nilai belajar: `(nilai_matematika_minggu2 < nilai_matematika_minggu1 - 10)`
  - [ ] Logika filter absensi kritis: `(alpa_bulan_ini >= 3)`
  - [ ] Pengelompokan siswa berisiko secara dinamis dari basis data
- [ ] **7.2 Integrasi Google Gemini 1.5 Flash API**
  - [ ] Service caller ke Google Generative Language API v1beta
  - [ ] Format prompt pedagogis: asisten guru empatik, analisis maksimal 3 kalimat + 1 saran konkret
  - [ ] Manajemen API Key aman via `.env` (`GEMINI_API_KEY`)
- [ ] **7.3 Tampilan UI Dasbor Analisis AI**
  - [ ] Kartu putih sudut membulat dengan ikon peringatan oranye/merah
  - [ ] Teks analisis ringkas rekomendasi AI Gemini
  - [ ] Tombol "Muat Ulang Analisis" interaktif
- [ ] **7.4 Floating AI Copilot & Slide-Out Assistant**
  - [ ] Floating button dengan gradient ungu-biru di pojok layar
  - [ ] Slide-out drawer dengan 5 prompt cepat kesiswaan (Ringkas presensi, Cek alpa berulang, Draf SP, Draf WA, Soal HOTS)

---

## 👥 Tahap 8: Master Data GTK, Log Audit & Backup Data ISO 27001
Fokus: Tata kelola kepegawaian Dapodik dan kepatuhan standar keamanan data internasional.

- [ ] **8.1 Master Data Guru & Tenaga Kependidikan (Eksklusif Admin/Kepsek)**
  - [ ] Manajemen CRUD data GTK (NIP, NUPTK, Nama & Gelar, Gender, Status Kepegawaian, Jabatan, Rombel Binaan, No WA)
  - [ ] Validasi penomoran NIP 18 digit & NUPTK 16 digit
- [ ] **8.2 Log Audit Kekal ISO/IEC 27001 A.12.4**
  - [ ] Pencatatan transaksi sistem (aktor, peran, timestamp, IP address, modul, payload sebelum/sesudah)
  - [ ] Fitur pelindung integritas log (*append-only*, tidak dapat di-edit atau dihapus oleh siapapun)
- [ ] **8.3 Manajemen Snapshot Backup 3-2-1**
  - [ ] Jadwal backup otomatis database & berkas terenkripsi
  - [ ] Simulasi uji restore pemulihan data bencana (*disaster recovery verification*)

---

## 🧪 Tahap 9: Pengujian Kualitas Sistem (Testing & Quality Assurance)
Fokus: Memastikan keandalan, keamanan, dan kinerja sistem sebelum rilis.

- [ ] **9.1 Automated Unit & Feature Testing (Pest / PHPUnit)**
  - [ ] Unit test `AttendanceRuleEngineService` (menguji semua skenario waktu masuk, terlambat, dan pulang)
  - [ ] Unit test `CheckoutJusticeService` (menguji keadilan siswa lupa tap pulang)
  - [ ] Feature test RBAC & Data Isolation (memastikan orang tua A tidak dapat melihat data siswa B)
- [ ] **9.2 Stress Testing Simulasi Gerbang Pagi**
  - [ ] Pengujian konkurensi 500 pemindaian beruntun (target response time < 50ms per scan)
  - [ ] Verifikasi antrean Redis WhatsApp API tidak mengalami bottle-neck
- [ ] **9.3 Pengujian Kompatibilitas Pemindai Kamera HP**
  - [ ] Uji coba akses kamera di Android Chrome & iOS Safari
  - [ ] Uji coba peralihan kamera depan/belakang dan senter

---

## 🚀 Tahap 10: Persiapan Produksi & Deployment (Go-Live)
Fokus: Penerapan sistem di server produksi dan serah terima operasional.

- [ ] **10.1 Konfigurasi Lingkungan Server Produksi**
  - [ ] Konfigurasi Nginx Web Server dengan proteksi HTTP Security Headers
  - [ ] Konfigurasi PHP 8.4-FPM, OPcache, dan JIT Compiler
  - [ ] Konfigurasi Supervisor Daemon untuk Worker Antrean Redis & Laravel Reverb
  - [ ] Konfigurasi Cron Job Laravel Scheduler (`* * * * * php artisan schedule:run`)
  - [ ] Penerbitan Sertifikat SSL Let's Encrypt (HTTPS wajib untuk WebRTC kamera HP)
- [ ] **10.2 Build & Optimasi Performa Produksi**
  - [ ] Kompilasi aset Vite/Tailwind (`npm run build`)
  - [ ] Optimasi caching Laravel (`php artisan config:cache`, `route:cache`, `view:cache`, `event:cache`)
- [ ] **10.3 Uji Coba Lapangan di Pos Gerbang & Sosialisasi Guru**
  - [ ] Uji coba langsung barcode scanner gun di laptop pos satpam
  - [ ] Uji coba pemindaian kamera HP oleh perwakilan wali kelas
  - [ ] Panduan ringkas operasional bagi guru dan satpam

---

## 📊 Matriks Progres Pekerjaan Ringkas
| Tahap | Deskripsi Modul / Paket Kerja | Status |
| :---: | :--- | :---: |
| **Tahap 1** | Inisialisasi Proyek & Fondasi Laravel 13 | ✅ Selesai (100%) |
| **Tahap 2** | Skema Basis Data, Eloquent & Master 29 Kelas (1A-6E) | ⏳ Pending |
| **Tahap 3** | Autentikasi Modern, RBAC & Clean White Shell | ⏳ Pending |
| **Tahap 4** | Presensi Gerbang 3-Mode & Pemindai Kamera HP | ⏳ Pending |
| **Tahap 5** | Direktori Siswa & Cetak Kartu Pelajar ISO CR-80 | ⏳ Pending |
| **Tahap 6** | Kesiswaan, Presensi KBM, Tatib, Prestasi & Whistleblower | ⏳ Pending |
| **Tahap 7** | Dasbor Analisis AI (Rule-Based & Google Gemini 1.5) | ⏳ Pending |
| **Tahap 8** | Master GTK, Log Audit & Backup ISO 27001 | ⏳ Pending |
| **Tahap 9** | Pengujian Kualitas Sistem (Testing & QA) | ⏳ Pending |
| **Tahap 10**| Deployment Server Produksi & Go-Live | ⏳ Pending |

*Dokumen ini akan terus diperbarui status checklist-nya seiring berjalannya setiap tahapan pekerjaan.*
