# Panduan & Prompt Orkestrasi Multi-Agent (docs/README.md)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Ekosistem Stack**: Full-Stack Laravel 13 (PHP 8.4) + Laravel Livewire 3 + Alpine.js + Tailwind CSS v4 + PostgreSQL 16 + Redis 7 + Laravel Reverb & Horizon  
**Format Kerja**: Single Source of Truth (SSOT)  
**Kepatuhan Standar**: ISO/IEC 27001:2022 & UU Pelindungan Data Pribadi

Dokumen ini memuat kumpulan **Prompt Siap Pakai** untuk memobilisasi **Lead Orchestrator** dan seluruh **Multiple Sub-Agent Worker Pools** (Backend, Frontend, QA Assault, Security, dan DevOps) dalam membangun aplikasi produksi EduTrack Pro berbasis **Laravel 13**.

---

## 👑 1. Master Prompt: Memulai Orkestrasi Otomatis (Full Swarm Run)

Salin dan jalankan prompt berikut kepada AI Agent utama Anda untuk memulai seluruh siklus orkestrasi pembangunan sistem:

```markdown
Anda berperan sebagai **Lead Orchestrator Agent**.
Tugas Anda adalah mengoordinasikan pembangunan sistem produksi **EduTrack Pro (SDN Kebonagung Kota Pasuruan)** menggunakan ekosistem **Full-Stack Laravel 13 (PHP 8.4) + Laravel Livewire 3 + Alpine.js + Tailwind CSS v4 + PostgreSQL 16 + Redis 7 + Laravel Reverb** berdasarkan dokumentasi Single Source of Truth di folder `plan/docs/` dan konsep acuan antarmuka di `C:\scholl_app\index.html`.

Daftar Acuan SSOT:
- `plan/docs/01_PRD.md` (Product & Business Rules: Scanner 3-Mode, Izin Fleksibel, Keadilan Pulang, Dasbor Analisis AI & Copilot)
- `plan/docs/02_DESIGN_SYSTEM.md` (Enterprise Clean White Admin Dashboard, Glassmorphism Welcome & Auth Modal, Charts, Audio Feedback, AI Tokens)
- `plan/docs/03_ARSITEKTUR.md` (Laravel 13 Architecture, DDL 21 Tabel PostgreSQL 16, Redis Queue, Reverb & Dual Deployment)
- `plan/docs/04_DESIGN_PATTERN.md` (3-Mode Scanner Pattern, Checkout Justice, HMAC Token & AES-256 via Laravel Crypt)
- `plan/docs/05_DESIGN_UI.md` (Wireframe 16 Halaman Enterprise Clean White Admin, Meja Scanner 3-Mode, Dasbor Analisis AI & Kartu CR-80)
- `plan/docs/06_PROJECT_MANAGEMENT.md` (WBS Laravel 13, Roadmap 5 Fase, Risk Register & DoD)
- `plan/docs/07_ORCHESTRASI.md` (Topologi Swarm Laravel 13, Batas Wilayah Worker & Gerbang Eksekusi)

ATURAN ISOLASI & EKSEKUSI KERJA:
1. Jalankan pembangunan secara bertahap menggunakan protokol gerbang (Gerbang 1 s/d Gerbang 7).
2. Setiap sub-agent wajib dipanggil dengan parameter `workspace: "share"` agar bekerja pada git worktree terisolasi tanpa konflik berkas.
3. Sub-agent implementasi dilarang mengubah skema basis data di luar persetujuan Lead Orchestrator.
4. Pastikan interaktivitas antarmuka dari `index.html` (computed scopes, modal, tabel reaktif) dimigrasikan secara rapi ke struktur komponen Livewire 3 + Alpine.js + Blade.

MULAI DENGAN TAHAP 1:
Panggil `db-architect-agent` untuk men-scaffold proyek Laravel 13 (PHP 8.4), mengonfigurasi koneksi PostgreSQL 16, dan mengeksekusi migrasi DDL 21 tabel relasional Eloquent lengkap dengan UUIDv7 dan data seeder realistis SDN Kebonagung Kota Pasuruan. Laporkan hasil integrasi setelah Gerbang 1 selesai untuk melanjutkan ke implementasi backend squad!
```

---

## 📑 2. Prompt Eksekusi Mandiri Per Tahapan (Phased Sub-Agent Prompts)

Gunakan prompt khusus berikut untuk mengeksekusi tahapan implementasi secara terfokus:

