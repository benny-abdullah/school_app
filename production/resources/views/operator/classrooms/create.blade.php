@extends('layouts.app')

@section('title', 'Tambah Rombel Kelas Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('classrooms.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition shadow-xs">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                    <i class="bi bi-plus-circle-fill"></i> Registrasi Rombongan Belajar Baru
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Rombel Kelas Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Mendaftarkan rombel kelas baru ke struktur akademik SDN Kebonagung.</p>
            </div>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="card p-6 sm:p-8">
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs">
                <div class="font-bold flex items-center gap-1.5 mb-1.5">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600 text-sm"></i> Mohon Perbaiki Isian Formulir:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('classrooms.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- SECTION 1: IDENTITAS TINGKAT & KODE ROMBEL -->
            <div class="p-5 bg-gradient-to-br from-indigo-50/70 via-white to-indigo-50/30 border border-indigo-200 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold text-indigo-950 uppercase tracking-wider">
                        <i class="bi bi-diagram-3-fill text-indigo-600 text-base"></i>
                        <span>1. Struktur Identitas & Tingkat Kelas</span>
                    </div>
                    <span class="text-[10px] bg-indigo-100 text-indigo-800 font-bold px-2 py-0.5 rounded-full border border-indigo-200">
                        Otomatisasi Kode Kelas
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="grade_level" class="form-label text-xs font-bold text-slate-800">
                            Tingkat Kelas <span class="text-rose-500">*</span>
                        </label>
                        <select name="grade_level" id="grade_level" required class="form-input text-xs font-bold" onchange="updateClassPreview()">
                            <option value="">-- Pilih Tingkat Kelas --</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ old('grade_level') == $i ? 'selected' : '' }}>Kelas {{ $i }} (Sekolah Dasar)</option>
                            @endfor
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Pilih tingkat kelas dari 1 sampai dengan 6.</p>
                    </div>

                    <div>
                        <label for="section" class="form-label text-xs font-bold text-slate-800">
                            Huruf Seksi / Sub-Kelas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="section" id="section" value="{{ old('section') }}" placeholder="Contoh: E, F, atau A" maxlength="5" required class="form-input text-xs font-bold uppercase" oninput="updateClassPreview()">
                        <p class="text-[11px] text-slate-500 mt-1">Huruf seksi rombel (misal: E untuk Kelas 1E).</p>
                    </div>

                    <!-- Live Preview Box -->
                    <div class="sm:col-span-2 p-3.5 bg-white border border-indigo-200 rounded-xl flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-xs font-mono" id="previewBadge">
                                -
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-slate-900" id="previewTitle">Nama Rombel: (Pilih tingkat & seksi)</div>
                                <div class="text-[11px] text-indigo-600 font-mono" id="previewSubtitle">Kode Unik: -</div>
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-semibold hidden sm:inline">Pratinjau Otomatis</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PENETAPAN SK WALI KELAS -->
            <div class="p-5 bg-slate-50/80 border border-slate-200 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-900 uppercase tracking-wider">
                        <i class="bi bi-person-badge-fill text-indigo-600 text-base"></i>
                        <span>2. Penetapan SK Guru Wali Kelas (Opsional)</span>
                    </div>
                    <span class="text-[10px] bg-slate-200 text-slate-700 font-bold px-2 py-0.5 rounded-full">
                        Dapat Diatur Kemudian
                    </span>
                </div>

                <div class="p-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 flex items-start gap-2.5">
                    <i class="bi bi-info-circle-fill text-indigo-600 text-sm flex-shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold">Proteksi Anti-Bentrok:</span>
                        Satu guru hanya boleh memegang 1 rombel. Jika Anda memilih guru yang saat ini sedang memegang rombel lain, guru tersebut akan dimutasikan ke rombel ini secara otomatis.
                    </div>
                </div>

                <div>
                    <label for="homeroom_teacher_id" class="form-label text-xs font-bold text-slate-800">
                        Pilih Guru Wali Kelas Resmi
                    </label>
                    <select name="homeroom_teacher_id" id="homeroom_teacher_id" class="form-input text-xs font-medium">
                        <option value="">-- Belum Ditugaskan / Kosongkan Posisi Wali Kelas --</option>
                        @foreach($teachers as $teacher)
                            @php
                                $assignedClass = $assignedClasses[$teacher->id] ?? null;
                            @endphp
                            <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }} (NIP: {{ $teacher->nip_nuptk }})
                                @if($assignedClass)
                                    — [Saat ini Wali Kelas {{ $assignedClass }} - Akan Dimutasi]
                                @else
                                    — [Tersedia / Belum Memegang Kelas]
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Daftar memuat seluruh guru aktif ber-NIP resmi di SDN Kebonagung.</p>
                </div>
            </div>

            <!-- SECTION 3: SARANA, KAPASITAS & AKADEMIK -->
            <div class="p-5 bg-slate-50/80 border border-slate-200 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-900 uppercase tracking-wider">
                        <i class="bi bi-building text-rose-600 text-base"></i>
                        <span>3. Konfigurasi Sarana Ruangan & Daya Tampung</span>
                    </div>
                    <span class="text-[10px] bg-slate-200 text-slate-700 font-bold px-2 py-0.5 rounded-full">
                        Standar Dapodik
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="capacity" class="form-label text-xs font-bold text-slate-800">
                            Daya Tampung / Kapasitas Siswa <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="capacity" id="capacity" min="15" max="40" value="{{ old('capacity', 32) }}" required class="form-input text-xs font-bold">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium">Siswa</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Standar rombel SD adalah 28 - 32 siswa (Maksimal: 40).</p>
                    </div>

                    <div>
                        <label for="room_name" class="form-label text-xs font-bold text-slate-800">
                            Lokasi & Nama Ruang Fisik
                        </label>
                        <input type="text" name="room_name" id="room_name" value="{{ old('room_name') }}" placeholder="Contoh: Gedung C Lt. 2 - Ruang 205" class="form-input text-xs">
                        <p class="text-[11px] text-slate-500 mt-1">Nama gedung atau nomor ruang fisik di SDN Kebonagung.</p>
                    </div>

                    <div>
                        <label for="academic_year" class="form-label text-xs font-bold text-slate-800">
                            Tahun Ajaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', '2025/2026') }}" required class="form-input text-xs font-medium" placeholder="Contoh: 2025/2026">
                    </div>

                    <div>
                        <label for="status" class="form-label text-xs font-bold text-slate-800">
                            Status Operasional Rombel <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required class="form-input text-xs font-medium">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                Aktif (Digunakan PBM dan Penerimaan Siswa)
                            </option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                Nonaktif (Ruang Cadangan / Sedang Dalam Renovasi)
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('classrooms.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-200 transition flex items-center justify-center gap-2">
                    <i class="bi bi-check-circle-fill text-base"></i>
                    <span>Simpan & Daftarkan Rombel</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateClassPreview() {
    const grade = document.getElementById('grade_level').value;
    const section = document.getElementById('section').value.trim().toUpperCase();
    const badge = document.getElementById('previewBadge');
    const title = document.getElementById('previewTitle');
    const subtitle = document.getElementById('previewSubtitle');

    if (grade && section) {
        const code = grade + section;
        badge.innerText = code;
        title.innerText = 'Nama Rombel: Kelas ' + code;
        subtitle.innerText = 'Kode Unik: ' + code;
    } else if (grade) {
        badge.innerText = grade + '?';
        title.innerText = 'Nama Rombel: Kelas ' + grade + ' (Masukkan seksi)';
        subtitle.innerText = 'Kode Unik: ' + grade + '?';
    } else {
        badge.innerText = '-';
        title.innerText = 'Nama Rombel: (Pilih tingkat & seksi)';
        subtitle.innerText = 'Kode Unik: -';
    }
}
</script>
@endsection
