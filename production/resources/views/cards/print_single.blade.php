<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Siswa - {{ $student->name }} (NISN: {{ $student->nisn }})</title>
    <!-- Favicon Resmi SDN Kebonagung -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 0;
            min-height: 100vh;
        }

        /* Top Action Bar (Hanya tampil di layar) */
        .toolbar {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #e11d48;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #be123c;
        }

        .btn-secondary {
            background-color: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }

        /* CARD CONTAINER: STANDAR CR-80 (85.60 mm x 53.98 mm) */
        .card-container {
            width: 85.6mm;
            height: 53.98mm;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
            border-radius: 3.18mm;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            background-color: #ffffff;
            page-break-inside: avoid;
        }

        /* SISI DEPAN */
        .card-front {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #be123c 0%, #9f1239 40%, #0f172a 100%);
            color: #ffffff;
            padding: 3mm 3.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .front-header {
            display: flex;
            align-items: center;
            gap: 2mm;
            border-bottom: 0.3mm solid rgba(255, 255, 255, 0.25);
            padding-bottom: 1.5mm;
        }

        .school-logo {
            width: 7mm;
            height: 7mm;
            object-fit: contain;
            background: #ffffff;
            border-radius: 1.5mm;
            padding: 0.4mm;
        }

        .header-text {
            line-height: 1.15;
        }

        .instansi {
            font-size: 5.5pt;
            font-weight: 800;
            color: #fecdd3;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .title {
            font-size: 8pt;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.3px;
        }

        .npsn {
            font-size: 4.8pt;
            color: #ffe4e6;
            font-family: monospace;
        }

        /* Body Front */
        .front-body {
            display: flex;
            align-items: center;
            gap: 2.5mm;
            margin: 1mm 0;
        }

        .student-photo {
            width: 16.5mm;
            height: 22mm;
            border-radius: 1.5mm;
            border: 0.4mm solid #ffffff;
            object-fit: cover;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .photo-fallback {
            width: 16.5mm;
            height: 22mm;
            border-radius: 1.5mm;
            border: 0.4mm solid #ffffff;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 5pt;
            font-weight: bold;
        }

        .bio-data {
            flex: 1;
            font-size: 6.2pt;
            line-height: 1.25;
        }

        .bio-label {
            font-size: 4.6pt;
            font-weight: 800;
            color: #fecdd3;
            text-transform: uppercase;
        }

        .bio-name {
            font-size: 7.5pt;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 38mm;
        }

        .bio-row {
            display: flex;
            gap: 3mm;
            margin-top: 0.5mm;
        }

        .student-qr {
            width: 13mm;
            height: 13mm;
            background: #ffffff;
            border-radius: 1.5mm;
            padding: 0.8mm;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .student-qr svg {
            width: 100%;
            height: 100%;
        }

        /* Footer Front (Barcode) */
        .front-footer {
            background-color: #ffffff;
            border-radius: 1.5mm;
            padding: 0.8mm 1.5mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .barcode-container {
            width: 100%;
            height: 5.5mm;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }

        .barcode-container svg {
            width: 100%;
            height: 100%;
        }

        .barcode-text {
            font-size: 5pt;
            font-family: monospace;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1.5px;
            margin-top: 0.2mm;
        }

        /* SISI BELAKANG */
        .card-back {
            width: 100%;
            height: 100%;
            background-color: #0f172a;
            color: #ffffff;
            padding: 3mm 3.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 0.3mm solid #334155;
        }

        .back-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 0.3mm solid #334155;
            padding-bottom: 1mm;
        }

        .back-title {
            font-size: 6pt;
            font-weight: 800;
            color: #fb7185;
            letter-spacing: 0.3px;
        }

        .back-period {
            font-size: 4.8pt;
            font-family: monospace;
            color: #94a3b8;
        }

        .rules-list {
            font-size: 5pt;
            line-height: 1.35;
            color: #cbd5e1;
            margin: 1mm 0;
            padding-left: 3mm;
        }

        .rules-list li {
            margin-bottom: 0.5mm;
        }

        .back-footer {
            border-top: 0.3mm solid #334155;
            padding-top: 1mm;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 5pt;
        }

        .kepsek-info {
            line-height: 1.25;
        }

        .kepsek-title {
            color: #94a3b8;
            font-size: 4.5pt;
        }

        .kepsek-name {
            font-size: 5.8pt;
            font-weight: 800;
            color: #ffffff;
            text-decoration: underline;
            margin-top: 1.5mm;
        }

        .kepsek-nip {
            font-size: 4.8pt;
            font-family: monospace;
            color: #94a3b8;
        }

        .kepsek-qr-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .kepsek-qr {
            width: 8.5mm;
            height: 8.5mm;
            background: #ffffff;
            border-radius: 1mm;
            padding: 0.5mm;
        }
        .kepsek-qr svg {
            width: 100%;
            height: 100%;
        }

        .tte-badge {
            font-size: 3.8pt;
            font-weight: 800;
            color: #34d399;
            margin-top: 0.4mm;
            font-family: monospace;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .toolbar {
                display: none !important;
            }

            .card-container {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }

            @page {
                size: 85.6mm 53.98mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Toolbar -->
    <div class="toolbar">
        <div>
            <div style="font-size: 14px; font-weight: 800; color: #0f172a;">Pratinjau Cetak Kartu Pelajar (CR-80)</div>
            <div style="font-size: 11px; color: #64748b;">Standar ISO 7810 ID-1 (85.60 x 53.98 mm) &bull; {{ $student->name }}</div>
        </div>

        <button type="button" onclick="window.print()" class="btn btn-primary">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/><path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>
            <span>Cetak Kartu Sekarang</span>
        </button>

        <a href="{{ route('cards.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Direktori Kartu
        </a>
    </div>

    <!-- 1. KARTU SISI DEPAN -->
    <div class="card-container">
        <div class="card-front">
            <div class="front-header">
                <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo SDN Kebonagung" class="school-logo">
                <div class="header-text">
                    <div class="instansi">UPTD SDN KEBONAGUNG KOTA PASURUAN</div>
                    <div class="title">KARTU TANDA SISWA</div>
                    <div class="npsn">NPSN: 20535412 &bull; Jl. Panglima Sudirman No. 120</div>
                </div>
            </div>

            <div class="front-body">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="student-photo">
                @else
                    <div class="photo-fallback">
                        <span style="font-size: 14pt;">👤</span>
                        <span>PAS FOTO</span>
                    </div>
                @endif

                <div class="bio-data">
                    <div class="bio-label">Nama Lengkap Siswa</div>
                    <div class="bio-name">{{ $student->name }}</div>

                    <div class="bio-row">
                        <div>
                            <div class="bio-label">NISN Siswa</div>
                            <div style="font-weight: 800; font-family: monospace;">{{ $student->nisn }}</div>
                        </div>
                        <div>
                            <div class="bio-label">Rombel</div>
                            <div style="font-weight: 800; color: #fde047;">Kelas {{ $student->classroom->code ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="bio-row">
                        <div>
                            <div class="bio-label">Jenis Kelamin</div>
                            <div style="font-weight: 700;">{{ $student->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                        </div>
                        <div>
                            <div class="bio-label">Status Siswa</div>
                            <div style="font-weight: 800; color: #86efac;">AKTIF</div>
                        </div>
                    </div>
                </div>

                <div class="student-qr">
                    {!! $qrCodeSvg !!}
                </div>
            </div>

            <div class="front-footer">
                <div class="barcode-container">
                    {!! $barcodeSvg !!}
                </div>
                <div class="barcode-text">* {{ $student->nisn }} *</div>
            </div>
        </div>
    </div>

    <!-- 2. KARTU SISI BELAKANG -->
    <div class="card-container">
        <div class="card-back">
            <div class="back-header">
                <span class="back-title">KETENTUAN KARTU TANDA SISWA</span>
                <span class="back-period">TP. 2024 - 2026</span>
            </div>

            <ol class="rules-list">
                <li>Kartu ini adalah identitas resmi siswa UPTD SDN Kebonagung Kota Pasuruan.</li>
                <li>Wajib dibawa setiap hari efektif untuk presensi barcode gerbang tap masuk & pulang.</li>
                <li>Kartu tidak boleh dipindahtangankan, dipinjamkan, atau digandakan tanpa izin.</li>
                <li>Jika kartu hilang/rusak, segera laporkan ke Ruang Tata Usaha untuk penggantian.</li>
                <li>Penemu kartu ini dimohon mengembalikan ke: Jl. Panglima Sudirman No. 120 Kota Pasuruan.</li>
            </ol>

            <div class="back-footer">
                <div class="kepsek-info">
                    <div class="kepsek-title">Kota Pasuruan, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}</div>
                    <div class="kepsek-title">Kepala UPTD SDN Kebonagung,</div>
                    <div class="kepsek-name">{{ $kepsek?->name ?? 'WAHYU AGUNG SETIYAWAN S.Pd' }}</div>
                    <div class="kepsek-nip">NIP. {{ $kepsek?->nip_nuptk ?? '198708222019031004' }}</div>
                </div>

                <div class="kepsek-qr-box">
                    <div class="kepsek-qr">
                        {!! $kepsekQrSvg !!}
                    </div>
                    <div class="tte-badge">TTE VALID</div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
