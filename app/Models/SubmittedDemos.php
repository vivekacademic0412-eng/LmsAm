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
}
