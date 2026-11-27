# Perbandingan Mendalam: Sistem Eksisting vs. Arsitektur Target (Technical Detail)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Status**: Analisis Komparatif Mendalam (Baseline Mockup vs. Target Production Laravel 13)

Dokumen ini memuat perbandingan teknis, arsitektural, operasional, dan keamanan secara komprehensif baris-demi-baris antara sistem prototipe saat ini (*existing in-memory SPA*) pada [index.html](file:///c:/scholl_app/index.html) dan arsitektur target yang direncanakan (*target production Laravel 13 + Livewire 3 + PostgreSQL 16*).

---

## 1. Matriks Komparasi Parameter Teknis

| Dimensi Arsitektur | Sistem Eksisting (Mockup `index.html`) | Arsitektur Target (Planned Production Laravel 13) | Dampak & Alasan Transformasi |
| :--- | :--- | :--- | :--- |
| **Ekosistem & Runtime** | File HTML statis dibuka di browser (`Vue.createApp`). | **Full-Stack Laravel 13 (PHP 8.4) + Laravel Livewire 3 + Alpine.js + Tailwind CSS v4**. | Menyatukan reaktivitas komponen antarmuka langsung dengan backend tanpa kerumitan memelihara dua aplikasi terpisah (SPA & API). |
| **Penyimpanan Data (Persistence)** | Browser RAM Memory (`ref([])` di JavaScript). Data hilang seketika saat halaman di-refresh (*F5*). | Basis Data Relasional **PostgreSQL 16 Enterprise** via **Laravel Eloquent Migrations** (ACID, UUIDv7, CHECK constraints). | Menjamin persistensi data permanen, kapasitas ribuan siswa, dan integritas data antar semester tanpa risiko data hilang. |
| **Konkurensi & Multi-Device** | **Zero Concurrency**: Hanya berjalan di 1 tab browser pada 1 komputer pengembang lokal. | **Multi-Device Concurrent Engine**: Pos Satpam, Ruang Guru, Meja BK, dan Ponsel Ortu terhubung serentak secara real-time via **Laravel Reverb**. | Memungkinkan satpam memindai kartu di gerbang sementara wali kelas mengabsen di kelas dan orang tua menerima WA serentak. |
| **Modus Pemindai Gerbang** | Simulasi dropdown jam manual (`06:45`, `07:10`, dsb.) hanya fokus pada jam masuk pagi. | **3-Mode Auto-Switch Scanner**: Mode 1 Masuk (06:00-08:00), Mode 2 Siaga Gate Pass (08:01-13:00), Mode 3 Pulang (13:01-17:00). | Satpam tidak perlu memencet tombol ganti mode; scanner otomatis menyesuaikan fungsi sesuai jam server nyata. |
| **Kecepatan Presensi Gerbang** | Pencarian array JS lokal di browser. | **High-Throughput Endpoint (`POST /api/attendance/gate-scan`)** dengan latensi $\le 50\text{ ms}$ pada Laravel 13 Controller + OPcache JIT. | 500 siswa di gerbang dapat dipindai tuntas dalam waktu 15-20 menit tanpa antrean mengular. |
| **Presensi Pulang & Notifikasi** | Belum ada alur kepulangan otomatis; hanya ada kolom kosong. | **Sore Check-out + WhatsApp Kepulangan**: Nada Chime 3 nada C-E-G dan notifikasi WA otomatis ke orang tua bahwa anak telah keluar gerbang. | Memberikan ketenangan batin (*peace of mind*) kepada orang tua untuk memantau kepulangan anak ke rumah secara tepat waktu. |
| **Keadilan Lupa Tap Pulang** | Tidak ada penanganan khusus (berpotensi tercatat tidak hadir). | **Pola Keadilan Checkout Fallback**: Siswa yang hadir di KBM sesi terakhir tetap berstatus `Hadir (Check-out Default)` via `CheckoutJusticeService`. | Menjaga keadilan: Siswa SD yang berebut keluar gerbang dan lupa tap kartu tidak dihukum Alpa. |
| **Pengajuan Izin Sakit Ortu** | Wajib surat dokter klinik/RS (terlalu kaku untuk demam 1 hari). | **Izin Fleksibel**: Sakit 1 Hari cukup surat izin tertulis bertanda tangan orang tua (maks 2x/semester); Sakit >1 Hari wajib surat dokter. | Menghilangkan beban orang tua mencari surat dokter untuk sakit ringan dan mencegah sanksi Auto-Alpa keliru. |
| **Status Pulang Cepat (Gate Pass)**| Tercatat sebagai Gate Pass biasa tanpa penegasan status presensi harian. | **Status Mutasi Presensi**: Menjadi `Hadir (Izin Pulang Cepat: 10:15 WIB)` + Alert WA otomatis saat palang gerbang dibuka satpam. | Jam keluar tercatat presisi di database, terbebas dari tuduhan membolos, dan mencegah risiko penculikan anak. |
| **Deteksi Siswa Membolos KBM** | Tombol simulasi JS lokal. | **Relational Cross-Checking Engine**: Membandingkan log gerbang pagi dengan presensi KBM per jam pelajaran (+10 Poin & notif darurat BK). | Menutup celah siswa yang hadir di gerbang pagi namun membolos atau nongkrong di kantin saat pelajaran siang. |
| **Keamanan Kartu Pelajar** | Teks barcode alfanumerik biasa tanpa proteksi anti-pemalsuan. | **Dual-Symbology ISO CR-80**: Barcode 1D Code 128 + QR Code 2D bertanda tangan digital **HMAC-SHA256**. | Mengeliminasi kecurangan titip absen atau pemalsuan kartu via fotokopi/foto smartphone. |
| **Kanal Aduan & Whistleblower** | Teks biasa di memori browser (*plain text*). | **Enkripsi End-to-End Kolom Sensitif (AES-256)** via **Laravel Crypt**; identitas pelapor anonim terisolasi penuh. | Menjamin kerahasiaan identitas korban perundungan (*bullying*) sesuai mandat UU Perlindungan Data Pribadi (UU PDP). |
| **Pengiriman Notifikasi WhatsApp**| Jendela modal sembulan simulasi di layar pengembang. | **Asynchronous Redis Queue Worker (`SendWhatsAppNotificationJob`)** yang menembakkan pesan resmi via WhatsApp Business API Gateway. | Orang tua langsung menerima pesan resmi di ponsel masing-masing (notif masuk, notif pulang, alert alpa/bolos). |
| **Master Guru & GTK (Admin)** | Array statis 7 guru/staf di memori JavaScript. | **Tabel Relasional `teachers`**: Validasi NIP/NUPTK resmi, hak akses eksklusif Super Admin, dan audit trail ISO 27001. | Memfasilitasi tata kelola kepegawaian sekolah secara tertib administrasi Dapodik dan terlindung dari akses ilegal. |
| **Gaya Desain & Komponen UI** | **Enterprise Clean White Dashboard + Glassmorphism Welcome & Auth Modal (HRD-Style)**: Sidebar responsif (260px expanded, 68px mini, overlay drawer), Tautan rounded-xl (Rose-50 active), Sticky glassmorphism header, 5-Role quick login switcher, pagination dinamis, multi-filter dropdown, dan visualisasi grafik Chart.js (Rose-600 & Emerald-500). | **Komponen Terintegrasi Penuh (Laravel Livewire 3 + Blade + Alpine.js)**: Mempertahankan 100% tata visual Enterprise Clean White & Glassmorphism modal, dipadukan reaktivitas server tanpa reload, state management aman, dan micro-interaction mutakhir. | Menghasilkan antarmuka visual yang sangat bersih, elegan, terstandarisasi industri, dan sangat mudah digunakan oleh guru, satpam, maupun orang tua. |
| **Kecerdasan Buatan (AI) & KBM** | **Dasbor Analisis AI (Rule-Based & Google Gemini 1.5 Flash)**: Deteksi otomatis penurunan nilai matematika & absensi kritis siswa, rekomendasi empatik guru, Executive Briefing Dashboard, dan Floating Copilot. | **AI Engine & Hybrid LLM Services (PHP 8.4)**: Terhubung langsung ke API Gemini 1.5 Flash / LLM dengan rule-based filtering kesiswaan real-time dan memory context kesiswaan. | Memangkas beban guru dalam mendeteksi siswa berisiko secara dini dan memberikan rekomendasi preventif bagi wali kelas dan kepala sekolah. |
| **Jejak Audit Sistem** | Array `auditLogs` lokal yang terhapus saat refresh. | **Tabel Kekal `audit_logs` (ISO 27001 A.12.4)** dilindungi *PostgreSQL Database Trigger* dilarang UPDATE & DELETE. | Menjamin jejak mutasi data (*Who, What, When, IP Address*) tidak dapat dimanipulasi bahkan oleh administrator sekolah. |
| **Pencadangan Data (Backup)** | Simulasi timeout JS (1.2 detik). | **Automated 3-2-1 Encrypted Backup**: Dump database terenkripsi AES-256 otomatis direplikasi ke AWS S3 Vault SG via Laravel Scheduler. | Memberikan jaminan pemulihan bencana (*disaster recovery*) jika server lokal mengalami kerusakan perangkat keras. |

---

## 2. Perbandingan Alur Bisnis Kritis (Workflow Deep-Dive)

### A. Alur Presensi Kepulangan Sore (Afternoon Check-out Workflow)

```text
ALUR MOCKUP EKSISTING:
1. Tidak ada mekanisme tap kepulangan yang berfungsi secara reaktif.
2. Kolom check_out_time di tabel dibiarkan kosong.
MASALAH: Orang tua tidak tahu jam berapa anaknya pulang sekolah; sekolah tidak tahu apakah anak masih di lingkungan sekolah atau sudah pulang.

ALUR TARGET PRODUKSI:
1. Pukul 13:01 WIB, scanner gerbang otomatis berganti ke MODE 3: PRESENSI PULANG (CHECK-OUT).
2. Siswa men-tap kartu pelajar pada laser scanner saat berjalan keluar gerbang.
3. Mesin Laravel memproses scan (< 50ms) dan mengisi 'check_out_time = NOW()'.
4. Speaker pos gerbang membunyikan nada melodi Chime 3 nada C-E-G ("Selamat Pulang").
5. Redis Queue Worker mendorong notifikasi WhatsApp ke ponsel orang tua:
   "Bapak/Ibu, ananda [Nama] telah selesai KBM dan keluar gerbang sekolah pukul 14:15 WIB. Mohon dipantau kepulangan ananda ke rumah."
6. Rekonsiliasi Jam 17:00 WIB: Jika siswa hadir KBM jam terakhir tapi lupa tap kartu pulang,
   CheckoutJusticeService secara otomatis menandai status 'Hadir (Check-out Default: Selesai KBM)' tanpa menjatuhkan penalti Alpa.
```

### B. Alur Pengajuan Izin Sakit Fleksibel (Flexible Leave Workflow)

```text
ALUR TARGET PRODUKSI:
1. Anak demam di rumah pukul 06:30 WIB.
2. Orang tua membuka portal di ponsel, memilih menu 'Ajukan Izin / Sakit Anak'.
3. Orang tua memilih 'Sakit 1 Hari (Surat Izin Ortu)' dan mengunggah foto surat izin tulisan tangan bertanda tangan.
4. Sistem memverifikasi sisa kuota izin mandiri semester ini (maks 2x per semester).
5. Pengajuan masuk ke antrean dashboard Wali Kelas dengan banner notifikasi kuning.
6. Wali Kelas menekan tombol '✓ Setujui Izin'.
7. Status presensi anak hari itu otomatis terkunci menjadi 'Sakit', membebaskan anak dari vonis Auto-Alpa jam 08:00 WIB.
```
