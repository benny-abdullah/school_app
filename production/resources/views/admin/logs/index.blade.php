@extends('layouts.app')

@section('title', 'Log Aktifitas Sistem & Jejak Audit (Audit Trail)')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold mb-2">
                <i class="bi bi-journal-text"></i> Forensik Keamanan & Audit Kepatuhan
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Log Aktifitas & Jejak Audit Sistem</h1>
            <p class="text-xs text-slate-500 mt-1">
                Catatan kronologis kekal (*immutable append-only*) dari seluruh aktivitas autentikasi, manajemen data kesiswaan, kepegawaian, dan rotasi rombel.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-extrabold">
                <i class="bi bi-shield-check"></i> Audit Trail Aktif
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Total Entri Log</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900">{{ number_format($stats['total_logs']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Seluruh Transaksi</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Aktivitas Hari Ini</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-indigo-600">{{ number_format($stats['today_logs']) }}</div>
            <p class="text-[11px] text-indigo-600 font-semibold mt-0.5">Transaksi Hari Ini</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Sesi Login Hari Ini</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ number_format($stats['today_logins']) }}</div>
            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Autentikasi Pengguna</p>
        </div>

        <div class="card p-4">
            <span class="text-[11px] font-bold text-slate-500 uppercase">Perubahan Data</span>
            <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-purple-600">{{ number_format($stats['data_changes']) }}</div>
            <p class="text-[11px] text-slate-400 mt-0.5">Create / Update / Delete</p>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card overflow-hidden">
        <div class="card-header flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Rekapitulasi Riwayat Transaksi</h2>
                    <p class="text-xs text-slate-500">Log tersimpan secara permanen dan tidak dapat diubah maupun dihapus (*Append-Only*)</p>
                </div>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('admin.logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                <div class="sm:col-span-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari aksi, nama user, IP, deskripsi..." class="form-input text-xs w-full">
                </div>

                <div>
                    <select name="module" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $moduleFilter === 'all' ? 'selected' : '' }}>Semua Modul</option>
                        @foreach($modules as $mod)
                            <option value="{{ $mod }}" {{ $moduleFilter === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="role" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Semua Role</option>
                        <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="operator" {{ $roleFilter === 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="guru/walikelas" {{ $roleFilter === 'guru/walikelas' ? 'selected' : '' }}>Guru</option>
                        <option value="orangtua" {{ $roleFilter === 'orangtua' ? 'selected' : '' }}>Orang Tua</option>
                        <option value="guest" {{ $roleFilter === 'guest' ? 'selected' : '' }}>Tamu / Publik</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select name="date" class="form-input text-xs w-full" onchange="this.form.submit()">
                        <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>Semua Tanggal</option>
                        <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="7days" {{ $dateFilter === '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="30days" {{ $dateFilter === '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    </select>
                    <button type="submit" class="btn-secondary px-3 py-2 text-xs flex-shrink-0">
                        <i class="bi bi-search"></i>
                    </button>
                    @if($search || $moduleFilter !== 'all' || $roleFilter !== 'all' || $dateFilter !== 'all')
                        <a href="{{ route('admin.logs.index') }}" class="p-2 text-slate-400 hover:text-slate-600 text-xs flex-shrink-0" title="Reset Filter">
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
                        <th class="w-36">Waktu Transaksi</th>
                        <th>Aktor Pengguna</th>
                        <th>Modul</th>
                        <th>Aksi Event</th>
                        <th>Deskripsi Aktivitas</th>
                        <th>IP & Perangkat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div class="font-mono text-xs font-bold text-slate-800">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                <div class="font-bold text-slate-900 text-xs">{{ $log->user_name }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">
                                    Role: {{ $log->user_role }}
                                </div>
                            </td>
                            <td>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[11px] border border-slate-200">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border font-mono {{ $log->action_badge_class }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <div class="text-xs text-slate-800 leading-relaxed font-medium max-w-md">
                                    {{ $log->description }}
                                </div>
                            </td>
                            <td>
                                <div class="font-mono text-[11px] text-slate-600">{{ $log->ip_address ?? '127.0.0.1' }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 30) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                Belum ada catatan log aktivitas yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
