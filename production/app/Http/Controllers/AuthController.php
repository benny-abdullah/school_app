<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login dengan captcha penjumlahan matematika.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        // Generate soal penjumlahan matematika sederhana
        $num1 = rand(2, 9);
        $num2 = rand(1, 9);
        session(['captcha_answer' => $num1 + $num2]);

        return view('auth.login', compact('num1', 'num2'));
    }

    /**
     * Refresh Captcha secara AJAX
     */
    public function refreshCaptcha()
    {
        $num1 = rand(2, 9);
        $num2 = rand(1, 9);
        session(['captcha_answer' => $num1 + $num2]);

        return response()->json([
            'num1' => $num1,
            'num2' => $num2,
            'question' => "Berapakah {$num1} + {$num2} = ?",
            'answer' => $num1 + $num2,
        ]);
    }

    /**
     * Proses Login Multi-Identifier (NIP / NUPTK / NISN / Username) + Captcha
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
            'captcha'    => 'required|numeric',
        ], [
            'identifier.required' => 'NIP / NUPTK / NISN wajib diisi.',
            'password.required'   => 'Kata sandi wajib diisi.',
            'captcha.required'    => 'Hasil penjumlahan captcha wajib diisi.',
            'captcha.numeric'     => 'Captcha harus berupa angka.',
        ]);

        // Verifikasi Captcha Penjumlahan
        $expectedCaptcha = session('captcha_answer');
        if ((int)$request->captcha !== (int)$expectedCaptcha) {
            // Re-generate captcha baru untuk percobaan berikutnya
            $num1 = rand(2, 9);
            $num2 = rand(1, 9);
            session(['captcha_answer' => $num1 + $num2]);

            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['captcha' => 'Hasil perhitungan captcha tidak sesuai. Silakan hitung kembali!']);
        }

        // Hapus captcha yang sudah dipakai
        session()->forget('captcha_answer');

        $identifier = trim($request->identifier);

        // Cari user berdasarkan NIP/NUPTK, NISN, Username, atau Email
        $user = User::where('nip_nuptk', $identifier)
            ->orWhere('nisn', $identifier)
            ->orWhere('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Catat log percobaan login gagal
            ActivityLog::record('LOGIN_FAILED', 'Autentikasi', "Percobaan login gagal untuk identifier: '{$identifier}'");

            // Re-generate captcha untuk percobaan login berikutnya
            $num1 = rand(2, 9);
            $num2 = rand(1, 9);
            session(['captcha_answer' => $num1 + $num2]);

            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'Identitas (NIP/NUPTK/NISN) atau kata sandi tidak cocok.']);
        }

        if ($user->status !== 'active') {
            ActivityLog::record('LOGIN_FAILED', 'Autentikasi', "Login ditolak karena akun nonaktif: '{$identifier}'", null, $user);

            return back()
                ->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.']);
        }

        // Berhasil login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        ActivityLog::record('LOGIN_SUCCESS', 'Autentikasi', "Pengguna {$user->name} ({$user->role_label}) berhasil login ke sistem", null, $user);

        return $this->redirectByRole($user)->with('success', "Selamat datang, {$user->name}!");
    }

    /**
     * Redirect pengguna ke dashboard sesuai role masing-masing
     */
    public function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin'          => redirect()->route('admin.dashboard'),
            'operator'       => redirect()->route('operator.dashboard'),
            'guru/walikelas' => redirect()->route('guru.dashboard'),
            'orangtua'       => redirect()->route('ortu.dashboard'),
            default          => redirect()->route('login'),
        };
    }

    /**
     * Logout pengguna
     */
    public function logout(Request $request)
    {
        $currentUser = Auth::user();
        $userName = $currentUser?->name ?? 'Pengguna';

        if ($currentUser) {
            ActivityLog::record('LOGOUT', 'Autentikasi', "Pengguna {$userName} telah logout dari sistem", null, $currentUser);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', "Anda telah berhasil logout. Sampai jumpa kembali, {$userName}!");
    }
}
