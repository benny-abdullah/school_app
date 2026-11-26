# Product Requirements Document (PRD): EduTrack Pro
**Aplikasi**: Sistem Monitoring Terpadu Sekolah (EduTrack Pro)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Versi**: 2.4 (SSOT Canonical - Production Blueprint)  
**Status**: Disetujui (Approved for Production Implementation)  
**Standar Regulasi**: ISO/IEC 27001:2022 & UU Pelindungan Data Pribadi (UU PDP No. 27/2022)

---

## 1. Ikhtisar Produk & Visi Bisnis (Executive Summary)

**EduTrack Pro** adalah sistem informasi dan pemantauan terpadu kesiswaan modern yang dirancang untuk menyatukan pencatatan presensi kehadiran (gerbang barcode & kelas KBM), pembinaan kedisiplinan dan tata tertib (poin demerit & otomatisasi SP ber-QR Code), apresiasi prestasi (poin merit & portofolio digital), kanal aduan rahasia (whistleblowing anti-perundungan/pungli terenkripsi AES-256), serta portal mandiri wali murid dengan integrasi notifikasi instan WhatsApp.

Sistem ini memecahkan 6 tantangan operasional fundamental di sekolah:
1. **Antrean Presensi & Keterlambatan Siswa**: Menggantikan presensi tanda tangan manual dengan pemindaian barcode kartu pelajar ultra-cepat ($\le 300\text{ ms}$ per siswa) yang terhubung langsung ke mesin kalkulasi penalti keterlambatan berjenjang dan alert darurat ke WhatsApp orang tua.
2. **Kecurangan Siswa Membolos KBM**: Mengatasi celah siswa yang hadir di gerbang pagi namun membolos di jam pelajaran siang melalui modul sinkronisasi Presensi KBM Jam Mapel.
3. **Pencatatan Pelanggaran yang Tidak Transparan**: Mengotomatiskan buku saku pelanggaran menjadi sistem poin akumulatif transparan dengan penerbitan Surat Peringatan (SP 1, SP 2, SP 3) berkop surat resmi dan QR Code verifikasi keabsahan.
4. **Keamanan Korban Perundungan (Anti-Bullying Whistleblower)**: Menyediakan kanal aduan tertutup dengan enkripsi end-to-end dan opsi anonim agar siswa/orang tua tidak takut melapor intimidasi atau pungutan liar.
5. **Keterlibatan Orang Tua Siswa secara Real-Time**: Memberikan portal mandiri bagi orang tua untuk memantau rekap absensi anak, mengajukan surat izin sakit mandiri berlampiran foto surat dokter, dan menerima notifikasi kehadiran tanpa jeda.
6. **Kepatuhan Tata Kelola Data Nasional (ISO 27001 & UU PDP)**: Menerapkan pemisahan hak akses ketat (*Least Privilege*), jejak audit kekal (*Immutable Audit Trail*), serta pencadangan data terenkripsi otomatis dengan aturan redundansi 3-2-1.

---

## 2. Persona Pengguna & Matriks Hak Akses (RBAC & Data Scoping)

Sistem menerapkan **Role-Based Access Control (RBAC)** dengan isolasi cakupan data (*Data Scoping*) di level server:

| Persona / Role | Fungsi Utama | Cakupan Data (*Data Scope*) | Kebutuhan Antarmuka |
| :--- | :--- | :--- | :--- |
| **👑 Super Admin / Kepala Sekolah** (`admin`) | Pengawas tertinggi sistem; kontrol konfigurasi, master GTK, manajemen pengguna, backup data 3-2-1, dan audit trail ISO 27001. | **Seluruh Sekolah** (Akses Global) | Dashboard Eksekutif, Manajemen GTK, Manajemen User, Log Audit, Snapshot Backup, Cetak Kartu Pelajar, Pusat Aduan. |
| **🛡️ Guru BK / Kesiswaan** (`bk`) | Penanganan kasus disiplin, bimbingan konseling, investigasi aduan whistleblower, verifikasi prestasi, dan pencetakan kartu pelajar fisik. | **Seluruh Siswa** (Fokus Kasus & Konseling) | Modul Tatib & SP, Bank Prestasi, Pusat Aduan (Ruang Investigasi Chat), Scanner Gerbang, Cetak Kartu Pelajar PVC. |
| **📋 Wali Kelas** (`wali_kelas`) | Presensi harian bulk rombel binaan, presensi KBM, persetujuan izin sakit dari orang tua, dan pembinaan siswa binaan. | **Terisolasi pada Rombel Binaan** (contoh: Kelas X-RPL-1 / Kelas 4-A) | Presensi Cepat H/S/I/A/T, Presensi KBM Mapel, Verifikasi Pengajuan Izin Ortu, Digital Gate Pass, Aduan Siswa Kelas. |
| **👨‍👩‍👦 Orang Tua / Wali Murid** (`orang_tua`) | Memantau rekap kehadiran anak, riwayat poin pelanggaran/prestasi anak, mengajukan izin sakit mandiri, dan membuat aduan. | **Terisolasi hanya untuk Ananda Asuh** (contoh: Ahmad Rifai) | Dashboard Ringkasan Anak, Pengajuan Izin/Sakit Mandiri, Pengaduan Saya (Chat Rahasia), Simulasi Notifikasi WhatsApp. |
| **🎓 Siswa** (`siswa`) | Memantau kehadiran mandiri, buku saku poin tata tertib, portofolio prestasi digital, dan tiket digital gate pass. | **Terisolasi hanya untuk Data Diri Siswa Sendiri** | Data Diri Siswa, Rekap Presensi, Buku Saku Pelanggaran, Portofolio Prestasi, Tiket Gate Pass (Menu aduan & cetak kartu disembunyikan). |
| **👮 Petugas Keamanan / Satpam** (`satpam`) | Mengoperasikan scanner barcode kartu pelajar di pos gerbang dan memverifikasi tiket keluar izin siswa. | **Pos Gerbang Sekolah** | Layanan Pemindai Barcode Gerbang (Live Stream/Wedge) & Tab Verifikasi Digital Gate Pass. |

