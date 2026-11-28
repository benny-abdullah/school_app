@extends('layouts.app')

@section('title', 'Dasbor Operator Sekolah & Tata Usaha')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold mb-2">
                <i class="bi bi-gear-fill"></i> Modul Operator Dapodik & Tata Usaha
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pusat Manajemen Data Sekolah</h1>
            <p class="text-xs text-slate-500 mt-1">
                Pengelolaan master 29 rombel kelas, cetak kartu pelajar CR-80, dan konfigurasi scanner presensi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="bi bi-upc-scan"></i>
                <span>Scanner Gerbang</span>
            </button>
            <button type="button" class="btn-outline-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer border border-rose-200">
                <i class="bi bi-person-badge"></i>
                <span>Cetak Kartu Siswa</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5">
            <span class="text-xs font-bold text-slate-500 uppercase">Master Rombel Kelas</span>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalClasses }} Rombel</div>
            <p class="text-xs text-indigo-600 font-semibold mt-1">1A-1D & 2A-6E Terdaftar di DB</p>
        </div>

        <div class="card p-5">
            <span class="text-xs font-bold text-slate-500 uppercase">Total Siswa Binaan</span>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($totalStudents) }} Siswa</div>
            <p class="text-xs text-emerald-600 font-semibold mt-1">Sinkron dengan Basis Data</p>
        </div>

        <div class="card p-5">
            <span class="text-xs font-bold text-slate-500 uppercase">Tahun Ajaran</span>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">2025/2026</div>
            <p class="text-xs text-slate-500 font-semibold mt-1">Semester Ganjil - Aktif</p>
        </div>
    </div>

    <!-- Master 29 Classes Management Section -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Lengkap 29 Master Rombongan Belajar</h2>
                <p class="text-xs text-slate-500">Master data rombel SDN Kebonagung Kota Pasuruan</p>
            </div>

            <!-- Grade Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <a href="{{ route('operator.dashboard', ['grade' => 'all']) }}" 
                    class="px-3 py-1.5 rounded-lg transition {{ $selectedGrade === 'all' ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua (29)
                </a>
                @for($g = 1; $g <= 6; $g++)
                    <a href="{{ route('operator.dashboard', ['grade' => $g]) }}" 
                        class="px-2.5 py-1.5 rounded-lg transition {{ $selectedGrade == $g ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Kelas {{ $g }}
                    </a>
                @endfor
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-16">Kode</th>
                        <th>Nama Rombel</th>
                        <th>Tingkat</th>
                        <th>Wali Kelas</th>
                        <th>NIP Wali</th>
                        <th class="text-center">Siswa / Kuota</th>
                        <th>Tahun Ajaran</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classrooms as $room)
                        <tr>
                            <td>
                                <span class="font-extrabold text-slate-900 px-2 py-1 bg-slate-100 rounded-lg text-xs font-mono">
                                    {{ $room->code }}
                                </span>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800">{{ $room->name }}</div>
                            </td>
                            <td>
                                <span class="text-xs text-slate-600 font-medium">Tingkat {{ $room->grade_level }}</span>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="bi bi-person-badge text-slate-400"></i>
                                    <span>{{ $room->homeroom_teacher }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-xs text-slate-500">{{ $room->homeroom_nip ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center gap-1 font-bold text-xs text-slate-800">
                                    <span>{{ $room->total_students }}</span>
                                    <span class="text-slate-400">/</span>
                                    <span class="text-slate-500">{{ $room->capacity }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="text-xs text-slate-600">{{ $room->academic_year }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-active">
                                    <i class="bi bi-check-circle-fill"></i> Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Tidak ada kelas ditemukan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
