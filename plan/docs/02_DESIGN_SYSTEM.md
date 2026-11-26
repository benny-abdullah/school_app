# Design System & UI Token Standard (SSOT)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Tema Utama**: Dominan Putih Minimalis Modern Profesional (White Minimalist)  
**Kombinasi Framework**: Bootstrap 5 Modern Effects + Tailwind CSS Utility Classes + Octicons / Bootstrap Icons SVG  
**Komponen Wajib**: Pagination, Debounced Search, Multi-Filter, Interactive Charts, KPI Info Cards, Info Icons

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh antarmuka visual, palet warna putih modern, komponen kartu statistik, grafik analitik data, tipografi, dan umpan balik audio pemindai 3 mode.

---

## 1. Token Warna Global (White Minimalist Palette)

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