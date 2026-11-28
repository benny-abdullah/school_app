@extends('exports.layout_pdf')

@section('title', 'Direktori Akun Pengguna Sistem - SDN Kebonagung')
@section('doc_title', 'LAPORAN DIREKTORI MANAJEMEN AKUN PENGGUNA SISTEM')
@section('doc_subtitle', 'Sistem STAR-KIDS UPTD SDN Kebonagung Kota Pasuruan &bull; Dicetak: ' . date('d/m/Y H:i') . ' WIB &bull; Total: ' . $users->count() . ' Akun')

@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 140px;">Nama Pengguna</th>
            <th style="width: 90px;">Username</th>
            <th style="width: 120px;">Email</th>
            <th style="width: 85px;" class="text-center">Role Akses</th>
            <th style="width: 100px;" class="text-center">NIP / NISN</th>
            <th style="width: 80px;" class="text-center">No. HP</th>
            <th style="width: 60px;" class="text-center">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users as $idx => $user)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td><strong>{{ $user->name }}</strong></td>
                <td style="font-family: monospace; font-size: 8pt;">{{ $user->username ?? '-' }}</td>
                <td style="font-size: 8pt;">{{ $user->email ?? '-' }}</td>
                <td class="text-center" style="font-weight: bold; font-size: 7.5pt;">
                    @if($user->role === 'admin')
                        KEPALA SEKOLAH
                    @elseif($user->role === 'operator')
                        OPERATOR TU
                    @elseif($user->role === 'guru/walikelas')
                        GURU / WALI KELAS
                    @elseif($user->role === 'orangtua')
                        ORANG TUA MURID
                    @else
                        {{ strtoupper($user->role) }}
                    @endif
                </td>
                <td class="text-center" style="font-family: monospace; font-size: 8pt;">
                    {{ $user->nip_nuptk ?? $user->nisn ?? '-' }}
                </td>
                <td class="text-center" style="font-size: 8pt;">{{ $user->phone ?? '-' }}</td>
                <td class="text-center">
                    @if($user->status === 'active')
                        <span class="badge badge-active">AKTIF</span>
                    @else
                        <span class="badge badge-inactive">SUSPEND</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data pengguna ditemukan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
