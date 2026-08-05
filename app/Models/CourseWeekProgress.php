<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseWeekProgress extends Model
{
    protected $fillable = [
        'user_id', 'course_enrollment_id', 'course_week_id',
        'total_items', 'completed_items', 'percent', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function enrollment(): BelongsTo { return $this->belongsTo(CourseEnrollment::class, 'course_enrollment_id'); }
    public function week(): BelongsTo { return $this->belongsTo(CourseWeek::class, 'course_week_id'); }
}