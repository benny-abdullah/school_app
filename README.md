# school_app - EduTrack Pro

Aplikasi Mockup Interaktif Sistem Monitoring Terpadu Sekolah (Kehadiran, Pelanggaran, Prestasi, Aduan/Whistleblower, Portal Orang Tua) berbasis standar **ISO/IEC 27001** dengan desain **Clean White Minimalist**.

---

## 🚀 Cara Menjalankan & Menguji Aplikasi

Aplikasi dibuat mandiri (*standalone single-page application*) sehingga dapat dijalankan dengan 2 cara:

### Cara 1: Menjalankan Server Lokal (PHP)
Buka terminal / PowerShell di direktori `c:\scholl_app` lalu jalankan:
```bash
php -S 127.0.0.1:8000
```
Buka browser Anda dan kunjungi:
👉 **http://127.0.0.1:8000**

### Cara 2: Membuka Langsung File di Browser
Cukup klik ganda (*double click*) file `c:\scholl_app\index.html` atau buka melalui browser Google Chrome / Edge:
```
file:///c:/scholl_app/index.html
```

---

## 👥 5 Peran (Roles) & Akses Halaman

Pada halaman login dan header aplikasi, tersedia **Quick Role Switcher** untuk menguji sistem dari berbagai sudut pandang:

| Role | Akun Demo | Hak Akses & Fitur yang Dapat Diuji |
| :--- | :--- | :--- |
| **👑 Super Admin / Kepala Sekolah** | `admin@edutrack.sch.id` | Akses penuh ke seluruh modul, Dashboard Eksekutif, Manajemen Presensi & Pelanggaran, Verifikasi Prestasi, Pusat Aduan, serta **Log Audit ISO/IEC 27001 (A.12.4)**. |
| **🛡️ Guru BK / Kesiswaan** | `bk@edutrack.sch.id` | Modul Pelanggaran (catat poin tatib, otomatisasi SP), Penerbitan Surat Peringatan ber-QR code, **Kanal Aduan & Whistleblower Terenkripsi** (chat investigasi rahasia), dan verifikasi poin prestasi. |
| **📋 Wali Kelas (X-RPL-1)** | `walikelas@edutrack.sch.id` | Modul Presensi Harian Cepat (*Bulk Attendance Mode*), mengubah status siswa (H/S/I/A/T) dengan trigger notifikasi instan ke orang tua, memantau siswa kelas binaan. |
| **👨‍👩‍👦 Orang Tua Siswa** | `ortu@edutrack.sch.id` | Dashboard khusus ananda (Ahmad Rifai), rekap kehadiran persentase bulanan, riwayat poin kedisiplinan, prestasi anak, formulir pengaduan ke sekolah, dan **Simulasi Notifikasi WhatsApp**. |
| **🎓 Siswa** | `siswa@edutrack.sch.id` | Dashboard mandiri siswa, riwayat kehadiran, buku saku poin kedisiplinan, portofolio prestasi digital, dan **Kanal Pengaduan Rahasia (Anti-Bullying Whistleblowing)**. |

---

## 🧪 Skenario Pengujian Interaktif

1. **Uji Coba Pencarian & Filter Cepat (Searching & Filtering)**:
   - Masuk ke menu **Kehadiran Siswa**, ketik nama "Ahmad" di kotak pencarian. Data langsung terfilter secara *real-time*.
   - Coba dropdown **Filter Kelas** atau **Filter Status** untuk memfilter data.
   - Klik tombol **Reset Filter** untuk mengembalikan data awal.
2. **Uji Coba Paginasi Dinamis (Pagination)**:
   - Gunakan tombol *Sebelumnya* / *Berikutnya* di bagian bawah tabel untuk berpindah halaman secara halus tanpa reload.
3. **Uji Coba Presensi Cepat (Bulk Mode)**:
   - Sebagai Wali Kelas, klik tombol **"✓ Set Semua Hadir"** atau klik tombol cepat `H`, `S`, `I`, `A`, `T` pada baris siswa.
   - Jika diset `A` (Alpa), sistem akan memicu simulasi peringatan WhatsApp ke orang tua.
