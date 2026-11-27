# Desain Antarmuka Pengguna & Spesifikasi Halaman (Design UI) - SSOT
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Gaya Desain**: Enterprise Clean White Modern Minimalist Dashboard (HRD-Style) + Glassmorphism Welcome Page & Modern Auth Modal  
**Tipografi**: Plus Jakarta Sans (300-800)  
**Palet Utama**: Clean White Slate Canvas (`#f8fafc`), Primary Rose-600 (`#e11d48`), Rose-700 hover (`#be123c`), Soft Tint Rose-50 (`#fff1f2`), Emerald-500, Amber-500  
**Komponen Inti**: Responsive Mini-Collapsible Sidebar (260px / 68px), Sticky Glassmorphism Header (`backdrop-blur-md bg-white/90`), Role Switcher Pill di Header, Omnibox Quick Search (`Ctrl+K`), Ambient Subtle Pastel Blurs, Rounded-2xl Cards, Soft Pill Badges, Studio Bahan Ajar AI, Floating Copilot  
**Total Tampilan**: 16 Modul Halaman & 12 Modal Interaktif

Dokumen ini merupakan **Single Source of Truth (SSOT)** untuk seluruh tata letak visual (*wireframe*), struktur shell Enterprise Clean White Admin, halaman pendaratan Glassmorphism Welcome Page & Modal Otentikasi Terpadu, kartu statistik `rounded-2xl`, grafik analitik data, bilah pencarian & filter dinamis, paginasi, studio bahan ajar AI, dan alur antarmuka pemindai gerbang 3 mode.

---

## 1. Peta Rute Navigasi Sistem (Sitemap)

```mermaid
graph TD
    LOGIN["🔐 Glassmorphism Welcome Page & Modern Auth Modal (5 Role Quick Switcher & Trust Metrics)"]

    subgraph Internal_Portal ["Enterprise Clean White Admin Portal - Responsive Shell (Desktop 260px/68px & Mobile Drawer)"]
        DASH["📊 Dashboard Utama & AI Executive Briefing (/dashboard)"]
        
        subgraph Modul_Presensi ["Operasional Kehadiran & Gerbang 3 Mode"]
            ATT_DAY["✅ Kehadiran Siswa Harian Bulk H/S/I/A/T (/presensi)"]
            ATT_KBM["📚 Presensi KBM Jam Mapel & Deteksi Membolos (/presensi#kbm)"]
            SCAN_3M["📷 Pemindai Gerbang 3 Mode Otomatis (/scanner)"]
            GATE_PASS["🎫 Verifikasi Digital Gate Pass Satpam (/scanner#gate-pass)"]
        end

        subgraph Modul_Kesiswaan ["Pembinaan Karakter & Siswa"]
            CARD_GEN["🪪 Cetak Kartu Pelajar PVC ISO CR-80 (/kartu-pelajar)"]
            TATIB["⚠️ Kedisiplinan, Poin Tatib & Cetak SP (/pelanggaran)"]
            PRESTASI["🏆 Bank Prestasi & Merit Points (/prestasi)"]
            WHISTLE["📢 Pusat Aduan & Whistleblower Terenkripsi AES-256 (/aduan)"]
        end

        subgraph Modul_AI ["Kecerdasan Buatan & Kurikulum Merdeka 2026"]
            AI_STUDIO["✨ Studio Bahan Ajar AI: Modul Ajar, Soal HOTS, LKPD, Rubrik KKTP (/bahan_ajar)"]
            AI_COPILOT["💬 Floating AI Copilot Assistant & Slide-Out Chat Drawer"]
        end

        subgraph Modul_Master ["Master Data & Pengguna"]
            GTK["👔 Manajemen Guru & Pegawai - GTK Admin (/guru-pegawai)"]
            SISWA["🎓 Manajemen Siswa & Rombel (/siswa-mgmt)"]
            ORTU["👨‍👩‍👦 Manajemen Orang Tua & Aktivasi WA (/ortu-mgmt)"]
            USERS["👥 Manajemen Pengguna & Scope (/users)"]
        end

        subgraph Modul_Keamanan ["Kepatuhan ISO/IEC 27001"]
            BACKUP["💾 Backup Data Terenkripsi 3-2-1 (/backup)"]
            AUDIT["🔒 Log Audit Kekal A.12.4 (/audit-log)"]
        end
    end

    LOGIN --> DASH
    DASH --> Modul_Presensi & Modul_Kesiswaan & Modul_AI & Modul_Master & Modul_Keamanan
```

