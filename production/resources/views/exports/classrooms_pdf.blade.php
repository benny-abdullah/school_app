@extends('exports.layout_pdf')

@section('title', 'Rekapitulasi Rombel & SK Wali Kelas - SDN Kebonagung')
@section('doc_title', 'REKAPITULASI ROMBONGAN BELAJAR & PENETAPAN SK WALI KELAS')
@section('doc_subtitle', 'Tahun Ajaran 2025/2026 &bull; Total: ' . $classrooms->count() . ' Rombel Kelas &bull; UPTD SDN Kebonagung')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 45px;" class="text-center">Kode</th>
            <th>Nama Rombel & Ruang Fisik</th>
            <th style="width: 50px;" class="text-center">Tingkat</th>
            <th>Guru Wali Kelas Resmi</th>
            <th style="width: 110px;" class="text-center">NIP Guru</th>
            <th style="width: 65px;" class="text-center">Siswa / Kuota</th>
            <th style="width: 60px;" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($classrooms as $idx => $cls)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center" style="font-family: monospace; font-weight: bold;">{{ $cls->code }}</td>
                <td>
                    <strong>{{ $cls->name }}</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">{{ $cls->room_name ?? 'Ruang Kelas ' . $cls->code }}</span>
                </td>
                <td class="text-center">Kelas {{ $cls->grade_level }}</td>
                <td>
                    @if($cls->homeroomTeacher)
                        <strong>{{ $cls->homeroomTeacher->name }}</strong>
                    @elseif($cls->homeroom_teacher)
                        {{ $cls->homeroom_teacher }}
                    @else
                        <span style="color: #b45309; font-style: italic;">Belum Ada Wali</span>
                    @endif
                </td>
                <td class="text-center" style="font-family: monospace; font-size: 8pt;">
                    {{ $cls->homeroomTeacher?->nip_nuptk ?? ($cls->homeroom_nip ?? '-') }}
                </td>
                <td class="text-center">
                    <strong>{{ $cls->students_count > 0 ? $cls->students_count : $cls->total_students }}</strong> / {{ $cls->capacity }}
                </td>
                <td class="text-center">
                    <span class="badge {{ $cls->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($cls->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data rombel ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
