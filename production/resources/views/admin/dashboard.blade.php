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
            <button type="button" @click="$dispatch('open-ai-copilot')" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-chat-dots-fill text-amber-300"></i>
                <span>Buka AI Copilot Drawer</span>
            </button>
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
@endsection
