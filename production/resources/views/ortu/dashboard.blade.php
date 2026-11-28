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
                    Memantau kehadiran dan kesiswaan ananda: <strong class="text-slate-800">{{ $studentName }}</strong> (NISN: <span class="font-mono font-bold">{{ $studentNisn }}</span> &bull; Kelas: {{ $classroomCode }})
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('ortu.child.profile') }}" class="btn-outline-primary px-4 py-2.5 text-xs flex items-center gap-2 cursor-pointer border border-rose-200 shadow-2xs">
                <i class="bi bi-person-vcard text-rose-600"></i>
                <span>Biodata & Edit Foto Anak</span>
            </a>
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
            <div class="card p-6 bg-gradient-to-br from-white to-slate-50/50 border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i class="bi bi-broadcast text-rose-600 animate-pulse"></i> Status Presensi Hari Ini
                    </span>

                    @if($todayAttendance)
                        @if($todayAttendance->status === 'Hadir' && $todayAttendance->late_level === 'T-0')
                            <span class="status-badge status-hadir text-xs px-3 py-1">
                                <i class="bi bi-check-circle-fill"></i> Hadir Tepat Waktu
                            </span>
                        @elseif($todayAttendance->status === 'Terlambat')
                            <span class="status-badge status-pending text-xs px-3 py-1">
                                <i class="bi bi-clock-history"></i> Terlambat ({{ $todayAttendance->late_level }})
                            </span>
                        @else
                            <span class="status-badge status-hadir text-xs px-3 py-1">
                                <i class="bi bi-check-circle-fill"></i> {{ $todayAttendance->status }}
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-600 text-xs font-bold flex items-center gap-1.5">
                            <i class="bi bi-hourglass-split text-amber-500"></i> Belum Melakukan Presensi
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-2">
                    <!-- Check-in Time -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Jam Masuk (Check-In)</div>
                        @if($todayAttendance && $todayAttendance->checkin_time)
                            <div class="text-2xl font-extrabold text-emerald-600 mt-1">
                                {{ \Carbon\Carbon::parse($todayAttendance->checkin_time)->format('H:i:s') }} 
                                <span class="text-xs font-medium text-slate-500">WIB</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <i class="bi bi-geo-alt-fill text-slate-400"></i> Gerbang Sekolah (Metode: {{ $todayAttendance->method === 'camera' ? 'Kamera WebRTC' : 'Barcode USB' }})
                            </div>
                        @else
                            <div class="text-2xl font-extrabold text-slate-400 mt-1">
                                --:--:-- <span class="text-xs font-medium text-slate-400">WIB</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                                <i class="bi bi-info-circle"></i> Menunggu siswa tap di gerbang sekolah
                            </div>
                        @endif
                    </div>

                    <!-- Check-out Time -->
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-2xs">
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Jam Pulang (Check-Out)</div>
                        @if($todayAttendance && $todayAttendance->checkout_time)
                            <div class="text-2xl font-extrabold text-blue-600 mt-1">
                                {{ \Carbon\Carbon::parse($todayAttendance->checkout_time)->format('H:i:s') }} 
                                <span class="text-xs font-medium text-slate-500">WIB</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <i class="bi bi-check2-all text-blue-500"></i> Telah menyelesaikan kepulangan
                            </div>
                        @elseif($todayAttendance && $todayAttendance->checkin_time)
                            <div class="text-xl font-bold text-slate-700 mt-1.5 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                                <span>Sedang Mengikuti KBM</span>
                            </div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <i class="bi bi-bell text-slate-400"></i> Estimasi bel kepulangan: 12:30 WIB
                            </div>
                        @else
                            <div class="text-xl font-bold text-slate-400 mt-1.5 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span>
                                <span>Belum Masuk Sekolah</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                                <i class="bi bi-bell text-slate-400"></i> Estimasi bel kepulangan: 12:30 WIB
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>Wali Kelas: <span class="font-bold text-slate-800">{{ $classroom->homeroom_teacher ?? '-' }}</span> (Kelas {{ $classroomCode }})</div>
                    <div class="text-emerald-700 font-semibold flex items-center gap-1">
                        <i class="bi bi-shield-check"></i> Basis Data Relasional Terverifikasi
                    </div>
                </div>
            </div>

            <!-- Attendance History Table (100% Dinamis dari Tabel attendances) -->
            <div class="card overflow-hidden">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Riwayat Kehadiran Terakhir</h2>
                        <p class="text-xs text-slate-500">Rekap kehadiran riil ananda {{ $studentName }} dari basis data</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        Rasio Hadir {{ $attendanceRate }}%
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
                            @forelse($attendanceHistory as $hist)
                                <tr>
                                    <td>
                                        <div class="font-bold text-slate-800">
                                            {{ \Carbon\Carbon::parse($hist->date)->translatedFormat('l, d F Y') }}
                                        </div>
                                        @if($hist->notes)
                                            <div class="text-[10px] text-slate-400">{{ $hist->notes }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hist->checkin_time)
                                            <span class="font-mono text-xs text-emerald-600 font-bold">
                                                {{ \Carbon\Carbon::parse($hist->checkin_time)->format('H:i:s') }} WIB
                                            </span>
                                        @else
                                            <span class="font-mono text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hist->checkout_time)
                                            <span class="font-mono text-xs text-slate-700 font-bold">
                                                {{ \Carbon\Carbon::parse($hist->checkout_time)->format('H:i:s') }} WIB
                                            </span>
                                        @elseif($hist->checkin_time)
                                            <span class="text-xs text-blue-600 font-medium">Sedang KBM</span>
                                        @else
                                            <span class="font-mono text-xs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($hist->status === 'Hadir')
                                            <span class="status-badge status-hadir">
                                                <i class="bi bi-check-circle-fill"></i> Tepat Waktu
                                            </span>
                                        @elseif($hist->status === 'Terlambat')
                                            <span class="status-badge status-pending">
                                                <i class="bi bi-clock-history"></i> Terlambat ({{ $hist->late_level }})
                                            </span>
                                        @else
                                            <span class="status-badge status-hadir">
                                                {{ $hist->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-slate-400">
                                        <i class="bi bi-calendar-x text-3xl block mb-2 text-slate-300"></i>
                                        Belum ada riwayat rekaman presensi gerbang untuk ananda {{ $studentName }}.
                                        <p class="text-[11px] text-slate-400 mt-1">Data presensi akan otomatis tercatat setiap kali kartu di-scan oleh petugas di gerbang sekolah.</p>
                                    </td>
                                </tr>
                            @endforelse
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
                        <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo" class="w-10 h-10 object-contain bg-white rounded-lg p-0.5 shadow-2xs">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-rose-200">KARTU TANDA SISWA</div>
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

                        <div class="space-y-1 text-xs flex-1 min-w-0">
                            <div>
                                <div class="text-[9px] text-rose-200 uppercase font-medium">Nama Siswa</div>
                                <div class="font-extrabold text-sm tracking-wide truncate">{{ $studentName }}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-1">
                                <div>
                                    <div class="text-[9px] text-rose-200 uppercase font-medium">NISN</div>
                                    <div class="font-mono font-bold text-[11px]">{{ $studentNisn }}</div>
                                </div>
                                <div>
                                    <div class="text-[9px] text-rose-200 uppercase font-medium">Rombel</div>
                                    <div class="font-bold text-[11px] text-amber-300">Kelas {{ $classroomCode }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barcode Strip Preview -->
                    <div class="mt-4 pt-2.5 border-t border-white/20 bg-white rounded-lg p-2 text-center">
                        @if($barcodeSvg)
                            <div class="w-full overflow-hidden flex justify-center max-h-7">
                                {!! $barcodeSvg !!}
                            </div>
                        @else
                            <div class="h-6 flex items-center justify-center space-x-1 select-none">
                                <span class="w-1 h-full bg-slate-900"></span>
                                <span class="w-2 h-full bg-slate-900"></span>
                                <span class="w-0.5 h-full bg-slate-900"></span>
                                <span class="w-1.5 h-full bg-slate-900"></span>
                            </div>
                        @endif
                        <div class="font-mono text-[9px] font-bold text-slate-800 tracking-widest mt-0.5">
                            * {{ $studentNisn }} *
                        </div>
                    </div>

                    <div class="text-[9px] text-rose-200/80 text-center mt-3">
                        Berlaku selama ananda aktif sebagai siswa SDN Kebonagung
                    </div>
                </div>

                <div class="mt-4 space-y-2">
                    <a href="{{ route('ortu.child.profile') }}" class="btn-primary text-xs w-full py-2 flex items-center justify-center gap-2">
                        <i class="bi bi-person-vcard"></i>
                        <span>Lihat Profil Lengkap & Edit Foto</span>
                    </a>
                    <p class="text-[11px] text-slate-400 text-center">Kartu digital ini dapat disimpan atau difoto pada HP orang tua jika kartu fisik tertinggal.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
