# Design System & UI Token Standard (SSOT)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Tema Utama**: Metis Bootstrap 5 Admin Dashboard Template + Colorlib Login Form 11  
**Kombinasi Framework**: Bootstrap 5.3 Modern Effects + Tailwind CSS Utility + Bootstrap Icons (BI)  
**Komponen Wajib**: Collapsible Mini-Sidebar, Split Login Form 11, Pagination, Debounced Search, Multi-Filter, Interactive Charts, KPI Info Cards, Studio Bahan Ajar AI & Floating Copilot

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh antarmuka visual, palet warna, layout shell Metis Admin, formulir login Colorlib Form 11, kartu statistik, grafik analitik data, tipografi, studio bahan ajar AI, dan umpan balik audio pemindai 3 mode.

---

## 1. Token Tata Letak Metis Admin Dashboard

Sistem menerapkan shell admin standar **Metis Dashboard** yang sepenuhnya responsif di semua perangkat (Desktop, Tablet, Mobile):

```css
:root {
  /* Dimensi Shell Metis Admin */
  --sidebar-width: 260px;                /* Lebar sidebar expanded default */
  --sidebar-collapsed-width: 68px;       /* Lebar sidebar mini / icon-only mode */
  --navbar-height: 60px;                 /* Tinggi top bar navigasi */
  
  /* Latar & Permukaan Metis */
  --bg-app-canvas: #f8fafc;              /* Canvas latar belakang utama */
  --bg-card-surface: #ffffff;            /* Permukaan kartu & tabel */
  --bg-sidebar: #ffffff;                 /* Permukaan bilah samping navigasi */
  --border-subtle: #e2e8f0;              /* Garis pembatas halus */
  
  /* Brand Primary & Aksen Metis */
  --color-primary: #2563eb;              /* Biru Metis Utama */
  --color-primary-hover: #1d4ed8;        /* Hover Biru */
  --color-primary-subtle: #eff6ff;       /* Latar aktif item navigasi */
  
  /* AI Copilot & Studio Gradients */
  --ai-gradient-primary: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
  --ai-gradient-dark: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  --ai-gradient-briefing: linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%);
}

/* 1.1 State Sidebar Responsif */
.admin-sidebar {
  width: var(--sidebar-width);
  transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Mode Mini-Collapsed (Desktop) */
.sidebar-collapsed .admin-sidebar {
  width: var(--sidebar-collapsed-width);
}
.sidebar-collapsed .admin-sidebar .sidebar-nav-text,
.sidebar-collapsed .admin-sidebar .sidebar-brand-text,
.sidebar-collapsed .admin-sidebar .sidebar-section-title,
.sidebar-collapsed .admin-sidebar .badge {
  display: none !important;
}

/* Mode Mobile Overlay Drawer (< 992px) */
@media (max-width: 991.98px) {
  .admin-sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 1040;
    transform: translateX(-100%);
  }
  .mobile-open .admin-sidebar {
    transform: translateX(0);
    box-shadow: 0 0 40px rgba(0, 0, 0, 0.35);
  }
}
```

---

## 2. Token Warna Global & Status Semantik

Token warna baku didefinisikan pada `:root` untuk menghasilkan nuansa bersih, kontras tinggi, dan berkelas korporat/edukasi:

```css
:root {
  /* Canvas & Background (Dominan Putih & Netral Lembut) */
  --bg-app-canvas: #F8FAFC;              /* Latar luar aplikasi (Slate-50) */
  --bg-card-surface: #FFFFFF;            /* Permukaan kartu utama (Pure White) */
  --bg-subtle-surface: #F1F5F9;          /* Kontainer input, filter bar & stripe tabel */
  --bg-hover-surface: #F8FAFC;           /* State hover baris tabel & tombol */

  /* Borders & Dividers */
  --border-card-subtle: #E2E8F0;         /* Garis batas kartu halus (Slate-200) */
  --border-input-focus: #0F172A;         /* Garis fokus input teks aktif (Slate-900) */
  --border-divider: #CBD5E1;             /* Garis pemisah antar seksi */

  /* Typography / Text Colors */
  --text-main: #0F172A;                  /* Teks utama judul & angka KPI (Slate-900) */
  --text-secondary: #475569;             /* Teks badan & deskripsi (Slate-600) */
  --text-muted: #64748B;                 /* Label bantuan, placeholder, NISN (Slate-500) */
  --text-light: #94A3B8;                 /* Teks nonaktif / disabled */

  /* Semantic Status Colors (Badges & Alerts) */
  --status-success: #10B981;             /* Hadir / Tepat Waktu / Disetujui (Emerald-500) */
  --status-success-bg: #ECFDF5;          /* Latar badge sukses */
  --status-warning: #F59E0B;             /* Terlambat Ringan T-1 / Menunggu Verifikasi */
  --status-warning-bg: #FFFBEB;          /* Latar badge warning */
  --status-danger: #EF4444;              /* Alpa / Terlambat Berat T-3 / SP / Bolos */
  --status-danger-bg: #FEF2F2;           /* Latar badge bahaya */
  --status-info: #0EA5E9;                /* Izin / Pulang Cepat / Gate Pass / Info WA */
  --status-info-bg: #F0F9FF;             /* Latar badge info */
  --status-purple: #8B5CF6;              /* Prestasi Merit / Karakter */
  --status-purple-bg: #F5F3FF;

  /* Bootstrap 5 Elevation & Shadows */
  --card-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.03);
  --card-shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05);
  --card-shadow-lg: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.04);
}
```

