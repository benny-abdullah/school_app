@extends('layouts.app')

@section('title', 'Profil Pengguna - STAR-KIDS')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Profile Banner -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4 sm:gap-6">
            <!-- Avatar Initials -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-700 text-white flex items-center justify-center text-2xl sm:text-3xl font-black shadow-md flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>

            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold mb-1.5
                    @if(Auth::user()->isAdmin()) bg-rose-50 text-rose-700 border border-rose-200
                    @elseif(Auth::user()->isOperator()) bg-indigo-50 text-indigo-700 border border-indigo-200
                    @elseif(Auth::user()->isGuru()) bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif(Auth::user()->isOrangTua()) bg-amber-50 text-amber-700 border border-amber-200
                    @endif">
                    <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                    <span>{{ Auth::user()->role_label }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ Auth::user()->name }}</h1>
                <p class="text-xs text-slate-500 font-mono mt-0.5">
                    @if(Auth::user()->isOrangTua())
                        NISN Siswa: <span class="font-bold text-slate-800">{{ Auth::user()->identifier }}</span>
                    @else
                        NIP/NUPTK: <span class="font-bold text-slate-800">{{ Auth::user()->identifier }}</span>
                    @endif
                    • Status Akun: <span class="text-emerald-600 font-bold">Terverifikasi Aktif</span>
                </p>
            </div>
        </div>

        <div>
            <a href="{{ route('home') }}" class="btn-outline-primary px-4 py-2 text-xs flex items-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Dasbor</span>
            </a>
        </div>
    </div>

    <!-- Main Grid: Info Peran & Pengaturan Akun -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Detail Informasi Resmi Peran (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-xs font-bold text-slate-800 uppercase tracking-wider">
                    <i class="bi bi-info-circle-fill text-rose-600"></i>
                    <span>Informasi Akun & Jabatan Resmi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Instansi -->
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Instansi Sekolah</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">SDN Kebonagung</div>
                        <div class="text-slate-500">Kota Pasuruan, Jawa Timur</div>
                    </div>

                    <!-- Role -->
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Kewenangan / Role</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">{{ Auth::user()->role_label }}</div>
                        <div class="text-slate-500">Akses Sistem Terlindungi (RBAC)</div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase">Periode Masa Tugas</div>
                            <div class="font-bold text-slate-900 mt-0.5">{{ $roleData['periode'] ?? '2024 - 2028' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase">Otoritas Kepemimpinan</div>
                            <div class="font-semibold text-slate-700 mt-0.5">{{ $roleData['wewenang'] ?? '-' }}</div>
                        </div>

                    @elseif(Auth::user()->isOperator())
                        <div class="sm:col-span-2">
                            <div class="text-[11px] font-bold text-slate-400 uppercase">Tugas Pokok & Fungsi</div>
                            <div class="font-semibold text-slate-700 mt-0.5">{{ $roleData['tugas'] ?? '-' }}</div>
                        </div>

                    @elseif(Auth::user()->isGuru())
                        <div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase">Penugasan Wali Kelas</div>
                            <div class="font-bold text-rose-600 text-sm mt-0.5">
                                {{ isset($roleData['classroom']) && $roleData['classroom'] ? 'Kelas ' . $roleData['classroom']->code : 'Guru Mapel / Non-Wali' }}
                            </div>
                            <div class="text-slate-500">
                                {{ isset($roleData['classroom']) && $roleData['classroom'] ? 'Kapasitas: ' . ($roleData['total_students'] ?? 32) . ' Peserta Didik' : 'Pendidik SDN Kebonagung' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-slate-400 uppercase">Mata Pelajaran</div>
                            <div class="font-bold text-slate-900 mt-0.5">Guru Kelas</div>
                        </div>

                    @elseif(Auth::user()->isOrangTua())
                        <div class="sm:col-span-2 p-4 bg-amber-50/60 border border-amber-200 rounded-xl space-y-3">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-800">
                                <i class="bi bi-mortarboard-fill"></i> Data Peserta Didik (Putra / Putri Binaan)
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Nama Siswa</span>
                                    <span class="font-extrabold text-slate-900 text-sm">{{ $roleData['student_name'] }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">NISN Siswa</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $roleData['student_nisn'] }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Rombel</span>
                                    <span class="font-bold text-indigo-700">Kelas {{ $roleData['student_class'] }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Hubungan Keluarga</span>
                                    <span class="font-medium text-slate-700">{{ $roleData['relation'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Alamat / Kontak Default -->
                    <div class="sm:col-span-2 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-slate-500 text-[11px]">
                        <div>Email Terdaftar: <span class="font-mono font-bold text-slate-700">{{ Auth::user()->email ?? 'Belum disetel' }}</span></div>
                        <div>Terakhir Masuk: <span class="font-bold text-slate-700">{{ now('Asia/Jakarta')->translatedFormat('d M Y, H:i') }} WIB</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Perbarui Kontak & Ganti Sandi (1 Col) -->
        <div class="space-y-6">
            <div class="card p-6">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-xs font-bold text-slate-800 uppercase tracking-wider">
                    <i class="bi bi-shield-lock-fill text-indigo-600"></i>
                    <span>Pengaturan Akun & Sandi</span>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- No Telepon / WA -->
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">
                            Nomor WhatsApp / Kontak
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                                <i class="bi bi-whatsapp"></i>
                            </span>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                placeholder="Contoh: 081234567890"
                                class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        </div>
                    </div>

                    <!-- Divider Sandi -->
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-500 uppercase block mb-2">Ubah Kata Sandi (Opsional)</span>
                        
                        <!-- Sandi Saat Ini -->
                        <div class="mb-3">
                            <label for="current_password" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                Kata Sandi Saat Ini
                            </label>
                            <input type="password" id="current_password" name="current_password" placeholder="Masukkan sandi lama"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        </div>

                        <!-- Sandi Baru -->
                        <div class="mb-3">
                            <label for="new_password" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                Kata Sandi Baru
                            </label>
                            <input type="password" id="new_password" name="new_password" placeholder="Minimal 6 karakter"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        </div>

                        <!-- Konfirmasi Sandi Baru -->
                        <div>
                            <label for="new_password_confirmation" class="block text-[11px] font-semibold text-slate-600 mb-1">
                                Konfirmasi Sandi Baru
                            </label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi sandi baru"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn-primary w-full py-2.5 text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="bi bi-save"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
