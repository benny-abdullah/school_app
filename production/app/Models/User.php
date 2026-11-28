<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'nip_nuptk',
        'nuptk',
        'nisn',
        'role',
        'phone',
        'avatar',
        'gender',
        'employment_status',
        'position',
        'status',
        'additional_info',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'additional_info' => 'array',
        ];
    }

    /**
     * Relasi ke Rombel yang Diampu (Jika User adalah Wali Kelas)
     */
    public function homeroomClassroom(): HasOne
    {
        return $this->hasOne(Classroom::class, 'homeroom_teacher_id');
    }

    /**
     * Relasi ke Data Siswa Anak Asuh (Jika User adalah Orang Tua / Wali Murid)
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'nisn', 'nisn');
    }

    /**
     * Relasi ke Log Aktivitas yang Dilakukan User
     */
    public function activityLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * URL Avatar Foto Profil
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Role checking helpers
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru/walikelas';
    }

    public function isOrangTua(): bool
    {
        return $this->role === 'orangtua';
    }

    /**
     * Display name with role title
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Kepala Sekolah (Super Admin)',
            'operator' => 'Operator Sekolah & TU',
            'guru/walikelas' => 'Guru & Wali Kelas',
            'orangtua' => 'Orang Tua / Wali Murid',
            default => ucfirst($this->role),
        };
    }

    /**
     * Primary identifier for display
     */
    public function getIdentifierAttribute(): string
    {
        if ($this->role === 'orangtua') {
            return $this->nisn ?? $this->username ?? $this->email ?? '-';
        }
        return $this->nip_nuptk ?? $this->username ?? $this->email ?? '-';
    }
}
