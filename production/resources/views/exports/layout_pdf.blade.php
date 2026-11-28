<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dokumen Resmi SDN Kebonagung')</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.35;
        }
        /* Kop Surat Resmi */
        .kop-table {
            width: 100%;
            border-bottom: 3px double #0f172a;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-text {
            text-align: center;
        }
        .kop-instansi {
            font-size: 11pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-sekolah {
            font-size: 14pt;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            margin: 2px 0;
            letter-spacing: 0.5px;
        }
        .kop-alamat {
            font-size: 8.5pt;
            color: #64748b;
        }
        /* Judul Dokumen */
        .doc-header {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .doc-subtitle {
            font-size: 8.5pt;
            color: #64748b;
        }
        /* Tabel Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        table.data-table th {
            background-color: #e11d48;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        table.data-table th.text-center {
            text-align: center;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        table.data-table td.text-center {
            text-align: center;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        /* Tanda Tangan */
        .ttd-table {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .ttd-table td {
            vertical-align: top;
            font-size: 9pt;
        }
        .ttd-box {
            text-align: center;
            width: 250px;
            float: right;
        }
        .ttd-space {
            height: 55px;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .ttd-nip {
            font-size: 8.5pt;
            color: #475569;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-active { background-color: #ecfdf5; color: #047857; }
        .badge-inactive { background-color: #fef2f2; color: #b91c1c; }
    </style>
</head>
<body>
    <!-- Kop Surat Resmi -->
    <table class="kop-table">
        <tr>
            <td class="kop-text">
                <div class="kop-instansi">Pemerintah Kota Pasuruan &bull; Dinas Pendidikan dan Kebudayaan</div>
                <div class="kop-sekolah">UPTD SDN KEBONAGUNG KOTA PASURUAN</div>
                <div class="kop-alamat">Jl. Panglima Sudirman No. 120, Purworejo, Kec. Purworejo, Kota Pasuruan, Jawa Timur 67116 | NPSN: 20535412</div>
            </td>
        </tr>
    </table>

    <!-- Header Dokumen -->
    <div class="doc-header">
        <div class="doc-title">@yield('doc_title')</div>
        <div class="doc-subtitle">@yield('doc_subtitle')</div>
    </div>

    <!-- Isi Dokumen -->
    @yield('content')

    <!-- Kolom Tanda Tangan -->
    @hasSection('signature')
        @yield('signature')
    @else
        <table class="ttd-table">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%; text-align: center;">
                    <div>Kota Pasuruan, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
                    <div style="margin-top: 2px;">Kepala UPTD SDN Kebonagung,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name">{{ $kepsek?->name ?? 'WAHYU AGUNG SETIYAWAN S.Pd' }}</div>
                    <div class="ttd-nip">NIP. {{ $kepsek?->nip_nuptk ?? '198708222019031004' }}</div>
                </td>
            </tr>
        </table>
    @endif
</body>
</html>