---

## 2. Komponen Antarmuka Wajib di Setiap Modul

### 2.1 Kartu Statistik KPI & Info Card Widget (Bootstrap 5 Modern)
Setiap modul dihiasi kartu ringkasan metrik di bagian atas:
- **Struktur Kartu**: Latar belakang putih polos (`bg-white`), sudut membulat elegan (`rounded-4` / `16px`), border halus (`border border-slate-200`), dan bayangan lembut (`shadow-sm`).
- **Komponen Dalam Kartu**:
  1. *Info Icon*: Ikon vektor SVG berada dalam kontainer bulat pastel semantik (misal: ikon toga ungu untuk prestasi, ikon jam kuning untuk keterlambatan, ikon perisai hijau untuk kehadiran).
  2. *Label Metrik*: Teks abu-abu huruf kapital kecil (`text-xs font-semibold text-slate-500 uppercase tracking-wider`).
  3. *Nilai Angka Besar*: Teks kontras tinggi (`text-3xl font-bold text-slate-900 tracking-tight`).
  4. *Lencana Indikator Tren*: Badge persentase naik/turun dengan panah tren (`+2.4% vs minggu lalu`).
  5. *Sparkline Chart Mini*: Grafik garis halus di sudut bawah kartu untuk visualisasi tren 7 hari terakhir.

```
+-------------------------------------------------------------+
| [📈 Icon] TOTAL KEHADIRAN SISWA                [+3.2% Trend]|
| 94.8%                                                       |
| 474 dari 500 Siswa Hadir Hari Ini    ~~~~/\_/\_ (Sparkline) |
+-------------------------------------------------------------+
```

---

### 2.2 Bilah Pencarian & Filter Cepat Multi-Kriteria (*Search & Multi-Filter Bar*)
Setiap tabel data kesiswaan dilengkapi bilah penyaring interaktif:
1. **Kotak Pencarian Instan (Debounced Search)**:
   - Dilengkapi ikon kaca pembesar SVG di sisi kiri.
   - Respon pencarian reaktif dengan debounce waktu $250\text{ ms}$ (pencarian nama, NISN, NIP, atau nomor tiket).
   - Dilengkapi tombol silang pembersih (*Clear Button*) di sisi kanan input.
2. **Dropdown Multi-Kriteria Bertingkat**:
   - Filter Pilihan Rombel/Kelas (`Semua Kelas`, `X-RPL-1`, `XI-TKJ-2`, dll.).
   - Filter Status Kehadiran (`Semua Status`, `Hadir`, `Terlambat T-1/T-2/T-3`, `Sakit`, `Izin`, `Alpa`, `Pulang Cepat`).
   - Filter Tingkat Pelanggaran / Prestasi (`Semua Tingkat`, `Ringan`, `Sedang`, `Berat`).
3. **Tombol Reset Filter Cepat**:
   - Tombol outline *"↺ Reset Filter"* untuk mengembalikan tabel ke kondisi awal dalam 1 klik.

---

### 2.3 Komponen Paginasi Dinamis (*Dynamic Pagination Component*)
Menjamin antarmuka tetap bersih dan cepat dimuat saat mengelola ribuan data siswa:
- **Tampilan Rentang Data**: Menampilkan posisi baris yang sedang dilihat, contoh: `"Menampilkan 1 - 10 dari 480 siswa"`.
- **Pemilih Baris Per Halaman (*Page Size Selector*)**: Dropdown pilihan `10`, `25`, `50`, atau `100` baris per halaman.
- **Navigasi Tombol Halus**:
  - Tombol *"Sebelumnya"* (`<`) dan *"Berikutnya"* (`>`).
  - Penomoran tombol halaman dinamis dengan indikator elipsis (`1, 2, 3 ... 10`).
  - Halaman aktif disorot dengan warna hitam Slate-900 dan teks putih kontras (`bg-slate-900 text-white rounded-lg shadow-sm`).