---

## 2. Struktur Tata Letak Halaman Standar (Enterprise Clean White Shell)

Setiap halaman modul menerapkan shell standar Enterprise Clean White Modern Minimalist yang elegan, bersih, dan sangat responsif:

```
+--------------------------------------------------------------------------------------------------------------------------------+
| [E] EduTrack.Pro  [≡ Toggle]  [🔍 Search... (Ctrl+K)]        [Role: 👑 Super Admin v]  [🚪 Keluar]  [🔔 3]  [👤 John Doe (Super Admin)]|
+---------------+----------------------------------------------------------------------------------------------------------------+
| SIDEBAR       | CONTENT AREA (admin-main container-fluid p-3 p-lg-4, Canvas bg-slate-100 / ambient subtle pastel blurs)       |
| (260px/68px)  |                                                                                                                |
|               | [ AI EXECUTIVE BRIEFING & SMART INSIGHT CARD (Border Rose-100 / bg-white rounded-2xl shadow-sm) ]              |
| [📊 Dashboard]| +------------------------------------------------------------------------------------------------------------+ |
| [✅ Kehadiran]| | [✨] AI Executive Briefing · Analisis Real-Time Pagi Hari             [📄 Drafkan SP]  [💬 Buka AI Copilot] | |
| [📷 Scanner]  | | Kehadiran 94.8% · Ditemukan 1 Kasus Kesiswaan Perlu Tindak Lanjut: Budi Santoso (XI-TKJ-2) Alpa 3 hari.   | |
| [🪪 Kartu]    | +------------------------------------------------------------------------------------------------------------+ |
| [⚠️ Tatib]    |                                                                                                                |
| [🏆 Prestasi] | [ 4 ENTERPRISE STATS CARDS (TOTAL SISWA, KEHADIRAN HARI INI, PELANGGARAN AKTIF, LAPORAN ADUAN) ]              |
| [✨ Bahan Ajar| +--------------------+ +--------------------+ +--------------------+ +-----------------------------------------+ |
|     (AI)]     | | TOTAL SISWA AKTIF  | | KEHADIRAN HARI INI | | PELANGGARAN AKTIF  | | ADUAN / WHISTLEBLOWER                   | |
| [📢 Aduan]    | | 432 Siswa   ↑ 1.2% | | 94.8% Hadir ↑ 5.4% | | 3 Kasus    ↓ 25%   | | 0 Kasus Terbuka (Aman)                | |
| [👔 GTK]      | +--------------------+ +--------------------+ +--------------------+ +-----------------------------------------+ |
| [🎓 Siswa]    |                                                                                                                |
| [👨‍👩‍👦 Ortu]     | [ SEKSI GRAFIK & AKTIVITAS DUA KOLOM (Clean White Card rounded-2xl border-slate-100) ]                        |
| [👥 User]     | +------------------------------------------------------------+ +-----------------------------------------------+ |
| [💾 Backup]   | | TREN KEHADIRAN MINGGUAN (Rose-600 & Emerald-500 Curves)    | | RECENT ACTIVITY / LOG KESISWAAN               | |
| [🔒 Audit]    | | ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~                         | | • Siswa baru terdaftar                        | |
|               | | ----------------------------------                         | | • Presensi gerbang selesai (410 hadir)        | |
|               | +------------------------------------------------------------+ +-----------------------------------------------+ |
|               |                                                                                                                |
|               | [ TABEL DATA INTERAKTIF DENGAN ACTION DROPDOWN & SOFT STATUS PILLS ]                                           |
|               | +------------------------------------------------------------------------------------------------------------+ |
|               | | NISN/SISWA         | KELAS   | JAM MASUK | JAM PULANG & STATUS | STATUS    | REKONSILIASI   | AKSI CEPAT   | |
|               | | Ahmad Rifai        | X-RPL-1 | 07:15 WIB | ✔ Pulang: 14:15 WIB | Terlambat | Ban bocor      | [Catatan][H] | |
|               | | Siti Nurhaliza     | X-RPL-1 | 06:45 WIB | ✔ Pulang: 14:15 WIB | Hadir     | Tepat waktu    | [Catatan][H] | |
|               | | Budi Santoso       | XI-TKJ-2| -         | -                   | Alpa (3x) | Butuh HomeVisit| [Catatan][A] | |
|               | +------------------------------------------------------------------------------------------------------------+ |
|               |                                                                                                                |
|               | [ FOOTER: EduTrack Pro © 2026. SDN Kebonagung Pasuruan · Sistem Informasi Terpadu Kesiswaan & Presensi ]       |
+---------------+----------------------------------------------------------------------------------------------------------------+
                                                                                       [ ✨ AI Copilot Launcher Pill (Rose-600) ]
```

