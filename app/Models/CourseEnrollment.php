<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseEnrollment extends Model
{
    use HasFactory;

     protected $table = 'course_enrollments';

    protected $fillable = [
        'course_id',
        'student_id',
        'trainer_id',
        'assigned_by',
        'status',
        'enrolled_at',

        // New columns
        'order_reference',
        'amount_paid',
        'registered_at',
        'zero_day_start_at',
        'progress_percent',
        'certificate_unlocked_at',
        'batch_id',
        'course_level_id',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'zero_day_start_at' => 'date',
        'certificate_unlocked_at' => 'datetime',
        'enrolled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function progressItems(): HasMany
    {
        return $this->hasMany(CourseProgress::class, 'course_enrollment_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(CourseItemSubmission::class, 'course_enrollment_id');
    }
    public function batche(): BelongsTo
    {
        return $this->belongsTo(Batch::class,'course_id');
    }
     public function zeroDayReached(): bool
    {
        return $this->zero_day_start_at !== null
            && now()->startOfDay()->gte($this->zero_day_start_at);
    }

    /**
     * Recompute progress_percent from session_progress and flip the
     * enrollment to completed / certificate-unlocked once the course
     * type's threshold (80%, 75% etc.) is crossed. Call this after any
     * session_progress update.
     */
    public function recalculateProgress(): void
    {
        $totalItems = $this->course->weeks()
            ->with('sessions.items')
            ->get()
            ->flatMap(fn ($week) => $week->sessions)
            ->flatMap(fn ($session) => $session->items)
            ->count();

        if ($totalItems === 0) {
            return;
        }

        $completedItems = $this->sessionProgress()->where('is_completed', true)->count();

        $percent = (int) round(($completedItems / $totalItems) * 100);

        $threshold = optional($this->course->courseType->setting)->completion_threshold_percent ?? 80;

        $this->progress_percent = $percent;

        if ($percent >= $threshold && ! $this->certificate_unlocked_at) {
            $this->certificate_unlocked_at = now();
            $this->status = 'completed';
        }

        $this->save();
    }
}
