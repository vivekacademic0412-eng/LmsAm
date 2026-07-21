<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'course_level_id',
        'batch_id',
        'order_reference',
        'amount_paid',
        'registered_at',
        'zero_day_start_at',
        'progress_percent',
        'status',
        'certificate_unlocked_at',
    ];

    protected $casts = [
        'registered_at'           => 'datetime',
        'zero_day_start_at'       => 'date',
        'certificate_unlocked_at' => 'datetime',
        'amount_paid'             => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseLevel(): BelongsTo
    {
        return $this->belongsTo(CourseLevel::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function sessionProgress(): HasMany
    {
        return $this->hasMany(SessionProgress::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * True once "zero day" has actually arrived — before this date the
     * course clock hasn't started even though the user is registered.
     */
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