@extends('layouts.app')

@section('title', 'Edit Data GTK - ' . $gtk->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('gtk.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition shadow-xs">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                    <i class="bi bi-pencil-square"></i> Edit Data Kepegawaian
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit GTK: {{ $gtk->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Memperbarui informasi biodata, jabatan, hak akses, serta status kepegawaian.</p>
            </div>
        </div>

        @if($gtk->homeroomClassroom)
            <span class="px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold text-xs rounded-xl flex items-center gap-1.5">
                <i class="bi bi-door-open-fill text-emerald-600"></i> Wali Kelas {{ $gtk->homeroomClassroom->code }}
            </span>
        @endif
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

        <form action="{{ route('gtk.update', $gtk->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Upload Pas Foto GTK -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                <label class="form-label text-xs font-bold text-slate-800 mb-2 block">
                    Pas Foto Resmi GTK
                </label>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative w-20 h-24 bg-white border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
                        @if($gtk->avatar_url)
                            <img id="gtkPhotoPreview" src="{{ $gtk->avatar_url }}" class="w-full h-full object-cover" alt="{{ $gtk->name }}">
                            <div id="gtkPhotoPlaceholder" class="text-center p-2 text-slate-400 hidden">
                                <i class="bi bi-person-bounding-box text-2xl"></i>
                                <span class="block text-[9px] mt-1">3x4 Resmi</span>
                            </div>
                        @else
                            <img id="gtkPhotoPreview" class="w-full h-full object-cover hidden" alt="Pratinjau Foto">
                            <div id="gtkPhotoPlaceholder" class="text-center p-2 text-slate-400">
                                <i class="bi bi-person-bounding-box text-2xl"></i>
                                <span class="block text-[9px] mt-1">3x4 Resmi</span>
                            </div>
                        @endif
                    </div>
                    <div class="space-y-1.5 w-full">
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp" class="form-input text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" onchange="previewGtkPhoto(event)">
                        <p class="text-[11px] text-slate-500">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG/PNG/WebP, maksimal 2 MB.</p>
                    </div>
                </div>
            </div>

            <!-- Biodata GTK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="form-label text-xs font-bold text-slate-800">
                        Nama Lengkap & Gelar Akademik <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $gtk->name) }}" required class="form-input text-xs font-bold">
                    <p class="text-[11px] text-slate-500 mt-1">Tulis nama lengkap beserta gelar depan dan belakang sesuai SK.</p>
                </div>

                <div>
                    <label for="nip_nuptk" class="form-label text-xs font-bold text-slate-800">
                        NIP (Nomor Induk Pegawai - 18 Digit)
                    </label>
                    <input type="text" name="nip_nuptk" id="nip_nuptk" value="{{ old('nip_nuptk', $gtk->nip_nuptk) }}" maxlength="20" class="form-input text-xs font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Digunakan sebagai pengenal utama saat login.</p>
                </div>

                <div>
                    <label for="nuptk" class="form-label text-xs font-bold text-slate-800">
                        NUPTK (16 Digit)
                    </label>
                    <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk', $gtk->nuptk) }}" maxlength="20" class="form-input text-xs font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Nomor Unik Pendidik dan Tenaga Kependidikan.</p>
                </div>

                <div>
                    <label for="gender" class="form-label text-xs font-bold text-slate-800">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="gender" id="gender" required class="form-input text-xs font-medium">
                        <option value="L" {{ old('gender', $gtk->gender) === 'L' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="P" {{ old('gender', $gtk->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="employment_status" class="form-label text-xs font-bold text-slate-800">
                        Status Kepegawaian <span class="text-rose-500">*</span>
                    </label>
                    <select name="employment_status" id="employment_status" required class="form-input text-xs font-medium">
                        <option value="PNS" {{ old('employment_status', $gtk->employment_status) === 'PNS' ? 'selected' : '' }}>PNS (Pegawai Negeri Sipil)</option>
                        <option value="PPPK" {{ old('employment_status', $gtk->employment_status) === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="GTT" {{ old('employment_status', $gtk->employment_status) === 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap)</option>
                        <option value="PTT" {{ old('employment_status', $gtk->employment_status) === 'PTT' ? 'selected' : '' }}>PTT (Pegawai Tidak Tetap)</option>
                        <option value="Honorer" {{ old('employment_status', $gtk->employment_status) === 'Honorer' ? 'selected' : '' }}>Honorer Sekolah</option>
                    </select>
                </div>

                <div>
                    <label for="position" class="form-label text-xs font-bold text-slate-800">
                        Jabatan Utama <span class="text-rose-500">*</span>
                    </label>
                    <select name="position" id="position" required class="form-input text-xs font-medium">
                        <option value="Guru Kelas" {{ old('position', $gtk->position) === 'Guru Kelas' ? 'selected' : '' }}>Guru Kelas (Wali Kelas)</option>
                        <option value="Guru Mapel PAI" {{ old('position', $gtk->position) === 'Guru Mapel PAI' ? 'selected' : '' }}>Guru Mapel Pendidikan Agama Islam</option>
                        <option value="Guru Mapel PJOK" {{ old('position', $gtk->position) === 'Guru Mapel PJOK' ? 'selected' : '' }}>Guru Mapel PJOK / Olahraga</option>
                        <option value="Guru Mapel Bahasa Inggris" {{ old('position', $gtk->position) === 'Guru Mapel Bahasa Inggris' ? 'selected' : '' }}>Guru Mapel Bahasa Inggris</option>
                        <option value="Operator Dapodik & TU" {{ old('position', $gtk->position) === 'Operator Dapodik & TU' ? 'selected' : '' }}>Operator Dapodik & Tata Usaha</option>
                        <option value="Tenaga Perpustakaan" {{ old('position', $gtk->position) === 'Tenaga Perpustakaan' ? 'selected' : '' }}>Tenaga Administrasi Perpustakaan</option>
                        <option value="Penjaga Sekolah" {{ old('position', $gtk->position) === 'Penjaga Sekolah' ? 'selected' : '' }}>Penjaga Sekolah / Keamanan</option>
                        <option value="Kepala Sekolah" {{ old('position', $gtk->position) === 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                    </select>
                </div>

                <div>
                    <label for="role" class="form-label text-xs font-bold text-slate-800">
                        Hak Akses Sistem (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" required class="form-input text-xs font-medium">
                        <option value="guru/walikelas" {{ old('role', $gtk->role) === 'guru/walikelas' ? 'selected' : '' }}>Guru / Wali Kelas</option>
                        <option value="operator" {{ old('role', $gtk->role) === 'operator' ? 'selected' : '' }}>Operator Sekolah / TU</option>
                        <option value="admin" {{ old('role', $gtk->role) === 'admin' ? 'selected' : '' }}>Kepala Sekolah (Admin)</option>
                    </select>
                </div>

                <div>
                    <label for="phone" class="form-label text-xs font-bold text-slate-800">
                        Nomor Kontak WhatsApp / HP
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $gtk->phone) }}" class="form-input text-xs">
                </div>

                <div>
                    <label for="email" class="form-label text-xs font-bold text-slate-800">
                        Alamat Email Resmi
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $gtk->email) }}" class="form-input text-xs">
                </div>

                <div class="sm:col-span-2">
                    <label for="status" class="form-label text-xs font-bold text-slate-800">
                        Status Keaktifan Akun <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="form-input text-xs font-medium">
                        <option value="active" {{ old('status', $gtk->status) === 'active' ? 'selected' : '' }}>Aktif (Dapat Login ke Sistem)</option>
                        <option value="inactive" {{ old('status', $gtk->status) === 'inactive' ? 'selected' : '' }}>Nonaktif / Cuti / Pensiun (Akses Diblokir)</option>
                    </select>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('gtk.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-200 transition flex items-center justify-center gap-2">
                    <i class="bi bi-check2-circle text-base"></i>
                    <span>Simpan Perubahan GTK</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewGtkPhoto(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('gtkPhotoPreview');
    const placeholder = document.getElementById('gtkPhotoPlaceholder');
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
