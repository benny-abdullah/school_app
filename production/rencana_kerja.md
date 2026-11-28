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
- [x] **2.3 Database Seeder & Impor Data Riil Dapodik (SDN Kebonagung Kota Pasuruan)**
  - [x] Impor Data Resmi 40 GTK (Guru & Tenaga Kependidikan) dari berkas `data Guru dan pegawai SDN Kebonagung.xlsx`:
    - [x] Kepala Sekolah: Wahyu Agung Setiyawan, S.Pd. (NIP `198708222019031004`, Role: `admin`)
    - [x] 28 Wali Kelas resmi untuk kelas 1A s/d 6E (Role: `guru/walikelas`)
    - [x] Guru Mapel PAI & PJOK (Role: `guru/walikelas`)
    - [x] Tenaga Kependidikan TU & PPSD/Satpam (Role: `operator`)
    - [x] Pembuatan akun login otomatis dengan Username = NIP 18 digit dan Password default = NIP
  - [x] Sinkronisasi 28 Rombongan Belajar Resmi SDN Kebonagung (1A s/d 6E):
    - [x] Tingkat 1 (4 Kelas): `1A`, `1B`, `1C`, `1D`
    - [x] Tingkat 2 (4 Kelas): `2A`, `2B`, `2C`, `2D`
    - [x] Tingkat 3 (5 Kelas): `3A`, `3B`, `3C`, `3D`, `3E`
    - [x] Tingkat 4 (5 Kelas): `4A`, `4B`, `4C`, `4D`, `4E`
    - [x] Tingkat 5 (5 Kelas): `5A`, `5B`, `5C`, `5D`, `5E`
    - [x] Tingkat 6 (5 Kelas): `6A`, `6B`, `6C`, `6D`, `6E`
    - [x] Keterhubungan relasi `homeroom_teacher_id` ke tabel `users` 100% lengkap tanpa orphan
  - [x] Impor Data Resmi 791 Siswa Aktif dari berkas `data siswa sdn kebonagung 791.xlsx`:
    - [x] Validasi 10 digit NISN unik resmi Kemdikbud (0 duplikasi)
    - [x] Penyesuaian kolom: Penambahan kolom `religion` (Agama: Islam, Kristen, Hindu) pada tabel `students`
    - [x] Parser cerdas tanggal lahir (mendukung format ISO YYYY-MM-DD, serial angka Excel, dan DD/MM/YYYY)
    - [x] Relasi langsung ke rombel `classroom_id` yang sesuai
    - [x] Pembersihan nomor kontak orang tua / WhatsApp
  - [x] Pembuatan 791 Akun Pengguna Orang Tua (`role: orangtua`) di tabel `users` dengan kredensial Login Username = NISN dan Password = NISN
  - [x] Rekalkulasi kapasitas dan jumlah siswa riil di setiap kelas (`total_students` terisi 26-32 siswa)
- [x] **2.4 Master CRUD 28 Rombel Kelas & Relasi Data Guru/Wali Kelas (Operator & Admin)**
  - [x] Relasi Skema Basis Data: Penambahan foreign key `homeroom_teacher_id` pada tabel `classrooms` yang merujuk langsung ke tabel data Guru/GTK
  - [x] Relasi Eloquent Dua Arah: `Classroom::belongsTo(User::class, 'homeroom_teacher_id')` dan `User::hasOne(Classroom::class, 'homeroom_teacher_id')`
  - [x] Wewenang Role Operator (Tata Usaha): Form edit teknis rombel (pengaturan kuota daya tampung maks 32, penamaan ruang kelas, tahun ajaran aktif)
  - [x] Wewenang Role Admin (Kepala Sekolah): Modul SK Penugasan Wali Kelas resmi (menetapkan dan merotasi guru pengampu 28 rombel 1A s/d 6E)
  - [x] Antarmuka CRUD Rombel Interaktif: Tabel direktori rombel, filter tingkat 1 s/d 6, metrik kuota dan kapasitas kelas
  - [x] Fitur Tambah Rombel Baru (`create` & `store`): Pendaftaran kelas baru jika terjadi pemekaran/penambahan rombel PPDB lengkap dengan *live preview* kode dan nama rombel otomatis
  - [x] Fitur Hapus Rombel Aman (`destroy`): Proteksi *Anti-Orphan Siswa* (sistem melarang keras penghapusan kelas jika masih berisi siswa aktif; siswa wajib dimutasikan terlebih dahulu) dan pelepasan SK wali kelas otomatis
  - [x] Integrasi Dropdown Dinamis Terpusat: Seluruh *dropdown list* pada formulir pendaftaran siswa, mutasi kelas, filter direktori, dan profil GTK otomatis tersinkron saat rombel ditambah/diedit
  - [x] Validasi Anti-Bentrok Penugasan: 1 guru hanya boleh mengampu 1 rombel (sistem otomatis memutasi penugasan jika guru dipindahkan antar-kelas)
  - [x] Sinkronisasi Otomatis ke Dasbor Guru: Guru yang login langsung terhubung ke rombel asuhannya secara dinamis dari basis data relasional
  - [x] Fitur Ekspor Master Rombel (Download PDF & Download Excel .xlsx untuk Operator & Admin)
