<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    //
    protected $fillable = [
    'user_id',
    'first_name',
    'last_name',
    'dob',
    'gender',
    'category',
    'mobile_number',
    'whatsapp_number',
    'email',
    'city_district',
    'residential_address',
    'id_proof_type',
    'id_number',
];
}
