<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'nis',
        'name',
        'gender',
        'classroom_id',
        'birth_place',
        'birth_date',
        'address',
        'guardian_name',
        'guardian_relation',
        'guardian_phone',
        'status',
        'point_balance',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'point_balance' => 'integer',
        ];
    }

    /**
     * Relasi ke Rombel Kelas
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Scope untuk siswa aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pencarian cerdas
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%");
            });
        });

        $query->when($filters['classroom_id'] ?? null, function ($q, $classroomId) {
            if ($classroomId !== 'all') {
                $q->where('classroom_id', $classroomId);
            }
        });

        $query->when($filters['status'] ?? null, function ($q, $status) {
            if ($status !== 'all') {
                $q->where('status', $status);
            }
        });

        $query->when($filters['gender'] ?? null, function ($q, $gender) {
            if ($gender !== 'all') {
                $q->where('gender', $gender);
            }
        });
    }

    /**
     * Badge visual status kesiswaan
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif Belajar',
            'graduated' => 'Lulus / Alumni',
            'moved' => 'Mutasi / Pindah',
            'inactive' => 'Nonaktif',
            default => ucfirst($this->status),
        };
    }
}
