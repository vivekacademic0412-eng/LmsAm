<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER_HR = 'manager_hr';
    public const ROLE_IT = 'it';
    public const ROLE_TRAINER = 'trainer';
    public const ROLE_STUDENT = 'student';
    public const ROLE_DEMO = 'demo';

    public const ROLES = [
        self::ROLE_SUPERADMIN,
        self::ROLE_ADMIN,
        self::ROLE_MANAGER_HR,
        self::ROLE_IT,
        self::ROLE_TRAINER,
        self::ROLE_STUDENT,
        self::ROLE_DEMO,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'role',
        'is_active',
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
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_SUPERADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAGER_HR => 'Manager / HR',
            self::ROLE_IT => 'IT',
            self::ROLE_TRAINER => 'Teacher / Trainer',
            self::ROLE_STUDENT => 'Student / User',
            self::ROLE_DEMO => 'Demo User',
        ];
    }

    public function coursesCreated(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function enrollmentsAsStudent(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'student_id');
    }

    public function enrollmentsAsTrainer(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'trainer_id');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        return asset('storage/'.ltrim($this->avatar, '/'));
    }
}
