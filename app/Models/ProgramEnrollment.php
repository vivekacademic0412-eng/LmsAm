<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramEnrollment extends Model
{
    //
    protected $fillable = [
    'user_id',
    'program_category_id',
    'program_id',
    'batch_id',
    'mode_of_learning',
    'preferred_start_date',
    'referral_source',
    'career_goal',
    'status',
];
}