---

### 2.4 Grafik Visualisasi Data Interaktif (*Interactive Charts*)
Menggunakan library **Chart.js** atau **ApexCharts** dengan tema monokrom bersih dan aksen semantik:
1. **Grafik Batang Tren Kehadiran Mingguan**: Menampilkan perbandingan jumlah siswa Tepat Waktu, Terlambat, dan Alpa dari hari Senin s/d Jumat.
2. **Diagram Donut Rasio Kedisiplinan vs Prestasi**: Visualisasi lingkaran proporsi siswa berprestasi merit vs siswa yang membutuhkan pembinaan demerit.
3. **Diagram Garis Jam Masuk Gerbang**: Menampilkan kurva kepadatan pemindaian kartu di gerbang sekolah antara pukul 06:15 hingga 07:30 WIB.

---

## 3. Spesifikasi Umpan Balik Audio Pemindai 3 Mode (Web Audio API)

Alat pemindai gerbang dilengkapi nada audio sintetis otomatis menggunakan Web Audio API tanpa membutuhkan file audio eksternal:

| Mode & Skenario Pemindaian | Frekuensi Suara (Pitch) | Durasi | Tipe Gelombang | Makna Operasional Bagi Satpam |
| :--- | :--- | :--- | :--- | :--- |
| **Mode 1: Masuk Tepat Waktu (06:00-07:00)** | $880\text{ Hz}$ (Nada A5 Tinggi) | $100\text{ ms}$ | `sine` | Suara Beep Ramah: Siswa tepat waktu, masuk kelas normal. |
| **Mode 1: Terlambat Ringan T-1 (07:01-07:15)** | $660\text{ Hz} \to 550\text{ Hz}$ | $180\text{ ms}$ | `triangle` | Nada peringatan lembut: Terlambat -2 poin tatib. |
| **Mode 1: Terlambat Sedang/Berat (>07:15)** | $180\text{ Hz}$ (Buzz Rendah) | $300\text{ ms}$ | `sawtooth` | Peringatan Tegas: Gerbang ditutup, arahkan ke piket/BK. |
| **Mode 2: Digital Gate Pass Divalidasi** | $523\text{ Hz} \to 659\text{ Hz}$ | $200\text{ ms}$ | `sine` | Nada Palang Terbuka: Tiket izin keluar sah, satpam buka palang. |
| **Mode 3: Presensi Pulang (13:01-17:00)** | $523\text{ Hz} \to 659\text{ Hz} \to 784\text{ Hz}$ | $300\text{ ms}$ | `sine` (3-Chime) | Nada Chime Melodi C-E-G: *"Selamat Pulang"*, notif WA terkirim. |
| **Error / Kartu Ditolak / Palsu** | $150\text{ Hz} \times 2$ (Double Buzz) | $200\text{ ms} + 200\text{ ms}$ | `square` | Peringatan Bahaya: Kartu tidak terdaftar atau telah di-revoke. |

---

## 4. Spesifikasi Kartu Pelajar PVC ISO CR-80

Kartu pelajar fisik mematuhi standar internasional perbankan **ISO/IEC 7810 ID-1 CR-80**:
- **Dimensi Fisik**: $85.60\text{ mm} \times 53.98\text{ mm}$ (Ketebalan $0.76\text{ mm}$, material PVC glossy/doff tahan air).
- **Tampak Depan (*Front View*)**:
  - Latar putih bersih dengan watermark logo resmi SDN Kebonagung Kota Pasuruan.
  - Pas foto 3x4 potret resmi siswa.
  - Biodata: Nama Lengkap Siswa, NISN, NIS, Rombel/Kelas, TTL, Golongan Darah.
  - Barcode 1D Code 128 di sudut kiri bawah untuk laser scanner cepat pos gerbang.
  - QR Code 2D bertanda tangan digital HMAC-SHA256 di sudut kanan bawah.
- **Tampak Belakang (*Back View*)**:
  - Tata tertib pemegang kartu identitas siswa.
  - Tanda tangan digital Kepala Sekolah ber-stempel kedinasan resmi.
  - Kontak darurat pos pengaduan sekolah.

---

## 5. Spesifikasi Halaman Login Split-Screen (Colorlib Form 11)