- [x] **2.5 Master CRUD Operator (Siswa, Mutasi Rombel, Orang Tua & Impor Dapodik)**
  - [x] Model & Migrasi Tabel `students` (NISN, Nama, Gender, Classroom, Religion, TTL, Ortu, No WA, Alamat, Poin Tatib, Status)
  - [x] Form CRUD Master Siswa yang Disesuaikan: Form tambah siswa & edit biodata lengkap dengan input Agama (Islam, Kristen, Katolik, Hindu, Buddha, Konghucu), NISN 10 digit, nomor kontak ortu/WA
  - [x] Halaman Detail Siswa Lengkap (`show.blade.php`): Informasi agama, kontak wali, riwayat kelas, dan status keaktifan
  - [x] Fitur Unggah Berkas Pas Foto Siswa (JPEG/PNG/WebP maks 2MB, live preview & penyimpanan storage public)
  - [x] Fitur Mutasi Rombel Siswa: Pindah kelas antar rombel (misal 1A ke 1B) & sinkronisasi otomatis kapasitas
  - [x] CRUD Relasi Orang Tua / Wali Murid: Pengaturan kontak WhatsApp untuk notifikasi gerbang real-time
  - [x] Filter Cerdas Direktori Siswa (Pencarian nama/NISN, Filter 28 Rombel, Status Kesiswaan, Gender)
  - [x] Fitur Ekspor Data Siswa (Download PDF Rekap Buku Induk & Download Excel .xlsx untuk Operator & Admin)
  - [x] Panel Statistik Interaktif & Card Info di Dasbor Eksekutif (Chart Distribusi Rombel, Rasio Gender Siswa 411 L / 380 P, Status GTK PNS/PPPK, dan Daya Tampung Kuota)

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
    - [x] Fitur Ekspor Presensi Kelas Binaan Guru (Download PDF Jurnal Presensi Harian & Download Excel .xlsx untuk Guru/Wali Kelas)
    - [x] **Dashboard Orang Tua**: Terkoneksi 100% dinamis ke basis data riil tabel `attendances` (menghapus data dummy statis; menampilkan status `--:--:-- WIB` & "Belum Melakukan Presensi" jika anak belum tap gerbang, serta jam tap aktual & riwayat presensi riil).
  - [x] Notifikasi Alert Flash (Success, Error, Warning, Info)
  - [x] Halaman & Menu Lihat Profil Pengguna Terintegrasi untuk 4 Role (Admin, Operator, Guru, Orang Tua) beserta fitur pembaruan kontak & sandi
  - [x] **Modul Biodata & Profil Anak (Khusus Role Orang Tua / Wali Murid)**:
    - [x] Halaman Detail Biodata Lengkap Anak: Informasi identitas resmi Dapodik (Nama, NISN 10 digit, NIS, Rombel, Wali Kelas & Kontak WA, TTL, Agama, Alamat Domisili, Saldo Poin Tatib, dan Pratinjau Kartu Pelajar Digital ISO CR-80).
    - [x] Fitur Edit & Unggah Pas Foto Anak: Orang tua berhak mengunggah foto resmi ananda terbaru (JPG/JPEG/PNG/WebP maks 2MB, live preview, tersimpan ke storage publik).
    - [x] Proteksi Integritas Data Pokok (*Read-Only*): Field identitas akademik vital (NISN, Nama, Rombel, TTL, Poin Tatib) dikunci dan hanya dapat diubah oleh Operator Sekolah/Admin Dapodik.
    - [x] Proteksi Anti-Hapus & Keamanan Akses (*Strict Zero-IDOR & Forbidden Delete*): Orang tua dilarang keras menghapus data anaknya (tombol hapus ditiadakan di antarmuka orang tua dan endpoint delete diproteksi 403 Forbidden). Orang tua hanya dapat melihat dan memperbarui foto anak kandungnya sendiri.
