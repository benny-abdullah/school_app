# Design System & UI Token Standard (SSOT)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Tema Utama**: Clean White Minimalist & GitHub Repository Theme  
**Framework**: Tailwind CSS v3/v4 + Native CSS Custom Properties + Octicons Icons

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh antarmuka visual, token warna, tipografi, komponen interaktif, tata letak dua kolom bergaya GitHub, halaman depan edukasi, dan umpan balik audio scanner.

---

## 1. Token Warna Global (CSS Design Tokens)

Token warna baku didefinisikan pada `:root` untuk menjamin konsistensi kontras tinggi dan estetika *Clean White*:

```css
:root {
  /* Canvas & Background */
  --color-canvas-default: #FFFFFF;        /* Latar belakang utama kartu & konten */
  --color-canvas-subtle: #F8FAFC;         /* Latar belakang layout luar & tabel zebra */
  --color-canvas-inset: #F1F5F9;          /* Kontainer input, filter bar & badge disabled */

  /* Borders & Dividers */
  --color-border-default: #E2E8F0;        /* Garis pembatas tabel & kotak panel */
  --color-border-subtle: #CBD5E1;         /* Garis pembatas kartu & header */
  --color-border-active: #0F172A;         /* Border focus elemen terpilih */

  /* Typography / Text */
  --color-fg-default: #0F172A;            /* Teks utama (Slate-900) */
  --color-fg-muted: #64748B;              /* Teks sekunder, label, subjudul (Slate-500) */
  --color-fg-subtle: #94A3B8;             /* Placeholder teks & ikon nonaktif */
  --color-fg-on-dark: #FFFFFF;            /* Teks di atas tombol gelap */

  /* GitHub Theme Accents */
  --color-github-header: #24292F;         /* Header gelap khas GitHub */
  --color-github-orange: #FD8C73;         /* Aksen oranye garis bawah tab aktif GitHub */
  --color-github-green: #2DA44E;          /* Tombol hijau utama (<> Code / Simpan) */
  --color-github-blue: #0969DA;           /* Tautan breadcrumb & hash komit */

  /* Semantic Status Colors */
  --color-status-success: #10B981;        /* Hadir tepat waktu / Disetujui / Prestasi */
  --color-status-success-bg: #ECFDF5;
  --color-status-warning: #F59E0B;        /* Terlambat Ringan T-1 / Menunggu Verifikasi */
  --color-status-warning-bg: #FFFBEB;
  --color-status-danger: #EF4444;         /* Alpa / Terlambat Berat T-3 / SP / Pelanggaran */
  --color-status-danger-bg: #FEF2F2;
  --color-status-info: #0EA5E9;           /* Izin / Sakit / Gate Pass / Info WA */
  --color-status-info-bg: #F0F9FF;

  /* Welcome Landing Page (Lavender Education Theme) */
  --color-lavender-hero: #F3F0FF;         /* Latar belakang hero landing page */
  --color-lavender-accent: #7C3AED;       /* Aksen ungu edukasi Dribbble */
  --color-khaki-pns: #C8B282;             /* Seragam dinas Guru PNS ASN */
}
```

---

## 2. Tipografi & Hirarki Teks

Menggunakan font sistem modern **Inter** / `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto`:

| Kelas Tipografi | Ukuran (Size) | Bobot (Weight) | Jarak Baris (Line Height) | Penggunaan |
| :--- | :--- | :--- | :--- | :--- |
| **Display Title** | $28\text{ px}$ (`text-2xl` / `text-3xl`) | Bold (`font-bold`, 700) | $1.25$ | Judul modul utama & Hero Welcome Page |
| **Section Heading** | $20\text{ px}$ (`text-xl`) | Semibold (`font-semibold`, 600) | $1.35$ | Judul kartu panel & header modal popup |
| **Card / Table Title**| $15\text{ px}$ (`text-base`) | Medium (`font-medium`, 500) | $1.4$ | Header kolom tabel & nama siswa pada daftar |
| **Body Regular** | $14\text{ px}$ (`text-sm`) | Normal (`font-normal`, 400) | $1.5$ | Teks konten, keterangan izin, deskripsi aduan |
| **Caption / Helper** | $12\text{ px}$ (`text-xs`) | Medium / Normal | $1.4$ | Badge status, timestamp audit log, NISN helper |
| **Monospace Code** | $12\text{ px}$ / $13\text{ px}$ (`font-mono`) | Regular | $1.3$ | Hash komit git, token QR HMAC, NIP, NISN |

---

## 3. Komponen Tata Letak Dua Kolom Bergaya GitHub

Aplikasi mengadopsi struktur repositori visual GitHub yang memudahkan pengawasan menyeluruh:

```
+-----------------------------------------------------------------------------------------+
| [LOGO] sdn-kebonagung-pasuruan / school_app  [Public]   [Search / ]   [👑 Role Switcher]|
+-----------------------------------------------------------------------------------------+
| [Code] [Dashboard] [Presensi] [Scanner] [Kartu Pelajar] [Tatib & SP] [Prestasi] [Aduan]|
+-----------------------------------------------------------------------------------------+
| [branch: main v]  [3 Tags]  [142 Commits]                          [<> Code (Download) v]|
+------------------------------------------------------------------+----------------------+
| KOLOM UTAMA (MAIN CONTENT - 75% Lebar):                          | SIDEBAR KANAN (25%): |
|  - Commit Bar: "Update role permissions & ISO 27001 audit logs"  | About:               |
|    Verified 34acc33 • 2 hours ago • Drs. H. Mulyadi M.Pd         | Sistem Terpadu SDN   |
|  - Card Konten Modul Aktif:                                      | Kebonagung Pasuruan  |
|    - Filter Cepat (Pencarian, Rombel, Status)                    |                      |
|    - Action Toolbar (+ Tambah Data, Bulk Action, Ekspor)         | Releases:            |
|    - Data Table dengan Server-side Pagination & Sortable Headers | v2.4-Production-Ready|
|                                                                  |                      |
|                                                                  | Deployments:         |
|                                                                  | - Server Daemon OK   |
|                                                                  | - S3 Backup Vault OK |
|                                                                  | - WA Gateway Ready   |
|                                                                  |                      |
|                                                                  | Data Scope Active:   |
|                                                                  | [X-RPL-1]            |
+------------------------------------------------------------------+----------------------+
```

