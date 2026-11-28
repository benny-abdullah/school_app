@extends('layouts.app')

@section('title', 'Manajemen Pengguna Sistem (User Management)')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold mb-2">
                <i class="bi bi-shield-lock-fill"></i> Otoritas Khusus Super Admin / Kepala Sekolah
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Akun Pengguna</h1>
            <p class="text-xs text-slate-500 mt-1">
                Tata kelola seluruh akun pengguna STAR-KIDS: Kepala Sekolah, Operator TU, Guru/Wali Kelas, dan Orang Tua Siswa.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 shadow-xs">
                <i class="bi bi-person-plus-fill text-sm"></i>
                <span>Tambah Pengguna Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Pengguna</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total'] }}</div>
            <p class="text-[11px] text-indigo-600 font-semibold mt-0.5">Semua Role</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Super Admin</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-rose-600">{{ $stats['admin'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Kepala Sekolah</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Operator TU</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-600">{{ $stats['operator'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Tata Usaha & Dapodik</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Guru / Wali Kelas</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['guru'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Pendidik 29 Rombel</p>
        </div>

        <div class="card p-4 col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Orang Tua Siswa</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $stats['ortu'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Wali Murid Terdaftar</p>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Direktori Akun Terdaftar</h2>
                    <p class="text-xs text-slate-500">Suspend akun, reset kata sandi, dan mutasi hak akses sistem</p>
                </div>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, username, NIP, NISN, email..." class="form-input text-xs w-full">
                </div>

                <div>
                    <select name="role" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Semua Hak Akses Role</option>
                        <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Kepala Sekolah (Super Admin)</option>
                        <option value="operator" {{ $roleFilter === 'operator' ? 'selected' : '' }}>Operator Sekolah / TU</option>
                        <option value="guru/walikelas" {{ $roleFilter === 'guru/walikelas' ? 'selected' : '' }}>Guru / Wali Kelas</option>
                        <option value="orangtua" {{ $roleFilter === 'orangtua' ? 'selected' : '' }}>Orang Tua Murid</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select name="status" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Ditangguhkan (Suspend)</option>
                    </select>
                    <button type="submit" class="btn-secondary px-3 py-2 text-xs flex-shrink-0">
                        <i class="bi bi-search"></i>
                    </button>
                    @if($search || $roleFilter !== 'all' || $statusFilter !== 'all')
                        <a href="{{ route('admin.users.index') }}" class="p-2 text-slate-400 hover:text-slate-600 text-xs flex-shrink-0" title="Reset Filter">
                            <i class="bi bi-x-circle-fill text-base"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Pengenal (NIP / NISN)</th>
                        <th>Role Hak Akses</th>
                        <th>Kontak & Email</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-extrabold text-xs flex items-center justify-center flex-shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                            <span>{{ $u->name }}</span>
                                            @if(auth()->id() === $u->id)
                                                <span class="text-[9px] bg-rose-100 text-rose-700 font-extrabold px-1.5 py-0.2 rounded-full">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">User ID: #{{ $u->id }} • @ {{ $u->username ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($u->role === 'orangtua')
                                    <span class="font-mono text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200">
                                        NISN: {{ $u->nisn ?? '-' }}
                                    </span>
                                @else
                                    <span class="font-mono text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-200">
                                        NIP: {{ $u->nip_nuptk ?? '-' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $roleBadge = match($u->role) {
                                        'admin'          => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'operator'       => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'guru/walikelas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'orangtua'       => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default          => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $roleBadge }}">
                                    {{ $u->role_label }}
                                </span>
                            </td>
                            <td>
                                <div class="text-xs text-slate-700 font-medium">{{ $u->phone ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $u->email ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                @if($u->status === 'active')
                                    <span class="status-badge status-active">Aktif</span>
                                @else
                                    <span class="status-badge status-inactive">Suspend</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 transition" title="Edit Akun">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>

                                    @if(auth()->id() !== $u->id)
                                        <!-- Toggle Status (Suspend / Aktifkan) -->
                                        <form action="{{ route('admin.users.toggle-status', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Ubah status aktif akun {{ $u->name }}?');">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg border {{ $u->status === 'active' ? 'border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700' : 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }} transition" title="{{ $u->status === 'active' ? 'Suspend Akun' : 'Aktifkan Akun' }}">
                                                <i class="bi {{ $u->status === 'active' ? 'bi-slash-circle-fill' : 'bi-check-circle-fill' }} text-xs"></i>
                                            </button>
                                        </form>

                                        <!-- Reset Password -->
                                        <form action="{{ route('admin.users.reset-password', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Reset kata sandi akun {{ $u->name }} ke default (NIP/NISN)?');">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition" title="Reset Password ke NIP/NISN">
                                                <i class="bi bi-key-fill text-xs"></i>
                                            </button>
                                        </form>

                                        <!-- Delete User -->
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }} secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus Akun">
                                                <i class="bi bi-trash-fill text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                Tidak ada akun pengguna yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
