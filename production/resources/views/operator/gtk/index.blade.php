@extends('layouts.app')

@section('title', 'Master Data Guru & Pegawai (GTK)')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold mb-2">
                <i class="bi bi-people-fill"></i> Manajemen Tenaga Pendidik & Kependidikan
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Data Guru & Pegawai (GTK)</h1>
            <p class="text-xs text-slate-500 mt-1">
                Direktori resmi Pendidik dan Tenaga Kependidikan SDN Kebonagung, akun sistem, serta penugasan rombel binaan.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('gtk.create') }}" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 shadow-xs">
                <i class="bi bi-person-plus-fill text-sm"></i>
                <span>Tambah GTK Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total GTK</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total_gtk'] }}</div>
            <p class="text-[11px] text-indigo-600 font-semibold mt-0.5">Personil Sekolah</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Wali Kelas</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['total_guru_kelas'] }}</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Rombel 1A s/d 6E</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Aparatur PNS</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-600">{{ $stats['total_pns'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Pegawai Negeri</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Pegawai PPPK</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-purple-600">{{ $stats['total_pppk'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">PPPK Daerah</p>
        </div>

        <div class="card p-4 col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-slate-500 uppercase">GTT / Honorer</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-amber-600">{{ $stats['total_honorer'] }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Tenaga Honorer</p>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Daftar Guru & Tenaga Kependidikan</h2>
                    <p class="text-xs text-slate-500">Kelola akun, NIP/NUPTK, status kepegawaian, dan reset kata sandi</p>
                </div>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('gtk.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIP, NUPTK, email..." class="form-input text-xs w-full">
                </div>

                <div>
                    <select name="employment" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $employmentFilter === 'all' ? 'selected' : '' }}>Semua Status Kepegawaian</option>
                        <option value="PNS" {{ $employmentFilter === 'PNS' ? 'selected' : '' }}>PNS (Pegawai Negeri Sipil)</option>
                        <option value="PPPK" {{ $employmentFilter === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="GTT" {{ $employmentFilter === 'GTT' ? 'selected' : '' }}>GTT (Guru Tidak Tetap)</option>
                        <option value="PTT" {{ $employmentFilter === 'PTT' ? 'selected' : '' }}>PTT (Pegawai Tidak Tetap)</option>
                        <option value="Honorer" {{ $employmentFilter === 'Honorer' ? 'selected' : '' }}>Honorer</option>
                    </select>
                </div>

                <div>
                    <select name="role" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Semua Hak Akses Role</option>
                        <option value="guru/walikelas" {{ $roleFilter === 'guru/walikelas' ? 'selected' : '' }}>Guru / Wali Kelas</option>
                        <option value="operator" {{ $roleFilter === 'operator' ? 'selected' : '' }}>Operator Sekolah / TU</option>
                        <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Kepala Sekolah (Admin)</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select name="status" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status Akun</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <button type="submit" class="btn-secondary px-3 py-2 text-xs flex-shrink-0">
                        <i class="bi bi-search"></i>
                    </button>
                    @if($search || $statusFilter !== 'all' || $employmentFilter !== 'all' || $roleFilter !== 'all')
                        <a href="{{ route('gtk.index') }}" class="p-2 text-slate-400 hover:text-slate-600 text-xs flex-shrink-0" title="Reset Filter">
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
                        <th class="w-12 text-center">Foto</th>
                        <th>Nama GTK & NIP/NUPTK</th>
                        <th>Jabatan & Penugasan</th>
                        <th>Status Kepegawaian</th>
                        <th>Rombel Binaan</th>
                        <th>Kontak / WA</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gtks as $gtk)
                        <tr>
                            <td class="text-center">
                                @if($gtk->avatar_url)
                                    <img src="{{ $gtk->avatar_url }}" alt="{{ $gtk->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs mx-auto">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-xs flex items-center justify-center mx-auto">
                                        {{ strtoupper(substr($gtk->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $gtk->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono flex items-center gap-1.5 mt-0.5">
                                    <span>NIP: {{ $gtk->nip_nuptk ?? '-' }}</span>
                                    @if($gtk->nuptk)
                                        <span class="text-slate-300">•</span>
                                        <span>NUPTK: {{ $gtk->nuptk }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-800 text-xs">
                                    {{ $gtk->position ?? ($gtk->additional_info['jabatan'] ?? 'Guru') }}
                                </div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                                    {{ $gtk->role_label }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $emp = $gtk->employment_status ?? 'PNS';
                                    $badgeClass = match($emp) {
                                        'PNS'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'PPPK'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'GTT'     => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Honorer' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default   => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badgeClass }}">
                                    {{ $emp }}
                                </span>
                            </td>
                            <td>
                                @if($gtk->homeroomClassroom)
                                    <a href="{{ route('classrooms.edit', $gtk->homeroomClassroom->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-extrabold rounded-lg hover:bg-emerald-100 transition">
                                        <i class="bi bi-door-open-fill text-emerald-600"></i>
                                        <span>Wali Kelas {{ $gtk->homeroomClassroom->code }}</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium">Bukan Wali Kelas</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-xs text-slate-700 font-medium flex items-center gap-1">
                                    <i class="bi bi-whatsapp text-emerald-500"></i>
                                    <span>{{ $gtk->phone ?? '-' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400">{{ $gtk->email ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                @if($gtk->status === 'active')
                                    <span class="status-badge status-active">Aktif</span>
                                @else
                                    <span class="status-badge status-inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('gtk.edit', $gtk->id) }}" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 transition" title="Edit Data GTK">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>

                                    <form action="{{ route('gtk.reset-password', $gtk->id) }}" method="POST" class="inline" onsubmit="return confirm('Reset kata sandi akun {{ $gtk->name }} ke default NIP/NUPTK?');">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 transition" title="Reset Password ke NIP">
                                            <i class="bi bi-key-fill text-xs"></i>
                                        </button>
                                    </form>

                                    @if(auth()->id() !== $gtk->id)
                                        <form action="{{ route('gtk.destroy', $gtk->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data GTK {{ $gtk->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus GTK">
                                                <i class="bi bi-trash-fill text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Tidak ada data Guru atau Pegawai yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($gtks->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $gtks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
