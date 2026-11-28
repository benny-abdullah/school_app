<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        // Cek apakah role pengguna ada di daftar roles yang diperbolehkan
        // Catatan: Role admin (Kepala Sekolah) memiliki hak supervisi eksekutif atas modul operator
        $hasAccess = in_array($user->role, $roles) || ($user->isAdmin() && in_array('operator', $roles));

        if (!$hasAccess) {
            // Redirect aman ke dashboard resmi user dengan pemberitahuan
            $redirectRoute = match ($user->role) {
                'admin'          => 'admin.dashboard',
                'operator'       => 'operator.dashboard',
                'guru/walikelas' => 'guru.dashboard',
                'orangtua'       => 'ortu.dashboard',
                default          => 'login',
            };

            return redirect()->route($redirectRoute)->with(
                'error',
                "Akses ditolak! Anda masuk sebagai {$user->role_label} dan tidak memiliki hak akses ke halaman tersebut."
            );
        }

        return $next($request);
    }
}
