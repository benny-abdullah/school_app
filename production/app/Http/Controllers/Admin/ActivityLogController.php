<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Bangun query log aktivitas terfilter
     */
    protected function getFilteredQuery(Request $request)
    {
        $search = $request->get('search');
        $moduleFilter = $request->get('module', 'all');
        $actionFilter = $request->get('action', 'all');
        $roleFilter = $request->get('role', 'all');
        $dateFilter = $request->get('date', 'all');

        $query = ActivityLog::query()->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        if ($moduleFilter !== 'all') {
            $query->where('module', $moduleFilter);
        }

        if ($actionFilter !== 'all') {
            $query->where('action', $actionFilter);
        }

        if ($roleFilter !== 'all') {
            $query->where('user_role', $roleFilter);
        }

        if ($dateFilter === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateFilter === '7days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($dateFilter === '30days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        return $query;
    }

    /**
     * Tampilkan antarmuka Audit Trail & Log Aktivitas Sistem (Eksklusif Admin)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $moduleFilter = $request->get('module', 'all');
        $actionFilter = $request->get('action', 'all');
        $roleFilter = $request->get('role', 'all');
        $dateFilter = $request->get('date', 'all');

        $logs = $this->getFilteredQuery($request)->paginate(25)->withQueryString();

        $stats = [
            'total_logs'   => ActivityLog::count(),
            'today_logs'   => ActivityLog::whereDate('created_at', Carbon::today())->count(),
            'today_logins' => ActivityLog::whereIn('action', ['LOGIN', 'LOGIN_SUCCESS'])->whereDate('created_at', Carbon::today())->count(),
            'data_changes' => ActivityLog::whereIn('action', ['CREATE', 'CREATE_GTK', 'CREATE_USER', 'CREATE_STUDENT', 'UPDATE', 'UPDATE_GTK', 'UPDATE_USER', 'UPDATE_STUDENT', 'DELETE', 'DELETE_GTK', 'DELETE_USER', 'DELETE_STUDENT', 'RESET_PASSWORD'])->count(),
        ];

        // Daftar modul untuk filter
        $modules = ActivityLog::select('module')->distinct()->pluck('module');

        return view('admin.logs.index', compact('logs', 'stats', 'search', 'moduleFilter', 'actionFilter', 'roleFilter', 'dateFilter', 'modules'));
    }

    /**
     * Ekspor Log Audit Trail ke Format PDF Resmi Kop Surat
     */
    public function exportPdf(Request $request)
    {
        $logs = $this->getFilteredQuery($request)->limit(500)->get();

        return ExportService::pdf(
            'Laporan_Audit_Trail_Log_SDN_Kebonagung_' . date('Ymd_His'),
            'exports.activity_logs_pdf',
            compact('logs'),
            'landscape'
        );
    }

    /**
     * Ekspor Log Audit Trail ke Format Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $logs = $this->getFilteredQuery($request)->limit(1000)->get();

        $headers = [
            'no'          => 'No',
            'waktu'       => 'Waktu Transaksi (WIB)',
            'user_name'   => 'Nama Aktor Pengguna',
            'user_role'   => 'Role Akses',
            'module'      => 'Modul Sistem',
            'action'      => 'Aksi Event',
            'description' => 'Deskripsi Aktivitas',
            'ip_address'  => 'IP Address',
        ];

        $rows = $logs->map(function ($log, $idx) {
            return [
                'no'          => $idx + 1,
                'waktu'       => $log->created_at->format('d/m/Y H:i:s'),
                'user_name'   => $log->user_name,
                'user_role'   => strtoupper($log->user_role),
                'module'      => $log->module,
                'action'      => $log->action,
                'description' => $log->description,
                'ip_address'  => $log->ip_address ?? '127.0.0.1',
            ];
        })->toArray();

        return ExportService::excel(
            'Laporan_Audit_Trail_Log_SDN_Kebonagung_' . date('Ymd_His'),
            'Log Aktifitas & Jejak Audit Sistem (Audit Trail ISO 27001)',
            'Rekapitulasi Append-Only Seluruh Transaksi dan Kemanan Sistem',
            $headers,
            $rows
        );
    }
}
