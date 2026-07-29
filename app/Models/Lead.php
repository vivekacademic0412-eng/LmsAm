<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Notifications\VerifyEmailNotification;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Lead extends Model
{
     use Notifiable;
   
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
        'email_verified_at',
        'user_id',

        'status',

    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function routeNotificationForMail()
    {
        return $this->email;
    }
    public function trafficSource()
    {
        return $this->belongsTo(TrafficSource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }
      public function getEmailForVerification(): string
    {
        return $this->email;
    }

}
