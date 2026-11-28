@extends('layouts.app')

@section('title', 'Kelola Rombel ' . $classroom->code . ' & SK Wali Kelas')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('classrooms.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition shadow-xs">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                    <i class="bi bi-gear-fill"></i> Manajemen Rombel & Kepegawaian
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Kelola Kelas {{ $classroom->code }} — {{ $classroom->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Penetapan SK Guru Wali Kelas dan konfigurasi sarana kapasitas rombel SDN Kebonagung.</p>
            </div>
        </div>

        <div class="hidden sm:flex items-center gap-2">
            <span class="px-3 py-1 bg-slate-100 border border-slate-200 text-slate-700 font-extrabold text-xs rounded-xl font-mono">
                Kode: {{ $classroom->code }}
            </span>
            <span class="px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-xs rounded-xl">
                Tingkat {{ $classroom->grade_level }}
            </span>
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

        <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- SECTION 1: PENETAPAN SK WALI KELAS RESMI -->
            <div class="p-5 bg-gradient-to-br from-indigo-50/70 via-white to-indigo-50/30 border border-indigo-200 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold text-indigo-950 uppercase tracking-wider">
                        <i class="bi bi-person-badge-fill text-indigo-600 text-base"></i>
                        <span>1. Penetapan SK & Rotasi Guru Wali Kelas</span>
                    </div>
                    <span class="text-[10px] bg-indigo-100 text-indigo-800 font-bold px-2 py-0.5 rounded-full border border-indigo-200">
                        Otoritas Kepala Sekolah & TU
                    </span>
                </div>

                <div class="p-3 bg-white/80 border border-indigo-100 rounded-xl text-xs text-indigo-900 leading-relaxed flex items-start gap-2.5">
                    <i class="bi bi-info-circle-fill text-indigo-600 text-sm flex-shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold">Proteksi Anti-Bentrok Penugasan:</span>
                        Setiap guru hanya dapat mengampu 1 rombongan belajar secara aktif. Apabila guru yang Anda pilih saat ini sedang bertugas di rombel lain, sistem secara otomatis akan memutasi guru tersebut ke Kelas {{ $classroom->code }} dan mengosongkan rombel lamanya.
                    </div>
                </div>

                <div>
                    <label for="homeroom_teacher_id" class="form-label text-xs font-bold text-slate-800">
                        Pilih Guru Wali Kelas Resmi <span class="text-rose-500">*</span>
                    </label>
                    <select name="homeroom_teacher_id" id="homeroom_teacher_id" class="form-input text-xs font-medium">
                        <option value="">-- Belum Ditugaskan / Kosongkan Posisi Wali Kelas --</option>
                        @foreach($teachers as $teacher)
                            @php
                                $assignedClass = $assignedClasses[$teacher->id] ?? null;
                                $isCurrent = ($classroom->homeroom_teacher_id == $teacher->id);
                            @endphp
                            <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id', $classroom->homeroom_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }} (NIP: {{ $teacher->nip_nuptk }})
                                @if($isCurrent)
                                    — [Wali Kelas Ini Saat Ini]
                                @elseif($assignedClass)
                                    — [Saat ini Wali Kelas {{ $assignedClass }} - Akan Dimutasi]
                                @else
                                    — [Tersedia / Belum Memegang Kelas]
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Daftar memuat seluruh guru aktif ber-NIP resmi di SDN Kebonagung.</p>
                </div>

                <!-- Preview Wali Kelas Saat Ini -->
                @if($classroom->homeroomTeacher)
                    <div class="p-3.5 bg-white border border-slate-200 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-sm border border-emerald-300">
                                {{ strtoupper(substr($classroom->homeroomTeacher->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-slate-900">{{ $classroom->homeroomTeacher->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">NIP: {{ $classroom->homeroomTeacher->nip_nuptk }} • No. WA: {{ $classroom->homeroomTeacher->phone_number ?? '-' }}</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="bi bi-patch-check-fill"></i> SK Aktif
                        </span>
                    </div>
                @else
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-2 text-xs text-amber-800">
                        <i class="bi bi-exclamation-circle-fill text-amber-600"></i>
                        <span>Rombel ini saat ini <strong>belum memiliki Guru Wali Kelas definitif</strong>. Harap pilih guru pengampu di atas.</span>
                    </div>
                @endif
            </div>

            <!-- SECTION 2: KONFIGURASI SARANA, DAYA TAMPUNG & AKADEMIK -->
            <div class="p-5 bg-slate-50/70 border border-slate-200 rounded-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-900 uppercase tracking-wider">
                        <i class="bi bi-building text-rose-600 text-base"></i>
                        <span>2. Konfigurasi Sarana Ruangan & Daya Tampung</span>
                    </div>
                    <span class="text-[10px] bg-slate-200 text-slate-800 font-bold px-2 py-0.5 rounded-full">
                        Standar Dapodik Kemendikbud
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Nama & Kode Rombel</label>
                        <input type="text" value="{{ $classroom->name }} ({{ $classroom->code }})" disabled class="form-input bg-slate-100 text-slate-500 cursor-not-allowed font-medium text-xs">
                        <p class="text-[10px] text-slate-400 mt-1">Struktur kode rombel 1A-1D & 2A-6E terkunci sesuai kurikulum.</p>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Tingkat Kelas</label>
                        <input type="text" value="Kelas {{ $classroom->grade_level }} (Sekolah Dasar)" disabled class="form-input bg-slate-100 text-slate-500 cursor-not-allowed font-medium text-xs">
                        <p class="text-[10px] text-slate-400 mt-1">Tingkat kelas 1 s.d. 6.</p>
                    </div>

                    <div>
                        <label for="capacity" class="form-label text-xs font-bold text-slate-800">
                            Daya Tampung / Kapasitas Siswa <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="capacity" id="capacity" min="15" max="40" value="{{ old('capacity', $classroom->capacity) }}" required class="form-input text-xs font-bold">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium">Siswa</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Standar rombel SD adalah 28 - 32 siswa (Maksimal sistem: 40).</p>
                    </div>

                    <div>
                        <label class="form-label text-xs font-bold text-slate-700">Siswa Aktif Terdaftar</label>
                        <div class="form-input bg-slate-100 text-slate-800 flex items-center justify-between text-xs font-bold">
                            <span>{{ $classroom->students_count > 0 ? $classroom->students_count : $classroom->total_students }} Siswa Terdaftar</span>
                            <a href="{{ route('operator.students.index', ['classroom_id' => $classroom->id]) }}" class="text-[11px] text-rose-600 hover:underline inline-flex items-center gap-1 font-semibold">
                                <span>Lihat Siswa</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Data siswa terdaftar secara otomatis tersinkron dari Dapodik.</p>
                    </div>

                    <div>
                        <label for="room_name" class="form-label text-xs font-bold text-slate-800">
                            Lokasi & Nama Ruang Fisik
                        </label>
                        <input type="text" name="room_name" id="room_name" value="{{ old('room_name', $classroom->room_name) }}" placeholder="Contoh: Gedung A Lt. 1 - Ruang 101" class="form-input text-xs">
                        <p class="text-[11px] text-slate-500 mt-1">Nama gedung atau nomor ruang kelas fisik di SDN Kebonagung.</p>
                    </div>

                    <div>
                        <label for="academic_year" class="form-label text-xs font-bold text-slate-800">
                            Tahun Ajaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', $classroom->academic_year) }}" required class="form-input text-xs font-medium" placeholder="Contoh: 2025/2026">
                        <p class="text-[11px] text-slate-500 mt-1">Tahun ajaran operasional rombel saat ini.</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="status" class="form-label text-xs font-bold text-slate-800">
                            Status Operasional Rombel <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" class="form-input text-xs font-medium">
                            <option value="active" {{ old('status', $classroom->status) === 'active' ? 'selected' : '' }}>
                                Aktif (Digunakan PBM dan Penerimaan Siswa)
                            </option>
                            <option value="inactive" {{ old('status', $classroom->status) === 'inactive' ? 'selected' : '' }}>
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
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-200 transition flex items-center justify-center gap-2">
                    <i class="bi bi-check2-circle text-base"></i>
                    <span>Simpan Perubahan Rombel & SK Wali</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
