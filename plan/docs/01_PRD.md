# Product Requirements Document (PRD): EduTrack Pro
**Aplikasi**: Sistem Monitoring Terpadu Sekolah (EduTrack Pro)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Versi**: 2.5 (SSOT Canonical - Production Blueprint)  
**Teknologi Acuan**: Full-stack JavaScript / TypeScript (Nuxt 3 Vue 3 / Node.js Fastify + PostgreSQL 16 + Redis)  
**Estetika UI**: Dominan Putih Minimalis Modern Profesional (Bootstrap 5 Effects + Tailwind Utility)  
**Standar Regulasi**: ISO/IEC 27001:2022 & UU Pelindungan Data Pribadi (UU PDP No. 27/2022)

---

## 1. Ikhtisar Produk & Visi Bisnis (Executive Summary)

**EduTrack Pro** adalah sistem informasi dan pemantauan terpadu kesiswaan modern yang dibangun di atas ekosistem **Full-stack JavaScript / TypeScript** (Nuxt 3 / Node.js Fastify) untuk menghadirkan performa reaktif berlatensi rendah, kemudahan migrasi komponen Vue 3 dari [index.html](file:///c:/scholl_app/index.html), dan keandalan operasional sekolah nyata.

Sistem ini dirancang untuk menyatukan:
1. **Presensi Otomatis 3 Jendela Waktu**:
   - **Mode 1 Pagi (06:00 - 08:00 WIB)**: Presensi Masuk (Check-in) dengan aturan toleransi keterlambatan berjenjang (Hadir, T-1, T-2, T-3) dan Auto-Alpa pukul 08:00 WIB.
   - **Mode 2 Siang (08:01 - 13:00 WIB)**: Siaga Digital Gate Pass untuk verifikasi izin keluar/pulang cepat satpam pos gerbang.
   - **Mode 3 Sore (13:01 - 17:00 WIB)**: Presensi Pulang (Check-out) dengan nada Chime ramah dan notifikasi WhatsApp kepulangan ke orang tua.
2. **Keadilan Siswa Lupa Tap Pulang**: Siswa yang telah hadir di pagi hari dan mengikuti KBM sampai jam pelajaran terakhir **TIDAK DIHUKUM ALPA** saat pulang, melainkan berstatus tetap `Hadir` dengan catatan `Check-out Default (Selesai KBM)`.
3. **Fleksibilitas Pengajuan Izin / Sakit**:
   - *Sakit 1 Hari*: Cukup surat izin tertulis bertanda tangan orang tua (tanpa surat dokter klinik, kuota maks 2x/semester).
   - *Sakit > 1 Hari*: Wajib melampirkan foto surat dokter / Puskesmas / RS.
   - *Izin Keperluan Keluarga*: Rentang tanggal izin terencana.
   - *Dispensasi Tugas / Lomba*: Hadir resmi yang diajukan oleh guru pembina.
   - *Eskalasi Alpa $\ge 3$ Hari*: Pemicu otomatis surat pemanggilan fisik orang tua & rekomendasi Home Visit oleh Guru BK.
4. **Presensi KBM Jam Mapel**: Sinkronisasi presensi gerbang dengan presensi ruang kelas per jam pelajaran untuk mendeteksi siswa membolos KBM (+10 Poin Tatib).
5. **Buku Tatib Digital & Otomatisasi SP**: Katalog pelanggaran, demerit points akumulatif, dan penerbitan Surat Peringatan (SP 1, SP 2, SP 3) ber-QR Code verifikasi.
6. **Bank Prestasi & Merit System**: Pencatatan penghargaan siswa (Sekolah s/d Internasional) sebagai penyeimbang skor kedisiplinan.
7. **Kanal Aduan & Whistleblower Terenkripsi AES-256**: Pelaporan rahasia anti-bullying dengan opsi anonim dan ruang obrolan investigasi dua arah.
8. **Master GTK (Guru & Tenaga Kependidikan)**: CRUD lengkap biodata pendidik dan tenaga kependidikan eksklusif hak akses Super Admin.
9. **Desain Putih Minimalis Modern Profesional**: Antarmuka serba putih elegan dilengkapi komponen interaktif wajib: *Pagination*, *Instant Searching*, *Multi-Criteria Filter*, *KPI Info Cards*, *Semantic Badges*, *Info Icons*, dan *Interactive Data Charts*.

---

## 2. Persona Pengguna & Matriks Hak Akses (RBAC & Data Scoping)

Sistem menerapkan **Role-Based Access Control (RBAC)** dengan isolasi data sisi server (*Server-Side Scoping*) untuk mencegah celah IDOR:

| Persona / Role | Fungsi Utama | Cakupan Data (*Data Scope*) | Kebutuhan Antarmuka |
| :--- | :--- | :--- | :--- |
| **👑 Super Admin / Kepala Sekolah** (`admin`) | Pengawas tertinggi sistem; kontrol konfigurasi, master GTK, manajemen pengguna, backup data 3-2-1, dan audit trail ISO 27001. | **Seluruh Sekolah** (Akses Global) | Dashboard Eksekutif, Grafik Tren KPI, Manajemen GTK, Manajemen User, Log Audit, Snapshot Backup, Cetak Kartu Pelajar, Pusat Aduan. |
| **🛡️ Guru BK / Kesiswaan** (`bk`) | Penanganan kasus disiplin, bimbingan konseling, investigasi aduan whistleblower, verifikasi prestasi, eskalasi alpa 3 hari, dan pencetakan kartu fisik. | **Seluruh Siswa** (Fokus Kasus & Konseling) | Modul Tatib & SP, Bank Prestasi, Pusat Aduan (Ruang Investigasi Chat), Scanner Gerbang, Cetak Kartu Pelajar PVC, Home Visit. |
| **📋 Wali Kelas** (`wali_kelas`) | Presensi harian bulk rombel binaan, presensi KBM, persetujuan izin sakit/keluarga orang tua, dan penerbitan Digital Gate Pass. | **Terisolasi pada Rombel Binaan** (contoh: Kelas X-RPL-1 / Kelas 4-A) | Presensi Cepat H/S/I/A/T, Presensi KBM Mapel, Verifikasi Pengajuan Izin Ortu, Digital Gate Pass, Aduan Siswa Kelas. |
| **👨‍👩‍👦 Orang Tua / Wali Murid** (`orang_tua`) | Memantau kehadiran masuk/pulang anak, riwayat poin tatib/prestasi, mengajukan izin sakit mandiri, dan menerima notifikasi WhatsApp. | **Terisolasi hanya untuk Ananda Asuh** (contoh: Ahmad Rifai) | Dashboard Ringkasan Anak, Pengajuan Izin/Sakit Mandiri, Pengaduan Saya (Chat Rahasia), Riwayat Notifikasi WhatsApp. |
| **🎓 Siswa** (`siswa`) | Memantau kehadiran mandiri, buku saku poin tata tertib, portofolio prestasi digital, dan tiket digital gate pass. | **Terisolasi hanya untuk Data Diri Siswa Sendiri** | Data Diri Siswa, Rekap Presensi Masuk/Pulang, Buku Saku Pelanggaran, Portofolio Prestasi, Tiket Gate Pass. |
| **👮 Petugas Keamanan / Satpam** (`satpam`) | Mengoperasikan mesin scanner 3 mode di pos gerbang dan memverifikasi tiket keluar izin siswa. | **Pos Gerbang Sekolah** | Layanan Pemindai Barcode Gerbang 3 Mode (Masuk / Gate Pass / Pulang) dengan umpan balik audio. |

---

## 3. Fitur Utama & Kebutuhan Fungsional (Functional Requirements)

### Modul 1: Presensi Gerbang 3 Jendela Waktu Otomatis (*3-Mode Auto-Switch Scanner*)
- **Mode 1: Presensi Masuk (Check-in) [06:00 - 08:00 WIB]**:
  - `06:00 - 07:00 WIB`: **Hadir Tepat Waktu** (0 Poin, masuk kelas normal, audio Beep 880Hz).
  - `07:01 - 07:15 WIB`: **Terlambat Ringan (T-1)** (-2 Poin Tatib, dispensasi masuk kelas, audio Buzz 180Hz, notif WA info).
  - `07:16 - 07:30 WIB`: **Terlambat Sedang (T-2)** (-5 Poin Tatib, pembinaan di pos piket/literasi).
  - `> 07:30 WIB`: **Terlambat Berat (T-3)** (-10 Poin Tatib, penanganan Guru BK, panggilan orang tua).
  - `08:00 WIB Cutoff`: Cron Job otomatis menandai siswa yang belum tap sebagai **Alpa** (-15 Poin Tatib) dan menembakkan alert darurat WhatsApp ke orang tua.
- **Mode 2: Siaga Digital Gate Pass / Izin Keluar [08:01 - 13:00 WIB]**:
  - Pintu gerbang utama terkunci. Scanner hanya menerima pemindaian tiket QR Digital Gate Pass.
  - Satpam memverifikasi kesesuaian penjemput. Saat tombol *"✓ Izinkan Keluar & Buka Palang"* ditekan:
    - Jam keluar siswa (`early_leave_time`) tercatat resmi di database.
    - Status presensi hari itu bermutasi menjadi `Hadir (Izin Pulang Cepat: [Jam] WIB)`.
    - Sistem seketika menembakkan notifikasi WhatsApp keberangkatan ke orang tua siswa.
- **Mode 3: Presensi Pulang (Check-out) [13:01 - 17:00 WIB]**:
  - Siswa men-tap kartu pelajar pada scanner pos gerbang saat jam pulang sekolah.
  - Suara audio bersuara ramah Chime 3 nada (*"Selamat Pulang"*).
  - Kolom `check_out_time` terisi otomatis di basis data.
  - Sistem mengirim notifikasi WhatsApp kepulangan ke ponsel orang tua:
    > *"Bapak/Ibu, ananda [Nama] telah men-tap kepulangan di gerbang sekolah pada pukul [Jam] WIB. Mohon dipantau kepulangan ananda ke rumah."*
- **Kaidah Keadilan Lupa Tap Pulang**:
  - Jika siswa hadir di gerbang pagi dan hadir di KBM jam pelajaran terakhir, namun lupa men-tap kartu saat berebut keluar gerbang sore, siswa **TIDAK DIHUKUM ALPA**. Status tetap `Hadir` dengan catatan `Check-out Default (Selesai KBM)`.

### Modul 2: Skema Pengajuan Izin / Sakit Fleksibel oleh Orang Tua
1. **Sakit 1 Hari (Tanpa Surat Dokter)**:
   - Formulir digital mengizinkan orang tua mengunggah foto surat izin tertulis bertanda tangan sendiri.
   - Dibatasi kuota maksimal 2 kali per semester per siswa agar tidak disalahgunakan.
2. **Sakit > 1 Hari (Wajib Surat Dokter)**:
   - Mewajibkan unggah foto/PDF surat keterangan istirahat resmi dari klinik, Puskesmas, atau dokter.
3. **Izin Keperluan Keluarga Mendesak**:
   - Memilih rentang tanggal izin (1 s/d 3 hari) dan mengisi alasan kepentingan keluarga.
4. **Dispensasi Tugas Sekolah / Lomba Eksternal**:
   - Diajukan langsung oleh Guru Pendamping / Wali Kelas. Status: **Dispensasi** (Hadir 100% dan bebas Auto-Alpa).
5. **Eskalasi Alpa Berturut-turut $\ge$ 3 Hari**:
   - Jika siswa Alpa 3 hari berturut-turut tanpa kabar, sistem otomatis menerbitkan rekomendasi **Surat Tugas Home Visit (Kunjungan Rumah)** dan panggilan fisik orang tua ke sekolah pada dashboard Guru BK.

### Modul 3: Presensi KBM Jam Mapel (Deteksi Siswa Membolos)
- Guru mata pelajaran melakukan verifikasi kehadiran siswa di setiap pergantian jam KBM.
- Fitur pembanding otomatis: Jika siswa berstatus hadir di gerbang pagi namun tidak ada di kelas, guru mengklik tombol merah **"🚨 Lapor Membolos ke BK"**.
- Sistem otomatis mencatat pelanggaran *"Membolos Jam Pelajaran"* (+10 Poin Tatib) dan menembakkan alert darurat WhatsApp ke orang tua saat jam pelajaran tersebut berlangsung.

### Modul 4: Buku Tatib Digital, Poin Pelanggaran & Penerbitan SP
- Katalog pelanggaran berjenjang: Ringan (5-15 Poin), Sedang (20-45 Poin), Berat (50-100 Poin).
- Otomatisasi penerbitan Surat Peringatan: SP-1 ($\ge 50$ Poin), SP-2 ($\ge 75$ Poin), SP-3 ($\ge 100$ Poin).
- Dokumen SP berformat resmi berkop kedinasan SDN Kebonagung Kota Pasuruan lengkap dengan **QR Code Verifikasi Keabsahan**.

### Modul 5: Bank Prestasi & Merit Points
- Pencatatan prestasi berjenjang: Sekolah (+25), Kota/Kabupaten (+50), Provinsi (+75), Nasional (+100), Internasional (+150).
- Poin merit menjadi penyeimbang skor kedisiplinan siswa pada rapor karakter akhir semester.

### Modul 6: Pusat Aduan & Whistleblower Terenkripsi (Anti-Bullying)
- Opsi pelaporan **Anonim (Whistleblower)** atau **Terbuka**.
- Enkripsi tingkat kolom **AES-256-GCM** pada isi laporan dan riwayat pesan chat investigasi dua arah antara pelapor dan Tim Satgas Anti-Kekerasan Sekolah.

### Modul 7: Generator & Cetak Kartu Pelajar PVC (ISO CR-80)
- Dimensi fisik perbankan internasional: $85.60\text{ mm} \times 53.98\text{ mm}$ (Tebal $0.76\text{ mm}$).
- Simbologi ganda: Barcode 1D (Code 128) dan QR Code 2D bertanda tangan digital **HMAC-SHA256**.
- Kebijakan **One-Card One-Active**: Tombol *Revoke & Regenerate* seketika menonaktifkan kartu lama yang hilang.

### Modul 8: Master Data Guru & Tenaga Kependidikan (GTK) — Eksklusif Admin
- CRUD lengkap biodata GTK: NIP, NUPTK, Nama Lengkap & Gelar, Gender, Status Kepegawaian (PNS / PPPK / Honorer Daerah / Kontrak), Jabatan Utama, Rombel/Mapel Binaan, Kontak WhatsApp, dan Status Keaktifan.
- Seluruh mutasi GTK tercatat dalam Log Audit ISO 27001.

### Modul 9: Komponen Tampilan Wajib (Putih Minimalis Modern Profesional)
Setiap tabel dan tampilan fitur wajib dilengkapi:
1. **Dynamic Pagination**: Navigasi halaman cepat dengan indikator jumlah baris dan pemilih *per-page* (10, 25, 50).
2. **Debounced Instant Search**: Kolom cari teks reaktif yang memfilter data secara instan saat mengetik.
3. **Multi-Criteria Dynamic Filters**: Dropdown filter kombinasi (Rombel, Status Kehadiran, Tingkat Kasus, dll.) dengan tombol *Reset Filter*.
4. **Data Visualization Charts**: Grafik batang kehadiran mingguan/bulanan (Chart.js / ApexCharts), donut chart disiplin vs prestasi, dan sparkline mini.
5. **Info Cards & KPI Widgets**: Kartu putih dengan bayangan halus (`shadow-sm`), border tipis, angka statistik besar, dan lencana tren persentase.
6. **Info Icons & Semantic Badges**: Ikon vektor SVG (Bootstrap Icons / Octicons) dan lencana status berpewarna semantik tegas (Emerald, Blue, Amber, Rose, Sky).

---

## 4. Kebutuhan Non-Fungsional (Non-Functional Requirements)

1. **Throughput Scanner Gerbang**: Waktu respon endpoint scan $\le 100\text{ ms}$ pada Node.js Fastify/Nitro Engine untuk menangani 500 siswa dalam 20 menit.
2. **Ketersediaan (*Availability*)**: Target SLA $\ge 99.8\%$ jam operasional sekolah (06:00 - 18:00 WIB).
3. **Keamanan Informasi (*Information Security*)**:
   - Enkripsi transit wajib HTTPS / TLS 1.3.
   - Enkripsi database at-rest AES-256 untuk data sensitif.
   - Hashing password menggunakan **Argon2id**.
4. **Infrastruktur Cadangan (*Backup*)**:
   - Redundansi 3-2-1 otomatis dengan replikasi offsite terenkripsi ke AWS S3 Vault Singapore.