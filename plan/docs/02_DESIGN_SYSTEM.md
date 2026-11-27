# Design System & UI Token Standard (SSOT)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Tema Utama**: Enterprise Clean White Modern Minimalist Dashboard + Direct Modern Login Page (HRD-Style)  
**Tipografi Baku**: Plus Jakarta Sans (Weights: 300, 400, 500, 600, 700, 800)  
**Kombinasi Framework**: Tailwind CSS Utility + Bootstrap Icons (BI) + Bootstrap 5.3 Modern Components  
**Komponen Wajib**: Direct Modern Login Page (1-Click Role Switcher), Ambient Pastel Glows, Collapsible Mini-Sidebar, Header Role Switcher Pill, Dynamic Pagination, Debounced Search, Multi-Filter, Interactive Charts, KPI Info Cards, Dasbor Analisis AI (Gemini 1.5 Flash) & Floating Copilot

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh antarmuka visual, palet warna, layout shell admin modern, halaman login mandiri langsung (*direct login page*), kartu statistik, grafik analitik data, tipografi, Dasbor Analisis AI (Rule-Based & Gemini 1.5 Flash), dan umpan balik audio pemindai 3 mode.

---

## 1. Token Tata Letak Enterprise Modern Minimalist (HRD-Style)

Sistem menerapkan shell admin standar **Clean White Modern Minimalist** yang sepenuhnya responsif di semua perangkat (Desktop, Tablet, Mobile):

```css
:root {
  /* Tipografi Utama */
  --font-family-base: 'Plus Jakarta Sans', sans-serif;

  /* Dimensi Shell Admin */
  --sidebar-width: 260px;                /* Lebar sidebar expanded default */
  --sidebar-collapsed-width: 68px;       /* Lebar sidebar mini / icon-only mode */
  --navbar-height: 58px;                 /* Tinggi top bar navigasi */
  
  /* Latar & Permukaan Clean White */
  --bg-app-canvas: #f8fafc;              /* Canvas latar belakang utama (Slate-50) */
  --bg-card-surface: #ffffff;            /* Permukaan kartu & tabel (Pure White) */
  --bg-sidebar: #ffffff;                 /* Permukaan bilah samping navigasi */
  --border-subtle: #e2e8f0;              /* Garis pembatas halus (Slate-200) */
  
  /* Brand Primary & Aksen Rose-600 */
  --color-primary: #e11d48;              /* Rose-600 Utama */
  --color-primary-hover: #be123c;        /* Hover Rose-700 */
  --color-primary-subtle: #fff1f2;       /* Latar aktif item navigasi (Rose-50) */
  --color-primary-border: #fecdd3;       /* Border aksen halus (Rose-200) */
  
  /* AI Copilot & Studio Gradients */
  --ai-gradient-primary: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
  --ai-gradient-dark: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  --ai-gradient-briefing: linear-gradient(135deg, #fff1f2 0%, #f5f3ff 100%);
}

/* 1.1 State Sidebar Responsif */
.admin-sidebar {
  width: var(--sidebar-width);
  transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
  .mobile-open .admin-sidebar,
  .admin-sidebar.show {
    transform: translateX(0);
    box-shadow: 0 0 40px rgba(0, 0, 0, 0.25);
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
  --border-input-focus: #E11D48;         /* Garis fokus input teks aktif (Rose-600) */
  --border-divider: #CBD5E1;             /* Garis pemisah antar seksi */

  /* Typography / Text Colors */
  --text-main: #0F172A;                  /* Teks utama judul & angka KPI (Slate-900) */
  --text-secondary: #475569;             /* Teks badan & deskripsi (Slate-600) */
  --text-muted: #64748B;                 /* Label bantuan, placeholder, NISN (Slate-500) */
  --text-light: #94A3B8;                 /* Teks nonaktif / disabled */

  /* Semantic Status Colors (Soft Pill Badges) */
  --status-success: #10B981;             /* Hadir / Tepat Waktu / Disetujui (Emerald-500) */
  --status-success-bg: #ECFDF5;          /* Latar badge sukses */
  --status-warning: #F59E0B;             /* Terlambat Ringan T-1 / Menunggu Verifikasi */
  --status-warning-bg: #FFFBEB;          /* Latar badge warning */
  --status-danger: #E11D48;              /* Alpa / Terlambat Berat T-3 / SP / Bolos (Rose-600) */
  --status-danger-bg: #FFF1F2;           /* Latar badge bahaya */
  --status-info: #0284C7;                /* Sakit / Info WA */
  --status-info-bg: #F0F9FF;             /* Latar badge info */
  --status-purple: #7C3AED;              /* Gate Pass / Prestasi Merit / Karakter */
  --status-purple-bg: #FAF5FF;

  /* Card Elevation & Shadows */
  --card-shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --card-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.03);
  --card-shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.07), 0 2px 4px -2px rgba(15, 23, 42, 0.05);
  --card-shadow-lg: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.04);
}
```