### 🔹 Fase 1: Scaffolding Proyek Laravel 13 & Skema Database (Gerbang 1)
```markdown
Panggil sub-agent `db-architect-agent` (workspace: "share"):
"Scaffold proyek Full-Stack Laravel 13 (PHP 8.4) dengan Livewire 3 sesuai plan/docs/03_ARSITEKTUR.md.
Konfigurasikan database PostgreSQL 16 dan tulis 21 berkas migrasi Eloquent untuk tabel-tabel:
- users, teachers (Master GTK Admin), classrooms, students, parents, student_parent_relations
- student_cards (Barcode Code 128 + HMAC QR), attendances_daily (dengan check_in, check_out, early_leave, checkout_status), subjects, attendances_subject
- leave_requests (tipe izin fleksibel), gate_passes, violation_presets, student_violations, warning_letters
- achievements, complaints (AES-256 encrypted via Crypt), complaint_messages, audit_logs (Immutable), backup_archives.
Pasang constraint CHECK (demerit_points >= 0), CHECK (merit_points >= 0), implementasikan trait HasUuids, dan buat skrip seeder lengkap SDN Kebonagung Kota Pasuruan (Master GTK, akun 5 demo roles, preset tatib, siswa, orang tua). Jalankan perintah `php artisan migrate --seed` dan pastikan zero-error!"
```

### 🔹 Fase 2: Fan-Out Implementasi Backend Squad Laravel 13 (4 Workers Paralel)
```markdown
Luncurkan 4 sub-agent backend paralel sekaligus di runtime PHP 8.4 / Laravel 13 (workspace: "share"):
1. Sub-agent `be-worker-attendance`: Implementasikan `POST /api/attendance/gate-scan` via GateAttendanceScanController (AttendanceRuleEngineService: evaluasi otomatis 3 mode sesuai jam server: Mode 1 Masuk 06:00-08:00, Mode 2 Siaga Gate Pass 08:01-13:00, Mode 3 Pulang 13:01-17:00; cooldown 5 menit, latensi <= 50ms), artisan command Auto-Alpa pukul 08:00 WIB, CheckoutJusticeService (siswa lupa tap pulang tetap Hadir jika ada di KBM terakhir), dan verifikasi Digital Gate Pass.
2. Sub-agent `be-worker-discipline`: Implementasikan pencatatan pelanggaran, penambahan poin tatib, generator dokumen PDF Surat Peringatan (SP 1, SP 2, SP 3) dengan kop resmi SDN Kebonagung dan QR Code tanda tangan digital HMAC-SHA256 (BarcodeCryptoService), serta modul bank prestasi.
3. Sub-agent `be-worker-portal`: Implementasikan integrasi antrean Redis Queue (SendWhatsAppNotificationJob) untuk pengiriman notifikasi WhatsApp otomatis ke wali murid (notif masuk, notif pulang, dan alert alpa/bolos), alur pengajuan izin fleksibel orang tua (sakit 1 hari tanpa surat dokter maks 2x vs sakit >1 hari wajib surat dokter), dan CRUD Master GTK (eksklusif Super Admin).
4. Sub-agent `be-worker-whistle`: Implementasikan modul aduan anti-bullying terenkripsi AES-256 via Laravel Crypt, fitur whistleblower anonim, ruang obrolan investigasi dua arah, dan append-only logger ISO 27001 A.12.4.
```

### 🔹 Fase 3: Fan-Out Implementasi Frontend Squad Livewire 3 (3 Workers Paralel - Enterprise Clean White)
```markdown
Luncurkan 3 sub-agent frontend paralel sekaligus di Laravel Livewire 3 + Alpine.js (workspace: "share"):
1. Sub-agent `fe-worker-scanner`: Bangun komponen Livewire Meja Scanner Gerbang 3-Mode (/scanner) dengan penanganan burst input barcode scanner laser USB, banner indikator 3 mode otomatis, umpan balik suara Web Audio API (Beep 880Hz masuk, Buzz 180Hz terlambat, Chime 3 nada C-E-G pulang), verifikasi Gate Pass satpam, serta modul generator cetak kartu pelajar PVC ISO CR-80 (preview bolak-balik & tombol revoke).
2. Sub-agent `fe-worker-academic`: Bangun komponen Livewire Presensi Kehadiran (/presensi) dengan tab Presensi Cepat H/S/I/A/T rombel, tab Presensi KBM Jam Mapel (dengan tombol merah 'Lapor Membolos ke BK'), modal pengajuan izin sakit fleksibel ortu, tabel kedisiplinan tatib, lengkap dengan pagination dinamis Livewire, debounced search (wire:model.live.debounce), dan multi-filter.
3. Sub-agent `fe-worker-admin`: Bangun komponen Livewire Welcome Page edukasi (karakter Guru PNS khaki, lencana SDN Kebonagung), Dashboard Eksekutif bergaya GitHub (Commit Bar hash 34acc33, KPI kartu statistik putih dengan shadow-sm, grafik Chart.js tren kehadiran mingguan dan donut chart disiplin), modul Master GTK (CRUD lengkap Admin), Manajemen User Scopes, dan Backup Snapshot ISO 27001.
```

