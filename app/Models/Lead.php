<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Lead extends Model
{
    use HasFactory;

    protected $fillable = [

        'lead_type',

        'name',
        'email',
        'phone',

        'company',
        'designation',

        'message',

        'traffic_source_id',

        'user_id',

        'status',

    ];

    public function trafficSource()
    {
        return $this->belongsTo(TrafficSource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}