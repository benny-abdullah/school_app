# Panduan Pemahaman: Cetak Biru EduTrack Pro (Framework JavaScript)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Teknologi Utama**: Full-stack JavaScript / TypeScript (Nuxt 3 Vue 3 / Node.js Fastify + PostgreSQL 16 + Redis BullMQ)  
**Gaya Desain**: Dominan Putih Minimalis Modern Profesional (Bootstrap 5 Modern Effects + Tailwind CSS + Chart.js)  
**Berkas Panduan**: `C:\scholl_app\pemahaman.md`

---

## 🌟 1. Apa Itu Folder `plan/`?

Folder `C:\scholl_app\plan\` adalah **Cetak Biru (Blueprint / Gambar Kerja Teknik)** resmi untuk mentransformasikan prototipe [index.html](file:///c:/scholl_app/index.html) menjadi **Aplikasi Produksi Resmi Skala Penuh berbasis Framework JavaScript**.

Folder ini memuat seluruh spesifikasi teknis dan bisnis:
- **Skema 21 Tabel Relasional PostgreSQL 16** (via Drizzle ORM).
- **Aturan Presensi 3 Mode Otomatis** (Pagi Masuk, Siang Gate Pass, Sore Pulang).
- **Keadilan Presensi Kepulangan** (Siswa lupa tap pulang tidak dihukum Alpa).
- **Kelonggaran Izin Sakit** (Sakit 1 hari cukup surat izin orang tua).
- **Pencatatan Pulang Cepat** (Jam keluar tercatat presisi & alert WA otomatis).
- **Standar Visual Putih Minimalis Modern** dengan komponen lengkap (*Pagination*, *Debounced Search*, *Multi-Filter*, *Chart.js*, *KPI Info Cards*, dan *Info Icons*).

---

## 🚀 2. Mengapa Memilih Framework JavaScript (Nuxt 3 / Node.js)?

1. **Migrasi Alami & Mulus dari `index.html`**:
   - File prototipe `index.html` Anda sudah dibuat menggunakan **Vue 3 (Composition API)**.
   - Dengan framework full-stack seperti **Nuxt 3 (Vue 3)**, seluruh komponen tabel, modal popup, filter reaktif, dan logika tampilan dari `index.html` dapat dipindahkan **hampir 1:1** tanpa membuang pekerjaan desain yang sudah Anda buat!
2. **Kinerja Scanner Gerbang Super Cepat ($\le 50\text{ ms}$)**:
   - Backend berbasis **Node.js (Fastify / Nitro Engine)** memiliki arsitektur *non-blocking I/O* yang sangat cepat, sanggup memproses 500 pemindaian barcode siswa di pos gerbang dalam waktu 15 menit tanpa antrean macet.
3. **Database Modern & Aman dengan Drizzle ORM**:
   - Mengelola 21 tabel relasional PostgreSQL 16 dengan tipe data ketat (*type-safe*), UUIDv7 untuk mencegah manipulasi IDOR, dan trigger database untuk mengunci keaslian log audit ISO 27001.
4. **Antrean Pesan WhatsApp Asinkron (BullMQ + Redis)**:
   - Pengiriman pesan WhatsApp (notifikasi masuk pagi, kepulangan sore, dan alert darurat) diproses di latar belakang (*background worker*) sehingga alat scanner di pos satpam tidak terganggu.

---

## ⏰ 3. Solusi 4 Celah Operasional Lapangan yang Baru Disempurnakan

### A. Skema Pemindai Gerbang 3 Jendela Waktu Otomatis (*3-Mode Auto-Switch Scanner*)
Satpam tidak perlu repot menekan tombol ubah mode secara manual. Komputer scanner gerbang otomatis beralih fungsi mengikuti jam server:
1. **Mode 1: Presensi Masuk (Check-in) [06:00 - 08:00 WIB]**:
   - Mengevaluasi jam scan: Hadir Tepat Waktu (0 poin, suara Beep 880Hz), Terlambat T-1 (-2 poin, suara Buzz 180Hz), Terlambat T-2 (-5 poin), dan Terlambat T-3 (-10 poin).
   - **Auto-Alpa Cutoff (08:00 WIB)**: Cron Job otomatis menandai siswa yang belum tap dan tidak punya surat izin menjadi **Alpa** (-15 poin) dan mengirim alert darurat WhatsApp ke orang tua.
2. **Mode 2: Siaga Digital Gate Pass / Izin Keluar [08:01 - 13:00 WIB]**:
   - Pintu gerbang utama terkunci selama KBM aktif. Scanner hanya menerima QR Tiket Digital Gate Pass.
   - Satpam mencocokkan identitas penjemput, lalu menekan *"✓ Izinkan Keluar & Buka Palang"*.
3. **Mode 3: Presensi Pulang (Check-out) [13:01 - 17:00 WIB]**:
   - Siswa men-tap kartu pelajar pada scanner gerbang saat jam pulang sekolah.
   - Suara audio bersuara melodi ramah **Chime 3 nada C-E-G** (*"Selamat Pulang"*).
   - Sistem mencatat `check_out_time` di database dan mengirim notifikasi WhatsApp ke orang tua bahwa ananda telah melangkah keluar gerbang sekolah.

---

### B. Keadilan Bagi Siswa yang Lupa Tap Pulang (*Checkout Justice Fallback*)
- **Masalah**: Siswa SD seringkali lari berebut keluar gerbang saat bel pulang berbunyi dan lupa men-tap kartu.
- **Solusi**: Pukul 17:00 WIB, sistem otomatis menjalankan rekonsiliasi sore. Sistem memeriksa apakah siswa hadir di KBM jam pelajaran terakhir. Jika hadir di kelas, status presensi siswa **TETAP HADIR** (tidak boleh di-Alpakan!), dengan catatan kepulangan `Check-out Default (Selesai KBM)`.

---

### C. Kelonggaran Pengajuan Izin / Sakit oleh Orang Tua
1. **Sakit 1 Hari (Tanpa Surat Dokter)**:
   - Cukup foto surat izin tertulis bertanda tangan orang tua di atas kertas biasa (tanpa perlu ke klinik dokter).
   - Dibatasi kuota maksimal 2 kali per semester per siswa agar tidak disalahgunakan.
2. **Sakit > 1 Hari (Wajib Surat Dokter)**:
   - Wajib melampirkan foto surat dokter / Puskesmas / klinik resmi.
3. **Izin Keperluan Keluarga Mendesak**:
   - Rentang tanggal izin terencana (1 s/d 3 hari).
4. **Dispensasi Tugas Sekolah / Lomba**:
   - Diajukan langsung oleh guru pembina (status Hadir 100% dan bebas penalti Auto-Alpa).
5. **Eskalasi Alpa $\ge$ 3 Hari**:
   - Jika siswa Alpa 3 hari berturut-turut tanpa kabar, sistem otomatis menerbitkan rekomendasi **Home Visit (Kunjungan Rumah)** dan surat panggilan orang tua pada dashboard Guru BK.

---

### D. Pencatatan Pulang Lebih Awal yang Tertib & Aman
- Siswa yang pulang lebih awal (misal: sakit di UKS atau ada urusan darurat keluarga) wajib menggunakan **Digital Gate Pass**.
- Saat satpam membuka palang gerbang, sistem merekam jam keluar (`early_leave_time`) dan mengubah status presensi harian menjadi `Hadir (Izin Pulang Cepat: [Jam] WIB)`.
- Orang tua seketika menerima notifikasi WhatsApp kepulangan cepat, menutup risiko penculikan atau anak kabur tanpa izin.

---

## 🎨 4. Konsep Desain: Dominan Putih Minimalis Modern Profesional

Setiap halaman modul dirancang dengan tampilan yang bersih, kontras tinggi, dan kaya informasi menggunakan standar **Bootstrap 5 Modern Effects + Tailwind CSS Utility**:

1. **Bilah Kartu Statistik KPI (Info Cards)**:
   - Kartu putih elegan berlatar `#FFFFFF`, border halus `border-slate-200`, dan bayangan lembut `shadow-sm`.
   - Memuat angka metrik besar, ikon SVG semantik dalam kontainer pastel, lencana tren persentase (`+3.2% vs minggu lalu`), dan grafik garis mini (*sparkline chart*).