Menerapkan tata letak 2 kolom standar Colorlib Form 11:
1. **Kolom Kiri (Formulir Masuk / Sign In - 50% Desktop)**:
   - Header "Sign in" dengan deskripsi ramah akun dinas.
   - **Quick Demo Role Switcher Chips (1-Klik)**:
     - `👑 Super Admin / Kepsek` (admin@edutrack.sch.id)
     - `🛡️ Guru BK / Kesiswaan` (bk@edutrack.sch.id)
     - `📋 Wali Kelas (X-RPL-1)` (walikelas@edutrack.sch.id)
     - `👨‍👩‍👦 Orang Tua Siswa` (ortu@edutrack.sch.id)
     - `🎓 Siswa (Ahmad Rifai)` (siswa@edutrack.sch.id)
   - Input Email & Password dengan tombol intip sandi (`bi bi-eye` / `bi bi-eye-slash`).
   - Checkbox "Ingat sesi ini (30 hari)" dan link "Forgot?".
   - Tombol Utama `[ ➔ Sign in to Dashboard ]` warna biru Metis (`#2563eb`).
   - Tombol Single Sign-On alternatif: `Google SSO` dan `SSO Kemdikbud`.
2. **Kolom Kanan (Hero Value Proposition - 50% Desktop)**:
   - Latar belakang abu-abu netral lembut (`#f8fafc`) dengan tipografi bold.
   - Judul Headline: *"Sistem Manajemen Kesiswaan, Presensi Gerbang 0.3s, Kedisiplinan & Merit Bank Terpadu."*
   - Daftar Keunggulan Ber-Centang Biru Bulat:
     - 3 Mode Waktu Scanner Gerbang (Auto-switch).
     - Prinsip Keadilan: Siswa lupa tap tidak dihukum Alpa jika KBM hadir.
     - Notifikasi Real-time WhatsApp ke wali murid.
     - Poin Prestasi & Tatib Bertingkat (Auto SP-1 s/d SP-3).
   - Metrik Kepercayaan di Bagian Bawah:
     - **432** SISWA AKTIF
     - **0.3s** RFID / BARCODE
     - **ISO 27001** KEAMANAN DATA

---

## 6. Spesifikasi Desain AI Teaching Studio & Floating Copilot

### 6.1 AI Executive Briefing Card (Dashboard)
- Latar gradien halus: `linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%)`.
- Border: `1px solid #dbeafe`, sudut `rounded-3`.
- Ikon bintang berkilau `bi bi-stars` berlatar biru dengan efek elevasi lembut.
- Tombol aksi terintegrasi: `[ 📄 Drafkan SP & Surat ]` dan `[ 💬 Buka AI Copilot ]`.

### 6.2 Studio Bahan Ajar AI View (`activePage === 'bahan_ajar'`)
- **Banner Gelap Modern**: Gradien `linear-gradient(135deg, #1e293b 0%, #0f172a 100%)` dengan lencana *Kurikulum Merdeka 2026* dan *Fase A - F*.
- **Toolbar 6 Alat**:
  1. `modul_ajar`: Modul Ajar / RPP Lengkap
  2. `bank_soal`: Bank Soal HOTS & Asesmen AKM
  3. `lkpd`: Lembar Kerja Siswa (LKPD)
  4. `rubrik_kktp`: Rubrik KKTP Kriteria Nilai BSKAP
  5. `ice_breaking`: Ice Breaking & Kuis KBM
  6. `remedial`: Program Remedial & Pengayaan
- **Dua Kolom Kerja**:
  - Kolom Kiri: Form Parameter (Preset cepat, Mapel, Fase, Topik, Model Pembelajaran, Alokasi Waktu, Jumlah Soal) + Tombol `[ 🚀 Generate Bahan Ajar AI ]`.
  - Kolom Kanan: Card Dokumen Terbitan AI + Tombol `[ 📋 Salin ]`, `[ 🖨️ Cetak ]`, `[ 💾 Unduh ]`.

### 6.3 Floating Copilot Launcher & Slide-Out Chat Drawer
- **Launcher Pill**: Posisi `fixed; bottom: 24px; right: 24px; z-index: 1045;`, gradien biru-ungu `linear-gradient(135deg, #2563eb, #7c3aed)`, border halus putih semi-transparan, ping status online.
- **Drawer Panel**: Posisi `fixed; bottom: 82px; right: 24px; width: 395px; max-width: calc(100vw - 32px); height: 570px; z-index: 1050; border-radius: 16px; border: 1px solid #cbd5e1;`.
- **Chips Prompt Cepat**: Tombol bulat geser horizontal untuk query umum presensi, SP, WA, dan soal HOTS.
- **Gelembung Obrolan**: Gelembung biru (`bg-primary text-white`) untuk pengguna dan abu-abu bersih (`bg-slate-100 text-dark border`) untuk AI.
