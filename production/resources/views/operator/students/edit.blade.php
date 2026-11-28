@extends('layouts.app')

@section('title', 'Edit & Mutasi Siswa - Operator')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('operator.students.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition shadow-xs">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Data & Mutasi Rombel Siswa</h1>
                <p class="text-xs text-slate-500 mt-0.5">Memperbarui data siswa: <span class="font-bold text-slate-800">{{ $student->name }}</span> (NISN: {{ $student->nisn }})</p>
            </div>
        </div>

        <span class="px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-xs rounded-xl">
            Rombel Saat Ini: Kelas {{ $student->classroom->code }}
        </span>
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

        <form action="{{ route('operator.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Mutasi Rombel Kelas -->
            <div class="p-4 bg-indigo-50/60 border border-indigo-200 rounded-2xl">
                <div class="flex items-center gap-2 mb-2 text-xs font-bold text-indigo-900 uppercase tracking-wider">
                    <i class="bi bi-arrow-left-right text-indigo-600"></i>
                    <span>Pengaturan Rombongan Belajar (Mutasi Kelas)</span>
                </div>
                <p class="text-xs text-indigo-700 mb-3">
                    Untuk memutasikan siswa ke kelas lain, cukup pilih rombel tujuan di bawah ini. Sistem akan menyinkronkan data kapasitas kelas secara otomatis.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="classroom_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih Rombel Penempatan <span class="text-rose-500">*</span>
                        </label>
                        <select id="classroom_id" name="classroom_id" required class="w-full px-3.5 py-2.5 bg-white border border-indigo-300 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                            @foreach($classrooms as $cls)
                                <option value="{{ $cls->id }}" {{ old('classroom_id', $student->classroom_id) == $cls->id ? 'selected' : '' }}>
                                    Kelas {{ $cls->code }} (Wali: {{ $cls->homeroom_teacher }}) - {{ $cls->total_students }} Siswa
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Status Kesiswaan <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="active" {{ old('status', $student->status) === 'active' ? 'selected' : '' }}>Aktif Belajar</option>
                            <option value="moved" {{ old('status', $student->status) === 'moved' ? 'selected' : '' }}>Mutasi / Pindah Sekolah</option>
                            <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>Lulus / Alumni</option>
                            <option value="inactive" {{ old('status', $student->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Identitas Pokok Siswa -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100 text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <i class="bi bi-person-badge-fill text-rose-600"></i>
                    <span>Data Pokok Kesiswaan (Dapodik)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Upload & Preview Pas Foto Siswa -->
                    <div class="sm:col-span-2 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            <i class="bi bi-camera-fill text-rose-600"></i> Pas Foto Resmi Siswa (Format JPG / PNG / WebP, Maks 2MB)
                        </label>
                        <div class="flex items-center gap-4">
                            <!-- Preview Box -->
                            <div class="w-16 h-20 rounded-xl border border-dashed border-slate-300 bg-white flex items-center justify-center overflow-hidden shadow-2xs flex-shrink-0" id="photoPreviewContainer">
                                @if($student->photo)
                                    <img id="photoPreviewImg" src="{{ asset('storage/' . $student->photo) }}" alt="Pas Foto {{ $student->name }}" class="w-full h-full object-cover">
                                    <i class="bi bi-person text-3xl text-slate-300 hidden" id="photoPreviewPlaceholder"></i>
                                @else
                                    <i class="bi bi-person text-3xl text-slate-300" id="photoPreviewPlaceholder"></i>
                                    <img id="photoPreviewImg" src="" alt="Preview Pas Foto" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp"
                                    onchange="previewStudentPhoto(event)"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 transition cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-1">
                                    @if($student->photo)
                                        <span class="text-emerald-600 font-bold">Pas foto tersimpan di sistem.</span> Pilih file baru jika ingin mengganti.
                                    @else
                                        Pilih pas foto untuk dicetak pada Kartu Pelajar Digital (CR-80) dan pemindai presensi.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- NISN -->
                    <div>
                        <label for="nisn" class="block text-xs font-bold text-slate-700 mb-1.5">
                            NISN (10 Digit Angka) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}" required maxlength="10"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- NIS Sekolah -->
                    <div>
                        <label for="nis" class="block text-xs font-bold text-slate-700 mb-1.5">
                            NIS Sekolah
                        </label>
                        <input type="text" id="nis" name="nis" value="{{ old('nis', $student->nis) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Lengkap Siswa <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select id="gender" name="gender" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-Laki (L)</option>
                            <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="birth_place" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tempat Lahir
                        </label>
                        <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="birth_date" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tanggal Lahir
                        </label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Agama -->
                    <div>
                        <label for="religion" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Agama
                        </label>
                        <select id="religion" name="religion"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="Islam" {{ old('religion', $student->religion ?? 'Islam') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('religion', $student->religion) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('religion', $student->religion) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('religion', $student->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('religion', $student->religion) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('religion', $student->religion) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>

                    <!-- Alamat -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alamat Tempat Tinggal
                        </label>
                        <textarea id="address" name="address" rows="2"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">{{ old('address', $student->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Orang Tua & Kontak Realtime WA -->
            <div>
                <div class="flex items-center gap-2 pb-2 mb-4 border-b border-slate-100 text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <i class="bi bi-people-fill text-emerald-600"></i>
                    <span>Data Orang Tua / Wali & Kontak Notifikasi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Wali -->
                    <div>
                        <label for="guardian_name" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Orang Tua / Wali
                        </label>
                        <input type="text" id="guardian_name" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    </div>

                    <!-- Hubungan -->
                    <div>
                        <label for="guardian_relation" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Hubungan Keluarga
                        </label>
                        <select id="guardian_relation" name="guardian_relation" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            <option value="Ayah Kandung" {{ old('guardian_relation', $student->guardian_relation) == 'Ayah Kandung' ? 'selected' : '' }}>Ayah Kandung</option>
                            <option value="Ibu Kandung" {{ old('guardian_relation', $student->guardian_relation) == 'Ibu Kandung' ? 'selected' : '' }}>Ibu Kandung</option>
                            <option value="Wali / Kerabat" {{ old('guardian_relation', $student->guardian_relation) == 'Wali / Kerabat' ? 'selected' : '' }}>Wali / Kerabat</option>
                        </select>
                    </div>

                    <!-- No WhatsApp -->
                    <div class="sm:col-span-2">
                        <label for="guardian_phone" class="block text-xs font-bold text-slate-700 mb-1.5">
                            No. WhatsApp Notifikasi Gerbang
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                                <i class="bi bi-whatsapp"></i>
                            </span>
                            <input type="text" id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone) }}"
                                class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        </div>
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
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewStudentPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('photoPreviewImg');
                const placeholder = document.getElementById('photoPreviewPlaceholder');
                img.src = e.target.result;
                img.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
