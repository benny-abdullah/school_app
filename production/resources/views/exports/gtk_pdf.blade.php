@extends('exports.layout_pdf')

@section('title', 'Daftar Nominatif GTK - SDN Kebonagung')
@section('doc_title', 'DAFTAR NOMINATIF GURU DAN TENAGA KEPENDIDIKAN (GTK)')
@section('doc_subtitle', 'Tahun Ajaran 2025/2026 &bull; Total: ' . $gtks->count() . ' Personil &bull; UPTD SDN Kebonagung')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th>Nama Lengkap & Gelar</th>
            <th style="width: 105px;" class="text-center">NIP / NUPTK</th>
            <th style="width: 30px;" class="text-center">L/P</th>
            <th>Jabatan & Tugas</th>
            <th style="width: 55px;" class="text-center">Status</th>
            <th>Rombel Asuhan</th>
            <th style="width: 85px;">Kontak WA</th>
            <th style="width: 45px;" class="text-center">Akun</th>
        </tr>
    </thead>
    <tbody>
        @forelse($gtks as $idx => $g)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td><strong>{{ $g->name }}</strong></td>
                <td class="text-center" style="font-family: monospace; font-size: 8pt;">
                    {{ $g->nip_nuptk ?? ($g->nuptk ?? '-') }}
                </td>
                <td class="text-center">{{ $g->gender ?? '-' }}</td>
                <td>{{ $g->position ?? ($g->additional_info['jabatan'] ?? 'Pendidik') }}</td>
                <td class="text-center">
                    <span style="font-size: 7.5pt; font-weight: bold;">
                        {{ $g->employment_status ?? 'PNS' }}
                    </span>
                </td>
                <td>
                    @if($g->homeroomClassroom)
                        <strong>Wali Kelas {{ $g->homeroomClassroom->code }}</strong>
                    @else
                        <span style="color: #64748b; font-size: 7.5pt;">Non-Wali</span>
                    @endif
                </td>
                <td>{{ $g->phone ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $g->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($g->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data GTK ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