- [x] **3.4 Master CRUD Admin & Operator: GTK, Manajemen Akun Pengguna & Log Aktifitas Sistem**
  - [x] **Master Data GTK (Guru & Tenaga Kependidikan)**:
    - [x] Akses untuk Operator & Admin (Supervisi Kepala Sekolah)
    - [x] Tabel data direktori GTK lengkap: Nama & Gelar, NIP (18 digit), NUPTK (16 digit), Jenis Kelamin, Status Kepegawaian (PNS, PPPK, GTT/Honorer, PTT), Jabatan Utama, Rombel Asuhan (relasi `classrooms`), No WA, Status Akun
    - [x] Form Tambah GTK baru dengan integrasi pembuatan akun login `users` otomatis (password default NIP/NUPTK)
    - [x] Form Edit GTK, upload pas foto resmi GTK, pembaruan data kepegawaian & jabatan
    - [x] Fitur Reset Password GTK ke default NIP/NUPTK
    - [x] Fitur Nonaktifkan / Hapus GTK dengan proteksi pelepasan rombel wali kelas otomatis
    - [x] Fitur Ekspor Data GTK (Download PDF Daftar Nominatif & Download Excel .xlsx untuk Operator & Admin)
  - [x] **Master Manajemen User (*User Management* - Eksklusif Role Admin)**:
    - [x] Akses eksklusif Kepala Sekolah (Admin) untuk tata kelola seluruh akun pengguna (`admin`, `operator`, `guru/walikelas`, `orangtua`)
    - [x] Filter direktori akun berdasarkan Role, Status (`active` / `inactive`), dan pencarian (Nama, Username, NIP, NISN, Email)
    - [x] Form Tambah Akun Pengguna baru untuk segala role
    - [x] Form Edit Akun Pengguna (Nama, Email, Role, Telepon, Status, Ubah Sandi)
    - [x] Tombol 1-Klik Suspend / Aktivasi Akun Pengguna
    - [x] Tombol 1-Klik Reset Password akun ke default (NIP atau NISN)
    - [x] Proteksi keamanan akun: Mencegah admin menghapus atau menonaktifkan akunnya sendiri yang sedang aktif
    - [x] Fitur Ekspor Pengguna Sistem (Download Excel .xlsx & Download PDF untuk Admin)
  - [x] **Modul Log Aktifitas Sistem (*Audit Trail ISO 27001* - Eksklusif Role Admin)**:
    - [x] Skema tabel `activity_logs`: Timestamp, User (ID, Nama, Role), Action Event, Modul, Deskripsi Aktivitas, IP Address, User Agent
    - [x] Service Logger Otomatis (`ActivityLog::record()`) yang mencatat peristiwa penting: Autentikasi (login, logout, gagal), CRUD Siswa, CRUD GTK, Manajemen User, Mutasi Rombel, Ubah Profil/Sandi
    - [x] Antarmuka Audit Trail Viewer khusus Admin dengan filter Modul, Role, Rentang Tanggal, Pencarian Teks, dan Metrik Ringkas
    - [x] Desain *Append-Only* (tidak ada tombol edit atau hapus log untuk menjamin integritas forensik audit)
    - [x] Fitur Ekspor Audit Trail ISO 27001 (Download PDF Berita Acara & Download Excel .xlsx untuk Admin)
  - [x] Modul Penetapan & Rotasi Wali Kelas: Pengaturan guru pemegang rombel 1A s/d 6E secara terpusat oleh Kepsek
  - [ ] CRUD Master Aturan Poin Tatib & Prestasi: Konfigurasi ambang batas SP1, SP2, SP3, bobot minus pelanggaran, dan reward merit points
  - [x] Halaman Khusus Analisis AI Kesiswaan & Manajemen API Key Gemini (Menggantikan Floating Drawer)

---

## ⚡ Tahap 4: Modul Presensi Gerbang 3-Mode & Pemindai Barcode HP
Fokus: Pembangunan mesin scanner presensi gerbang, WebRTC kamera HP, dan otomatisasi aturan jam masuk. (Hanya Role Admin, Operator, dan Guru/Wali Kelas yang berhak scan semua siswa; Dilengkapi metode input manual NISN jika siswa lupa bawa kartu).

