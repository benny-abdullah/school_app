<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Student;
use App\Services\CardService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChildProfileController extends Controller
{
    /**
     * Dapatkan siswa yang terikat dengan akun orang tua yang sedang login
     */
    protected function getAuthenticatedStudent(): ?Student
    {
        $user = Auth::user();
        if (!$user || !$user->isOrangTua()) {
            abort(403, 'Hanya akun wali murid / orang tua yang berhak mengakses menu ini.');
        }

        $studentNisn = $user->nisn ?? ($user->additional_info['student_nisn'] ?? $user->username);

        return Student::with(['classroom', 'todayAttendance'])
            ->where('nisn', $studentNisn)
            ->first();
    }

    /**
     * Tampilkan Halaman Detail Profil & Biodata Lengkap Anak
     */
    public function show()
    {
        $user = Auth::user();
        $student = $this->getAuthenticatedStudent();

        if (!$student) {
            return redirect()->route('ortu.dashboard')
                ->with('error', 'Data biodata anak tidak ditemukan dalam basis data Dapodik sekolah.');
        }

        $classroom = $student->classroom;

        // Generator Barcode & QR Code Digital Card CR-80
        $barcodeSvg = CardService::generateBarcodeSvg($student->nisn);
        $qrCodeSvg = CardService::generateQrCodeSvg(CardService::getStudentPayload($student));
        $kepsekQrSvg = CardService::generateKepsekSignatureQr();
        $kepsek = ExportService::getKepalaSekolah();

        return view('ortu.child_profile', compact(
            'user',
            'student',
            'classroom',
            'barcodeSvg',
            'qrCodeSvg',
            'kepsekQrSvg',
            'kepsek'
        ));
    }

    /**
     * Perbarui Pas Foto Resmi Siswa oleh Orang Tua
     * (Hanya foto & kontak wali yang boleh diubah, data pokok akademik berstatus Read-Only)
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();
        $student = $this->getAuthenticatedStudent();

        if (!$student) {
            abort(404, 'Data anak tidak ditemukan.');
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'photo.required' => 'Silakan pilih berkas pas foto anak terlebih dahulu.',
            'photo.image'    => 'Berkas harus berupa gambar foto.',
            'photo.mimes'    => 'Format foto harus berupa JPG, JPEG, PNG, atau WebP.',
            'photo.max'      => 'Ukuran foto maksimal adalah 2 MB.',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada di storage
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('students', 'public');
            $student->update(['photo' => $path]);

            ActivityLog::record(
                'UPDATE_STUDENT_PHOTO',
                'Portal Orang Tua',
                "Wali Murid ({$user->name}) memperbarui pas foto siswa ananda {$student->name} (NISN: {$student->nisn})"
            );

            return redirect()->route('ortu.child.profile')
                ->with('success', "Pas foto resmi ananda {$student->name} berhasil diperbarui dan tersimpan ke sistem!");
        }

        return back()->with('error', 'Gagal memproses unggah foto. Silakan coba kembali.');
    }

    /**
     * Proteksi Keamanan: Orang Tua DILARANG KERAS Menghapus Data Anak
     */
    public function destroy()
    {
        abort(403, 'Aksi ditolak! Orang tua / wali murid tidak memiliki wewenang untuk menghapus data peserta didik.');
    }
}
