<?php

namespace App\Providers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan data metrik dinamis ke layout sidebar & shell aplikasi serta dokumen ekspor
        View::composer(['layouts.app', 'layouts.*', 'exports.*'], function ($view) {
            try {
                if (Schema::hasTable('classrooms')) {
                    $view->with([
                        'sidebarClassroomCount' => Classroom::count(),
                        'sidebarGtkCount'        => User::whereIn('role', ['guru/walikelas', 'operator', 'admin'])->count(),
                        'sidebarStudentCount'    => Student::where('status', 'active')->count(),
                    ]);
                }

                if (Schema::hasTable('users')) {
                    $kepsek = User::where(function ($q) {
                        $q->where('additional_info->jabatan', 'like', '%Kepala Sekolah%')
                          ->orWhere('role', 'admin');
                    })->first() ?? User::where('name', 'like', '%Wahyu Agung%')->first();

                    $view->with('kepsek', $kepsek);
                    $view->with('kepalaSekolah', $kepsek);
                }
            } catch (\Throwable $e) {
                // Fail-safe saat migrasi awal belum selesai
            }
        });
    }
}