- [x] **4.1 Layanan Bisnis Presensi (`AttendanceRuleEngineService.php`)**
  - [x] Mode 1 Masuk Pagi (06:00 - 08:00 WIB): Evaluasi Tepat Waktu vs Terlambat T-1 (-2 pt), T-2 (-5 pt), T-3 (-10 pt)
  - [x] Mode 2 Siaga Digital Gate Pass (08:01 - 13:00 WIB): Verifikasi QR izin keluar siswa & validasi satpam
  - [x] Mode 3 Pulang Sore (13:01 - 17:00 WIB): Perekaman check-out kepulangan otomatis
  - [x] Layanan Keadilan Checkout (`CheckoutJusticeService.php` / Rule Engine): Siswa lupa tap pulang tidak dihukum Alpa jika hadir di KBM jam terakhir
  - [x] Scheduled Task Cron Cutoff 08:00 WIB: Auto-Alpa bagi yang belum tap masuk + alert darurat
- [x] **4.2 Komponen Meja Scanner Gerbang (`/scanner`)**
  - [x] Tab Switcher 3 Mode waktu otomatis
  - [x] Simulasi Jam Gerbang & Tombol Uji Coba Tap 1-Klik
  - [x] Terminal Input Box (dukungan Barcode Gun USB keyboard-wedge & input manual NISN / nama autocomplete jika siswa tidak membawa kartu)
  - [x] Kartu Monitor Real-Time: Foto siswa, NISN, Rombel, Hasil Evaluasi, Status Poin Tatib
  - [x] Tabel Riwayat Scan Gerbang Hari Ini (live updates via AJAX/fetch polling)
- [x] **4.3 Pemindai Barcode Kamera HP (Mobile WebRTC Scanner)**
  - [x] Pemilih Lensa: Kamera Belakang (*Environment*) vs Kamera Depan (*User / Stand*)
  - [x] Kontrol Kamera: Tombol Buka/Tutup Kamera, Senter (*Torch*), status aktif
  - [x] Viewfinder Frame dengan sudut target dan animasi garis laser merah (`.scanner-laser`)
  - [x] Algoritma deteksi instan Barcode 1D Code 128 & QR Code 2D (`html5-qrcode`)
  - [x] Getar Haptic HP ganda `[70ms, 40ms, 70ms]` & audio chime feedback Web Audio API sintetis (C-E-G chime, beep 880Hz, buzz 180Hz)
  - [x] Fitur *Auto-Continuous Scan* dengan jeda anti-duplikasi 2.5 detik
  - [x] Otomasi hemat baterai (kamera mati saat pindah menu atau switch tab)

---

## 🪪 Tahap 5: Direktori Siswa & Generator Cetak Kartu Pelajar ISO CR-80
Fokus: Pengelolaan data siswa skala besar dan pencetakan kartu fisik standar perbankan.

- [x] **5.1 Direktori Siswa Berkapasitas Tinggi (*High-Capacity Directory*)**
  - [x] 4 Kartu KPI Ringkasan: Total Siswa, Rombongan Belajar, Siswa Laki-Laki, Siswa Perempuan
  - [x] Live Search Bar (pencarian nama, NISN, atau kelas secara real-time)
  - [x] Toolbar Filter Cerdas: Filter Rombel Kelas, Filter Gender, Pencarian Teks
  - [x] Pemilih Baris per Halaman (Paginasi interaktif) & Tombol Reset Filter
  - [x] Tabel Siswa Interaktif dengan highlight baris aktif (`bg-rose-50/60 font-semibold`)
  - [x] Fitur Multi-Select Checkbox untuk Cetak Siswa Terpilih secara massal
- [x] **5.2 Generator Kanvas Fisik Kartu ISO CR-80 (85.60 x 53.98 mm)**
  - [x] Pratinjau bolak-balik fisik CR-80 skala akurat di samping tabel (Tab Sisi Depan vs Sisi Belakang)
  - [x] Sisi Depan: Kop sekolah, Pas foto 3x4, biodata lengkap, Barcode 1D Code 128 (NISN), QR Code otentikasi publik
  - [x] Sisi Belakang: 4 Butir ketentuan tata tertib kartu, masa berlaku, Tanda Tangan Digital Resmi Kepala Sekolah Wahyu Agung Setiyawan S.Pd & QR TTE
- [x] **5.3 Layanan Keamanan & Pencetakan Kartu**
  - [x] Tombol Cetak Tunggal (CSS Print presisi tinggi khusus printer ID Card PVC ukuran 85.60 x 53.98 mm)
  - [x] Tombol Cetak Massal 1 Rombel & Siswa Terpilih (Template Grid A4 isi 8 kartu per lembar lengkap dengan garis potong pembatas)
  - [x] Halaman Publik Verifikasi QR Code Kartu Pelajar (`/card/verify/{student}`) saat di-scan oleh orang tua / pihak luar
  - [x] Integrasi Navigasi Sidebar Operator & Admin aktif (menggantikan status `Soon` menjadi rute `cards.index`)

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

