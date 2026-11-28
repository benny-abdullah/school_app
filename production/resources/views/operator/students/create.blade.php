@extends('layouts.app')

@section('title', 'Tambah Siswa Baru - Operator')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('operator.students.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition shadow-xs">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Peserta Didik Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Entri data pokok kesiswaan SDN Kebonagung Kota Pasuruan</p>
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

        <form action="{{ route('operator.students.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Identitas Pokok Siswa -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100 text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <i class="bi bi-person-badge-fill text-rose-600"></i>
                    <span>1. Data Pokok Kesiswaan (Dapodik)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- NISN -->
                    <div>
                        <label for="nisn" class="block text-xs font-bold text-slate-700 mb-1.5">
                            NISN (10 Digit Angka) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required maxlength="10" placeholder="Contoh: 0081234567"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        <p class="text-[11px] text-slate-400 mt-1">NISN resmi dari arsip Dapodik Kemdikbud.</p>
                    </div>

                    <!-- NIS Sekolah -->
                    <div>
                        <label for="nis" class="block text-xs font-bold text-slate-700 mb-1.5">
                            NIS Sekolah (Buku Induk)
                        </label>
                        <input type="text" id="nis" name="nis" value="{{ old('nis') }}" placeholder="Contoh: 202601001"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Lengkap Siswa <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Muhammad Alif Pratama"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select id="gender" name="gender" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-Laki (L)</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>

                    <!-- Rombel Kelas -->
                    <div>
                        <label for="classroom_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Penempatan Rombel Kelas <span class="text-rose-500">*</span>
                        </label>
                        <select id="classroom_id" name="classroom_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="">-- Pilih Rombel (1A s/d 6E) --</option>
                            @foreach($classrooms as $cls)
                                <option value="{{ $cls->id }}" {{ old('classroom_id') == $cls->id ? 'selected' : '' }}>
                                    Kelas {{ $cls->code }} (Wali: {{ $cls->homeroom_teacher }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="birth_place" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tempat Lahir
                        </label>
                        <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', 'Pasuruan') }}" placeholder="Contoh: Pasuruan"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="birth_date" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tanggal Lahir
                        </label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Alamat -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alamat Tempat Tinggal
                        </label>
                        <textarea id="address" name="address" rows="2" placeholder="Nama jalan, RT/RW, Kelurahan..."
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Orang Tua & Kontak Realtime WA -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100 text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <i class="bi bi-people-fill text-emerald-600"></i>
                    <span>2. Data Orang Tua / Wali & Kontak Notifikasi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Wali -->
                    <div>
                        <label for="guardian_name" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Orang Tua / Wali
                        </label>
                        <input type="text" id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Contoh: Bpk. Bambang Prasetyo"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Hubungan -->
                    <div>
                        <label for="guardian_relation" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Hubungan Keluarga
                        </label>
                        <select id="guardian_relation" name="guardian_relation" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="Ayah Kandung" {{ old('guardian_relation') == 'Ayah Kandung' ? 'selected' : '' }}>Ayah Kandung</option>
                            <option value="Ibu Kandung" {{ old('guardian_relation') == 'Ibu Kandung' ? 'selected' : '' }}>Ibu Kandung</option>
                            <option value="Wali / Kerabat" {{ old('guardian_relation') == 'Wali / Kerabat' ? 'selected' : '' }}>Wali / Kerabat</option>
                        </select>
                    </div>

                    <!-- No WhatsApp -->
                    <div>
                        <label for="guardian_phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                            No. WhatsApp Notifikasi Gerbang
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                                <i class="bi bi-whatsapp"></i>
                            </span>
                            <input type="text" id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="Contoh: 081234567890"
                                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk kirim laporan tap presensi otomatis.</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Status Kesiswaan <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif Belajar</option>
                            <option value="moved" {{ old('status') === 'moved' ? 'selected' : '' }}>Mutasi / Pindah</option>
                            <option value="graduated" {{ old('status') === 'graduated' ? 'selected' : '' }}>Lulus / Alumni</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('operator.students.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-6 py-2.5 text-xs font-bold flex items-center gap-2 cursor-pointer shadow-md">
                    <i class="bi bi-check-lg text-base"></i>
                    <span>Simpan Data Siswa</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
