<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'grade_level',
        'section',
        'homeroom_teacher',
        'homeroom_nip',
        'capacity',
        'total_students',
        'academic_year',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
            'capacity' => 'integer',
            'total_students' => 'integer',
        ];
    }

    /**
     * Relasi ke Siswa dalam Rombel
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
