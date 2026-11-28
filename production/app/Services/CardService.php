<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Picqer\Barcode\BarcodeGeneratorSVG;

class CardService
{
    /**
     * Generate Barcode 1D Code 128 dalam format SVG murni (Tajam dan bebas blur saat dicetak)
     */
    public static function generateBarcodeSvg(string $code, int $widthFactor = 2, int $totalHeight = 45): string
    {
        $generator = new BarcodeGeneratorSVG();
        // Bersihkan karakter selain alfanumerik
        $cleanedCode = preg_replace('/[^A-Za-z0-9]/', '', $code);
        if (empty($cleanedCode)) {
            $cleanedCode = '0000000000';
        }

        $svg = $generator->getBarcode($cleanedCode, $generator::TYPE_CODE_128, $widthFactor, $totalHeight);
        
        // Buang deklarasi <?xml agar bisa di-embed langsung di HTML Blade
        $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
        return trim($svg);
    }

    /**
     * Generate Token Keamanan HMAC-SHA256 untuk proteksi anti-pemalsuan kartu
     */
    public static function generateHmacToken(Student $student): string
    {
        $secret = config('app.key', 'starkids-kebonagung-secret');
        $payload = "SDN-KEBONAGUNG:{$student->nisn}:{$student->id}";
        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Payload verifikasi resmi gerbang & verifikasi publik via scan QR
     */
    public static function getStudentPayload(Student $student): string
    {
        return route('cards.verify', $student);
    }

    /**
     * Generate QR Code Otentikasi Siswa dalam format SVG murni
     */
    public static function generateQrCodeSvg(string $data, int $size = 120): string
    {
        $options = new QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'outputBase64'    => false,
            'svgAddXmlHeader' => false,
            'addQuietzone'    => false,
        ]);

        $qrcode = new QRCode($options);
        $svg = (string)$qrcode->render($data);
        return trim($svg);
    }

    /**
     * QR Code Validasi Tanda Tangan Digital Kepala Sekolah
     */
    public static function generateKepsekSignatureQr(?User $kepsek = null): string
    {
        $kepsek = $kepsek ?? ExportService::getKepalaSekolah();
        $name = $kepsek?->name ?? 'WAHYU AGUNG SETIYAWAN S.Pd';
        $nip = $kepsek?->nip_nuptk ?? '198708222019031004';
        $payload = "TTE-VALID:UPTD SDN KEBONAGUNG:{$nip}:{$name}:NPSN 20535412";

        return self::generateQrCodeSvg($payload, 90);
    }
}