2. **Grafik Analitik Interaktif (Chart.js / ApexCharts)**:
   - Grafik batang visualisasi tren kehadiran mingguan (Senin s/d Jumat).
   - Diagram donut perbandingan proporsi siswa bersih, siswa berprestasi merit, dan siswa dalam pembinaan demerit.
3. **Bilah Pencarian & Filter Multi-Kriteria (Multi-Filter Bar)**:
   - Kotak pencarian teks reaktif (*debounced search*) dengan respon instan saat mengetik nama atau NISN.
   - Dropdown bertingkat: filter rombel kelas, filter status kehadiran, filter tingkat pelanggaran, dan tombol *Reset Filter*.
4. **Paginasi Dinamis (Dynamic Pagination)**:
   - Tampilan rentang data (*"Menampilkan 1 - 10 dari 480 data"*).
   - Pemilih jumlah baris (10, 25, 50, 100) dan tombol navigasi halaman halus.

---

## 📁 5. Daftar Isi 13 Dokumen di Folder `plan/docs/`

| Berkas Dokumen | Pokok Pembahasan Dokumen |
| :--- | :--- |
| **`01_PRD.md`** | Dokumen kebutuhan produk: Presensi 3 mode, izin fleksibel, keadilan pulang, otomatisasi SP 1-3, dan 5 peran pengguna. |
| **`02_DESIGN_SYSTEM.md`** | Standar visual putih minimalis, kartu KPI Bootstrap 5, grafik Chart.js, kartu fisik CR-80, dan audio Web Audio API. |
| **`03_ARSITEKTUR.md`** | Struktur monorepo JavaScript Nuxt 3/Fastify, **DDL 21 tabel relasional PostgreSQL 16**, BullMQ, dan konfigurasi server. |
| **`04_DESIGN_PATTERN.md`** | Pola perangkat lunak JavaScript: 3-mode auto-switch scanner, checkout justice fallback, HMAC token, dan enkripsi AES-256. |
| **`05_DESIGN_UI.md`** | Spesifikasi wireframe 15 modul halaman dan 12 modal interaktif lengkap dengan penataan komponen UI putih modern. |
| **`06_PROJECT_MANAGEMENT.md`** | Peta jalan WBS 5 fase kerja JavaScript, manajemen risiko lapangan (R-01 s/d R-05), dan kriteria selesai (*DoD*). |
| **`07_ORCHESTRASI.md`** | Topologi orkestrasi tim multi-agent AI berbasis npm/Node.js, batas wilayah worker, dan 7 Gerbang Kualitas (*Quality Gates*). |
| **`README.md`** | Master prompt siap pakai untuk memobilisasi agen AI membangun sistem produksi JavaScript secara otomatis. |
| **`KNOWLEDGE_GRAPH_EXISTING.md`** | Pemetaan arsitektur mockup di `index.html` (Vue 3 client-side in-memory SPA). |
| **`KNOWLEDGE_GRAPH_TARGET.md`** | Pemetaan arsitektur target produksi JavaScript terhubung ke database cloud dan hardware gerbang. |
| **`PERBEDAAN_EXISTING_VS_TARGET_DETAIL.md`** | Tabel perbandingan mendalam baris-demi-baris (17 parameter teknis mockup vs sistem produksi JS). |
| **`PERBEDAAN_EXISTING_VS_TARGET_EXECUTIVE_BRIEF.md`** | Ringkasan bahasa awam untuk Kepala Sekolah, Dewan Guru, dan Komite SDN Kebonagung. |
| **`RAG_CORPUS.json`** | Metadata JSON 10 chunk materi cerdas acuan arsitektur EduTrack Pro untuk AI. |

---

## 💡 6. Kesimpulan

Folder `plan/` kini telah **100% diperbarui dan disempurnakan**:
- Menggunakan pondasi **Framework JavaScript (Nuxt 3 Vue 3 / Node.js Fastify + PostgreSQL 16 Drizzle ORM)** yang selaras dengan `index.html`.
- Menutup **seluruh celah operasional lapangan** (skema 3 mode jam gerbang, keadilan lupa tap pulang, izin sakit fleksibel, pencatatan pulang cepat, dan notifikasi WA kepulangan).
- Mengadopsi **tampilan dominan putih minimalis modern profesional** yang dilengkapi kartu statistik, grafik, paginasi dinamis, pencarian reaktif, dan penyaring data lengkap.