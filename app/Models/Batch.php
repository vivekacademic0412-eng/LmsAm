<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'batch_type',
        'batch_code',
        'mode',
        'start_date',
        'zero_day_date',
        'max_weeks',
        'max_seats',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'zero_day_date' => 'date',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students(): HasMany
    {
        return $this->hasMany(BatchStudent::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }
    public function courseBatches()
    {
        return $this->hasMany(CourseBatch::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_batch')
            ->withPivot('trainer_id', 'status')
            ->withTimestamps();
    }
}
