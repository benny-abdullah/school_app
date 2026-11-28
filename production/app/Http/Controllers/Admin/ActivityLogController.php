<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
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

        $logs = $query->paginate(25)->withQueryString();

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
}
