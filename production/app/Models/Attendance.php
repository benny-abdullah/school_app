<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'nisn',
        'date',
        'checkin_time',
        'checkout_time',
        'status',
        'late_level',
        'late_minutes',
        'method',
        'notes',
        'operator_id',
        'gate_pass_active',
        'gate_pass_reason',
        'gate_pass_time',
    ];

    protected function casts(): array
    {
        return [
            'date'             => 'date',
            'late_minutes'     => 'integer',
            'gate_pass_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke Data Siswa
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi ke User Operator / Guru yang memindai
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Scope absensi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today('Asia/Jakarta'));
    }
}