---

## 3. Komponen Antarmuka Wajib di Setiap Modul

### 3.1 Kartu Statistik KPI & Info Card Widget (Rounded-2xl & Soft Badges)
Setiap modul dihiasi kartu ringkasan metrik di bagian atas:
- **Struktur Kartu**: Latar belakang putih polos (`bg-white`), sudut membulat elegan (`rounded-2xl` / `16px`), border halus (`border border-slate-200/80`), dan bayangan lembut (`shadow-xs hover:shadow-sm`).
- **Komponen Dalam Kartu**:
  1. *Info Icon*: Ikon vektor SVG berada dalam kontainer bulat pastel semantik (misal: ikon toga ungu untuk prestasi, ikon jam kuning untuk keterlambatan, ikon perisai hijau untuk kehadiran).
  2. *Label Metrik*: Teks abu-abu huruf kapital kecil (`text-xs font-semibold text-slate-500 uppercase tracking-wider`).
  3. *Nilai Angka Besar*: Teks kontras tinggi (`text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight`).
  4. *Lencana Indikator Tren*: Badge persentase naik/turun dengan panah tren (`+2.4% vs minggu lalu`).
  5. *Sparkline Chart Mini*: Grafik garis halus di sudut bawah kartu untuk visualisasi tren 7 hari terakhir.

```
+-------------------------------------------------------------+
| [📈 Icon] TOTAL KEHADIRAN SISWA                [+3.2% Trend]|
| 94.8%                                                       |
| 410 dari 432 Siswa Hadir Hari Ini    ~~~~/\_/\_ (Sparkline) |
+-------------------------------------------------------------+
```

---

### 3.2 Bilah Pencarian & Filter Cepat Multi-Kriteria (*Search & Multi-Filter Bar*)
Setiap tabel data kesiswaan dilengkapi bilah penyaring interaktif:
1. **Kotak Pencarian Instan (Debounced Search)**:
   - Dilengkapi ikon kaca pembesar di sisi kiri dan badge `Ctrl K` di sisi kanan.
   - Respon pencarian reaktif dengan debounce waktu $250\text{ ms}$ (pencarian nama, NISN, NIP, atau nomor tiket).
   - Format: `bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:bg-white focus:ring-2 focus:ring-rose-500/20`.
2. **Dropdown Multi-Kriteria Bertingkat**:
   - Filter Pilihan Rombel/Kelas (`Semua Kelas`, `X-RPL-1`, `Kelas 4-A`, dll.).
   - Filter Status Kehadiran (`Semua Status`, `Hadir`, `Terlambat T-1/T-2/T-3`, `Sakit`, `Izin`, `Alpa`, `Pulang Cepat`).
   - Filter Tingkat Pelanggaran / Prestasi (`Semua Tingkat`, `Ringan`, `Sedang`, `Berat`).
3. **Tombol Reset Filter Cepat**:
   - Tombol outline `↺ Reset Filter` untuk mengembalikan tabel ke kondisi awal dalam 1 klik.

---

### 3.3 Komponen Paginasi Dinamis (*Dynamic Pagination Component*)
Menjamin antarmuka tetap bersih dan cepat dimuat saat mengelola ratusan data siswa:
- **Tampilan Rentang Data**: Menampilkan posisi baris yang sedang dilihat, contoh: `"Menampilkan 1 - 10 dari 432 siswa"`.
- **Pemilih Baris Per Halaman (*Page Size Selector*)**: Dropdown pilihan `10`, `25`, `50`, atau `100` baris per halaman.
- **Navigasi Tombol Halus**:
  - Tombol *"Sebelumnya"* (`<`) dan *"Berikutnya"* (`>`).
  - Halaman aktif disorot dengan warna Rose-600 dan teks putih kontras (`bg-rose-600 text-white rounded-lg shadow-xs font-bold`).

---

### 3.4 Grafik Visualisasi Data Interaktif (*Interactive Charts*)
Menggunakan library **Chart.js** dengan tema monokrom bersih dan aksen semantik:
1. **Grafik Batang Tren Kehadiran Mingguan**: Menampilkan perbandingan jumlah siswa Tepat Waktu (Emerald), Terlambat (Amber), dan Alpa (Rose) dari hari Senin s/d Jumat.
2. **Diagram Donut Rasio Kedisiplinan vs Prestasi**: Visualisasi lingkaran proporsi siswa berprestasi merit vs siswa yang membutuhkan pembinaan demerit.
3. **Diagram Garis Jam Masuk Gerbang**: Menampilkan kurva kepadatan pemindaian kartu di pos gerbang sekolah antara pukul 06:15 hingga 07:30 WIB.

