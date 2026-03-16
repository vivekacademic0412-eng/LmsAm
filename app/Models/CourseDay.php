<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'day_number',
        'title',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CourseDayItem::class, 'course_day_id')
            ->orderByRaw("case item_type when 'intro' then 1 when 'main_video' then 2 when 'task' then 3 when 'quiz' then 4 else 5 end")
            ->orderBy('id');
    }
}
