<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'policy_id', 'policy_version', 'ip_address', 'user_agent',
        'declaration_confirmed', 'terms_agreed', 'marketing_opt_in', 'accepted_at',
    ];

    protected $casts = [
        'declaration_confirmed' => 'boolean',
        'terms_agreed'          => 'boolean',
        'marketing_opt_in'      => 'boolean',
        'accepted_at'           => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }
}