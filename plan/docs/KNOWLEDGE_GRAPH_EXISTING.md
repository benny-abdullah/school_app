# Knowledge Graph: Existing Codebase (Kondisi Mockup Saat Ini)
**Status**: Eksisting (In-Memory Client-Side SPA) | **Berkas Acuan**: `c:\scholl_app\index.html`  
**Institusi**: SDN Kebonagung Kota Pasuruan | **Framework**: Vue 3 (Composition API) + Tailwind CSS CDN

Dokumen ini memetakan seluruh entitas, ketergantungan modul, variabel reaktif, dan relasi data pada prototipe interaktif yang saat ini berjalan di file [index.html](file:///c:/scholl_app/index.html).

---

## 1. Entitas Inti Eksisting (Nodes)

| Node ID | Kategori | Entitas di `index.html` | Peran dalam Kode Prototipe Saat Ini |
| :--- | :--- | :--- | :--- |
| `IndexHtmlRoot` | UI Shell | `c:\scholl_app\index.html` | Berkas tunggal HTML (6.161 baris) memuat seluruh antarmuka, CSS, SVG Octicons, dan logika Vue. |
| `WelcomeLandingPage` | UI | `div v-if="!isLoggedIn"` | Halaman depan edukasi nuansa *Soft Lavender* dengan ilustrasi 3D Guru ASN/PNS khaki & lencana SDN Kebonagung. |
| `ExecutiveDashboard` | UI | `div v-if="activePage === 'dashboard'"` | Dashboard 2 kolom GitHub dengan Commit Bar (hash `34acc33`), metrik KPI, dan bilah samping About. |
| `AttendanceModule` | UI | `div v-if="activePage === 'presensi'"` | Tab Presensi Harian Cepat (H/S/I/A/T) dan Tab Presensi KBM Jam Mapel (deteksi membolos). |
| `GateScannerModule` | UI | `div v-if="activePage === 'scanner'"` | Tab Live Gate Scanner (simulasi 06:45 s/d 07:45) & Tab Verifikasi Digital Gate Pass satpam. |
| `CardGeneratorModule`| UI | `div v-if="activePage === 'kartu_pelajar'"`| Pratinjau tampak depan & belakang kartu pelajar ISO CR-80, Code 128, dan tombol revoke barcode. |
| `DisciplineModule` | UI | `div v-if="activePage === 'pelanggaran'"` | Rekapitulasi pelanggaran, modal tambah kasus, dan modal pratinjau dokumen resmi SP 1/2/3 ber-QR. |
| `AchievementModule` | UI | `div v-if="activePage === 'prestasi'"` | Rekapitulasi prestasi berjenjang (Sekolah s/d Internasional) dan penambahan poin merit. |
| `WhistleblowerModule`| UI | `div v-if="activePage === 'aduan'"` | Pusat aduan rahasia, formulir aduan anonim, dan modal ruang chat investigasi dua arah. |
| `TeacherStaffModule` | UI | `div v-if="activePage === 'guru_mgmt'"` | Modul Master GTK (Guru & Tenaga Kependidikan) dengan operasi CRUD lengkap (eksklusif Admin). |
| `StudentMgmtModule` | UI | `div v-if="activePage === 'siswa_mgmt'"` | Master Siswa (CRUD, NISN, Rombel, Biodata Ortu) dengan isolasi data scope per peran. |
| `ParentMgmtModule` | UI | `div v-if="activePage === 'ortu_mgmt'"` | Master Wali Murid (CRUD, nomor WA, relasi siswa, tombol kirim undangan portal). |
| `UserMgmtModule` | UI | `div v-if="activePage === 'users'"` | Manajemen akun, dropdown cakupan data dinamis, toggle aktif/nonaktif, dan reset kata sandi. |
| `IsoBackupModule` | UI | `div v-if="activePage === 'backup'"` | Panel strategi redundansi 3-2-1, generator snapshot instan, dan tombol unduh file `.enc`. |
| `AuditLogModule` | UI | `div v-if="activePage === 'audit_log'"` | Penampil riwayat audit trail (Timestamp, User, Role, Action, IP Address). |
| `VueAppInstance` | State | `Vue.createApp({...}).mount('#app')` | Instance Vue 3 mengelola seluruh *reactive state* dan *computed properties* di memori RAM browser. |
| `RoleDataScopeState` | Logic | `computed(() => roleDataScope)` | Logika isolasi data sisi klien yang memfilter data berdasarkan peran (`admin`, `bk`, `wali_kelas`, `orang_tua`, `siswa`). |
| `InMemoryDataStore` | Storage| Reactive Refs di JavaScript | Kumpulan array JavaScript di memori browser (`students`, `teachers`, `parents`, `attendanceList`, `violations`, dll.). |

---

## 2. Relasi Terarah Antar Komponen (Directed Relationships)

```text
VueAppInstance         --[manages_state_of]--------> InMemoryDataStore
VueAppInstance         --[computes_scopes_via]-----> RoleDataScopeState
RoleDataScopeState     --[filters_records_in]------> InMemoryDataStore

WelcomeLandingPage     --[triggers_login_modal]----> VueAppInstance
ExecutiveDashboard     --[renders_metrics_from]----> InMemoryDataStore
AttendanceModule       --[mutates_status_in]-------> InMemoryDataStore (attendanceList)
AttendanceModule       --[triggers_violation_via]--> DisciplineModule (Membolos Jam Pelajaran)
AttendanceModule       --[manages_leave_via]-------> InMemoryDataStore (leaveRequestsList)

GateScannerModule      --[evaluates_late_rules]----> VueAppInstance (executeBarcodeScan)
GateScannerModule      --[updates_records_in]------> InMemoryDataStore (attendanceList & violations)
GateScannerModule      --[verifies_ticket_in]------> InMemoryDataStore (gatePassList)

CardGeneratorModule    --[reads_active_student]----> InMemoryDataStore (students)
CardGeneratorModule    --[revokes_card_in]---------> VueAppInstance (revokeAndRegenerateCard)

DisciplineModule       --[calculates_demerit_to]---> InMemoryDataStore (students.demeritPoints)
DisciplineModule       --[generates_letter_from]---> InMemoryDataStore (violations & warningLetters)
AchievementModule      --[calculates_merit_to]-----> InMemoryDataStore (students.meritPoints)

WhistleblowerModule    --[stores_tickets_in]-------> InMemoryDataStore (complaints)
WhistleblowerModule    --[appends_messages_to]-----> InMemoryDataStore (complaints.messages)

TeacherStaffModule     --[executes_crud_on]--------> InMemoryDataStore (teachers)
StudentMgmtModule      --[executes_crud_on]--------> InMemoryDataStore (students)
ParentMgmtModule       --[executes_crud_on]--------> InMemoryDataStore (parents)
UserMgmtModule         --[executes_crud_on]--------> InMemoryDataStore (usersList)

VueAppInstance         --[logs_every_mutation_to]--> InMemoryDataStore (auditLogs)
IsoBackupModule        --[generates_mock_archive]--> InMemoryDataStore (backupList)
```

---

## 3. Keterbatasan Kritis Arsitektur Eksisting (Current Baseline Bottlenecks)

1. **Penyimpanan *In-Memory* Sementara**: Seluruh perubahan data (presensi baru, penambahan GTK, aduan baru) tersimpan di memori RAM tab browser. Jika halaman di-refresh (*F5*), seluruh data kembali ke kondisi default.
2. **Ketiadaan Konkurensi Multi-Perangkat**: Pos satpam di gerbang sekolah, wali kelas di ruang guru, dan orang tua di rumah tidak dapat saling bertukar data secara *real-time*.
3. **Isolasi Data Hanya di Sisi Klien**: Penyaringan data `roleDataScope` hanya dilakukan melalui *computed properties* JavaScript. Siapapun yang membuka *Developer Tools (F12)* dapat membaca array `students` dan `complaints` secara utuh.
4. **Ketiadaan Enkripsi Nyata**: Isi tiket aduan perundungan tersimpan sebagai teks biasa (*plain text*) di memori JavaScript browser.