### 🔹 Fase 4: Fan-Out QA Assault & Security Audit (4 QA Workers)
```markdown
Luncurkan 4 sub-agent pengujian paralel untuk memverifikasi keandalan sistem menggunakan Pest PHP / PHPUnit:
1. Sub-agent `qa-worker-scanner-load`: Tembakkan 50 request scan barcode gerbang paralel serentak pada beban jam masuk sekolah (06:55 WIB). Pastikan controller Laravel memproses seluruh request dengan rata-rata latensi <= 50 milidetik didukung OPcache JIT tanpa deadlock basis data.
2. Sub-agent `qa-worker-rbac-idor`: Uji penetrasi otorisasi (Pen-Test): Coba akses data siswa kelas lain menggunakan sesi Wali Kelas X-RPL-1, dan coba akses biodata siswa lain menggunakan sesi Orang Tua Ahmad Rifai. Pastikan seluruh query tertolak 403 Forbidden melalui Laravel Model Policies!
3. Sub-agent `qa-worker-e2e-flow`: Uji siklus operasional utuh: Siswa tap gerbang -> Hadir di gerbang tapi membolos di mapel KBM -> Guru mapel lapor bolos -> Poin bertambah -> Terbit SP-1 -> Siswa pulang lupa tap kartu -> Keadilan checkout berjalan (status tetap Hadir) -> Alert WA terdorong ke Redis Queue -> Tercatat di Log Audit ISO 27001.
4. Sub-agent `security-audit-agent`: Audit kode terhadap standar OWASP Top 10:2026 (Argon2id, SQL Injection prevention via Eloquent Parameter Binding, XSS escaping di Blade, enkripsi kolom AES-256 via Crypt, dan imutabilitas tabel audit_logs).
```

### 🔹 Fase 5: Pengemasan Infrastruktur & Deployment Ganda
```markdown
Panggil sub-agent `devops-deploy-agent`:
"Siapkan berkas konfigurasi deployment untuk 2 target infrastruktur:
 1. Opsi 1 (aaPanel Hosting Linux): Konfigurasi PHP 8.4-FPM + Nginx Reverse Proxy (SSL Let's Encrypt, HTTP/2, WebSocket Reverb di port 8080), konfigurasi Supervisor daemon (Queue Worker Redis & Reverb Server), dan Cron tab harian (artisan schedule:run).
 2. Opsi 2 (Bare VPS Ubuntu / Debian & Docker): Berkas `docker-compose.yml` multi-container (Nginx Alpine, PHP 8.4-FPM App container, PostgreSQL 16, Redis 7, Queue Worker, Reverb WebSocket) dan skrip otomatis `deploy/setup-vps.sh`.
 Uji seluruh konfigurasi agar siap deploy dalam sekali klik di kedua skenario."
```

---

## 🚦 3. Checklist Kriteria Gerbang Eksekusi (Quality Gate Checklist)

- [ ] **Gate 1 (DDL & Migrations)**: Migrasi 21 tabel PostgreSQL 16 via Laravel Migrations berhasil dieksekusi 100% tanpa error dengan seeder realistis.
- [ ] **Gate 2 (Scanner Latency & 3 Mode)**: Latensi endpoint `POST /api/attendance/gate-scan` berada di bawah $\le 50\text{ ms}$ pada controller Laravel 13 dan otomatis berganti 3 mode sesuai jam server.
- [ ] **Gate 3 (Keadilan Absensi Pulang)**: Siswa yang hadir di KBM jam terakhir namun lupa tap kartu pulang terbukti berstatus `Hadir (Check-out Default)`.
- [ ] **Gate 4 (Isolasi Data Sisi Server)**: Akun Orang Tua dan Siswa terbukti tidak dapat mengakses data di luar cakupannya (*Zero IDOR Leakage*) via Laravel Policies.
- [ ] **Gate 5 (Kepatuhan Enkripsi ISO 27001)**: Kolom aduan tersimpan dalam bentuk *cipher text* AES-256 via Laravel Crypt, dan tabel `audit_logs` terlindungi secara kekal (*immutable*).
- [ ] **Gate 6 (UI Putih Minimalis & Komponen Lengkap)**: Seluruh tabel Livewire 3 dilengkapi pagination dinamis, debounced search, multi-filter, kartu statistik KPI, dan grafik Chart.js.
- [ ] **Gate 7 (Dual Deployment Ready)**: Konfigurasi aaPanel (PHP 8.4-FPM + Supervisor) dan Docker Compose terverifikasi siap melayani domain produksi sekolah.
