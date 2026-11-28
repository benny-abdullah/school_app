@extends('exports.layout_pdf')

@section('title', 'Buku Induk Data Siswa - SDN Kebonagung')
@section('doc_title', 'DAFTAR NOMINATIF PESERTA DIDIK')
@section('doc_subtitle', 'Tahun Ajaran 2025/2026 &bull; ' . ($currentClassroom ? 'Rombel ' . $currentClassroom->name : 'Seluruh Rombongan Belajar (Semua Kelas)') . ' &bull; Total: ' . $students->count() . ' Siswa')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 80px;" class="text-center">NISN</th>
            <th style="width: 50px;" class="text-center">NIS</th>
            <th>Nama Lengkap Peserta Didik</th>
            <th style="width: 30px;" class="text-center">L/P</th>
            <th style="width: 50px;" class="text-center">Kelas</th>
            <th>Tempat, Tanggal Lahir</th>
            <th>Nama Orang Tua / Wali</th>
            <th style="width: 85px;">No. WhatsApp</th>
            <th style="width: 50px;" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($students as $idx => $s)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center" style="font-family: monospace;">{{ $s->nisn }}</td>
                <td class="text-center" style="font-family: monospace;">{{ $s->nis ?? '-' }}</td>
                <td><strong>{{ $s->name }}</strong></td>
                <td class="text-center">{{ $s->gender }}</td>
                <td class="text-center">{{ $s->classroom->code ?? '-' }}</td>
                <td>
                    @if($s->birth_place || $s->birth_date)
                        {{ $s->birth_place ?? '-' }}, {{ $s->birth_date ? \Carbon\Carbon::parse($s->birth_date)->translatedFormat('d/m/Y') : '' }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $s->guardian_name ?? '-' }} ({{ $s->guardian_relation ?? 'Wali' }})</td>
                <td>{{ $s->guardian_phone ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $s->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($s->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px;">Tidak ada data siswa ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