4. **Uji Coba Simulator Barcode Scanner Gerbang (0.3s/Siswa)**:
   - Buka menu **Scanner Barcode Gerbang** (Role Admin / Wali Kelas / BK).
   - Pilih skenario waktu simulasi:
     - `06:45 WIB`: Hadir Tepat Waktu (0 poin tatib).
     - `07:10 WIB`: Terlambat Ringan T-1 (-2 poin tatib).
     - `07:25 WIB`: Terlambat Sedang T-2 (-5 poin tatib & pembinaan gerbang).
     - `07:45 WIB`: Terlambat Berat T-3 (-10 poin tatib & panggilan BK).
   - Klik tombol **"Tap Scan"** pada siswa atau ketik NISN `0081234567`. Monitor gerbang langsung bereaksi menampilkan foto siswa, status kehadiran, kalkulasi poin, dan notifikasi WhatsApp ke orang tua.
5. **Uji Coba Generator & Cetak Kartu Pelajar PVC**:
   - Buka menu **Cetak Kartu Pelajar**.
   - Pilih nama siswa untuk melihat preview bolak-balik (*front & back view*) kartu fisik standar ISO CR-80 (85.6 x 54 mm).
   - Kartu memuat Barcode 1D (Code 128) dan QR Code 2D bertanda tangan digital (HMAC-SHA256).
   - Klik tombol **"🖨️ Cetak Kartu Siswa Ini"** untuk simulasi print atau tombol **"Revoke"** jika kartu hilang.
6. **Uji Coba Pencatatan Pelanggaran & Cetak SP**:
   - Masuk ke menu **Kedisiplinan & Tatib**, klik **"+ Catat Pelanggaran Siswa"**.
   - Pilih siswa dan jenis pelanggaran (misal: *Merokok di area kantin*). Poin (-50) dan tingkat keparahan otomatis terisi.
   - Klik simpan, lalu klik tombol **"📄 Lihat SP"** pada tabel untuk melihat pratinjau dokumen resmi Surat Peringatan (SP) ber-QR Code verifikasi.
7. **Uji Coba Kanal Aduan & Whistleblowing (Anti-Bullying)**:
   - Masuk ke menu **Pusat Aduan / Whistle**, klik **"+ Tulis Aduan Baru"**.
   - Centang opsi *"Kirim sebagai Anonim (Whistleblower)"* untuk menguji fitur perlindungan identitas siswa/orang tua.
   - Klik tombol **"💬 Buka Obrolan Investigasi"** untuk menguji komunikasi dua arah terenkripsi antara pelapor dan pihak sekolah.
8. **Uji Coba Notifikasi WhatsApp**:
   - Klik tombol **"📱 Tes Notif WA"** di header atas untuk melihat jendela simulasi pesan WhatsApp resmi yang diterima di ponsel orang tua secara *real-time*.
9. **Uji Coba Kepatuhan ISO 27001 (Admin)**:
   - Masuk ke menu **Log Audit ISO 27001** (Role Admin) untuk memeriksa pencatatan audit trail *immutable* (*Who, What, When, IP Address, Compliance Status*).
10. **Uji Coba Isolasi Data Antar Peran (Data Scoping)**:
    - Beralih ke **Role Orang Tua / Siswa**: Periksa menu Kehadiran, Pelanggaran, dan Prestasi — **hanya menampilkan data ananda Ahmad Rifai**.
    - Beralih ke **Role Wali Kelas**: Data terfilter secara otomatis **hanya untuk siswa kelas binaan (X-RPL-1)**.
    - Beralih ke **Role Guru BK / Admin**: Dapat melihat **seluruh siswa di seluruh kelas sekolah**.
11. **Uji Coba Manajemen Pengguna (Admin)**:
    - Buka menu **Manajemen Pengguna** (Role Admin) untuk melihat daftar akun, hak akses role, dan batasan cakupan data (*Data Scope*).
    - Coba tombol **"+ Tambah Pengguna Baru"** — kolom **Cakupan Data (Scope)** kini berupa **Dropdown List Dinamis** yang otomatis terisolasi sesuai pilihan peran pengguna (Admin = Seluruh Sekolah, Wali Kelas = Pilihan Kelas, Ortu/Siswa = Pilihan Siswa).
    - Coba tombol toggle **Aktif/Nonaktif**, dan fitur **Reset Sandi**.
12. **Uji Coba Backup Data Terenkripsi ISO 27001 (Admin)**:
    - Buka menu **Backup Data ISO 27001** (Role Admin).
    - Periksa ringkasan strategi redundansi **Aturan 3-2-1** (Lokal + Offsite AWS S3 Vault, enkripsi AES-256-GCM, SHA-256 Checksum).
    - Klik tombol **"⚡ Buat Cadangan Baru Sekarang"** untuk mensimulasikan pencadangan instan, atau tombol **"📥 Unduh (.enc)"**.
