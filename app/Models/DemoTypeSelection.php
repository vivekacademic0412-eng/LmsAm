<?php
// app/Models/DemoTypeSelection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoTypeSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'traffic_source_id',
        'session_id',
        'user_ip',
        'demo_type',
        'amount',
        'demo_user_id',
        'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function trafficSource()
    {
        return $this->belongsTo(TrafficSource::class);
    }

    public function demoUser()
    {
        return $this->belongsTo(DemoUser::class);
    }

    // Payment model arrives in Phase 3 — relation defined now so this
    // model doesn't need touching again once that table exists.
    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->demo_type === 'paid';
    }
}