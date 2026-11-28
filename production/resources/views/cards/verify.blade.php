<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keabsahan Kartu Siswa - {{ $student->name }} | SDN Kebonagung</title>
    <!-- Favicon Resmi SDN Kebonagung -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.08);
        }

        .card-top {
            background: linear-gradient(135deg, #be123c 0%, #9f1239 50%, #0f172a 100%);
            color: #ffffff;
            padding: 28px 24px 24px;
            text-align: center;
            position: relative;
        }

        .school-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            background: #ffffff;
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .badge-verified {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(52, 211, 153, 0.4);
            color: #34d399;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .card-body {
            padding: 24px;
        }

        .photo-wrapper {
            display: flex;
            justify-content: center;
            margin-top: -55px;
            margin-bottom: 16px;
        }

        .photo-img {
            width: 80px;
            height: 100px;
            border-radius: 12px;
            border: 3px solid #ffffff;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.15);
            object-fit: cover;
            background: #ffffff;
        }

        .photo-placeholder {
            width: 80px;
            height: 100px;
            border-radius: 12px;
            border: 3px solid #ffffff;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.15);
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 10px;
            font-weight: bold;
        }

        .student-name {
            font-size: 18px;
            font-weight: 900;
            text-align: center;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .student-nisn {
            font-family: monospace;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            color: #e11d48;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-table td.label {
            color: #64748b;
            font-weight: 600;
            width: 40%;
        }

        .info-table td.val {
            color: #0f172a;
            font-weight: 700;
        }

        .barcode-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 16px;
        }

        .barcode-svg {
            max-width: 220px;
            height: 38px;
            overflow: hidden;
            display: flex;
            justify-content: center;
        }
        .barcode-svg svg {
            width: 100%;
            height: 100%;
        }

        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <div class="card-top">
            <img src="{{ asset('images/logo_sdnkebonagung.jpeg') }}" alt="Logo SDN Kebonagung" class="school-logo">
            <div class="badge-verified">
                <i class="bi bi-patch-check-fill"></i> KARTU SISWA TERVERIFIKASI
            </div>
            <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #ffe4e6;">
                UPTD SDN KEBONAGUNG KOTA PASURUAN
            </div>
            <div style="font-size: 10px; color: #fecdd3; font-family: monospace; margin-top: 2px;">
                NPSN: 20535412 &bull; Jl. Panglima Sudirman No. 120
            </div>
        </div>

        <div class="card-body">
            <div class="photo-wrapper">
                @if($student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="photo-img">
                @else
                    <div class="photo-placeholder">
                        <i class="bi bi-person-fill" style="font-size: 32px;"></i>
                        <span>3x4</span>
                    </div>
                @endif
            </div>

            <div class="student-name">{{ $student->name }}</div>
            <div class="student-nisn">NISN: {{ $student->nisn }}</div>

            <table class="info-table">
                <tr>
                    <td class="label">Rombongan Belajar</td>
                    <td class="val">
                        <span style="color: #4f46e5; background: #eef2ff; padding: 2px 8px; border-radius: 6px; font-weight: 800;">
                            Kelas {{ $student->classroom->code ?? '-' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Tingkat Kelas</td>
                    <td class="val">Tingkat {{ $student->classroom->grade_level ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="val">{{ $student->gender == 'L' ? 'Laki-Laki (L)' : 'Perempuan (P)' }}</td>
                </tr>
                <tr>
                    <td class="label">Status Kesiswaan</td>
                    <td class="val" style="color: #16a34a;">
                        <i class="bi bi-check-circle-fill"></i> Terdaftar Aktif
                    </td>
                </tr>
                <tr>
                    <td class="label">Kepala Sekolah</td>
                    <td class="val">{{ $kepsek?->name ?? 'WAHYU AGUNG SETIYAWAN S.Pd' }}</td>
                </tr>
                <tr>
                    <td class="label">NIP Kepala Sekolah</td>
                    <td class="val" style="font-family: monospace;">{{ $kepsek?->nip_nuptk ?? '198708222019031004' }}</td>
                </tr>
                <tr>
                    <td class="label">Waktu Pengecekan</td>
                    <td class="val" style="font-family: monospace; font-size: 11px;">
                        {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y - H:i:s') }} WIB
                    </td>
                </tr>
            </table>

            <div class="barcode-box">
                <div class="barcode-svg">
                    {!! $barcodeSvg !!}
                </div>
                <div style="font-family: monospace; font-size: 11px; font-weight: 800; color: #475569; margin-top: 4px;">
                    * {{ $student->nisn }} *
                </div>
            </div>

            <div class="footer-note">
                Sistem STAR-KIDS SDN Kebonagung Kota Pasuruan.<br>
                Kartu ini sah sebagai tanda pengenal dan alat pemindai presensi digital.
            </div>
        </div>
    </div>

</body>
</html>
