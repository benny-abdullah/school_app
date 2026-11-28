<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Resolusi data resmi Kepala Sekolah dari basis data GTK / Pengguna
     * Mengutamakan guru/GTK dengan jabatan 'Kepala Sekolah', role 'admin', atau nama Wahyu Agung
     */
    public static function getKepalaSekolah(): ?User
    {
        return User::where(function ($q) {
            $q->where('additional_info->jabatan', 'like', '%Kepala Sekolah%')
              ->orWhere('role', 'admin');
        })->first() ?? User::where('name', 'like', '%Wahyu Agung%')->first();
    }

    /**
     * Download Excel (.xlsx) dengan PhpSpreadsheet yang terformat profesional
     *
     * @param string $filename Nama berkas (tanpa ekstensi .xlsx)
     * @param string $title Judul Dokumen Utama
     * @param string $subtitle Sub-judul / Periode
     * @param array $headers Array asosiatif [col_key => 'Label Kolom']
     * @param array $rows Data baris array
     * @return StreamedResponse
     */
    public static function excel(string $filename, string $title, string $subtitle, array $headers, array $rows): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr(preg_replace('/[^A-Za-z0-9 ]/', '', $title), 0, 30));

        $headerKeys = array_keys($headers);
        $headerLabels = array_values($headers);
        $totalCols = count($headerLabels);
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

        // 1. Kop / Header Dokumen
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->setCellValue('A1', 'PEMERINTAH KOTA PASURUAN - UPTD SDN KEBONAGUNG');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->setCellValue('A2', strtoupper($title));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells("A3:{$lastColLetter}3");
        $sheet->setCellValue('A3', $subtitle . ' | Dicetak: ' . date('d/m/Y H:i') . ' WIB');
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 2. Baris Header Tabel (Row 5)
        $headerRow = 5;
        foreach ($headerLabels as $index => $label) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $label);
        }

        // Styling Baris Header Tabel
        $headerRange = "A{$headerRow}:{$lastColLetter}{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE11D48'); // Rose-600
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        // 3. Mengisi Data Baris (Mulai Row 6)
        $currentRow = 6;
        foreach ($rows as $rowIndex => $rowData) {
            foreach ($headerKeys as $colIndex => $key) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $value = $rowData[$key] ?? '-';
                
                // Set explicit string untuk nomor NISN / NIP agar tidak berubah format ilmiah
                if (in_array($key, ['nip', 'nip_nuptk', 'nuptk', 'nisn', 'nis', 'phone', 'phone_number', 'guardian_phone'])) {
                    $sheet->setCellValueExplicit("{$colLetter}{$currentRow}", (string)$value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->getStyle("{$colLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } elseif ($key === 'no' || $key === 'id') {
                    $sheet->setCellValue("{$colLetter}{$currentRow}", $value);
                    $sheet->getStyle("{$colLetter}{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else {
                    $sheet->setCellValue("{$colLetter}{$currentRow}", $value);
                }
            }

            // Zebra Striping
            if ($rowIndex % 2 === 1) {
                $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }
            $sheet->getRowDimension($currentRow)->setRowHeight(20);
            $currentRow++;
        }

        // 4. Border Seluruh Tabel Data
        $tableRange = "A{$headerRow}:{$lastColLetter}" . ($currentRow - 1);
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');

        // 5. Tanda Tangan Resmi Kepala Sekolah (Dinamis dari Basis Data Guru)
        $kepsek = self::getKepalaSekolah();
        $kepsekName = $kepsek?->name ?? 'WAHYU AGUNG SETIYAWAN S.Pd';
        $kepsekNip = $kepsek?->nip_nuptk ?? '198708222019031004';

        $ttdRow = $currentRow + 2;
        $ttdColStart = max(1, $totalCols - 1);
        $ttdColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ttdColStart);
        $ttdColEndLetter = $lastColLetter;

        $sheet->mergeCells("{$ttdColLetter}{$ttdRow}:{$ttdColEndLetter}{$ttdRow}");
        $sheet->setCellValue("{$ttdColLetter}{$ttdRow}", 'Kota Pasuruan, ' . Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'));
        $sheet->getStyle("{$ttdColLetter}{$ttdRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ttdRow++;
        $sheet->mergeCells("{$ttdColLetter}{$ttdRow}:{$ttdColEndLetter}{$ttdRow}");
        $sheet->setCellValue("{$ttdColLetter}{$ttdRow}", 'Kepala UPTD SDN Kebonagung,');
        $sheet->getStyle("{$ttdColLetter}{$ttdRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ttdRow += 3; // Ruang tanda tangan
        $sheet->mergeCells("{$ttdColLetter}{$ttdRow}:{$ttdColEndLetter}{$ttdRow}");
        $sheet->setCellValue("{$ttdColLetter}{$ttdRow}", $kepsekName);
        $sheet->getStyle("{$ttdColLetter}{$ttdRow}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("{$ttdColLetter}{$ttdRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ttdRow++;
        $sheet->mergeCells("{$ttdColLetter}{$ttdRow}:{$ttdColEndLetter}{$ttdRow}");
        $sheet->setCellValueExplicit("{$ttdColLetter}{$ttdRow}", 'NIP. ' . $kepsekNip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->getStyle("{$ttdColLetter}{$ttdRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 6. Auto-size kolom
        for ($col = 1; $col <= $totalCols; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // 7. Response Stream
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, "{$filename}.xlsx", [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Download PDF dengan Kop Resmi SDN Kebonagung
     *
     * @param string $filename Nama berkas (tanpa ekstensi .pdf)
     * @param string $view Nama Blade view
     * @param array $data Data untuk Blade view
     * @param string $orientation 'portrait' atau 'landscape'
     * @return \Illuminate\Http\Response
     */
    public static function pdf(string $filename, string $view, array $data, string $orientation = 'portrait')
    {
        // Inject data kepala sekolah otomatis ke view jika belum disertakan
        if (!isset($data['kepsek'])) {
            $data['kepsek'] = self::getKepalaSekolah();
        }

        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('a4', $orientation);
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'sans-serif',
        ]);

        return $pdf->download("{$filename}.pdf");
    }
}