---

## 4. Spesifikasi Umpan Balik Audio Pemindai 3 Mode (Web Audio API)

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

## 5. Spesifikasi Kartu Pelajar PVC ISO CR-80 & Direktori Siswa Interaktif

### 5.1 Standar Fisik Kartu ISO/IEC 7810 ID-1 CR-80
- **Dimensi Fisik**: $85.60\text{ mm} \times 53.98\text{ mm}$ (Rasio aspek 85.6:53.98, ketebalan $0.76\text{ mm}$, material PVC tahan air).
- **Tampak Depan (*Front View*)**:
  - Latar putih bersih dengan kop resmi SDN Kebonagung Kota Pasuruan.
  - Pas foto 3x4 potret resmi siswa.
  - Biodata: Nama Lengkap Siswa, NISN, Rombel/Kelas, Status Siswa.
  - Barcode 1D Code 128 di sudut kiri bawah untuk laser scanner cepat pos gerbang.
  - QR Code 2D bertanda tangan digital HMAC-SHA256 di sudut kanan bawah.
- **Tampak Belakang (*Back View*)**:
  - Ketentuan & tata tertib pemegang kartu identitas siswa.
  - Masa berlaku kartu s.d. kelulusan dan nomor telepon darurat sekolah.
  - Tanda tangan digital Kepala Sekolah ber-NIP dan stempel resmi QR.

### 5.2 Antarmuka Direktori Siswa & Toolbar Pencarian Berkapasitas Tinggi
- **Komponen Search Bar**: Input `bg-slate-50 border-slate-200` berikon `bi-search` dengan clear button `bi-x-circle-fill`, pencarian live pada kolom Nama, NISN, dan Rombel Kelas.
- **Komponen Filter Toolbar**: Filter seleksi Rombel Kelas, Filter Status Kartu (*Aktif*, *Perlu Cetak Ulang*, *Revoked*), Selector Baris per Halaman (*5*, *10*, *20*), dan tombol Reset Filter.
- **Komponen Tabel Direktori**: Baris bergaris halus dengan badge inisial avatar, indikator kelas `bg-rose-50 text-rose-700`, monospace UID barcode `font-mono`, badge status penerbitan, baris aktif dengan sorotan `bg-rose-50/70 border-l-4 border-l-rose-600`, tombol aksi baris **"Pilih" / "Ditinjau"**, serta tombol cetak instan **"🖨️ Cetak"**.
- **Komponen Paginasi**: Teks indikator *"Menampilkan X - Y dari Z siswa"*, tombol Sebelumnya (*Prev*), tombol halaman bernomor dengan status aktif `bg-rose-600 text-white font-bold`, dan tombol Selanjutnya (*Next*).

### 5.3 Spesifikasi Komponen Kamera Pemindai Barcode HP (WebRTC Mobile Scanner)
- **Komponen Pemilih Kamera (*Facing Mode Switcher*)**: Tombol pill bersegmen ganda `[📷 Kamera Belakang (Utama)]` dan `[🤳 Kamera Depan (User/Stand)]` dengan indikator aktif putih beraksen `text-rose-700`.
- **Komponen Viewport Video**: Kotak rasio 4:3 / 16:9 berbingkai sudut tajam (*corner brackets*) `border-rose-500` dengan garis laser scan animasi vertikal `.scanner-laser` (durasi 2 detik, gradien `#e11d48` bercahaya `box-shadow`).
- **Komponen Tombol Kontrol**: Tombol Buka/Tutup Kamera (`bg-rose-600 hover:bg-rose-700` saat standby, `bg-slate-900` saat aktif), Tombol Lampu Senter (`bg-amber-100 text-amber-800` saat menyala), dan status badge real-time *Kamera Aktif & Memindai*.
- **Mekanisme Umpan Balik Kesiswaan**: Getar haptic HP ganda `[70ms, 40ms, 70ms]`, Web Audio synthesizer, dan jeda anti-duplikasi 2.5 detik antar-pemindaian.

---

## 6. Spesifikasi Halaman Welcome & Auth Modal (Clean White HRD-Style)

