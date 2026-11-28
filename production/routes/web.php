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

        // Master Manajemen User (Eksklusif Admin / Kepala Sekolah)
        Route::get('admin/users-export/pdf', [\App\Http\Controllers\Admin\UserController::class, 'exportPdf'])->name('admin.users.export.pdf');
        Route::get('admin/users-export/excel', [\App\Http\Controllers\Admin\UserController::class, 'exportExcel'])->name('admin.users.export.excel');
        Route::resource('admin/users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users');
        Route::post('admin/users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::post('admin/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('admin.users.reset-password');

        // Log Aktifitas Sistem & Jejak Audit ISO 27001 (Eksklusif Admin)
        Route::get('/admin/logs-export/pdf', [\App\Http\Controllers\Admin\ActivityLogController::class, 'exportPdf'])->name('admin.logs.export.pdf');
        Route::get('/admin/logs-export/excel', [\App\Http\Controllers\Admin\ActivityLogController::class, 'exportExcel'])->name('admin.logs.export.excel');
        Route::get('/admin/logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.logs.index');
    });

    // Modul Operasional & Kesiswaan (Operator & Supervisi Kepala Sekolah/Admin)
    Route::middleware('role:operator,admin')->group(function () {
        Route::get('/operator/dashboard', [DashboardController::class, 'operator'])->name('operator.dashboard');

        // Master Data GTK (Guru & Tenaga Kependidikan - Operator & Admin)
        Route::get('operator/gtk-export/pdf', [\App\Http\Controllers\GtkController::class, 'exportPdf'])->name('gtk.export.pdf');
        Route::get('operator/gtk-export/excel', [\App\Http\Controllers\GtkController::class, 'exportExcel'])->name('gtk.export.excel');
        Route::resource('operator/gtk', \App\Http\Controllers\GtkController::class)->names('gtk');
        Route::post('operator/gtk/{gtk}/reset-password', [\App\Http\Controllers\GtkController::class, 'resetPassword'])->name('gtk.reset-password');

        // Master CRUD Rombel Kelas & Relasi Wali Kelas (Operator & Admin)
        Route::get('operator/classrooms-export/pdf', [\App\Http\Controllers\ClassroomController::class, 'exportPdf'])->name('classrooms.export.pdf');
        Route::get('operator/classrooms-export/excel', [\App\Http\Controllers\ClassroomController::class, 'exportExcel'])->name('classrooms.export.excel');
        Route::resource('operator/classrooms', \App\Http\Controllers\ClassroomController::class)->names('classrooms');

        // Master CRUD Data Siswa & Mutasi Rombel
        Route::get('operator/students-export/pdf', [\App\Http\Controllers\Operator\StudentController::class, 'exportPdf'])->name('operator.students.export.pdf');
        Route::get('operator/students-export/excel', [\App\Http\Controllers\Operator\StudentController::class, 'exportExcel'])->name('operator.students.export.excel');
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
        Route::get('/guru/attendance-export/pdf', [DashboardController::class, 'exportGuruAttendancePdf'])->name('guru.attendance.export.pdf');
        Route::get('/guru/attendance-export/excel', [DashboardController::class, 'exportGuruAttendanceExcel'])->name('guru.attendance.export.excel');
    });

    // Dashboard Orang Tua Murid / Siswa
    Route::middleware('role:orangtua')->group(function () {
        Route::get('/ortu/dashboard', [DashboardController::class, 'orangtua'])->name('ortu.dashboard');
    });
});
