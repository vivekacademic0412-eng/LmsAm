<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrashCourseLink extends Model
{
    protected $fillable = [
        'source_course_id',
        'crash_course_id',
        'crash_level',
    ];

    public function sourceCourse()
    {
        return $this->belongsTo(Course::class, 'source_course_id');
    }

    public function crashCourse()
    {
        return $this->belongsTo(Course::class, 'crash_course_id');
    }
}