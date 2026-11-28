@extends('layouts.app')

@section('title', 'Dasbor Guru & Wali Kelas ' . ($classroom->code ?? 'Binaan'))

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold mb-2">
                <i class="bi bi-person-video3"></i> Portal Wali Kelas
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Presensi & Kesiswaan Kelas {{ $classroom->code ?? 'Binaan' }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                Wali Kelas: <span class="font-bold text-slate-800">{{ Auth::user()->name }}</span> (NIP: {{ Auth::user()->nip_nuptk }}) - SDN Kebonagung
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('guru.attendance.export.pdf') }}" class="btn-secondary px-3 py-2 text-xs flex items-center gap-1.5 shadow-xs border border-rose-200 text-rose-700 hover:bg-rose-50" target="_blank">
                <i class="bi bi-file-earmark-pdf-fill text-rose-600"></i>
                <span>Unduh PDF</span>
            </a>
            <a href="{{ route('guru.attendance.export.excel') }}" class="btn-secondary px-3 py-2 text-xs flex items-center gap-1.5 shadow-xs border border-emerald-200 text-emerald-700 hover:bg-emerald-50">
                <i class="bi bi-file-earmark-excel-fill text-emerald-600"></i>
                <span>Unduh Excel</span>
            </a>
            <button type="button" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="bi bi-camera-fill"></i>
                <span>Scan Barcode Siswa</span>
            </button>
            <button type="button" class="btn-outline-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer border border-rose-200">
                <i class="bi bi-pencil-square"></i>
                <span>Catatan Karakter</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Siswa {{ $classroom->code ?? '' }}</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $classroom->total_students ?? 30 }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Kapasitas Maksimal {{ $classroom->capacity ?? 32 }}</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Hadir Tepat Waktu</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-600">27</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Sebelum 07:00 WIB</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Terlambat</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-amber-500">1</div>
            <p class="text-[11px] text-amber-600 font-semibold mt-0.5">Toleransi s/d 07:15</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Izin / Sakit</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-600">2</div>
            <p class="text-[11px] text-blue-600 font-semibold mt-0.5">Surat Terverifikasi</p>
        </div>
    </div>

    <!-- Realtime Class 5A Roster Attendance -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Kehadiran Siswa Kelas {{ $classroom->code ?? '5A' }} (Hari Ini)</h2>
                <p class="text-xs text-slate-500">Status tercatat realtime dari pemindai barcode / gerbang sekolah</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('guru.attendance.export.pdf') }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 flex items-center gap-1" target="_blank" title="Cetak Jurnal PDF">
                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                </a>
                <a href="{{ route('guru.attendance.export.excel') }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 flex items-center gap-1" title="Unduh Rekap Excel">
                    <i class="bi bi-file-earmark-excel-fill"></i> Excel
                </a>
                <div class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                    <i class="bi bi-calendar-event"></i> Realtime Hari Ini
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>NISN Siswa</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">L/P</th>
                        <th>Jam Scan</th>
                        <th class="text-center">Status Kehadiran</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students5A as $idx => $s)
                        <tr>
                            <td class="text-center font-bold text-slate-400 text-xs">{{ $idx + 1 }}</td>
                            <td>
                                <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $s['nisn'] }}
                                </span>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900">{{ $s['name'] }}</div>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $s['gender'] == 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $s['gender'] }}
                                </span>
                            </td>
                            <td>
                                <span class="font-mono text-xs text-slate-600">{{ $s['time'] }}</span>
                            </td>
                            <td class="text-center">
                                @if($s['status'] === 'Hadir')
                                    <span class="status-badge status-hadir">
                                        <i class="bi bi-check-circle-fill"></i> Hadir
                                    </span>
                                @elseif($s['status'] === 'Terlambat')
                                    <span class="status-badge status-terlambat">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Terlambat
                                    </span>
                                @elseif($s['status'] === 'Izin')
                                    <span class="status-badge status-izin">
                                        <i class="bi bi-envelope-paper-fill"></i> Izin
                                    </span>
                                @elseif($s['status'] === 'Sakit')
                                    <span class="status-badge status-sakit">
                                        <i class="bi bi-bandaid-fill"></i> Sakit
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-xs text-slate-500">{{ $s['notes'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
