<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoUser extends Model
{
   protected $fillable = [
    'user_id',
    'full_name',
    'email_phone',
    'education_level_id',
    'interest_area_id',
    'preferred_course_id',
    'ip_address',
    'progress_demo',
    'demo_featured_video_id',
];
}
