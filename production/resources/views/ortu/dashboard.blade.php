@extends('layouts.app')

@section('title', 'Portal Wali Murid - Pemantauan Kehadiran Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header Welcome Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 text-2xl font-bold flex-shrink-0">
                👨‍👦
            </div>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold mb-1">
                    <i class="bi bi-people-fill"></i> Portal Orang Tua Murid
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Selamat Datang, {{ Auth::user()->name }}</h1>
                <p class="text-xs text-slate-500">
                    Memantau kehadiran dan kesiswaan ananda: <span class="font-bold text-slate-800">{{ $studentName }}</span> (NISN: {{ $studentNisn }})
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="btn-primary px-4 py-2.5 text-xs flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="bi bi-envelope-paper-fill"></i>
                <span>Ajukan Surat Izin / Sakit</span>
            </button>
        </div>
    </div>

    <!-- Student Attendance Status Today & Digital ID Card Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Kehadiran Hari Ini (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Today's Attendance Realtime Card -->
            <div class="card p-6 bg-gradient-to-br from-white to-emerald-50/30 border-emerald-200">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        <i class="bi bi-broadcast text-emerald-600 animate-pulse mr-1"></i> Status Presensi Hari Ini
                    </span>
                    <span class="status-badge status-hadir text-xs px-3 py-1">
                        <i class="bi bi-check-circle-fill"></i> Hadir Tepat Waktu
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-2">
                    <!-- Check-in Time -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Jam Masuk (Check-In)</div>
                        <div class="text-2xl font-extrabold text-emerald-600 mt-1">06:42:15 <span class="text-xs font-medium text-slate-500">WIB</span></div>
                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <i class="bi bi-geo-alt-fill text-slate-400"></i> Gerbang Utama Barat (Kamera Scanner)
                        </div>
                    </div>

                    <!-- Check-out Time -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Jam Pulang (Check-Out)</div>
                        <div class="text-xl font-bold text-slate-700 mt-1.5 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                            <span>Sedang Mengikuti KBM</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <i class="bi bi-bell text-slate-400"></i> Estimasi bel kepulangan: 12:30 WIB
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>Wali Kelas: <span class="font-bold text-slate-800">{{ $classroom->homeroom_teacher ?? 'Bambang S.Pd' }}</span> (Kelas {{ $classroomCode }})</div>
                    <div class="text-emerald-700 font-semibold flex items-center gap-1">
                        <i class="bi bi-shield-check"></i> Notifikasi Terverifikasi Sistem
                    </div>
                </div>
            </div>

            <!-- Attendance History Table -->
            <div class="card overflow-hidden">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Riwayat Kehadiran Terakhir</h2>
                        <p class="text-xs text-slate-500">Rekap 5 hari belajar efektif ananda Ahmad Rifai</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        Kehadiran 100%
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hari & Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Kepulangan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceHistory as $hist)
                                <tr>
                                    <td>
                                        <div class="font-bold text-slate-800">{{ $hist['date'] }}</div>
                                    </td>
                                    <td>
                                        <span class="font-mono text-xs text-emerald-600 font-bold">{{ $hist['checkin'] }}</span>
                                    </td>
                                    <td>
                                        <span class="font-mono text-xs text-slate-600">{{ $hist['checkout'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-hadir">
                                            <i class="bi bi-check-circle-fill"></i> {{ $hist['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Digital Student ID Card CR-80 Preview (1 Col) -->
        <div class="space-y-6">
            <div class="card p-5">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center justify-between">
                    <span>Kartu Pelajar Digital</span>
                    <span class="text-[10px] text-rose-600 font-bold">Format CR-80</span>
                </div>

                <!-- Realistic Student ID Card -->
                <div class="bg-gradient-to-br from-rose-700 via-rose-800 to-slate-900 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
                    <!-- School Header -->
                    <div class="flex items-center gap-3 border-b border-white/20 pb-3 mb-4">
                        <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo" class="w-10 h-10 object-contain bg-white rounded-lg p-0.5">
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-wider text-rose-200">KARTU TANDA SISWA</div>
                            <div class="text-xs font-extrabold tracking-tight">SDN KEBONAGUNG KOTA PASURUAN</div>
                        </div>
                    </div>

                    <!-- Student Details with Photo Frame -->
                    <div class="flex items-center gap-3.5">
                        <div class="w-14 h-18 bg-white/20 border border-white/30 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0 shadow-xs">
                            @if(isset($student) && $student?->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $studentName }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-center p-1 text-white/80">
                                    <i class="bi bi-person-fill text-2xl"></i>
                                    <span class="block text-[8px] font-mono mt-0.5">3 x 4</span>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-1.5 text-xs flex-1">
                            <div>
                                <div class="text-[10px] text-rose-200 uppercase font-medium">Nama Siswa</div>
                                <div class="font-extrabold text-sm tracking-wide">{{ $studentName }}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <div class="text-[10px] text-rose-200 uppercase font-medium">NISN</div>
                                    <div class="font-mono font-bold">{{ $studentNisn }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] text-rose-200 uppercase font-medium">Rombel</div>
                                    <div class="font-bold">Kelas {{ $classroomCode }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barcode Strip Preview -->
                    <div class="mt-4 pt-3 border-t border-white/20 bg-white rounded-lg p-2.5 text-center">
                        <div class="h-8 flex items-center justify-center space-x-1 select-none">
                            <span class="w-1 h-full bg-slate-900"></span>
                            <span class="w-0.5 h-full bg-slate-900"></span>
                            <span class="w-2 h-full bg-slate-900"></span>
                            <span class="w-1.5 h-full bg-slate-900"></span>
                            <span class="w-0.5 h-full bg-slate-900"></span>
                            <span class="w-2 h-full bg-slate-900"></span>
                            <span class="w-1 h-full bg-slate-900"></span>
                            <span class="w-0.5 h-full bg-slate-900"></span>
                            <span class="w-1.5 h-full bg-slate-900"></span>
                            <span class="w-2 h-full bg-slate-900"></span>
                            <span class="w-0.5 h-full bg-slate-900"></span>
                            <span class="w-1 h-full bg-slate-900"></span>
                            <span class="w-2 h-full bg-slate-900"></span>
                        </div>
                        <div class="font-mono text-[10px] font-bold text-slate-800 tracking-widest mt-1">
                            *{{ $studentNisn }}*
                        </div>
                    </div>

                    <div class="text-[9px] text-rose-200/80 text-center mt-3">
                        Berlaku selama ananda aktif sebagai siswa SDN Kebonagung
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-xs text-slate-500">Kartu ini dapat digunakan untuk presensi mandiri di gerbang sekolah.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
