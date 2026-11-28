@extends('layouts.app')

@section('title', 'Master Rombongan Belajar & Penugasan Wali Kelas')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold mb-2">
                <i class="bi bi-diagram-3-fill"></i> Manajemen Akademik & Kepegawaian
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Master Rombongan Belajar</h1>
            <p class="text-xs text-slate-500 mt-1">
                Pengelolaan kuota kelas, sarana ruangan, dan penetapan SK Guru Wali Kelas SDN Kebonagung Kota Pasuruan.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('classrooms.create') }}" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 shadow-xs">
                <i class="bi bi-plus-circle-fill text-sm"></i>
                <span>Tambah Rombel Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Rombel</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total_classes'] }} Kelas</div>
            <p class="text-[11px] text-indigo-600 font-semibold mt-0.5">{{ $stats['total_classes'] }} Rombel Aktif (Tingkat 1 - 6)</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Wali Kelas Terisi</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['assigned_teachers_count'] }} Guru</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">{{ $stats['assigned_teachers_count'] == $stats['total_classes'] ? 'Penugasan 100%' : $stats['assigned_teachers_count'] . ' Guru Ditugaskan' }}</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Siswa Binaan</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_students']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Peserta Didik</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Daya Tampung Kuota</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_capacity']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Maksimal 32/Kelas</p>
        </div>
    </div>

    <!-- Table Master Rombel Card -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Daftar Rombel & Guru Pengampu</h2>
                <p class="text-xs text-slate-500">Klik "Edit / Tetapkan Wali" untuk merotasi penugasan guru atau mengubah kapasitas</p>
            </div>

            <!-- Grade Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <a href="{{ route('classrooms.index', ['grade' => 'all']) }}" 
                    class="px-3 py-1.5 rounded-lg transition {{ $selectedGrade === 'all' ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua ({{ $stats['total_classes'] }})
                </a>
                @for($g = 1; $g <= 6; $g++)
                    <a href="{{ route('classrooms.index', ['grade' => $g]) }}" 
                        class="px-2.5 py-1.5 rounded-lg transition {{ $selectedGrade == $g ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Kelas {{ $g }}
                    </a>
                @endfor
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-16">Kode</th>
                        <th>Rombel & Lokasi Ruang</th>
                        <th>Tingkat</th>
                        <th>Wali Kelas Resmi</th>
                        <th>NIP Guru</th>
                        <th class="text-center">Siswa / Kuota</th>
                        <th>Tahun Ajaran</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classrooms as $cls)
                        <tr>
                            <td>
                                <span class="font-black text-slate-900 px-2 py-1 bg-slate-100 rounded-lg text-xs font-mono">
                                    {{ $cls->code }}
                                </span>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900">{{ $cls->name }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1">
                                    <i class="bi bi-geo-alt text-slate-400"></i>
                                    <span>{{ $cls->room_name ?? 'Ruang Kelas ' . $cls->code }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-xs font-semibold text-slate-700">Tingkat {{ $cls->grade_level }}</span>
                            </td>
                            <td>
                                @if($cls->homeroomTeacher)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-emerald-200">
                                            {{ strtoupper(substr($cls->homeroomTeacher->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-xs">{{ $cls->homeroomTeacher->name }}</div>
                                            <div class="text-[10px] text-emerald-600 font-medium">SK Wali Kelas Aktif</div>
                                        </div>
                                    </div>
                                @elseif($cls->homeroom_teacher)
                                    <div class="font-semibold text-slate-700 text-xs">{{ $cls->homeroom_teacher }}</div>
                                @else
                                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                        Belum Ada Wali
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="font-mono text-xs text-slate-600 font-medium">
                                    {{ $cls->homeroomTeacher?->nip_nuptk ?? $cls->homeroom_nip ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1 font-bold text-xs text-slate-800">
                                    <span class="text-rose-600 font-extrabold">{{ $cls->students_count > 0 ? $cls->students_count : $cls->total_students }}</span>
                                    <span class="text-slate-400">/</span>
                                    <span class="text-slate-500">{{ $cls->capacity }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="text-xs text-slate-600 font-medium">{{ $cls->academic_year }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-active">
                                    <i class="bi bi-check-circle-fill"></i> Aktif
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('classrooms.edit', $cls->id) }}" 
                                        class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 transition" title="Kelola / Penetapan SK">
                                        <i class="bi bi-pencil-square text-xs"></i>
                                    </a>

                                    @if(($cls->students_count > 0 ? $cls->students_count : $cls->total_students) == 0)
                                        <form action="{{ route('classrooms.destroy', $cls->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rombel {{ $cls->name }} secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus Rombel Kosong">
                                                <i class="bi bi-trash-fill text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="p-1.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-300 cursor-not-allowed transition" title="Rombel berisi {{ $cls->students_count > 0 ? $cls->students_count : $cls->total_students }} siswa. Mutasikan siswa terlebih dahulu untuk menghapus." onclick="alert('Kelas {{ $cls->code }} tidak dapat dihapus karena masih menampung {{ $cls->students_count > 0 ? $cls->students_count : $cls->total_students }} siswa aktif. Harap mutasikan siswa terlebih dahulu!');">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-slate-400">
                                Tidak ada rombel ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
