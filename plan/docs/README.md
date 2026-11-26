# Panduan & Prompt Orkestrasi Multi-Agent (docs/README.md)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Ekosistem Stack**: Full-stack JavaScript / TypeScript (Nuxt 3 Vue 3 / Node.js Fastify + Drizzle ORM + PostgreSQL 16 + Redis BullMQ)  
**Format Kerja**: Single Source of Truth (SSOT)  
**Kepatuhan Standar**: ISO/IEC 27001:2022 & UU Pelindungan Data Pribadi

Dokumen ini memuat kumpulan **Prompt Siap Pakai** untuk memobilisasi **Lead Orchestrator** dan seluruh **Multiple Sub-Agent Worker Pools** (Backend, Frontend, QA Assault, Security, dan DevOps) dalam membangun aplikasi produksi EduTrack Pro berbasis **Framework JavaScript**.

---

## 👑 1. Master Prompt: Memulai Orkestrasi Otomatis (Full Swarm Run)

Salin dan jalankan prompt berikut kepada AI Agent utama Anda untuk memulai seluruh siklus orkestrasi pembangunan sistem:

```markdown
Anda berperan sebagai **Lead Orchestrator Agent**.
Tugas Anda adalah mengoordinasikan pembangunan sistem produksi **EduTrack Pro (SDN Kebonagung Kota Pasuruan)** menggunakan ekosistem **Full-stack JavaScript / TypeScript (Nuxt 3 Vue 3 / Node.js Fastify + Drizzle ORM + PostgreSQL 16 + Redis BullMQ)** berdasarkan dokumentasi Single Source of Truth di folder `plan/docs/` dan konsep acuan antarmuka di `C:\scholl_app\index.html`.

Daftar Acuan SSOT:
- `plan/docs/01_PRD.md` (Product & Business Rules: Scanner 3-Mode, Izin Fleksibel, Keadilan Pulang, Studio Bahan Ajar AI & Copilot)
- `plan/docs/02_DESIGN_SYSTEM.md` (Metis Bootstrap 5 Admin Dashboard, Colorlib Login Form 11, Charts, Audio Feedback, AI Tokens)
- `plan/docs/03_ARSITEKTUR.md` (JavaScript/TypeScript Monorepo, DDL 21 Tabel PostgreSQL 16, BullMQ, AI Service & Dual Deployment)
- `plan/docs/04_DESIGN_PATTERN.md` (3-Mode Scanner Pattern, Checkout Justice, HMAC Token & AES-256)
- `plan/docs/05_DESIGN_UI.md` (Wireframe 16 Halaman Metis Admin, Meja Scanner 3-Mode, Studio Bahan Ajar AI & Kartu CR-80)
- `plan/docs/06_PROJECT_MANAGEMENT.md` (WBS JavaScript, Roadmap 5 Fase, Risk Register & DoD)
- `plan/docs/07_ORCHESTRASI.md` (Topologi Swarm JavaScript, Batas Wilayah Worker & Gerbang Eksekusi)

ATURAN ISOLASI & EKSEKUSI KERJA:
1. Jalankan pembangunan secara bertahap menggunakan protokol gerbang (Gerbang 1 s/d Gerbang 7).
2. Setiap sub-agent wajib dipanggil dengan parameter `workspace: "share"` agar bekerja pada git worktree terisolasi tanpa konflik berkas.
3. Sub-agent implementasi dilarang mengubah skema basis data di luar persetujuan Lead Orchestrator.
4. Pastikan komponen Vue 3 dari `index.html` (computed scopes, modal, tabel reaktif) dimigrasikan secara rapi ke struktur komponen Nuxt 3 / Vue 3.

MULAI DENGAN TAHAP 1:
Panggil `db-architect-agent` untuk men-scaffold monorepo TypeScript/JavaScript, mengonfigurasi Drizzle ORM, dan mengeksekusi migrasi DDL 21 tabel relasional PostgreSQL 16 lengkap dengan data seeder realistis SDN Kebonagung Kota Pasuruan. Laporkan hasil integrasi setelah Gerbang 1 selesai untuk melanjutkan ke implementasi backend squad!
```

---

## 📑 2. Prompt Eksekusi Mandiri Per Tahapan (Phased Sub-Agent Prompts)

Gunakan prompt khusus berikut untuk mengeksekusi tahapan implementasi secara terfokus:

### 🔹 Fase 1: Scaffolding Monorepo JavaScript & Skema Database (Gerbang 1)
```markdown
Panggil sub-agent `db-architect-agent` (workspace: "share"):
"Scaffold proyek Full-stack JavaScript / TypeScript (Nuxt 3 / Node.js Fastify) sesuai plan/docs/03_ARSITEKTUR.md.
Konfigurasikan Drizzle ORM di folder `database/` untuk 21 tabel relasional PostgreSQL 16:
- users, teachers (Master GTK Admin), classrooms, students, parents, student_parent_relations
- student_cards (Barcode Code 128 + HMAC QR), attendances_daily (dengan check_in, check_out, early_leave, checkout_status), subjects, attendances_subject
- leave_requests (tipe izin fleksibel), gate_passes, violation_presets, student_violations, warning_letters
- achievements, complaints (AES-256 encrypted), complaint_messages, audit_logs (Immutable), backup_archives.
Pasang constraint CHECK (demerit_points >= 0), CHECK (merit_points >= 0), dan buat skrip seeder lengkap SDN Kebonagung Kota Pasuruan (Master GTK, akun 5 demo roles, preset tatib, siswa, orang tua). Jalankan perintah `npm run db:migrate` dan pastikan zero-error!"
```

### 🔹 Fase 2: Fan-Out Implementasi Backend Squad JavaScript (4 Workers Paralel)
```markdown
Luncurkan 4 sub-agent backend paralel sekaligus di runtime Node.js/TypeScript (workspace: "share"):
1. Sub-agent `be-worker-attendance`: Implementasikan `POST /api/attendance/gate-scan` (AttendanceRuleEngineService: evaluasi otomatis 3 mode sesuai jam server: Mode 1 Masuk 06:00-08:00, Mode 2 Siaga Gate Pass 08:01-13:00, Mode 3 Pulang 13:01-17:00; cooldown 5 menit, latensi <= 50ms), scheduler Auto-Alpa pukul 08:00 WIB, CheckoutJusticeService (siswa lupa tap pulang tetap Hadir jika ada di KBM terakhir), dan verifikasi Digital Gate Pass.
2. Sub-agent `be-worker-discipline`: Implementasikan pencatatan pelanggaran, penambahan poin tatib, generator dokumen PDF Surat Peringatan (SP 1, SP 2, SP 3) dengan kop resmi SDN Kebonagung dan QR Code tanda tangan digital HMAC-SHA256, serta modul bank prestasi.
3. Sub-agent `be-worker-portal`: Implementasikan integrasi antrean BullMQ + Redis untuk pengiriman notifikasi WhatsApp otomatis ke wali murid (notif masuk, notif pulang, dan alert alpa/bolos), alur pengajuan izin fleksibel orang tua (sakit 1 hari tanpa surat dokter maks 2x vs sakit >1 hari wajib surat dokter), dan API Master GTK (eksklusif Super Admin).
4. Sub-agent `be-worker-whistle`: Implementasikan modul aduan anti-bullying terenkripsi AES-256-GCM, fitur whistleblower anonim, ruang obrolan investigasi dua arah, dan append-only logger ISO 27001 A.12.4.
```

### 🔹 Fase 3: Fan-Out Implementasi Frontend Squad (3 Workers Paralel - White Minimalist)
```markdown
Luncurkan 3 sub-agent frontend paralel sekaligus di Nuxt 3 / Vue 3 (workspace: "share"):
1. Sub-agent `fe-worker-scanner`: Bangun antarmuka Meja Scanner Gerbang 3-Mode (/scanner) dengan penanganan burst input barcode scanner laser USB, banner indikator 3 mode otomatis, umpan balik suara Web Audio API (Beep 880Hz masuk, Buzz 180Hz terlambat, Chime 3 nada C-E-G pulang), verifikasi Gate Pass satpam, serta modul generator cetak kartu pelajar PVC ISO CR-80 (preview bolak-balik & tombol revoke).
2. Sub-agent `fe-worker-academic`: Bangun halaman Presensi Kehadiran (/presensi) dengan tab Presensi Cepat H/S/I/A/T rombel, tab Presensi KBM Jam Mapel (dengan tombol merah 'Lapor Membolos ke BK'), modal pengajuan izin sakit fleksibel ortu, tabel kedisiplinan tatib, lengkap dengan pagination dinamis, debounced search, dan multi-filter.
3. Sub-agent `fe-worker-admin`: Bangun halaman Welcome Page edukasi (karakter Guru PNS khaki, lencana SDN Kebonagung), Dashboard Eksekutif bergaya GitHub (Commit Bar hash 34acc33, KPI kartu statistik putih dengan shadow-sm, grafik Chart.js tren kehadiran mingguan dan donut chart disiplin), modul Master GTK (CRUD lengkap Admin), Manajemen User Scopes, dan Backup Snapshot ISO 27001.
```