Menerapkan arsitektur halaman depan modern berkelas korporat/edukasi:
1. **Latar Belakang & Navigasi**:
   - Ambient subtle background glows pastel (`from-rose-100/50 via-red-50/30`, `from-indigo-100/40`, `from-emerald-100/40`).
   - Header Glassmorphism: `backdrop-blur-md bg-white/90 border-b border-slate-200/80`.
   - Logo gradien merah-rose `from-red-600 via-rose-600 to-rose-500` dengan huruf "E" (EduTrack.Pro).
   - Indikator status live: `Cloud EduTrack • SLA 99.8%` dengan pulsing emerald dot.
2. **Hero Section & Metrik Kepercayaan**:
   - Headline: *"Presensi Gerbang 3-Mode, Tatib Humanis & Bahan Ajar AI"*.
   - 4 Kartu Metrik Telemetri: `432 Siswa`, `≤ 0.3 Detik Scanner`, `ISO 27001`, `UU PDP AES-256`.
3. **5 Kartu Akses Peran Cepat (1-Klik Demo Login)**:
   - `👑 Super Admin / Kepsek` (Drs. H. Mulyadi M.Pd)
   - `🛡️ Guru BK / Kesiswaan` (Dra. Hj. Nurjanah M.Pd)
   - `📋 Wali Kelas` (Bambang S.Pd - X-RPL-1)
   - `👮 Satpam Pos Gerbang` (Pak Joko - Scanner 3-Mode)
   - `👨‍👩‍👦 Orang Tua Murid / Siswa` (Bpk. Hendra Gunawan / Ahmad Rifai)
4. **Modal Autentikasi (Clean White Modern)**:
   - Dialog mengambang dengan latar `backdrop-blur-sm bg-slate-900/40`.
   - Kontainer `bg-white rounded-3xl shadow-2xl border border-slate-200 p-6 sm:p-8`.
   - Tombol Masuk utama: `w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md shadow-rose-600/20`.
   - Selector peran demo cepat 1-klik di bagian bawah formulir.

---

## 7. Spesifikasi Desain Dasbor Analisis AI & Floating Copilot

### 7.1 Dasbor Analisis AI View (`activePage === 'bahan_ajar'`)
- **Header Banner Card**: Berlatar putih bersih dengan bayangan halus `shadow-xs border border-slate-100 rounded-2xl p-5`, ikon `bi bi-graph-up-arrow` bergradasi Rose-50, serta input pengaturan cepat `geminiApiKey`.
- **Tabel Data Siswa Simulasi**: Menampilkan data kehadiran dan nilai siswa (Ahmad, Budi, Citra) dengan indikator status otomatis:
  - Penurunan Nilai: Lencana Rose-50 (`nilai_matematika_minggu2 < nilai_matematika_minggu1 - 10`).
  - Absensi Kritis: Lencana Amber-50 (`alpa_bulan_ini >= 3`).
  - Normal/Aman: Lencana Emerald-50.
- **Kartu Utama "Analisis Mingguan AI"**:
  - Background putih, sudut membulat `rounded-2xl`, shadow halus `shadow-sm border border-slate-100 p-6 md:p-8`.
  - Ikon peringatan oranye/merah (`bi bi-exclamation-triangle-fill text-amber-500`) di sebelah kiri judul.
  - Judul: *"Analisis Mingguan AI"*.
  - Isi Teks: Output respons empatik hasil analisis Google Gemini 1.5 Flash (maksimal 3 kalimat penjelasan kondisi siswa bermasalah + 1 rekomendasi tindakan guru).
  - Tombol Aksi: Tombol *"Muat Ulang Analisis"* berwarna biru (`bg-blue-600 hover:bg-blue-700 text-white rounded-xl`) dengan ikon reload berputar (`spin-anim`) saat proses asinkron berlangsung.

### 7.3 Floating Copilot Launcher & Slide-Out Chat Drawer
- **Launcher Pill**: Posisi `fixed; bottom: 24px; right: 24px; z-index: 1045;`, gradien rose-crimson `linear-gradient(135deg, #e11d48, #be123c)`, shadow-lg `shadow-rose-600/35`, lencana online hijau.
- **Drawer Panel**: Posisi `fixed; bottom: 82px; right: 24px; width: 395px; max-width: calc(100vw - 32px); height: 570px; z-index: 1050; border-radius: 24px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);`.
- **Chips Prompt Cepat**: Tombol rounded-xl geser horizontal untuk query umum presensi, SP, WA, dan soal HOTS.
- **Gelembung Obrolan**: Gelembung rose (`bg-rose-600 text-white`) untuk pengguna dan abu-abu bersih (`bg-slate-100 text-slate-800 border border-slate-200`) untuk AI.
