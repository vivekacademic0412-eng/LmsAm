<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionProgress extends Model
{
    use HasFactory;

    protected $table = 'session_progress';

    protected $fillable = [
        'enrollment_id',
        'course_session_item_id',
        'watched_seconds',
        'percent_watched',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Student Enrollment
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Course Session Item
     */
    public function courseSessionItem()
    {
        return $this->belongsTo(CourseSessionItem::class);
    }

    /**
     * Mark session as completed.
     */
    public function markCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'percent_watched' => 100,
            'completed_at' => now(),
        ]);
    }

    /**
     * Check if session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }
}