---

## 3. Fitur Utama & Kebutuhan Fungsional (Functional Requirements)

### Modul 1: Presensi Gerbang Barcode & Rule Engine Keterlambatan Multi-Tier
- **Kecepatan Tinggi**: Waktu pemindaian $\le 300\text{ ms}$ menggunakan *USB Barcode Gun Scanner* (Keyboard Wedge) maupun kamera WebRTC/PWA.
- **Rule Engine Keterlambatan Berjenjang**:
  - `06:00 - 07:00 WIB`: **Hadir Tepat Waktu** (0 Poin, masuk kelas normal).
  - `07:01 - 07:15 WIB`: **Terlambat Ringan (T-1)** (-2 Poin Tatib, dispensasi lisan, notif WA info).
  - `07:16 - 07:30 WIB`: **Terlambat Sedang (T-2)** (-5 Poin Tatib, pembinaan karakter di pos piket/literasi).
  - `> 07:30 WIB`: **Terlambat Berat (T-3)** (-10 Poin Tatib, penanganan Guru BK, panggilan orang tua).
- **Auto-Alpa Cron Scheduler (Pukul 08:00 WIB)**: Cron Job otomatis memeriksa siswa yang belum tap dan tidak memiliki izin sah, otomatis menetapkan status **Alpa** (-15 Poin Tatib) dan menembakkan alert darurat WhatsApp ke orang tua.
- **Anti-Double Tap & Cooldown**: Kartu yang dipindai ulang dalam rentang $< 5\text{ menit}$ akan ditolak dengan notifikasi *"Siswa sudah tercatat hadir pada [jam]"*.

### Modul 2: Presensi KBM Jam Mata Pelajaran (Deteksi Siswa Membolos)
- Guru mata pelajaran melakukan verifikasi cepat kehadiran di setiap jam KBM.
- **Fitur Deteksi Silang Otomatis**: Jika siswa tercatat hadir di gerbang pagi namun ditandai tidak ada di jam pelajaran, guru dapat mengklik tombol **"🚨 Lapor Membolos ke BK"**.
- Sistem otomatis mencatat pelanggaran *"Membolos Jam Pelajaran"* (+10 Poin Tatib), mengubah status presensi, dan memicu notifikasi instan ke Guru BK dan Wali Kelas.

### Modul 3: Pengajuan Izin / Sakit Mandiri oleh Orang Tua
- Orang tua dapat mengajukan izin anak dari rumah melalui portal mandiri sebelum pukul 08:00 WIB.
- Formulir mewajibkan upload foto bukti (surat keterangan dokter klinik/rumah sakit untuk sakit, atau surat izin bermaterai/bertandatangan).
- Pengajuan masuk ke antrean Wali Kelas. Begitu disetujui (*1-click approve*), status presensi anak otomatis terkunci menjadi **Sakit / Izin**, membebaskan anak dari penalti Auto-Alpa.

### Modul 4: Digital Gate Pass (Surat Izin Keluar Gerbang Satpam)
- Diterbitkan oleh Wali Kelas atau Guru BK jika siswa harus pulang cepat (sakit demam di UKS atau keperluan keluarga mendesak).
- Memuat kode unik (misal: `GP-20261123-001`), alasan keluar, nama penjemput, dan verifikasi QR.
- Satpam di pos gerbang memindai kode tiket, mencocokkan identitas penjemput, dan menekan tombol **"✓ Izinkan Keluar & Buka Palang"**. Jam keluar tercatat resmi di database.

### Modul 5: Buku Tata Tertib Digital, Poin Pelanggaran & Penerbitan Surat Peringatan (SP)
- **Katalog Pelanggaran Berjenjang**:
  - Ringan (5 - 15 Poin): Terlambat hadir, atribut seragam tidak lengkap, dsb.
  - Sedang (20 - 45 Poin): Membolos pelajaran, membawa rokok/vape, bullying verbal.
  - Berat (50 - 100 Poin): Berkelahi/tawuran, asusila, narkoba/miras.