### 🔹 Fase 4: Fan-Out QA Assault & Security Audit (4 QA Workers)
```markdown
Luncurkan 4 sub-agent pengujian paralel untuk memverifikasi keandalan sistem:
1. Sub-agent `qa-worker-scanner-load`: Tembakkan 50 request scan barcode gerbang paralel serentak pada beban jam masuk sekolah (06:55 WIB). Pastikan server Fastify memproses seluruh request dengan rata-rata latensi <= 50 milidetik tanpa deadlock basis data.
2. Sub-agent `qa-worker-rbac-idor`: Uji penetrasi otorisasi (Pen-Test): Coba akses data siswa kelas lain menggunakan sesi Wali Kelas X-RPL-1, dan coba akses biodata siswa lain menggunakan sesi Orang Tua Ahmad Rifai. Pastikan seluruh query tertolak 403 Forbidden!
3. Sub-agent `qa-worker-e2e-flow`: Uji siklus operasional utuh: Siswa tap gerbang -> Hadir di gerbang tapi membolos di mapel KBM -> Guru mapel lapor bolos -> Poin bertambah -> Terbit SP-1 -> Siswa pulang lupa tap kartu -> Keadilan checkout berjalan (status tetap Hadir) -> Alert WA terkirim ke orang tua -> Tercatat di Log Audit ISO 27001.
4. Sub-agent `security-audit-agent`: Audit kode terhadap standar OWASP Top 10:2026 (Argon2id, SQL Injection prevention via Drizzle, XSS escaping, enkripsi kolom AES-256, dan imutabilitas tabel audit_logs).
```

### 🔹 Fase 5: Pengemasan Infrastruktur & Deployment Ganda
```markdown
Panggil sub-agent `devops-deploy-agent`:
"Siapkan berkas konfigurasi deployment untuk 2 target infrastruktur:
 1. Opsi 1 (aaPanel Hosting Linux): Berkas `ecosystem.config.js` untuk PM2 Cluster Mode (Node.js runtime port 3000 dan BullMQ worker daemon), konfigurasi Nginx Reverse Proxy (SSL Let's Encrypt, HTTP/2, WebSocket), dan skrip Cron tab harian (Auto-Alpa 08:00 WIB & Encrypted Backup S3 02:00 WIB).
 2. Opsi 2 (Bare VPS Ubuntu / Debian & Docker): Berkas `docker-compose.yml` multi-container (Nginx Alpine, Node.js App container, PostgreSQL 16, Redis 7, Cron Backup) dan skrip otomatis `deploy/setup-vps.sh`.
 Uji seluruh konfigurasi agar siap deploy dalam sekali klik di kedua skenario."
```

---

## 🚦 3. Checklist Kriteria Gerbang Eksekusi (Quality Gate Checklist)

- [ ] **Gate 1 (DDL & Schema)**: Migrasi 21 tabel PostgreSQL 16 via Drizzle ORM berhasil dieksekusi 100% tanpa error.
- [ ] **Gate 2 (Scanner Latency & 3 Mode)**: Latensi endpoint `POST /api/attendance/gate-scan` berada di bawah $\le 50\text{ ms}$ pada Node.js Fastify dan otomatis berganti 3 mode sesuai jam server.
- [ ] **Gate 3 (Keadilan Absensi Pulang)**: Siswa yang hadir di KBM jam terakhir namun lupa tap kartu pulang terbukti berstatus `Hadir (Check-out Default)`.
- [ ] **Gate 4 (Isolasi Data Sisi Server)**: Akun Orang Tua dan Siswa terbukti tidak dapat mengakses data di luar cakupannya (*Zero IDOR Leakage*).
- [ ] **Gate 5 (Kepatuhan Enkripsi ISO 27001)**: Kolom aduan tersimpan dalam bentuk *cipher text* AES-256, dan tabel `audit_logs` terlindungi secara kekal (*immutable*).
- [ ] **Gate 6 (UI Putih Minimalis & Komponen Lengkap)**: Seluruh tabel dilengkapi pagination dinamis, debounced search, multi-filter, kartu statistik KPI, dan grafik Chart.js.
- [ ] **Gate 7 (Dual Deployment Ready)**: Konfigurasi aaPanel PM2 Cluster dan Docker Compose terverifikasi siap melayani domain produksi sekolah.