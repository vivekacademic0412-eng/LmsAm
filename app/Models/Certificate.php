<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'certificate_number', 'user_id', 'category_id', 'course_id', 'course_week_id',
        'demo_submission_id', 'type', 'status', 'completion_percent', 'grade',
        'approved_by', 'approved_at', 'issued_at', 'file_path',
    ];

    protected $casts = [
        'completion_percent' => 'decimal:2',
        'approved_at'         => 'datetime',
        'issued_at'           => 'datetime',
    ];

    const TYPE_DEMO   = 'demo';
    const TYPE_WEEK   = 'week';
    const TYPE_LEVEL  = 'level';
    const TYPE_COURSE = 'course';

    const STATUS_LOCKED  = 'locked';
    const STATUS_PENDING = 'pending_admin_approval';
    const STATUS_UNLOCKED = 'unlocked';

    protected static function booted(): void
    {
        static::creating(function (Certificate $cert) {
            $cert->certificate_number ??= 'CERT-' . strtoupper(Str::random(10));
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(CourseCategory::class, 'category_id'); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function week(): BelongsTo { return $this->belongsTo(CourseWeek::class, 'course_week_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function isUnlocked(): bool
    {
        return $this->status === self::STATUS_UNLOCKED;
    }

    public function unlock(?int $approvedByUserId = null): void
    {
        $this->update([
            'status'      => self::STATUS_UNLOCKED,
            'approved_by' => $approvedByUserId,
            'approved_at' => now(),
            'issued_at'   => $this->issued_at ?? now(),
        ]);
    }

    /** Human label for admin table / student card */
    public function subjectTitle(): string
    {
        return match ($this->type) {
            self::TYPE_DEMO   => $this->category?->name ?? 'Subject Demo',
            self::TYPE_WEEK   => ($this->course?->title ?? 'Course') . ' — Week ' . $this->week?->week_number,
            self::TYPE_LEVEL  => ($this->course?->title ?? 'Course') . ' — ' . $this->course?->courseLevel?->name,
            self::TYPE_COURSE => $this->course?->title ?? 'Course',
            default           => 'Certificate',
        };
    }
}