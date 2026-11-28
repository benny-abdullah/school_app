@extends('layouts.app')

@section('title', 'Dasbor Eksekutif Kepala Sekolah')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome Banner -->
    <div class="bg-gradient-to-r from-rose-600 via-rose-700 to-rose-800 rounded-2xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold mb-3">
                <i class="bi bi-shield-check"></i> Hak Akses: Super Admin / Kepala Sekolah
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ Auth::user()->name }}</h1>
            <p class="text-rose-100 text-sm mt-1 max-w-2xl">
                Pantauan eksekutif kesiswaan, kepegawaian, dan kehadiran realtime 29 rombongan belajar SDN Kebonagung Kota Pasuruan.
            </p>
            <div class="flex flex-wrap items-center gap-4 mt-4 pt-3 border-t border-white/20 text-xs font-medium text-rose-100">
                <div class="flex items-center gap-1.5">
                    <i class="bi bi-clock-history"></i> Realtime: <span class="font-bold text-white">{{ $currentTimeWib }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="bi bi-geo-alt"></i> Lokasi: <span class="font-bold text-white">SDN Kebonagung Kota Pasuruan</span>
                </div>
            </div>
        </div>
        <!-- Decorative Circle -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Siswa -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Siswa Aktif</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ number_format($totalStudents) }}</span>
                <span class="text-xs font-semibold text-emerald-600 flex items-center">
                    <i class="bi bi-arrow-up-short text-base"></i> 100% Dapodik
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Tersebar di {{ $totalClasses }} Rombel Kelas</p>
        </div>

        <!-- Card 2: Kehadiran Hari Ini -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tingkat Kehadiran</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $attendanceStats['rate'] }}%</span>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    Sangat Baik
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $attendanceStats['present'] }} siswa hadir tepat waktu</p>
        </div>

        <!-- Card 3: Total Rombel Master -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rombongan Belajar</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalClasses }} Kelas</span>
                <span class="text-xs font-medium text-slate-500">Tingkat 1 - 6</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $totalClasses }} Rombel Aktif (Tingkat 1 - 6)</p>
        </div>

        <!-- Card 4: GTK & Tenaga Pendidik -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Guru & GTK</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="bi bi-person-workspace"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalTeachers }} Orang</span>
                <span class="text-xs font-semibold text-emerald-600">Aktif Mengajar</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Wali Kelas, Guru Mapel & TU</p>
        </div>
    </div>

    <!-- AI Pedagogical Analysis Banner (Tahap 7) -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 rounded-2xl p-5 text-white shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 border border-purple-800/40">
        <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-amber-400 to-rose-400 p-0.5 flex items-center justify-center text-slate-950 font-black text-xl shrink-0 shadow-sm mt-0.5">
                <i class="bi bi-stars"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-black uppercase tracking-wider bg-purple-500/30 border border-purple-400/40 text-purple-200 px-2 py-0.5 rounded-full">
                        AI Pedagogic Insight &bull; Gemini 1.5 Flash
                    </span>
                    <span class="text-xs text-slate-400 font-mono">Realtime Monitoring</span>
                </div>
                <h3 class="text-sm font-bold text-white">Analisis Kecerdasan Artifisial Kedisiplinan & Iklim Belajar Sekolah</h3>
                <p class="text-xs text-slate-300 mt-1 leading-relaxed max-w-2xl">
                    Tingkat kedisiplinan gerbang SDN Kebonagung hari ini terpantau <strong>{{ $attendanceStats['rate'] }}%</strong>. Sistem mendeteksi kestabilan kehadiran di seluruh rombel, dengan rekomendasi pendekatan disiplin positif untuk siswa dengan riwayat terlambat.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('ai.analytics.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-stars text-amber-300"></i>
                <span>Buka Pusat Analisis AI</span>
            </a>
        </div>
    </div>

    <!-- Statistik Interaktif & Grafik Chart.js Dapodik (Tahap 2) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Grafik 1: Distribusi Siswa di 28 Rombel (8 Kolom) -->
        <div class="lg:col-span-8 card p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-bar-chart-line-fill text-rose-600"></i> Distribusi Siswa Riil di 28 Rombel
                    </h3>
                    <p class="text-xs text-slate-500">Jumlah peserta didik per rombongan belajar (Tingkat 1A s/d 6E)</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    Total {{ number_format($totalStudents) }} Siswa
                </span>
            </div>

            <!-- Canvas Chart -->
            <div class="h-64 sm:h-72 w-full">
                <canvas id="rombelBarChart"></canvas>
            </div>
        </div>

        <!-- Grafik 2: Komposisi Gender & Kuota Sekolah (4 Kolom) -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Doughnut Chart Gender Siswa -->
            <div class="card p-5 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wide flex items-center gap-1.5">
                        <i class="bi bi-pie-chart-fill text-indigo-600"></i> Rasio Gender Siswa
                    </h3>
                    <span class="text-[11px] font-mono font-bold text-slate-500">791 Siswa</span>
                </div>

                <div class="h-44 w-full flex items-center justify-center">
                    <canvas id="genderDoughnutChart"></canvas>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-center">
                    <div class="p-2 rounded-xl bg-blue-50/70 border border-blue-100">
                        <span class="text-[10px] font-bold text-blue-700 uppercase">Laki-Laki</span>
                        <div class="text-lg font-black text-blue-900">{{ $genderStats['male'] }}</div>
                        <span class="text-[10px] text-blue-600 font-semibold">{{ round($genderStats['male'] / max(1, $totalStudents) * 100, 1) }}%</span>
                    </div>
                    <div class="p-2 rounded-xl bg-pink-50/70 border border-pink-100">
                        <span class="text-[10px] font-bold text-pink-700 uppercase">Perempuan</span>
                        <div class="text-lg font-black text-pink-900">{{ $genderStats['female'] }}</div>
                        <span class="text-[10px] text-pink-600 font-semibold">{{ round($genderStats['female'] / max(1, $totalStudents) * 100, 1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Info Card Daya Tampung Kuota Sekolah -->
            <div class="card p-4.5 bg-gradient-to-br from-slate-900 to-slate-800 text-white space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-buildings-fill text-amber-400"></i> Kuota & Daya Tampung
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        {{ $occupancyRate }}% Terisi
                    </span>
                </div>

                <div>
                    <div class="flex items-baseline justify-between text-xs mb-1">
                        <span class="text-slate-300">Total Terisi:</span>
                        <span class="font-extrabold text-white font-mono">{{ $totalStudents }} / {{ $totalCapacity }} Kursi</span>
                    </div>
                    <!-- Progress Bar Kuota -->
                    <div class="w-full bg-slate-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-400 to-teal-300 h-2 rounded-full" style="width: {{ $occupancyRate }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-700 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Sisa Kuota:</span>
                        <span class="font-bold text-amber-300">{{ $totalCapacity - $totalStudents }} Kursi</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Kapasitas Maks:</span>
                        <span class="font-bold text-slate-200">32 Siswa / Kelas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Rombel Overview -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Distribusi Master Rombongan Belajar</h2>
                <p class="text-xs text-slate-500">Seluruh kelas telah terdaftar di database MySQL <span class="font-mono text-rose-600 font-bold">db_starkids</span></p>
            </div>
            <span class="status-badge status-active">
                <i class="bi bi-database-check"></i> {{ $totalClasses }} Rombel Terverifikasi
            </span>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($classrooms as $cls)
                    <div class="p-3 rounded-xl border border-slate-200 bg-white hover:border-rose-300 hover:shadow-xs transition group">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-extrabold text-sm text-slate-900 group-hover:text-rose-600 transition">{{ $cls->code }}</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                {{ $cls->total_students }} Siswa
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 truncate" title="{{ $cls->homeroom_teacher }}">
                            <i class="bi bi-person text-slate-400"></i> {{ $cls->homeroom_teacher }}
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px]">
                            <span class="text-emerald-600 font-bold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 100%
                            </span>
                            <span class="text-slate-400">KBM Aktif</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ChartClass = window.Chart;
    if (!ChartClass) return;

    // 1. Bar Chart: Distribusi 28 Rombel
    const ctxBar = document.getElementById('rombelBarChart');
    if (ctxBar) {
        new ChartClass(ctxBar, {
            type: 'bar',
            data: {
                labels: @json($rombelLabels),
                datasets: [{
                    label: 'Jumlah Siswa (Kapasitas 32)',
                    data: @json($rombelData),
                    backgroundColor: 'rgba(225, 29, 72, 0.8)',
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
                                return context.raw + ' Siswa (Maks 32)';
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
    const ctxDoughnut = document.getElementById('genderDoughnutChart');
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
