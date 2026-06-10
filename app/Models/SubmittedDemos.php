<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmittedDemos extends Model
{
    protected $fillable = [
        'user_id',
        'demo_user_id',
        'course_id',
        'demo_topic',
        'demo_description',
        'demo_video',
        'completion_score',
        'status',
    ];

    public function demoUser()
    {
        return $this->belongsTo(DemoUser::class,'demo_user_id','id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class,'course_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}