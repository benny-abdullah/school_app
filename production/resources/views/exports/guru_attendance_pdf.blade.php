@extends('exports.layout_pdf')

@section('title', 'Jurnal Presensi Siswa Kelas ' . ($classroom->code ?? 'Binaan') . ' - SDN Kebonagung')
@section('doc_title', 'JURNAL PRESENSI & REKAP KEHADIRAN HARIAN SISWA')
@section('doc_subtitle', 'Rombongan Belajar Kelas ' . ($classroom->code ?? 'Binaan') . ' &bull; Tahun Ajaran 2025/2026 &bull; Dicetak: ' . date('d/m/Y H:i') . ' WIB')

@section('content')
<div style="margin-bottom: 12px; font-size: 8.5pt; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 10px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 50%; border: none; padding: 2px 0;"><strong>Kelas / Rombel:</strong> Kelas {{ $classroom->code ?? '-' }} (Tingkat {{ $classroom->grade_level ?? '-' }})</td>
            <td style="width: 50%; border: none; padding: 2px 0;"><strong>Wali Kelas Resmi:</strong> {{ $teacher->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;"><strong>Kapasitas / Terisi:</strong> {{ count($students) }} / {{ $classroom->capacity ?? 32 }} Siswa</td>
            <td style="border: none; padding: 2px 0;"><strong>NIP Guru:</strong> {{ $teacher->nip_nuptk ?? '-' }}</td>
        </tr>
    </table>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 85px;" class="text-center">NISN</th>
            <th style="width: 170px;">Nama Lengkap Siswa</th>
            <th style="width: 40px;" class="text-center">L/P</th>
            <th style="width: 80px;" class="text-center">Jam Presensi</th>
            <th style="width: 80px;" class="text-center">Status</th>
            <th>Catatan / Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $idx => $s)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center" style="font-family: monospace; font-size: 8pt;">{{ $s['nisn'] }}</td>
                <td><strong>{{ $s['name'] }}</strong></td>
                <td class="text-center" style="font-weight: bold; font-size: 8pt;">{{ $s['gender'] }}</td>
                <td class="text-center" style="font-family: monospace; font-size: 8pt;">{{ $s['time'] }}</td>
                <td class="text-center">
                    @if($s['status'] === 'Hadir')
                        <span class="badge badge-active">HADIR</span>
                    @elseif($s['status'] === 'Terlambat')
                        <span class="badge" style="background-color: #fffbeb; color: #b45309;">TERLAMBAT</span>
                    @elseif($s['status'] === 'Izin')
                        <span class="badge" style="background-color: #eff6ff; color: #1d4ed8;">IZIN</span>
                    @elseif($s['status'] === 'Sakit')
                        <span class="badge" style="background-color: #fdf2f8; color: #be185d;">SAKIT</span>
                    @else
                        <span class="badge">{{ $s['status'] }}</span>
                    @endif
                </td>
                <td style="font-size: 8pt;">{{ $s['notes'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data kehadiran siswa tercatat.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection

@section('signature')
<table class="ttd-table">
    <tr>
        <td style="width: 50%; text-align: center;">
            <div>Mengetahui,</div>
            <div style="margin-top: 2px;">Kepala UPTD SDN Kebonagung</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ $kepsek?->name ?? 'WAHYU AGUNG SETIYAWAN S.Pd' }}</div>
            <div class="ttd-nip">NIP. {{ $kepsek?->nip_nuptk ?? '198708222019031004' }}</div>
        </td>
        <td style="width: 50%; text-align: center;">
            <div>Kota Pasuruan, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
            <div style="margin-top: 2px;">Guru / Wali Kelas {{ $classroom->code ?? '' }}</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ $teacher->name }}</div>
            <div class="ttd-nip">NIP. {{ $teacher->nip_nuptk ?? '-' }}</div>
        </td>
    </tr>
</table>
@endsection
