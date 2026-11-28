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
                Pengelolaan master rombongan belajar, cetak kartu pelajar CR-80, dan konfigurasi scanner presensi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('scanner.index') }}" class="btn-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="bi bi-upc-scan"></i>
                <span>Scanner Gerbang</span>
            </a>
            <a href="{{ route('cards.index') }}" class="btn-outline-primary px-4 py-2 text-xs flex items-center gap-2 cursor-pointer border border-rose-200">
                <i class="bi bi-person-badge-fill text-rose-600"></i>
                <span>Cetak Kartu Siswa</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5">
            <span class="text-xs font-bold text-slate-500 uppercase flex items-center justify-between">
                <span>Master Rombel</span>
                <i class="bi bi-grid-fill text-indigo-500"></i>
            </span>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalClasses }} Rombel</div>
            <p class="text-xs text-indigo-600 font-semibold mt-1">1A-1D & 2A-6E Terdaftar di DB</p>
        </div>

        <div class="card p-5">
            <span class="text-xs font-bold text-slate-500 uppercase flex items-center justify-between">
                <span>Total Siswa Binaan</span>
                <i class="bi bi-people-fill text-rose-500"></i>
            </span>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($totalStudents) }} Siswa</div>
            <p class="text-xs text-slate-500 font-semibold mt-1">
                <span class="text-blue-600 font-bold">L: {{ $genderStats['male'] }}</span> &bull; 
                <span class="text-pink-600 font-bold">P: {{ $genderStats['female'] }}</span>
            </p>
        </div>

        <div class="card p-5">
            <span class="text-xs font-bold text-slate-500 uppercase flex items-center justify-between">
                <span>Pendidik & Tenaga Kependidikan</span>
                <i class="bi bi-person-workspace text-emerald-500"></i>
            </span>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalGtk }} GTK</div>
            <p class="text-xs text-emerald-600 font-semibold mt-1">Guru, Wali Kelas & Tenaga TU</p>
        </div>

        <div class="card p-5">
            <span class="text-xs font-bold text-slate-500 uppercase flex items-center justify-between">
                <span>Daya Tampung Sekolah</span>
                <i class="bi bi-pie-chart-fill text-amber-500"></i>
            </span>
            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $totalStudents }} <span class="text-sm font-medium text-slate-400">/ {{ $totalCapacity }}</span></div>
            <div class="mt-2 flex items-center gap-2">
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ round(($totalStudents / max(1, $totalCapacity)) * 100, 1) }}%"></div>
                </div>
                <span class="text-[11px] font-bold text-slate-600">{{ round(($totalStudents / max(1, $totalCapacity)) * 100, 1) }}%</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Sisa Kuota: <strong class="text-amber-600 font-mono">{{ $totalCapacity - $totalStudents }} Kursi</strong></p>
        </div>
    </div>

    <!-- Visual Charts & Demographic Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bar Chart: Distribusi 28 Rombel -->
        <div class="card lg:col-span-2 p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-bar-chart-fill text-rose-600"></i>
                        <span>Distribusi Jumlah Siswa per Rombel (28 Rombongan Belajar)</span>
                    </h3>
                    <p class="text-xs text-slate-500">Peta kepadatan peserta didik tiap rombel SDN Kebonagung (Kapasitas ideal 32)</p>
                </div>
                <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">
                    Total: {{ $totalStudents }} Siswa
                </span>
            </div>

            <div class="h-64 w-full">
                <canvas id="operatorRombelBarChart"></canvas>
            </div>

            <!-- Grade breakdown badges -->
            <div class="pt-3 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                @foreach($gradeCounts as $gc)
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-center">
                        <div class="text-[10px] font-bold text-slate-500 uppercase">Tingkat {{ $gc->grade_level }}</div>
                        <div class="text-sm font-extrabold text-slate-900">{{ $gc->students_count }} <span class="text-[10px] font-normal text-slate-500">Siswa</span></div>
                        <span class="text-[10px] text-indigo-600 font-semibold">{{ $gc->total_classes }} Rombel</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Doughnut Chart: Rasio Gender Siswa -->
        <div class="card p-5 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-indigo-600"></i>
                        <span>Rasio Gender Peserta Didik</span>
                    </h3>
                    <span class="text-xs font-mono font-bold text-slate-500">{{ $totalStudents }} Siswa</span>
                </div>

                <div class="h-48 w-full flex items-center justify-center my-3">
                    <canvas id="operatorGenderDoughnutChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-center">
                <div class="p-2.5 rounded-xl bg-blue-50/70 border border-blue-100">
                    <span class="text-[10px] font-bold text-blue-700 uppercase">Laki-Laki</span>
                    <div class="text-xl font-black text-blue-900">{{ $genderStats['male'] }}</div>
                    <span class="text-[11px] text-blue-600 font-semibold">{{ round($genderStats['male'] / max(1, $totalStudents) * 100, 1) }}%</span>
                </div>
                <div class="p-2.5 rounded-xl bg-pink-50/70 border border-pink-100">
                    <span class="text-[10px] font-bold text-pink-700 uppercase">Perempuan</span>
                    <div class="text-xl font-black text-pink-900">{{ $genderStats['female'] }}</div>
                    <span class="text-[11px] text-pink-600 font-semibold">{{ round($genderStats['female'] / max(1, $totalStudents) * 100, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Master 29 Classes Management Section -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Lengkap Master Rombongan Belajar</h2>
                <p class="text-xs text-slate-500">Master data rombel SDN Kebonagung Kota Pasuruan</p>
            </div>

            <!-- Grade Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <a href="{{ route('operator.dashboard', ['grade' => 'all']) }}" 
                    class="px-3 py-1.5 rounded-lg transition {{ $selectedGrade === 'all' ? 'bg-white text-rose-600 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua ({{ $totalClasses ?? count($classrooms) }})
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ChartClass = window.Chart;
    if (!ChartClass) return;

    // 1. Bar Chart: Distribusi 28 Rombel
    const ctxBar = document.getElementById('operatorRombelBarChart');
    if (ctxBar) {
        new ChartClass(ctxBar, {
            type: 'bar',
            data: {
                labels: @json($rombelLabels),
                datasets: [{
                    label: 'Jumlah Siswa (Kapasitas Maks 32)',
                    data: @json($rombelData),
                    backgroundColor: 'rgba(225, 29, 72, 0.82)',
                    hoverBackgroundColor: '#e11d48',
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' Siswa (Kapasitas 32)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 35,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9, weight: 'bold' } }
                    }
                }
            }
        });
    }

    // 2. Doughnut Chart: Rasio Gender Siswa
    const ctxDoughnut = document.getElementById('operatorGenderDoughnutChart');
    if (ctxDoughnut) {
        new ChartClass(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Laki-Laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $genderStats['male'] }}, {{ $genderStats['female'] }}],
                    backgroundColor: ['#2563eb', '#db2777'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
@endpush
@endsection
