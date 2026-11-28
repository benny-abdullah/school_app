@extends('layouts.app')

@section('title', 'Master Data Siswa & Mutasi Rombel')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold mb-2">
                <i class="bi bi-people-fill"></i> Manajemen Kesiswaan Dapodik
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Direktori Siswa</h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola data pokok siswa, mutasi 29 rombongan belajar, dan verifikasi kontak orang tua SDN Kebonagung.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('operator.students.create') }}" class="btn-primary px-4 py-2.5 text-xs flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Siswa Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Terdaftar Aktif</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_active']) }}</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Sinkron Basis Data</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Laki-Laki</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-600">{{ number_format($stats['total_male']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Peserta Didik</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Siswa Perempuan</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-pink-600">{{ number_format($stats['total_female']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Peserta Didik</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Rombel</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-indigo-600">{{ $stats['total_classes'] }} Kelas</div>
            <p class="text-[11px] text-indigo-600 font-semibold mt-0.5">1A-1D & 2A-6E</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar Card -->
    <div class="card p-5">
        <form action="{{ route('operator.students.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Cari Nama / NISN / Wali</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ $filters['search'] }}" 
                        placeholder="Ketik nama siswa atau NISN..." 
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                </div>
            </div>

            <!-- Filter Rombel -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Filter Rombel (29 Kelas)</label>
                <select name="classroom_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    <option value="all">Semua Rombel</option>
                    @foreach($classrooms as $c)
                        <option value="{{ $c->id }}" {{ $filters['classroom_id'] == $c->id ? 'selected' : '' }}>
                            {{ $c->code }} - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Kesiswaan</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                    <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>Aktif Belajar</option>
                    <option value="moved" {{ $filters['status'] == 'moved' ? 'selected' : '' }}>Mutasi / Pindah</option>
                    <option value="graduated" {{ $filters['status'] == 'graduated' ? 'selected' : '' }}>Lulus / Alumni</option>
                    <option value="inactive" {{ $filters['status'] == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary w-full py-2 px-3 text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="bi bi-funnel-fill"></i> Terapkan
                </button>
                @if($filters['search'] || $filters['classroom_id'] !== 'all' || $filters['status'] !== 'active')
                    <a href="{{ route('operator.students.index') }}" title="Reset Filter" class="p-2 border border-slate-200 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-slate-50 transition">
                        <i class="bi bi-x-circle text-base"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Interactive Students Table -->
    <div class="card overflow-hidden">
        <div class="card-header flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Siswa SDN Kebonagung</h2>
                <p class="text-xs text-slate-500">Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} dari total {{ $students->total() }} siswa</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">
                Paginasi Halaman {{ $students->currentPage() }} dari {{ $students->lastPage() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>NISN & NIS</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">Gender</th>
                        <th>Rombel Kelas</th>
                        <th>Wali Murid & Kontak WA</th>
                        <th class="text-center">Poin Tatib</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $idx => $s)
                        <tr>
                            <td class="text-center font-bold text-slate-400 text-xs">
                                {{ $students->firstItem() + $idx }}
                            </td>
                            <td>
                                <div class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded inline-block">
                                    {{ $s->nisn }}
                                </div>
                                @if($s->nis)
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">NIS: {{ $s->nis }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($s->photo)
                                        <img src="{{ asset('storage/' . $s->photo) }}" alt="{{ $s->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($s->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $s->name }}</div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $s->birth_place ? $s->birth_place . ', ' : '' }}{{ $s->birth_date ? $s->birth_date->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $s->gender == 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $s->gender }}
                                </span>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-xs rounded-lg">
                                    Kelas {{ $s->classroom->code ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-800">{{ $s->guardian_name ?? 'Belum terdata' }}</div>
                                @if($s->guardian_phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->guardian_phone) }}" target="_blank" class="text-[11px] text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1 mt-0.5">
                                        <i class="bi bi-whatsapp"></i> {{ $s->guardian_phone }}
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400">No WA belum diisi</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-full {{ $s->point_balance >= 80 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $s->point_balance }} Pt
                                </span>
                            </td>
                            <td class="text-center">
                                @if($s->status === 'active')
                                    <span class="status-badge status-active">
                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                    </span>
                                @elseif($s->status === 'moved')
                                    <span class="status-badge status-pending">
                                        <i class="bi bi-arrow-left-right"></i> Mutasi
                                    </span>
                                @else
                                    <span class="status-badge status-cancelled">
                                        <i class="bi bi-x-circle-fill"></i> {{ ucfirst($s->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Edit & Mutasi Button -->
                                    <a href="{{ route('operator.students.edit', $s->id) }}" title="Edit Data & Mutasi Rombel" 
                                        class="p-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition text-slate-600">
                                        <i class="bi bi-pencil-square text-sm"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('operator.students.destroy', $s->id) }}" method="POST" class="inline" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa {{ $s->name }}? Data kartu & riwayat akan dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Siswa" class="p-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 rounded-lg transition text-slate-600">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-slate-400">
                                <i class="bi bi-people text-3xl block mb-2 text-slate-300"></i>
                                Tidak ada data siswa yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($students->hasPages())
            <div class="card-footer p-4 border-t border-slate-100 flex items-center justify-between">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
