<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - STAR-KIDS SDN Kebonagung
|--------------------------------------------------------------------------
*/

// Root URL mengarah langsung ke login atau ke dashboard jika sudah login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return match ($user->role) {
            'admin'          => redirect()->route('admin.dashboard'),
            'operator'       => redirect()->route('operator.dashboard'),
            'guru/walikelas' => redirect()->route('guru.dashboard'),
            'orangtua'       => redirect()->route('ortu.dashboard'),
            default          => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login/refresh-captcha', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil Pengguna (Dapat Diakses Seluruh Role)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Dashboard Super Admin / Kepala Sekolah
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'));
    });

    // Modul Operasional & Kesiswaan (Operator & Supervisi Kepala Sekolah/Admin)
    Route::middleware('role:operator,admin')->group(function () {
        Route::get('/operator/dashboard', [DashboardController::class, 'operator'])->name('operator.dashboard');

        // Master CRUD Data Siswa & Mutasi Rombel
        Route::resource('operator/students', \App\Http\Controllers\Operator\StudentController::class)->names([
            'index'   => 'operator.students.index',
            'create'  => 'operator.students.create',
            'store'   => 'operator.students.store',
            'edit'    => 'operator.students.edit',
            'update'  => 'operator.students.update',
            'destroy' => 'operator.students.destroy',
        ]);
    });

    // Dashboard Guru & Wali Kelas
    Route::middleware('role:guru/walikelas')->group(function () {
        Route::get('/guru/dashboard', [DashboardController::class, 'guru'])->name('guru.dashboard');
    });

    // Dashboard Orang Tua Murid / Siswa
    Route::middleware('role:orangtua')->group(function () {
        Route::get('/ortu/dashboard', [DashboardController::class, 'orangtua'])->name('ortu.dashboard');
    });
});
