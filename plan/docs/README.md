# Panduan & Prompt Orkestrasi Multi-Agent (docs/README.md)
**Aplikasi**: EduTrack Pro (Sistem Monitoring Terpadu Sekolah)  
**Institusi**: SDN Kebonagung Kota Pasuruan  
**Format**: Single Source of Truth (SSOT)  
**Kepatuhan Standar**: ISO/IEC 27001:2022 & UU Pelindungan Data Pribadi

Dokumen ini memuat kumpulan **Prompt Siap Pakai** untuk memobilisasi **Lead Orchestrator** dan seluruh **Multiple Sub-Agent Worker Pools** (Backend, Frontend, QA Assault, Security, dan DevOps) dalam membangun aplikasi produksi EduTrack Pro berdasarkan konsep acuan [index.html](file:///c:/scholl_app/index.html).

---

## 👑 1. Master Prompt: Memulai Orkestrasi Otomatis (Full Swarm Run)

Salin dan jalankan prompt berikut kepada AI Agent utama Anda untuk memulai seluruh siklus orkestrasi pembangunan sistem:

```markdown
Anda berperan sebagai **Lead Orchestrator Agent**.
Tugas Anda adalah mengoordinasikan pembangunan sistem produksi **EduTrack Pro (SDN Kebonagung Kota Pasuruan)** secara multi-agent monorepo berdasarkan dokumentasi Single Source of Truth di folder `plan/docs/` dan konsep acuan antarmuka di `C:\scholl_app\index.html`.

Daftar Acuan SSOT:
- `plan/docs/01_PRD.md` (Product & Business Rules Kesiswaan Terpadu)
- `plan/docs/02_DESIGN_SYSTEM.md` (Clean White Minimalist, GitHub Layout & Audio Feedback)
- `plan/docs/03_ARSITEKTUR.md` (Laravel 11, DDL 21 Tabel PostgreSQL 16, Redis & Dual Deployment)
- `plan/docs/04_DESIGN_PATTERN.md` (Attendance Rule Engine, HMAC Token, AES-256 & Immutable Audit)
- `plan/docs/05_DESIGN_UI.md` (Wireframe 15 Halaman Sistem, Meja Scanner & Kartu CR-80)
- `plan/docs/06_PROJECT_MANAGEMENT.md` (WBS, Roadmap 5 Fase, Risk Register & DoD)
- `plan/docs/07_ORCHESTRASI.md` (Topologi Swarm, Batas Wilayah Worker & Gerbang Eksekusi)

ATURAN ISOLASI & EKSEKUSI KERJA:
1. Jalankan pembangunan secara bertahap menggunakan protokol gerbang (Gerbang 0 s/d Gerbang 6).
2. Setiap sub-agent wajib dipanggil dengan parameter `workspace: "share"` agar bekerja pada git worktree terisolasi tanpa konflik berkas.
3. Sub-agent implementasi dilarang mengubah skema database di luar persetujuan Lead Orchestrator.
4. Jangan menyetujui integrasi ke branch utama jika pengujian konkurensi (50 scan serentak gerbang) atau pengujian penetrasi IDOR menghasilkan kegagalan!

MULAI DENGAN TAHAP 1:
Panggil `db-architect-agent` untuk men-scaffold proyek Laravel 11/12 dan mengeksekusi migrasi DDL 21 tabel relasional PostgreSQL 16 lengkap dengan data seeder realistis SDN Kebonagung Kota Pasuruan. Laporkan hasil integrasi setelah Gerbang 1 selesai untuk melanjutkan ke Fan-Out implementasi backend!
```

---

## 📑 2. Prompt Eksekusi Mandiri Per Tahapan (Phased Sub-Agent Prompts)

Gunakan prompt khusus berikut untuk mengeksekusi tahapan implementasi secara terfokus:

### 🔹 Fase 1: Scaffolding Proyek & Skema Basis Data (Gerbang 1)
```markdown
Panggil sub-agent `db-architect-agent` (workspace: "share"):
"Scaffold proyek Laravel 11/12 dengan PHP 8.3 sesuai plan/docs/03_ARSITEKTUR.md Bagian 3.
Buat berkas migrasi database untuk 21 tabel relasional PostgreSQL:
- users, teachers (Master GTK), classrooms, students, parents, student_parent_relations
- student_cards (Barcode Code 128 + HMAC QR), attendances_daily, subjects, attendances_subject
- leave_requests, gate_passes, violation_presets, student_violations, warning_letters
- achievements, complaints (AES-256 encrypted), complaint_messages, audit_logs (Immutable), backup_archives.
Pasang constraint CHECK (demerit_points >= 0), CHECK (merit_points >= 0), dan buat skrip seeder lengkap SDN Kebonagung Kota Pasuruan (Master GTK, akun 5 demo roles, preset tatib, siswa, orang tua). Pastikan migrasi berjalan zero-error!"
```

### 🔹 Fase 2: Fan-Out Implementasi Backend Squad (4 Workers Paralel)
```markdown
Luncurkan 4 sub-agent backend paralel sekaligus (workspace: "share"):
1. Sub-agent `be-worker-attendance`: Implementasikan `POST /api/attendance/gate-scan` (AttendanceRuleEngineService: evaluasi Hadir, T-1, T-2, T-3, anti-double tap cooldown 5 menit, latensi <= 200ms), scheduler Auto-Alpa pukul 08:00 WIB, dan endpoint verifikasi Digital Gate Pass satpam.
2. Sub-agent `be-worker-discipline`: Implementasikan pencatatan pelanggaran, penambahan poin tatib, generator dokumen PDF Surat Peringatan (SP 1, SP 2, SP 3) dengan kop resmi SDN Kebonagung dan QR Code tanda tangan digital HMAC-SHA256, serta modul bank prestasi.
3. Sub-agent `be-worker-portal`: Implementasikan integrasi Redis Queue untuk pengiriman notifikasi WhatsApp otomatis ke wali murid, alur pengajuan izin/sakit mandiri orang tua, dan API Master GTK (eksklusif Super Admin).
4. Sub-agent `be-worker-whistle`: Implementasikan modul aduan anti-bullying terenkripsi AES-256-GCM, fitur whistleblower anonim, ruang obrolan investigasi dua arah, dan append-only logger ISO 27001 A.12.4.
```

### 🔹 Fase 3: Fan-Out Implementasi Frontend Squad (3 Workers Paralel)
```markdown
Luncurkan 3 sub-agent frontend paralel sekaligus (workspace: "share"):
1. Sub-agent `fe-worker-scanner`: Bangun antarmuka Livewire Meja Scanner Gerbang (/scanner) dengan penanganan burst input barcode scanner laser USB, simulasi jam (06:45, 07:10, 07:25, 07:45), umpan balik suara Web Audio API (Beep 880Hz / Buzz 180Hz), verifikasi Gate Pass satpam, serta modul generator cetak kartu pelajar PVC ISO CR-80 (preview bolak-balik & tombol revoke).
2. Sub-agent `fe-worker-academic`: Bangun halaman Presensi Kehadiran (/presensi) dengan tab Presensi Cepat H/S/I/A/T rombel, tab Presensi KBM Jam Mapel (dengan tombol merah 'Lapor Membolos ke BK'), modal pengajuan izin sakit ortu, dan tabel kedisiplinan tatib.
3. Sub-agent `fe-worker-admin`: Bangun halaman Welcome Page edukasi (karakter Guru PNS khaki, lencana SDN Kebonagung), Dashboard Eksekutif bergaya GitHub (Commit Bar hash 34acc33, KPI metrik, sidebar kanan About/Releases/Deployments/Data Scope), modul Master GTK (CRUD lengkap Admin), Manajemen User Scopes, dan Backup Snapshot ISO 27001.
```

### 🔹 Fase 4: Fan-Out QA Assault & Security Audit (4 QA Workers)
```markdown
Luncurkan 4 sub-agent pengujian paralel untuk memverifikasi keandalan sistem:
1. Sub-agent `qa-worker-scanner-load`: Tembakkan 50 request scan barcode gerbang paralel serentak pada beban jam masuk sekolah (06:55 WIB). Pastikan seluruh request diproses tanpa deadlock dan rata-rata latensi <= 200 milidetik.
2. Sub-agent `qa-worker-rbac-idor`: Uji penetrasi otorisasi (Pen-Test): Coba akses data siswa kelas lain menggunakan sesi Wali Kelas X-RPL-1, dan coba akses biodata siswa lain menggunakan sesi Orang Tua Ahmad Rifai. Pastikan seluruh query tertolak 403 Forbidden!
3. Sub-agent `qa-worker-e2e-flow`: Uji siklus operasional utuh: Siswa tap gerbang -> Hadir di gerbang tapi membolos di mapel KBM -> Guru mapel lapor bolos -> Poin bertambah -> Terbit SP-1 -> Alert WA terkirim ke orang tua -> Tercatat di Log Audit ISO 27001.
4. Sub-agent `security-audit-agent`: Audit kode terhadap standar OWASP Top 10:2026 (Argon2id, SQL Injection prevention, XSS escaping, enkripsi kolom AES-256, dan imutabilitas tabel audit_logs).
```

### 🔹 Fase 5: Pengemasan Infrastruktur & Deployment Ganda
```markdown
Panggil sub-agent `devops-deploy-agent`:
"Siapkan berkas konfigurasi deployment untuk 2 target infrastruktur:
 1. Opsi 1 (aaPanel Hosting Linux): Berkas konfigurasi Nginx Reverse Proxy (SSL Let's Encrypt, HTTP/2, FastCGI caching), konfigurasi Supervisor Worker untuk Redis Queue, dan skrip Cron tab harian (Auto-Alpa 08:00 WIB & Encrypted Backup S3 02:00 WIB).
 2. Opsi 2 (Bare VPS Ubuntu / Debian & Docker): Berkas `docker-compose.yml` multi-container (Nginx Alpine, PHP 8.3 FPM, PostgreSQL 16, Redis 7, Cron Backup) dan skrip otomatis `deploy/setup-vps.sh`.
 Uji seluruh konfigurasi agar siap deploy dalam sekali klik di kedua skenario."
```

---

## 🚦 3. Checklist Kriteria Gerbang Eksekusi (Quality Gate Checklist)

Sebelum merilis perubahan ke branch utama (*production*), pastikan seluruh gerbang berikut berstatus **PASS**:

- [ ] **Gate 1 (DDL & Schema)**: Migrasi 21 tabel PostgreSQL 16 dan seeder data SDN Kebonagung berhasil dieksekusi 100% tanpa error.
- [ ] **Gate 2 (Scanner Latency)**: Latensi endpoint `POST /api/attendance/gate-scan` berada di bawah $\le 200\text{ ms}$ pada pengujian beban.
- [ ] **Gate 3 (Anti-Double Tap Cooldown)**: Pemindaian kartu yang sama dalam jendela waktu $< 5\text{ menit}$ ditolak secara presisi dengan pesan peringatan yang sesuai.
- [ ] **Gate 4 (Isolasi Data Sisi Server)**: Akun Orang Tua dan Siswa terbukti tidak dapat mengakses data di luar cakupannya (*Zero IDOR Leakage*).
- [ ] **Gate 5 (Kepatuhan Enkripsi ISO 27001)**: Kolom isi aduan dan pesan tersimpan dalam bentuk *cipher text* AES-256 di basis data, dan tabel `audit_logs` terlindungi secara kekal (*immutable*).
- [ ] **Gate 6 (Otomatisasi SP & Kartu CR-80)**: Generator SP format PDF ber-QR Code verifikasi dan pratinjau kartu PVC ISO CR-80 berfungsi sempurna.
- [ ] **Gate 7 (Dual Deployment Ready)**: Konfigurasi aaPanel dan Docker Compose terverifikasi siap melayani domain produksi sekolah.

---

## 🤝 4. Panduan Estafet & Format Pelaporan Handover

Setiap sub-agent wajib menyerahkan hasil pekerjaannya kepada **Lead Orchestrator** menggunakan format terstruktur:

```markdown
### 📦 LAPORAN HANDOVER SUB-AGENT
- **Nama Sub-Agent**: [Nama Worker, misal: be-worker-attendance]
- **ID Kontrak Handover**: [H-01 s/d H-08]
- **Status**: [SELESAI / 100% PASS]
- **Worktree**: [Nama Worktree]

1. Berkas Dikerjakan:
   - [Daftar file yang dimodifikasi / dibuat]
2. Kepatuhan SSOT & Guardrails:
   - [x] Tidak menyentuh file di luar batas wilayah yang diizinkan
   - [x] Sesuai token 02_DESIGN_SYSTEM.md dan skema 03_ARSITEKTUR.md
3. Hasil Exit Gate Command:
   - [Hasil eksekusi test command / build command]
4. Catatan Integrasi:
   - [Informasi untuk worker berikutnya atau instruksi penggabungan]
```