- **Otomatisasi Penerbitan SP**:
  - Akumulasi Poin $\ge 50$: Penerbitan **SP-1**.
  - Akumulasi Poin $\ge 75$: Penerbitan **SP-2** & Panggilan Orang Tua.
  - Akumulasi Poin $\ge 100$: Penerbitan **SP-3** / Sidang Dewan Guru.
- Dokumen SP dilengkapi kop resmi SDN Kebonagung Kota Pasuruan dan **QR Code Verifikasi Keabsahan**.

### Modul 6: Bank Prestasi & Merit Points
- Pencatatan prestasi berjenjang: Sekolah (+25), Kota/Kabupaten (+50), Provinsi (+75), Nasional (+100), Internasional (+150).
- Verifikasi berkas sertifikat/piagam oleh Guru BK.
- Poin merit menjadi penyeimbang kalkulasi skor karakter siswa pada rapor kedisiplinan semesteran.

### Modul 7: Pusat Aduan & Whistleblower Terenkripsi (Anti-Bullying)
- Opsi pelaporan **Anonim (Whistleblower)** atau **Terbuka**.
- Kategori laporan: Perundungan / Bullying, Pungutan Liar / Keuangan, Fasilitas / Sarpras, Kekerasan Verbal / Fisik.
- **Enkripsi AES-256-GCM** pada isi laporan dan riwayat pesan chat investigasi dua arah antara pelapor dan Tim Penanganan Satgas Sekolah.

### Modul 8: Generator & Cetak Kartu Pelajar PVC (ISO CR-80)
- Dimensi fisik perbankan internasional: $85.60\text{ mm} \times 53.98\text{ mm}$ (Tebal $0.76\text{ mm}$).
- **Barcode 1D (Code 128)**: Kompatibel dengan semua jenis laser scanner optik.
- **QR Code 2D (HMAC-SHA256)**: Berisi string terverifikasi kriptografis: `EDUTRACK:<NISN>:<CARD_UUID>:<SIGNATURE_HASH>`.
- Kebijakan **One-Card One-Active**: Jika kartu hilang, kartu lama di-*revoke* seketika dan kartu baru diterbitkan dengan barcode baru.

### Modul 9: Master Data Guru & Tenaga Kependidikan (GTK) — Eksklusif Super Admin
- Full CRUD biodata guru dan staf: NIP, NUPTK, Nama Lengkap & Gelar, Jenis Kelamin, Status Kepegawaian (PNS / PPPK / Honorer Daerah / Pegawai Kontrak), Jabatan Utama, Rombel/Mapel Binaan, Nomor WhatsApp, Email Kedinasan, Status Keaktifan.
- Seluruh mutasi data GTK tercatat dalam Log Audit ISO 27001 (`CREATE_TEACHER`, `UPDATE_TEACHER`, `DELETE_TEACHER`).

### Modul 10: Master Data Siswa, Orang Tua & Manajemen Pengguna
- Sinkronisasi relasional antara data siswa dan akun wali murid.
- Tombol **"📲 Kirim Akses"** untuk mengirim tautan aktivasi portal monitoring ke nomor WhatsApp orang tua.
- Dynamic Scope Assignment pada pendaftaran pengguna baru (Admin = Seluruh Sekolah, Wali Kelas = Dropdown Pilihan Rombel, Ortu/Siswa = Pilihan Siswa).

### Modul 11: Kepatuhan Keamanan ISO/IEC 27001 & UU PDP
- **Log Audit Kekal (Immutable Audit Trail - A.12.4)**: Mencatat timestamp, user email, role, aksi mutasi, IP address, dan user agent.
- **Strategi Backup Redundansi 3-2-1 (A.12.3)**:
  - 3 salinan data (1 produksi live, 2 cadangan).
  - 2 media penyimpanan berbeda (SSD server lokal + cloud object storage).
  - 1 salinan offsite terenkripsi di AWS S3 Vault dengan verifikasi SHA-256 checksum harian.

---

## 4. Kebutuhan Non-Fungsional (Non-Functional Requirements)

1. **Performa & Throughput**:
   - Respon endpoint presensi gerbang $\le 200\text{ ms}$ pada beban puncak (500 siswa memindai dalam 20 menit).
   - Render antarmuka meja scanner dan pembaruan tabel $\le 100\text{ ms}$.
2. **Ketersediaan (*Availability*)**:
   - Target Service Level Agreement (SLA) $\ge 99.8\%$ jam operasional sekolah (06:00 - 18:00 WIB).
3. **Keamanan Informasi (*Information Security*)**:
   - Enkripsi data bergerak (*data in transit*) wajib TLS 1.3 / HTTPS.
   - Enkripsi data diam (*data at rest*) menggunakan cipher AES-256 untuk kolom-kolom sensitif.
   - Hashing password menggunakan algoritma **Argon2id**.
4. **Kompatibilitas Perangkat**:
   - Responsif pada layar monitor desktop pos satpam ($1920\times1080$), tablet kiosk ($1024\times768$), dan smartphone orang tua/guru ($360\text{px}$ viewport width).
   - Cetak dokumen SP dan Kartu Pelajar kompatibel dengan printer laser A4 standar dan printer ID Card thermal PVC (Zebra / Fargo / Evolis).