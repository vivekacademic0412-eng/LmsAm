<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicBackground extends Model
{
    //
   protected $fillable = [
    'user_id',
    'highest_qualification',
    'percentage_cgpa',
    'institution_name',
    'year_of_passing',
    'experience_level',
    'guardian_name',
    'guardian_mobile',
];
public function user()
    {
        return $this->belongsTo(User::class);
    }
}
