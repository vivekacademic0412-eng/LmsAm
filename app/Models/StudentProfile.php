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

 
    protected $casts = [
        'dob' => 'date',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
