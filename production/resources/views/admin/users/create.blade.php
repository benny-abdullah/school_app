@extends('layouts.app')

@section('title', 'Tambah Akun Pengguna Baru - Super Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition shadow-xs">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-bold uppercase tracking-wider mb-1">
                    <i class="bi bi-person-plus-fill"></i> Registrasi Akun Pengguna
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Akun Pengguna Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pembuatan akun otentikasi sistem untuk berbagai tingkatan hak akses.</p>
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

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="form-label text-xs font-bold text-slate-800">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso S.Pd" required class="form-input text-xs font-bold">
                </div>

                <div>
                    <label for="role" class="form-label text-xs font-bold text-slate-800">
                        Hak Akses (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" required class="form-input text-xs font-medium" onchange="toggleIdentifierInputs(this.value)">
                        <option value="">-- Pilih Role Pengguna --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Kepala Sekolah (Super Admin)</option>
                        <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator Sekolah & TU</option>
                        <option value="guru/walikelas" {{ old('role') === 'guru/walikelas' ? 'selected' : '' }}>Guru / Wali Kelas</option>
                        <option value="orangtua" {{ old('role') === 'orangtua' ? 'selected' : '' }}>Orang Tua Murid / Siswa</option>
                    </select>
                </div>

                <div>
                    <label for="username" class="form-label text-xs font-bold text-slate-800">
                        Username (Opsional)
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="Contoh: budi_santoso" class="form-input text-xs">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika ingin menggunakan NIP/NISN sebagai username.</p>
                </div>

                <div id="boxNip">
                    <label for="nip_nuptk" class="form-label text-xs font-bold text-slate-800">
                        NIP / NUPTK (Pendidik & Tenaga Kependidikan)
                    </label>
                    <input type="text" name="nip_nuptk" id="nip_nuptk" value="{{ old('nip_nuptk') }}" placeholder="Contoh: 198005122008011005" maxlength="25" class="form-input text-xs font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk login Guru, Operator, dan Admin.</p>
                </div>

                <div id="boxNisn">
                    <label for="nisn" class="form-label text-xs font-bold text-slate-800">
                        NISN Siswa (Untuk Akun Orang Tua)
                    </label>
                    <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" placeholder="Contoh: 0081234567" maxlength="15" class="form-input text-xs font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk login Orang Tua siswa binaan.</p>
                </div>

                <div>
                    <label for="phone" class="form-label text-xs font-bold text-slate-800">
                        Nomor WhatsApp / HP
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" class="form-input text-xs">
                </div>

                <div>
                    <label for="email" class="form-label text-xs font-bold text-slate-800">
                        Alamat Email
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Contoh: user@sdnkebonagung.sch.id" class="form-input text-xs">
                </div>

                <div>
                    <label for="password" class="form-label text-xs font-bold text-slate-800">
                        Kata Sandi Awal (Opsional)
                    </label>
                    <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" class="form-input text-xs">
                    <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, sandi otomatis sama dengan NIP atau NISN.</p>
                </div>

                <div>
                    <label for="status" class="form-label text-xs font-bold text-slate-800">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="form-input text-xs font-medium">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Ditangguhkan (Suspend)</option>
                    </select>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-200 transition flex items-center justify-center gap-2">
                    <i class="bi bi-person-check-fill text-base"></i>
                    <span>Buat Akun Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleIdentifierInputs(role) {
    const boxNip = document.getElementById('boxNip');
    const boxNisn = document.getElementById('boxNisn');
    if (role === 'orangtua') {
        boxNisn.style.display = 'block';
    } else {
        boxNip.style.display = 'block';
    }
}
</script>
@endsection
