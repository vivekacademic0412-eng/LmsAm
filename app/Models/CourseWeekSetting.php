<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseWeekSetting extends Model
{
    protected $fillable = [
        'course_week_id',
        'certificate_enabled',
        'min_completion_percent',
        'is_visible',
    ];

    protected $casts = [
        'certificate_enabled'    => 'boolean',
        'is_visible'             => 'boolean',
        'min_completion_percent' => 'integer',
    ];

    public function week(): BelongsTo
    {
        return $this->belongsTo(CourseWeek::class, 'course_week_id');
    }
}