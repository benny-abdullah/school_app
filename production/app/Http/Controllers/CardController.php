<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Services\CardService;
use App\Services\ExportService;
use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * Antarmuka Utama Direktori & Pratinjau Kartu Pelajar Digital ISO CR-80
     */
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('grade_level')->orderBy('section')->get();
        $selectedClassroomId = $request->get('classroom_id');
        $search = $request->get('search');
        $gender = $request->get('gender');

        $query = Student::with('classroom')->where('status', 'active');

        if ($selectedClassroomId && $selectedClassroomId !== 'all') {
            $query->where('classroom_id', $selectedClassroomId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($gender && in_array($gender, ['L', 'P'])) {
            $query->where('gender', $gender);
        }

        $students = $query->orderBy('name')->paginate(12)->withQueryString();

        // Resolusi Siswa Terpilih untuk Pratinjau Live CR-80
        $previewId = $request->get('preview');
        $previewStudent = null;

        if ($previewId) {
            $previewStudent = Student::with('classroom')->find($previewId);
        }

        if (!$previewStudent && $students->count() > 0) {
            $previewStudent = $students->first();
        }

        // Data SVG Barcode & QR Code untuk Siswa Pratinjau
        $barcodeSvg = null;
        $qrCodeSvg = null;
        $kepsekQrSvg = null;

        if ($previewStudent) {
            $barcodeSvg = CardService::generateBarcodeSvg($previewStudent->nisn);
            $qrCodeSvg = CardService::generateQrCodeSvg(CardService::getStudentPayload($previewStudent));
            $kepsekQrSvg = CardService::generateKepsekSignatureQr();
        }

        $kepsek = ExportService::getKepalaSekolah();

        $stats = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_classes'  => Classroom::count(),
            'total_male'     => Student::where('status', 'active')->where('gender', 'L')->count(),
            'total_female'   => Student::where('status', 'active')->where('gender', 'P')->count(),
        ];

        return view('cards.index', compact(
            'students',
            'classrooms',
            'selectedClassroomId',
            'search',
            'gender',
            'previewStudent',
            'barcodeSvg',
            'qrCodeSvg',
            'kepsekQrSvg',
            'kepsek',
            'stats'
        ));
    }

    /**
     * Tampilan Cetak Satuan Kartu Pelajar ISO CR-80 (Standar Printer PVC 85.6mm x 53.98mm)
     */
    public function printSingle(Student $student)
    {
        $student->load('classroom');
        $barcodeSvg = CardService::generateBarcodeSvg($student->nisn);
        $qrCodeSvg = CardService::generateQrCodeSvg(CardService::getStudentPayload($student));
        $kepsekQrSvg = CardService::generateKepsekSignatureQr();
        $kepsek = ExportService::getKepalaSekolah();

        return view('cards.print_single', compact('student', 'barcodeSvg', 'qrCodeSvg', 'kepsekQrSvg', 'kepsek'));
    }

    /**
     * Tampilan Cetak Massal Lembar Kerja A4 (Grid 8-10 Kartu Per Lembar Rombel)
     */
    public function printBulk(Request $request)
    {
        $classroomId = $request->get('classroom_id');
        $studentIds = $request->get('ids');
        $side = $request->get('side', 'both'); // 'front', 'back', 'both'

        $query = Student::with('classroom')->where('status', 'active');

        if (!empty($studentIds)) {
            $ids = is_array($studentIds) ? $studentIds : explode(',', $studentIds);
            $query->whereIn('id', $ids);
        } elseif (!empty($classroomId) && $classroomId !== 'all') {
            $query->where('classroom_id', $classroomId);
        } else {
            $query->limit(8);
        }

        $students = $query->orderBy('name')->get();
        $classroom = !empty($classroomId) && $classroomId !== 'all' ? Classroom::find($classroomId) : null;

        // Siapkan aset Barcode & QR Code untuk tiap siswa
        $cardItems = $students->map(function ($s) {
            return [
                'student'     => $s,
                'barcode_svg' => CardService::generateBarcodeSvg($s->nisn),
                'qr_svg'      => CardService::generateQrCodeSvg(CardService::getStudentPayload($s)),
            ];
        });

        $kepsekQrSvg = CardService::generateKepsekSignatureQr();
        $kepsek = ExportService::getKepalaSekolah();

        return view('cards.print_bulk', compact('cardItems', 'classroom', 'side', 'kepsek', 'kepsekQrSvg'));
    }

    /**
     * Verifikasi Publik Keabsahan Kartu Siswa (Saat QR di-scan smartphone)
     */
    public function verify(Student $student)
    {
        $student->load('classroom');
        $kepsek = ExportService::getKepalaSekolah();
        $barcodeSvg = CardService::generateBarcodeSvg($student->nisn);

        return view('cards.verify', compact('student', 'kepsek', 'barcodeSvg'));
    }
}
