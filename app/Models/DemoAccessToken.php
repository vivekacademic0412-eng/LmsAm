<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoAccessToken extends Model
{
        protected $fillable = [
        'user_id',
        'expires_at',
        'token',
        'is_completed',
        'used_at',
        'browser_fingerprint',
        'session_id'
      
        
    ];
}