---

## 3. Wireframe Studio Bahan Ajar AI (`/bahan_ajar`)

```
+--------------------------------------------------------------------------------------------------------------------------------+
| STUDIO BAHAN AJAR & ASISTEN GURU AI (KURIKULUM MERDEKA 2026)                                                                   |
| [Kurikulum Merdeka 2026] [AI Teaching Studio] [Fase A - F]                             [💬 Chat AI Copilot]                    |
+--------------------------------------------------------------------------------------------------------------------------------+
| [📘 Modul Ajar (RPP)] [📝 Bank Soal HOTS] [📋 Lembar LKPD] [🎯 Rubrik KKTP] [🎲 Ice Breaking] [💡 Remedial & Pengayaan]       |
+--------------------------------------------------------------------------------------------------------------------------------+
| PARAMETER & KONFIGURASI (KOLOM KIRI - 40%)                   | HASIL DOKUMEN TERBITAN AI (KOLOM KANAN - 60%)                  |
|                                                              |                                                                |
| Preset Cepat: [➗ Pecahan] [💧 Siklus Air] [📖 Fantasi]       | [Siap Digunakan]          [📋 Salin] [🖨️ Cetak] [💾 Unduh File] |
|                                                              | +------------------------------------------------------------+ |
| Mata Pelajaran: [Matematika                           v]     | | # MODUL AJAR KURIKULUM MERDEKA (RPP PLUS)                  | |
| Jenjang/Fase:   [Fase B - Kelas 4 SD                  v]     | | INFORMASI UMUM: SDN Kebonagung Kota Pasuruan               | |
| Topik/Materi:   [Pecahan Senilai dan Operasi Hitung   ]     | | Fase B / Kelas 4 / Matematika / 2 x 35 Menit (PBL)         | |
| Model Belajar:  [Problem Based Learning (PBL)         v]     | |                                                            | |
| Alokasi Waktu:  [2 x 35 Menit (2 JP)                  v]     | | I. TUJUAN PEMBELAJARAN (TP & KKTP):                        | |
| Jumlah Soal:    [5 Butir Soal                         v]     | | Peserta didik dapat menganalisis representasi pecahan...   | |
|                                                              | | II. KEGIATAN PEMBELAJARAN (LENGKAP):                       | |
| [ 🚀 Generate Bahan Ajar dengan AI ]                         | | - Pendahuluan (10 Menit): Ice breaking & apersepsi         | |
|                                                              | | - Inti (50 Menit): Orientasi masalah & diskusi kelompok    | |
| 🛡️ Terverifikasi BSKAP Kemendikbudristek & Profil Pancasila | | - Penutup (10 Menit): Refleksi & asesmen formatif          | |
|                                                              | +------------------------------------------------------------+ |
+--------------------------------------------------------------+----------------------------------------------------------------+
```

---

## 4. Rincian Wireframe Modul Kunci Lainnya

### 3.1 Scanner Barcode Gerbang 3 Mode Otomatis (`/scanner`)
- **Indikator Mode Aktif Otomatis**:
  - Banner dinamis di atas layar menampilkan mode yang sedang berjalan sesuai jam server:
    - `06:00 - 08:00 WIB` = **MODE 1: PRESENSI MASUK (CHECK-IN)** (Lencana Hijau Emerald).
    - `08:01 - 13:00 WIB` = **MODE 2: SIAGA DIGITAL GATE PASS (IZIN KELUAR)** (Lencana Biru Sky).
    - `13:01 - 17:00 WIB` = **MODE 3: PRESENSI PULANG (CHECK-OUT)** (Lencana Oranye Amber).
