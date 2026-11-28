<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Massal Kartu Siswa Lembar A4 - {{ $classroom ? 'Kelas ' . $classroom->code : 'SDN Kebonagung' }}</title>
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

        /* Toolbar Layar */
        .toolbar {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 210mm;
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

        /* LEMBAR A4 (210mm x 297mm) */
        .a4-sheet {
            width: 210mm;
            min-height: 297mm;
            background-color: #ffffff;
            padding: 10mm 12mm;
            margin-bottom: 20mm;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1);
            position: relative;
            page-break-after: always;
        }

        .sheet-header {
            text-align: center;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4mm;
            margin-bottom: 6mm;
            font-size: 8pt;
            color: #64748b;
        }
        .sheet-header strong {
            color: #0f172a;
            font-size: 10pt;
        }

        /* GRID 2 KOLOM X 4 BARIS = 8 KARTU PER LEMBAR */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 85.6mm);
            column-gap: 12mm;
            row-gap: 8mm;
            justify-content: center;
        }

        /* FRAME KARTU CR-80 */
        .card-item {
            width: 85.6mm;
            height: 53.98mm;
            border-radius: 3.18mm;
            position: relative;
            overflow: hidden;
            border: 1px dashed #cbd5e1; /* Garis potong / cutting guide */
            box-sizing: border-box;
            background-color: #ffffff;
        }

        /* SISI DEPAN */
        .card-front {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #be123c 0%, #9f1239 40%, #0f172a 100%);
            color: #ffffff;
            padding: 2.8mm 3.2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .front-header {
            display: flex;
            align-items: center;
            gap: 2mm;
            border-bottom: 0.3mm solid rgba(255, 255, 255, 0.25);
            padding-bottom: 1.2mm;
        }

        .school-logo {
            width: 6.5mm;
            height: 6.5mm;
            object-fit: contain;
            background: #ffffff;
            border-radius: 1.2mm;
            padding: 0.3mm;
        }

        .instansi {
            font-size: 5pt;
            font-weight: 800;
            color: #fecdd3;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .title {
            font-size: 7.5pt;
            font-weight: 900;
            color: #ffffff;
        }

        .npsn {
            font-size: 4.5pt;
            color: #ffe4e6;
            font-family: monospace;
        }

        .front-body {
            display: flex;
            align-items: center;
            gap: 2.2mm;
            margin: 0.8mm 0;
        }

        .student-photo {
            width: 15.5mm;
            height: 21mm;
            border-radius: 1.2mm;
            border: 0.3mm solid #ffffff;
            object-fit: cover;
            background: #ffffff;
        }

        .photo-fallback {
            width: 15.5mm;
            height: 21mm;
            border-radius: 1.2mm;
            border: 0.3mm solid #ffffff;
            background: #f1f5f9;
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 4.5pt;
            font-weight: bold;
        }

        .bio-data {
            flex: 1;
            font-size: 5.8pt;
            line-height: 1.2;
        }

        .bio-label {
            font-size: 4.3pt;
            font-weight: 800;
            color: #fecdd3;
            text-transform: uppercase;
        }

        .bio-name {
            font-size: 7pt;
            font-weight: 800;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 38mm;
        }

        .bio-row {
            display: flex;
            gap: 2.5mm;
            margin-top: 0.4mm;
        }

        .student-qr {
            width: 12mm;
            height: 12mm;
            background: #ffffff;
            border-radius: 1.2mm;
            padding: 0.6mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .student-qr svg {
            width: 100%;
            height: 100%;
        }

        .front-footer {
            background-color: #ffffff;
            border-radius: 1.2mm;
            padding: 0.6mm 1.2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .barcode-container {
            width: 100%;
            height: 5mm;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        .barcode-container svg {
            width: 100%;
            height: 100%;
        }

        .barcode-text {
            font-size: 4.8pt;
            font-family: monospace;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1.2px;
        }

        /* PRINT MEDIA STYLES */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .toolbar {
                display: none !important;
            }

            .a4-sheet {
                box-shadow: none;
                margin: 0;
                padding: 8mm 10mm;
                page-break-after: always;
            }

            .card-item {
                border: 0.5px dashed #cbd5e1; /* Garis potong halus */
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Toolbar -->
    <div class="toolbar">
        <div>
            <div style="font-size: 15px; font-weight: 800; color: #0f172a;">
                Cetak Massal Kartu Siswa (Format Lembar A4)
            </div>
            <div style="font-size: 11px; color: #64748b;">
                {{ $classroom ? 'Rombel: Kelas ' . $classroom->code . ' (' . $classroom->name . ')' : 'Seluruh Siswa Terpilih' }} &bull; Total: {{ $cardItems->count() }} Kartu Siswa
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/><path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>
                <span>Cetak Lembar A4 (Ctrl + P)</span>
            </button>

            <a href="{{ route('cards.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <!-- CHUNKING TIAP 8 KARTU PER LEMBAR A4 -->
    @php
        $chunks = $cardItems->chunk(8);
    @endphp

    @forelse($chunks as $pageIndex => $pageCards)
        <div class="a4-sheet">
            <div class="sheet-header">
                <strong>UPTD SDN KEBONAGUNG KOTA PASURUAN - LEMBAR CETAK KARTU PELAJAR ISO CR-80</strong><br>
                <span>Halaman {{ $pageIndex + 1 }} dari {{ $chunks->count() }} &bull; Gunakan kertas Art Paper / Glossy Paper 230-260 gsm lalu potong mengikuti garis batas putus-putus.</span>
            </div>

            <div class="card-grid">
                @foreach($pageCards as $item)
                    @php
                        $s = $item['student'];
                    @endphp
                    <div class="card-item">
                        <div class="card-front">
                            <div class="front-header">
                                <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo" class="school-logo">
                                <div class="header-text">
                                    <div class="instansi">UPTD SDN KEBONAGUNG KOTA PASURUAN</div>
                                    <div class="title">KARTU TANDA SISWA</div>
                                    <div class="npsn">NPSN: 20535412 &bull; Kota Pasuruan</div>
                                </div>
                            </div>

                            <div class="front-body">
                                @if($s->photo)
                                    <img src="{{ asset('storage/' . $s->photo) }}" alt="{{ $s->name }}" class="student-photo">
                                @else
                                    <div class="photo-fallback">
                                        <span style="font-size: 11pt;">👤</span>
                                        <span>3x4</span>
                                    </div>
                                @endif

                                <div class="bio-data">
                                    <div class="bio-label">Nama Siswa</div>
                                    <div class="bio-name">{{ $s->name }}</div>

                                    <div class="bio-row">
                                        <div>
                                            <div class="bio-label">NISN</div>
                                            <div style="font-weight: 800; font-family: monospace;">{{ $s->nisn }}</div>
                                        </div>
                                        <div>
                                            <div class="bio-label">Kelas</div>
                                            <div style="font-weight: 800; color: #fde047;">{{ $s->classroom->code ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="bio-row">
                                        <div>
                                            <div class="bio-label">Gender</div>
                                            <div style="font-weight: 700;">{{ $s->gender == 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                                        </div>
                                        <div>
                                            <div class="bio-label">Status</div>
                                            <div style="font-weight: 800; color: #86efac;">AKTIF</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="student-qr">
                                    {!! $item['qr_svg'] !!}
                                </div>
                            </div>

                            <div class="front-footer">
                                <div class="barcode-container">
                                    {!! $item['barcode_svg'] !!}
                                </div>
                                <div class="barcode-text">* {{ $s->nisn }} *</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="a4-sheet" style="display: flex; align-items: center; justify-content: center;">
            <p style="color: #94a3b8; font-size: 14px;">Tidak ada siswa yang dipilih untuk dicetak.</p>
        </div>
    @endforelse

</body>
</html>
