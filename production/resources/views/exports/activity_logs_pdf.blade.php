@extends('exports.layout_pdf')

@section('title', 'Berita Acara Audit Trail ISO 27001 - SDN Kebonagung')
@section('doc_title', 'LAPORAN BERITA ACARA AUDIT TRAIL LOG AKTIFITAS SISTEM')
@section('doc_subtitle', 'Kepatuhan Standar Keamanan Informasi ISO/IEC 27001 A.12.4 &bull; Dicetak: ' . date('d/m/Y H:i') . ' WIB &bull; Total: ' . $logs->count() . ' Transaksi')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 95px;" class="text-center">Waktu (WIB)</th>
            <th style="width: 100px;">Aktor Pengguna</th>
            <th style="width: 65px;" class="text-center">Role</th>
            <th style="width: 75px;">Modul</th>
            <th style="width: 70px;" class="text-center">Aksi Event</th>
            <th>Deskripsi Lengkap Aktivitas</th>
            <th style="width: 80px;" class="text-center">IP Address</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $idx => $log)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center" style="font-family: monospace; font-size: 8pt;">
                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                </td>
                <td><strong>{{ $log->user_name }}</strong></td>
                <td class="text-center" style="text-transform: uppercase; font-size: 7.5pt;">{{ $log->user_role }}</td>
                <td>{{ $log->module }}</td>
                <td class="text-center" style="font-family: monospace; font-size: 7.5pt; font-weight: bold;">{{ $log->action }}</td>
                <td>{{ $log->description }}</td>
                <td class="text-center" style="font-family: monospace; font-size: 8pt;">{{ $log->ip_address ?? '127.0.0.1' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada catatan log aktivitas ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