- **Tab 1: Live Scanner Screen**:
  - Input auto-focus konstan yang siap menerima semburan keystroke dari laser gun scanner.
  - Kartu Hasil Pemindaian Terakhir (*Feedback Box*):
    - Foto siswa format 3x4.
    - Nama, NISN, Rombel Kelas.
    - Status Kehadiran (Hadir, T-1, T-2, T-3, Pulang Tercatat).
    - Umpan Balik Audio (Beep 880Hz, Buzz 180Hz, Chime 3 nada).
    - Indikator centang hijau: *"Notifikasi WhatsApp Terkirim ke Wali Murid"*.
- **Tab 2: Verifikasi Digital Gate Pass Satpam**:
  - Formulir input/scan kode tiket gate pass (`GP-20261123-001`).
  - Menampilkan alasan izin keluar, pejabat yang menyetujui, dan nama penjemput yang terverifikasi.
  - Tombol **"✓ Izinkan Keluar & Buka Palang"**: Merekam `early_leave_time`, mengubah status presensi menjadi `Hadir (Izin Pulang Cepat)`, dan mengirim alert WhatsApp ke orang tua.

---

### 3.2 Modul Pengajuan Izin / Sakit Fleksibel Ortu
- **Tombol di Dashboard Ortu**: *"📝 Ajukan Izin / Sakit Anak"*.
- **Modal Interaktif Pilihan Kategori**:
  - Pilihan 1: **Sakit 1 Hari (Surat Izin Ortu)** — Menampilkan kuota tersisa (misal: *Sisa kuota semester ini: 1x*). Cukup unggah foto surat izin tulisan tangan bertanda tangan orang tua.
  - Pilihan 2: **Sakit > 1 Hari (Wajib Surat Dokter)** — Mewajibkan unggah surat dokter/Puskesmas.
  - Pilihan 3: **Izin Keperluan Keluarga Mendesak** — Rentang tanggal kalender.
- **Tampilan Sisi Wali Kelas**:
  - Banner peringatan di atas tabel presensi jika ada permohonan izin baru yang belum diverifikasi.
  - Tombol 1-klik: **"✓ Setujui Izin"** (otomatis membebaskan siswa dari Auto-Alpa).

---

### 3.3 Presensi KBM Jam Mapel & Deteksi Membolos
- Selector pilihan mata pelajaran aktif (contoh: *Pemrograman Web & Mobile*, *Matematika Terapan*).
- Menampilkan status awal siswa yang diambil dari presensi gerbang pagi.
- Jika siswa hadir pagi tetapi tidak ada di kursi saat pelajaran, guru mengklik tombol merah **"🚨 Lapor Membolos ke BK"**.
- Poin tatib bertambah +10, tercatat di log audit, dan alert WhatsApp darurat terkirim seketika.

---

### 3.4 Cetak Kartu Pelajar PVC ISO CR-80 (`/kartu-pelajar`)
- Pratinjau kanvas bolak-balik dimensi fisik $85.60\text{ mm} \times 53.98\text{ mm}$.
- Tampilan depan: Pas foto 3x4, Barcode Code 128 (NISN), dan QR Code 2D (HMAC-SHA256).
- Tampilan belakang: Aturan kartu dan tanda tangan digital kepala sekolah.
- Tombol **"🖨️ Cetak Kartu Siswa Ini"** (otomatis print CSS ukuran CR-80) dan tombol **"Revoke & Regenerate"** (jika kartu hilang).

---

### 3.5 Manajemen Guru & Tenaga Kependidikan (GTK - Master Staff)
- Modul eksklusif dilindungi hanya untuk role Super Admin / Kepala Sekolah.
- Kartu KPI: Total Guru PNS/PPPK, Total Tenaga Honorer, Petugas Sarpras & Keamanan.
- Tabel lengkap memuat NIP, NUPTK, Nama Lengkap & Gelar, Gender, Status Kepegawaian, Jabatan/Tugas Utama, Rombel/Mapel Binaan, Kontak WhatsApp, dan Status Keaktifan.
- Modal Tambah/Edit GTK dengan validasi penomoran kepegawaian otomatis dan audit log ISO 27001.