13. **Uji Coba Pengajuan Izin / Sakit Mandiri oleh Orang Tua**:
    - Beralih ke **Role Orang Tua Siswa** (atau Admin/Wali Kelas), klik tombol **"📝 Ajukan Izin / Sakit"** di Dashboard atau menu Kehadiran.
    - Isi formulir permohonan izin (Sakit dengan surat dokter terlampir atau urusan keluarga mendesak) lalu klik kirim.
    - Beralih ke **Role Wali Kelas**: Banner peringatan otomatis muncul memberitahukan adanya pengajuan izin baru. Klik **"✓ Setujui Izin"** untuk mengupdate presensi menjadi Sakit/Izin dan membebaskan dari penalti Auto-Alpa.
14. **Uji Coba Digital Gate Pass (Izin Keluar Gerbang Satpam)**:
    - Buka menu **Scanner Barcode Gerbang**, klik tab **"🎫 Verifikasi Tiket Izin Keluar (Digital Gate Pass)"** atau klik tombol **"+ Terbitkan Gate Pass"**.
    - Terbitkan izin keluar siswa (misal: sakit demam di UKS dan dijemput orang tua).
    - Satpam memeriksa kecocokan identitas penjemput dan mengklik tombol **"✓ Izinkan Keluar & Buka Palang"**. Waktu keluar tercatat otomatis dengan token verifikasi QR.
15. **Uji Coba Presensi KBM Per Jam Mapel (Deteksi Siswa Membolos)**:
    - Buka menu **Kehadiran Siswa**, lalu klik tab **"📚 Presensi KBM Jam Mapel (Guru Kelas)"**.
    - Pilih mata pelajaran (misal: *Pemrograman Web & Mobile* atau *Matematika Terapan*).
    - Guru memantau kehadiran siswa di jam pelajaran. Jika siswa hadir di gerbang pagi namun tidak berada di kelas saat KBM (status: *Membolos*), klik tombol **"🚨 Lapor Membolos ke BK"** untuk langsung mendokumentasikan pelanggaran disiplin KBM (+10 Poin Tatib) dan notifikasi ke Guru BK & Wali Kelas.
16. **Uji Coba Manajemen Siswa (Master Siswa - Full CRUD)**:
    - Buka menu **Manajemen Siswa** (Role Admin / BK / Wali Kelas).
    - Coba tombol **"+ Tambah Siswa Baru"** untuk mendaftarkan data siswa lengkap dengan NISN, kelas, nama orang tua, dan nomor WhatsApp.
    - Coba tombol **"✏️ Edit"** untuk memperbarui biodata siswa atau kontak orang tua secara *real-time*.
    - Coba tombol **"🗑️ Hapus"** untuk simulasi penghapusan data siswa yang tercatat dalam log audit ISO 27001.
    - Coba tombol **"🪪 Kartu"** untuk langsung membuka pratinjau kartu pelajar ber-barcode Code 128 & QR HMAC-SHA256.
17. **Uji Coba Manajemen Orang Tua & Wali Murid (Full CRUD & Aktivasi WhatsApp)**:
    - Buka menu **Manajemen Orang Tua** (Role Admin / BK / Wali Kelas).
    - Coba tombol **"+ Tambah Orang Tua Baru"** untuk menambahkan wali murid dan menghubungkannya dengan akun anak yang diasuh.
    - Klik tombol **"📲 Kirim Akses"** pada baris orang tua untuk mensimulasikan pengiriman undangan dan aktivasi portal monitoring via WhatsApp.
    - Gunakan tombol **"✏️ Edit"** dan **"🗑️ Hapus"** untuk mengelola data kontak wali murid.
18. **Uji Coba Lengkap CRUD di Seluruh Modul**:
    - **Manajemen Pengguna**: Mendukung Tambah, Edit Akun, Hapus Akun, Toggle Status, dan Reset Sandi.
    - **Kedisiplinan & Tatib**: Mendukung Tambah Pelanggaran, Edit Kasus, dan Batalkan/Hapus Pelanggaran (poin tatib otomatis dipulihkan).
    - **Bank Prestasi**: Mendukung Tambah Prestasi, Edit Prestasi, dan Hapus Prestasi (poin merit otomatis disesuaikan).
    - **Kehadiran Siswa**: Mendukung Tombol Cepat (H/S/I/A/T) serta tombol **"✏️ Catatan"** untuk koreksi status dan keterangan khusus per siswa.
