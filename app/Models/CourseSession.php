<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_week_id',
        'session_number',
        'title',
    ];

    public function week(): BelongsTo
    {
        return $this->belongsTo(CourseWeek::class, 'course_week_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CourseSessionItem::class, 'course_session_id')
            ->orderByRaw("case item_type when 'intro' then 1 when 'main_video' then 2 when 'task' then 3 when 'quiz' then 4 else 5 end")
            ->orderBy('id');
    }
}