---

## 4. Spesifikasi Kartu Pelajar Fisik (ISO/IEC 7810 ID-1 CR-80)

Kartu identitas siswa dirancang mematuhi standar dimensi kartu perbankan global:
- **Dimensi Fisik**: Lebar $85.60\text{ mm} \times$ Tinggi $53.98\text{ mm}$ (Rasio 1.586, Sudut Membulat Radius $3.18\text{ mm}$, Ketebalan $0.76\text{ mm}$ PVC).
- **Tata Letak Depan (*Front View*)**:
  - Kop Institusi: Logo SDN Kebonagung Kota Pasuruan, Alamat Resmi, Teks "KARTU IDENTITAS SISWA - ISO/IEC 27001 SECURED".
  - Foto Siswa Resmi: Format pas foto 3x4 rasio potret latar merah/biru.
  - Biodata Inti: Nama Lengkap (Kapital), NISN, NIS, Rombel/Kelas, Tempat Tanggal Lahir, Golongan Darah.
  - Barcode 1D (Code 128): Diposisikan di sudut kiri bawah dengan teks NISN terbaca manusia (*human-readable text*).
  - QR Code 2D Terverifikasi: Diposisikan di sudut kanan bawah dengan tanda tangan kriptografis HMAC-SHA256.
- **Tata Letak Belakang (*Back View*)**:
  - Ketentuan & Tata Tertib Penggunaan Kartu (Wajib dibawa setiap hari, tidak boleh dialihkan/dipinjamkan).
  - Tanda Tangan Digital Kepala Sekolah ber-stempel kedinasan.
  - Kotak Informasi Kontak Darurat Sekolah dan Nomor Layanan Pengaduan.

---

## 5. Token Umpan Balik Audio Pemindai Barcode (Web Audio API)

Untuk memfasilitasi operator pos gerbang bekerja *hands-free* tanpa terus melihat layar monitor, sistem memanfaatkan Web Audio API sintesis suara:

| Skenario Pemindaian | Nada Frekuensi (Frequency) | Durasi | Tipe Gelombang | Makna Operasional |
| :--- | :--- | :--- | :--- | :--- |
| **Scan Tepat Waktu (06:00 - 07:00)** | $880\text{ Hz}$ (Nada A5 Tinggi) | $100\text{ ms}$ | `sine` | Sukses Hadir! Siswa langsung masuk kelas. |
| **Terlambat Ringan T-1 (07:01 - 07:15)** | $660\text{ Hz} \to 550\text{ Hz}$ | $180\text{ ms}$ | `triangle` | Terlambat dispensasi; tercatat -2 poin. |
| **Terlambat Sedang/Berat (> 07:15)** | $180\text{ Hz}$ (Buzz Rendah) | $300\text{ ms}$ | `sawtooth` | Peringatan! Gerbang ditutup; siswa diarahkan ke piket. |
| **Kartu Ditolak / Tidak Terdaftar** | $150\text{ Hz} \times 2$ (Double Buzz) | $200\text{ ms} + 200\text{ ms}$ | `square` | Error! Barcode palsu atau belum terdaftar. |
| **Double Tap Cooldown (< 5 Menit)** | $440\text{ Hz}$ | $80\text{ ms}$ | `sine` | Siswa sudah dipindai sebelumnya. |
| **Palang Gerbang Terbuka (Gate Pass)**| $523\text{ Hz} \to 659\text{ Hz} \to 784\text{ Hz}$ | $250\text{ ms}$ | `sine` | Tiket Izin Keluar Valid. Palang gerbang dibuka. |

---

## 6. Komponen Antarmuka Kunci (Reusable UI Components)

1. **Quick Role Switcher (Header Global)**:
   - Dropdown selektor kilat untuk berpindah antar 5 peran demo (`Super Admin`, `Guru BK`, `Wali Kelas`, `Orang Tua`, `Siswa`).
   - Setiap pergantian langsung memutasi `roleDataScope`, memperbarui menu navigasi, dan menyaring tabel data secara otomatis.
2. **Bulk Attendance Fast Action**:
   - Tombol instan set status per baris: `H` (Hadir - Emerald), `S` (Sakit - Blue), `I` (Izin - Sky), `A` (Alpa - Rose), `T` (Terlambat - Amber).
   - Tombol master `"✓ Set Semua Hadir"` untuk mempercepat absensi kelas pagi oleh wali kelas.
3. **Modal Dialog Konfirmasi Destruktif**:
   - Dialog konfirmasi bertingkat sebelum eksekusi pembatalan SP, penghapusan akun, atau pencabutan kartu, dilengkapi pencatatan mutasi ke log audit ISO 27001.
4. **Enkripsi Chat Container (Whistleblowing Room)**:
   - Balon obrolan interaktif dengan penanda keamanan `"🔒 AES-256 Encrypted Tunnel"`, pembeda warna antara Pelapor (Biru Terang / Netral Anonim) dan Pihak Sekolah (Emerald).