19. **Aturan Hak Akses Menu Cetak Kartu Pelajar & Pengaduan (RBAC Least Privilege)**:
    - **Cetak Kartu Pelajar**: Hanya tersedia bagi **Super Admin** dan **Guru BK**. Menu ini otomatis dihilangkan dari peran Orang Tua, Wali Kelas, dan Siswa.
    - **Kanal Pengaduan & Whistleblowing**:
      - **Siswa**: Menu pengaduan dihilangkan secara penuh demi perlindungan privasi.
      - **Orang Tua**: Menu **"Pengaduan Saya"** dengan akses **CRUD Aduan Mandiri** (Tulis Aduan Baru, Edit Aduan, Hapus/Batalkan Aduan, serta Obrolan Investigasi dua arah). Orang tua hanya dapat melihat dan mengelola aduannya sendiri.
      - **Wali Kelas**: Menu **"Aduan Siswa Kelas"** yang secara otomatis hanya menampilkan aduan dari orang tua/siswa di kelas binaannya (X-RPL-1).
      - **Guru BK & Super Admin**: Menu **"Pusat Aduan / Whistle"** yang menampilkan seluruh aduan dari seluruh kelas sekolah secara terpusat untuk investigasi dan eskalasi kasus.
20. **Tampilan Menu Mandiri untuk Role Siswa, Orang Tua (Wali Murid), dan Wali Kelas**:
    - **Role Siswa**:
      - Menu **"Data Diri Siswa"** menampilkan profil kesiswaan resmi milik siswa itu sendiri (Ahmad Rifai), NISN, rombel kelas, catatan poin disiplin & prestasi, serta kontak orang tua terdaftar.
      - Siswa dapat mengklik **"✏️ Edit Kontak / Alamat"** untuk memperbarui nomor domisili.
    - **Role Orang Tua (Wali Murid)**:
      - Menu **"Data Siswa (Anak)"** menampilkan profil lengkap ananda yang diasuh (Ahmad Rifai).
      - Menu **"Data Orang Tua / Wali"** menampilkan profil diri wali murid (Bpk. Hendra Gunawan) dengan opsi **"✏️ Edit Kontak / Profil"** untuk memperbarui nomor WhatsApp, email, dan pekerjaan.
    - **Role Wali Kelas**:
      - Menu **"Data Siswa Kelas"** dan **"Data Wali Murid Kelas"** terisolasi hanya untuk murid dan orang tua di rombel kelas binaannya (X-RPL-1).
    - **Role Admin & Guru BK**:
      - Menu **"Manajemen Siswa"** dan **"Manajemen Orang Tua"** mencakup seluruh data siswa dan wali murid se-sekolah dengan kewenangan penuh (CRUD Master).
21. **Desain Tampilan GitHub Repository Theme & Logo Resmi EduTrack Pro**:
    - **Logo Resmi Aplikasi**: Simbol vektor modern yang memadukan perisai toga kesiswaan, gelombang pemindai barcode gerbang, dan titik verifikasi presensi (*emerald pulse*).
    - **Header Global GitHub**: Breadcrumb repositori (`smk-negeri-unggulan-1 / school_app`), badge status `Public`, kolom pencarian repositori (`Type / to search`), dan pemilih peran akun (*Role Switcher*).
    - **Tab Navigasi Horizontal Berikon Octicons**: Tab atas dengan ikon SVG GitHub Octicons lengkap (`Dashboard`, `Presensi`, `Scanner`, `Kartu Pelajar`, `Kedisiplinan`, `Bank Prestasi`, `Aduan`, dsb.) dengan garis bawah oranye khas GitHub pada tab aktif.
    - **Sub-Header Kontrol Git**: Pemilih branch (`main ▾`), counter branch & tag, serta tombol hijau khas GitHub (`<> Code ▾`).
    - **Tata Letak 2 Kolom (Main Content + Right Sidebar 'About')**:
      - **Kolom Utama (Kiri)**: Banner komit GitHub (*Commit Bar* memuat author, pesan komit, hash terverifikasi `34acc33`, dan counter komit) di atas modul kesiswaan aktif.
      - **Sidebar Kanan ('About')**: Persis seperti bilah samping repositori GitHub asli: bagian *About* (deskripsi, Readme, Activity, Stars/Prestasi, Watching/Siswa Aktif, Forks/Peran), bagian *Releases* (`v2.4-Production-Ready`), bagian *Deployments* (Server Daemon, AWS S3 Vault, WA Gateway), dan bagian *Hak Akses Pengguna* (Cakupan Data / Data Scope).
