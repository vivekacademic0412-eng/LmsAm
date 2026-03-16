<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'week_number',
        'title',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CourseSession::class, 'course_week_id')->orderBy('session_number');
    }
}
