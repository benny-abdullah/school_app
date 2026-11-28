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

// Verifikasi Publik Keabsahan Kartu Siswa (Otentikasi Scan QR Code)
Route::get('/card/verify/{student}', [\App\Http\Controllers\CardController::class, 'verify'])->name('cards.verify');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil Pengguna (Dapat Diakses Seluruh Role)
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // AI Copilot & Asisten Pedagogis Kesiswaan
    Route::post('/ai/copilot/chat', [\App\Http\Controllers\AiAnalyticsController::class, 'chat'])->name('ai.copilot.chat');

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

        // Generator & Cetak Kartu Pelajar Digital ISO CR-80 (Operator & Admin)
        Route::get('operator/cards', [\App\Http\Controllers\CardController::class, 'index'])->name('cards.index');
        Route::get('operator/cards/print-single/{student}', [\App\Http\Controllers\CardController::class, 'printSingle'])->name('cards.print.single');
        Route::get('operator/cards/print-bulk', [\App\Http\Controllers\CardController::class, 'printBulk'])->name('cards.print.bulk');

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

    // Modul Presensi Gerbang 3-Mode & Pemindai Barcode (Admin, Operator, & Guru/Wali Kelas)
    Route::middleware('role:admin,operator,guru/walikelas')->group(function () {
        Route::get('/scanner', [\App\Http\Controllers\ScannerController::class, 'index'])->name('scanner.index');
        Route::post('/scanner/scan', [\App\Http\Controllers\ScannerController::class, 'scan'])->name('scanner.scan');
        Route::get('/scanner/search-students', [\App\Http\Controllers\ScannerController::class, 'searchStudents'])->name('scanner.search');

        // Halaman Khusus Mandiri Analisis Data AI & Kesiswaan (Eksklusif Admin, Operator, Guru)
        Route::get('/ai-analytics', [\App\Http\Controllers\AiAnalyticsController::class, 'index'])->name('ai.analytics.index');
        Route::post('/ai-analytics/chat', [\App\Http\Controllers\AiAnalyticsController::class, 'chat'])->name('ai.analytics.chat');
        Route::post('/ai-analytics/test-connection', [\App\Http\Controllers\AiAnalyticsController::class, 'testConnection'])->name('ai.analytics.test');
        Route::post('/ai-analytics/save-settings', [\App\Http\Controllers\AiAnalyticsController::class, 'saveSettings'])->name('ai.analytics.settings');
    });

    // Dashboard Orang Tua Murid / Siswa & Profil Anak
    Route::middleware('role:orangtua')->group(function () {
        Route::get('/ortu/dashboard', [DashboardController::class, 'orangtua'])->name('ortu.dashboard');
        Route::get('/ortu/profil-anak', [\App\Http\Controllers\Ortu\ChildProfileController::class, 'show'])->name('ortu.child.profile');
        Route::post('/ortu/profil-anak/update-photo', [\App\Http\Controllers\Ortu\ChildProfileController::class, 'updatePhoto'])->name('ortu.child.update_photo');
        Route::delete('/ortu/profil-anak', [\App\Http\Controllers\Ortu\ChildProfileController::class, 'destroy'])->name('ortu.child.destroy');
    });
});
