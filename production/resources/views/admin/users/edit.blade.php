@extends('layouts.app')

@section('title', 'Edit Akun Pengguna - ' . $user->name)

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
                    <i class="bi bi-pencil-square"></i> Edit Akun Sistem
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Pengguna: {{ $user->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Memperbarui hak akses, biodata akun, kontak, serta pengaturan kata sandi.</p>
            </div>
        </div>

        <span class="px-3 py-1 bg-slate-100 border border-slate-200 text-slate-700 font-extrabold text-xs rounded-xl font-mono">
            ID: #{{ $user->id }}
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

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="form-label text-xs font-bold text-slate-800">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="form-input text-xs font-bold">
                </div>

                <div>
                    <label for="role" class="form-label text-xs font-bold text-slate-800">
                        Hak Akses (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" required class="form-input text-xs font-medium" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Kepala Sekolah (Super Admin)</option>
                        <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>Operator Sekolah & TU</option>
                        <option value="guru/walikelas" {{ old('role', $user->role) === 'guru/walikelas' ? 'selected' : '' }}>Guru / Wali Kelas</option>
                        <option value="orangtua" {{ old('role', $user->role) === 'orangtua' ? 'selected' : '' }}>Orang Tua Murid / Siswa</option>
                    </select>
                    @if(auth()->id() === $user->id)
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="text-[10px] text-amber-600 mt-1">Anda tidak dapat mengubah role akun Anda sendiri.</p>
                    @endif
                </div>

                <div>
                    <label for="username" class="form-label text-xs font-bold text-slate-800">
                        Username
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="form-input text-xs">
                </div>

                <div>
                    <label for="nip_nuptk" class="form-label text-xs font-bold text-slate-800">
                        NIP / NUPTK (Guru / Operator / Admin)
                    </label>
                    <input type="text" name="nip_nuptk" id="nip_nuptk" value="{{ old('nip_nuptk', $user->nip_nuptk) }}" maxlength="25" class="form-input text-xs font-mono">
                </div>

                <div>
                    <label for="nisn" class="form-label text-xs font-bold text-slate-800">
                        NISN Siswa (Akun Orang Tua)
                    </label>
                    <input type="text" name="nisn" id="nisn" value="{{ old('nisn', $user->nisn) }}" maxlength="15" class="form-input text-xs font-mono">
                </div>

                <div>
                    <label for="phone" class="form-label text-xs font-bold text-slate-800">
                        Nomor WhatsApp / HP
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="form-input text-xs">
                </div>

                <div>
                    <label for="email" class="form-label text-xs font-bold text-slate-800">
                        Alamat Email
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-input text-xs">
                </div>

                <div>
                    <label for="password" class="form-label text-xs font-bold text-slate-800">
                        Ganti Kata Sandi (Opsional)
                    </label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak diubah" class="form-input text-xs">
                    <p class="text-[10px] text-slate-400 mt-1">Isi hanya jika ingin menetapkan kata sandi baru secara manual.</p>
                </div>

                <div>
                    <label for="status" class="form-label text-xs font-bold text-slate-800">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="form-input text-xs font-medium" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Ditangguhkan (Suspend)</option>
                    </select>
                    @if(auth()->id() === $user->id)
                        <input type="hidden" name="status" value="{{ $user->status }}">
                        <p class="text-[10px] text-amber-600 mt-1">Anda tidak dapat menonaktifkan akun Anda sendiri.</p>
                    @endif
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-200 transition flex items-center justify-center gap-2">
                    <i class="bi bi-check2-circle text-base"></i>
                    <span>Simpan Perubahan Akun</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
