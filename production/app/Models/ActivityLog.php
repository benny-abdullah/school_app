<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * User yang melakukan aksi (opsional/nullable jika sistem atau user terhapus)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper static untuk mencatat log aktivitas sistem secara konsisten
     */
    public static function record(string $action, string $module, string $description, ?array $properties = null, ?User $user = null): ?self
    {
        try {
            $currentUser = $user ?? Auth::user();
            $request = request();

            return self::create([
                'user_id'     => $currentUser?->id,
                'user_name'   => $currentUser?->name ?? 'Tamu / Sistem',
                'user_role'   => $currentUser?->role ?? 'guest',
                'action'      => strtoupper($action),
                'module'      => $module,
                'description' => $description,
                'ip_address'  => $request ? $request->ip() : null,
                'user_agent'  => $request ? substr($request->userAgent() ?? '', 0, 500) : null,
                'properties'  => $properties,
            ]);
        } catch (\Throwable $e) {
            // Fail-safe: kegagalan penulisan log tidak boleh menggagalkan transaksi utama
            report($e);
            return null;
        }
    }

    /**
     * Action color badge helper
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match (strtoupper($this->action)) {
            'LOGIN', 'LOGIN_SUCCESS'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'LOGOUT'                  => 'bg-slate-100 text-slate-700 border-slate-200',
            'LOGIN_FAILED'            => 'bg-rose-50 text-rose-700 border-rose-200',
            'CREATE', 'CREATE_GTK', 'CREATE_STUDENT', 'CREATE_USER' => 'bg-blue-50 text-blue-700 border-blue-200',
            'UPDATE', 'UPDATE_GTK', 'UPDATE_STUDENT', 'UPDATE_USER', 'UPDATE_PROFILE' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'DELETE', 'DELETE_GTK', 'DELETE_STUDENT', 'DELETE_USER' => 'bg-rose-50 text-rose-700 border-rose-200',
            'RESET_PASSWORD'          => 'bg-amber-50 text-amber-700 border-amber-200',
            'ROTASI_ROMBEL'           => 'bg-purple-50 text-purple-700 border-purple-200',
            default                   => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
