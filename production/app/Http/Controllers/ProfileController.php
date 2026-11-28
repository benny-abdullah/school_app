<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna sesuai role masing-masing
     */
    public function show()
    {
        $user = Auth::user();

        // Data pendukung spesifik per role
        $roleData = [];

        if ($user->isAdmin()) {
            $roleData = [
                'instansi' => 'SDN Kebonagung Kota Pasuruan',
                'jabatan'  => 'Kepala Sekolah (Super Admin)',
                'periode'  => $user->additional_info['periode'] ?? '2024 - 2028',
                'wewenang' => 'Penanggung Jawab Kebijakan, Mutasi Guru, Audit ISO & Pengaturan Tatib',
            ];
        } elseif ($user->isOperator()) {
            $roleData = [
                'instansi' => 'SDN Kebonagung Kota Pasuruan',
                'jabatan'  => 'Operator Dapodik & Tata Usaha',
                'tugas'    => 'Pengelolaan Data Pokok Siswa, 29 Rombel, Cetak Kartu CR-80 & Scanner',
            ];
        } elseif ($user->isGuru()) {
            $classroomCode = $user->additional_info['classroom'] ?? '5A';
            $classroom = Classroom::where('code', $classroomCode)->first();
            $roleData = [
                'instansi'       => 'SDN Kebonagung Kota Pasuruan',
                'jabatan'        => 'Guru Kelas / Wali Kelas ' . $classroomCode,
                'classroom'      => $classroom,
                'total_students' => $classroom?->total_students ?? 30,
            ];
        } elseif ($user->isOrangTua()) {
            $studentNisn = $user->nisn ?? $user->additional_info['student_nisn'] ?? '0081234567';
            $student = Student::with('classroom')->where('nisn', $studentNisn)->first();
            $roleData = [
                'student'         => $student,
                'student_name'    => $student?->name ?? ($user->additional_info['student_name'] ?? 'Ahmad Rifai'),
                'student_nisn'    => $studentNisn,
                'student_class'   => $student?->classroom?->code ?? ($user->additional_info['classroom'] ?? '5A'),
                'relation'        => $user->additional_info['relation'] ?? 'Orang Tua Kandung',
                'address'         => $student?->address ?? ($user->additional_info['address'] ?? 'Kota Pasuruan'),
            ];
        }

        return view('profile.show', compact('user', 'roleData'));
    }

    /**
     * Perbarui data kontak atau ubah kata sandi
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone'            => 'nullable|string|max:30',
            'current_password' => 'nullable|required_with:new_password',
            'new_password'     => 'nullable|min:6|confirmed',
        ], [
            'current_password.required_with' => 'Kata sandi saat ini wajib diisi jika ingin mengubah kata sandi.',
            'new_password.min'               => 'Kata sandi baru minimal 6 karakter.',
            'new_password.confirmed'         => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil dan informasi akun Anda berhasil diperbarui!');
    }
}
