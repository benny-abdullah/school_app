@extends('layouts.app')

@section('title', 'Tambah Data Guru & Pegawai (GTK)')

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
                    <i class="bi bi-person-plus-fill"></i> Tambah Personil GTK Baru
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Tenaga Pendidik & Kependidikan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Mendaftarkan data guru atau staf tata usaha baru serta membuatkan akun login sistem.</p>
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

        <form action="{{ route('gtk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Notice Akun Otomatis -->
            <div class="p-4 bg-indigo-50/70 border border-indigo-200 rounded-2xl flex items-start gap-3">
                <i class="bi bi-shield-check text-indigo-600 text-lg flex-shrink-0 mt-0.5"></i>
                <div class="text-xs text-indigo-900 leading-relaxed">
                    <span class="font-bold">Otomatisasi Akun Sistem:</span>
                    Saat data GTK disimpan, sistem akan secara otomatis membuatkan akun login. Guru/Pegawai dapat login menggunakan <strong>NIP</strong> atau <strong>NUPTK</strong> yang didaftarkan, dengan kata sandi awal sama dengan NIP/NUPTK tersebut.
                </div>
            </div>

            <!-- Upload Pas Foto GTK -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                <label class="form-label text-xs font-bold text-slate-800 mb-2 block">
                    Pas Foto Resmi GTK (Opsional)
                </label>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative w-20 h-24 bg-white border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
                        <img id="gtkPhotoPreview" class="w-full h-full object-cover hidden" alt="Pratinjau Foto">
                        <div id="gtkPhotoPlaceholder" class="text-center p-2 text-slate-400">
                            <i class="bi bi-person-bounding-box text-2xl"></i>
                            <span class="block text-[9px] mt-1">3x4 Resmi</span>
                        </div>
                    </div>
                    <div class="space-y-1.5 w-full">
                        <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp" class="form-input text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" onchange="previewGtkPhoto(event)">
                        <p class="text-[11px] text-slate-500">Format file: JPG, PNG, atau WebP. Ukuran berkas maksimal 2 MB.</p>
                    </div>
                </div>
            </div>

            <!-- Biodata GTK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="form-label text-xs font-bold text-slate-800">
                        Nama Lengkap & Gelar Akademik <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Sri Rahayu S.Pd.SD" required class="form-input text-xs font-bold">
                    <p class="text-[11px] text-slate-500 mt-1">Tulis nama lengkap beserta gelar depan dan belakang sesuai SK/Ijazah.</p>
                </div>

                <div>
                    <label for="nip_nuptk" class="form-label text-xs font-bold text-slate-800">
                        NIP (Nomor Induk Pegawai - 18 Digit)
                    </label>
                    <input type="text" name="nip_nuptk" id="nip_nuptk" value="{{ old('nip_nuptk') }}" placeholder="Contoh: 198005122008012015" maxlength="20" class="form-input text-xs font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Wajib bagi PNS/PPPK. Digunakan sebagai username & password awal.</p>
                </div>

                <div>
                    <label for="nuptk" class="form-label text-xs font-bold text-slate-800">
                        NUPTK (16 Digit)
                    </label>
                    <input type="text" name="nuptk" id="nuptk" value="{{ old('nuptk') }}" placeholder="Contoh: 8452760662210033" maxlength="20" class="form-input text-xs font-mono">
                    <p class="text-[11px] text-slate-500 mt-1">Nomor Unik Pendidik dan Tenaga Kependidikan dari Kemendikbud.</p>
                </div>

                <div>
                    <label for="gender" class="form-label text-xs font-bold text-slate-800">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="gender" id="gender" required class="form-input text-xs font-medium">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="employment_status" class="form-label text-xs font-bold text-slate-800">
                        Status Kepegawaian <span class="text-rose-500">*</span>
                    </label>
                    <select name="employment_status" id="employment_status" required class="form-input text-xs font-medium">
                        <option value="">-- Pilih Status Kepegawaian --</option>
                        <option value="PNS" {{ old('employment_status') === 'PNS' ? 'selected' : '' }}>PNS (Pegawai Negeri Sipil)</option>
                        <option value="PPPK" {{ old('employment_status') === 'PPPK' ? 'selected' : '' }}>PPPK (Pegawai Pemerintah Perjanjian Kerja)</option>
                        <option value="GTT" {{ old('employment_status') === 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap)</option>
                        <option value="PTT" {{ old('employment_status') === 'PTT' ? 'selected' : '' }}>PTT (Pegawai Tidak Tetap)</option>
                        <option value="Honorer" {{ old('employment_status') === 'Honorer' ? 'selected' : '' }}>Honorer Sekolah</option>
                    </select>
                </div>

                <div>
                    <label for="position" class="form-label text-xs font-bold text-slate-800">
                        Jabatan Utama <span class="text-rose-500">*</span>
                    </label>
                    <select name="position" id="position" required class="form-input text-xs font-medium">
                        <option value="">-- Pilih Jabatan Utama --</option>
                        <option value="Guru Kelas" {{ old('position') === 'Guru Kelas' ? 'selected' : '' }}>Guru Kelas (Wali Kelas)</option>
                        <option value="Guru Mapel PAI" {{ old('position') === 'Guru Mapel PAI' ? 'selected' : '' }}>Guru Mapel Pendidikan Agama Islam</option>
                        <option value="Guru Mapel PJOK" {{ old('position') === 'Guru Mapel PJOK' ? 'selected' : '' }}>Guru Mapel PJOK / Olahraga</option>
                        <option value="Guru Mapel Bahasa Inggris" {{ old('position') === 'Guru Mapel Bahasa Inggris' ? 'selected' : '' }}>Guru Mapel Bahasa Inggris</option>
                        <option value="Operator Dapodik & TU" {{ old('position') === 'Operator Dapodik & TU' ? 'selected' : '' }}>Operator Dapodik & Tata Usaha</option>
                        <option value="Tenaga Perpustakaan" {{ old('position') === 'Tenaga Perpustakaan' ? 'selected' : '' }}>Tenaga Administrasi Perpustakaan</option>
                        <option value="Penjaga Sekolah" {{ old('position') === 'Penjaga Sekolah' ? 'selected' : '' }}>Penjaga Sekolah / Keamanan</option>
                        <option value="Kepala Sekolah" {{ old('position') === 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                    </select>
                </div>

                <div>
                    <label for="role" class="form-label text-xs font-bold text-slate-800">
                        Hak Akses Sistem (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" required class="form-input text-xs font-medium">
                        <option value="guru/walikelas" {{ old('role', 'guru/walikelas') === 'guru/walikelas' ? 'selected' : '' }}>Guru / Wali Kelas (Akses Dasbor KBM)</option>
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator Sekolah (Akses Penuh Master Data)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Kepala Sekolah / Admin Eksekutif</option>
                    </select>
                </div>

                <div>
                    <label for="phone" class="form-label text-xs font-bold text-slate-800">
                        Nomor Kontak WhatsApp / HP
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" class="form-input text-xs">
                    <p class="text-[11px] text-slate-500 mt-1">Nomor aktif untuk komunikasi dinas dan notifikasi sistem.</p>
                </div>

                <div>
                    <label for="email" class="form-label text-xs font-bold text-slate-800">
                        Alamat Email Resmi (Opsional)
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Contoh: guru@sdnkebonagung.sch.id" class="form-input text-xs">
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('gtk.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-200 transition flex items-center justify-center gap-2">
                    <i class="bi bi-person-check-fill text-base"></i>
                    <span>Simpan & Buat Akun GTK</span>
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
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