## 🤖 Tahap 7: Modul Analisis Data AI & Konfigurasi API Google Gemini (Dedicated Page)
Fokus: Analisis data kesiswaan terpadu pada halaman khusus mandiri (*Dedicated AI Analytics Page*) khusus untuk **Role Admin, Operator, dan Guru/Wali Kelas** (Role Orang Tua tidak memiliki akses), penghapusan floating drawer mengambang agar antarmuka bersih, dan form manajemen API Key AI di dasbor admin.

- [x] **7.1 Layanan Deteksi Dini Rule-Based (`AiStudentAnalysisService.php`)**
  - [x] Logika analisis kedisiplinan harian & pemantauan keterlambatan berulang (T-1, T-2, T-3)
  - [x] Logika filter absensi kritis & deteksi risiko kesiswaan
  - [x] Pengelompokan siswa berisiko secara dinamis dari basis data real-time
- [x] **7.2 Integrasi Google Gemini 1.5 Flash API & Engine AI**
  - [x] Service caller ke Google Generative Language API v1beta (`models/gemini-1.5-flash:generateContent`)
  - [x] Format prompt pedagogis: asisten guru empatik, analisis maksimal 3 kalimat + 1 saran konkret ramah anak
  - [x] Fallback cerdas ke STAR-KIDS Rule-Based Expert Engine jika API key belum disetel / offline
  - [x] Pembacaan dinamis API Key dari basis data (`app_settings`) atau fallback `.env` (`GEMINI_API_KEY`)
- [x] **7.3 Form Pengaturan API Key AI di Dasbor Admin (`/ai-analytics` Pengaturan Gemini)**
  - [x] Form input API Key Google Gemini (input bertipe password dengan tombol intip show/hide)
  - [x] Pilihan model AI: Google Gemini 1.5 Flash (Rekomendasi Cepat & Ringan) vs Gemini 1.5 Pro
  - [x] Tombol 1-Klik "Uji Koneksi API" (*Test Connection*) dengan indikator status aktif/gagal secara visual dan latensi ms
  - [x] Penyimpanan konfigurasi API Key ke basis data (tabel `app_settings`)
  - [x] Panduan praktis mendapatkan Google Gemini API Key gratis dari Google AI Studio
- [x] **7.4 Halaman Khusus Mandiri Analisis Data AI (*Dedicated Page: `/ai-analytics`*)**
  - [x] Penghapusan floating icon/drawer mengambang di kanan bawah agar layar bersih dan tidak menutupi tombol lain
  - [x] Penambahan menu baru di Sidebar: **"Analisis AI Kesiswaan"** (dengan ikon `bi-stars` & badge AI untuk Admin, Operator, Guru)
  - [x] 4 Kartu KPI Analisis AI: Total Siswa Binaan, Indeks Kehadiran Sekolah, Keterlambatan Gerbang, Early Warning System
  - [x] Matriks Deteksi Dini Kesiswaan (*Early Warning System Table*): Daftar siswa berisiko tinggi beserta riwayat dan faktor pemicu
  - [x] Stasiun Konsultasi AI Interaktif Layar Penuh (*Full-Page AI Copilot Workstation*):
    - [x] 5 Tombol Analisis Cepat: Ringkas Presensi Hari Ini, Deteksi Siswa Berisiko, Draf SP Kedisiplinan, Draf Pesan WA Santun Wali Murid, Tips Disiplin Positif
    - [x] Kolom tanya-jawab bebas konsultasi data sekolah secara real-time
    - [x] Konsultasi 1-klik terarah langsung dari tabel siswa berisiko (Early Warning)

---

## 👥 Tahap 8: Master Data GTK, Log Audit & Backup Data ISO 27001
Fokus: Tata kelola kepegawaian Dapodik dan kepatuhan standar keamanan data internasional.

- [x] **8.1 Master Data Guru & Tenaga Kependidikan (Eksklusif Admin/Kepsek & Operator)**
  - [x] Manajemen CRUD data GTK (NIP, NUPTK, Nama & Gelar, Gender, Status Kepegawaian, Jabatan, Rombel Binaan, No WA)
  - [x] Validasi penomoran NIP 18 digit & NUPTK 16 digit
- [x] **8.2 Log Audit Kekal ISO/IEC 27001 A.12.4**
  - [x] Pencatatan transaksi sistem (aktor, peran, timestamp, IP address, modul, payload sebelum/sesudah)
  - [x] Fitur pelindung integritas log (*append-only*, tidak dapat di-edit atau dihapus oleh siapapun)
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
