<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoUser extends Model
{
       protected $table = 'demo_users';
    protected $fillable = [
        'user_id',
        'full_name',
        'email_phone',
        'education_level_id',
        'interest_area_id',
        'preferred_course_id',
        'ip_address',
        'progress_demo',
        'demo_feature_video_id',
    ];

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'preferred_course_id');
    }

    public function submittedDemos()
    {
        return $this->hasMany(SubmittedDemos::class, 'demo_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}