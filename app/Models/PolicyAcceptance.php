<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyAcceptance extends Model
{
    //
    protected $fillable = [
    'user_id',
    'policy_id',
    'policy_version',
    'ip_address',
    'user_agent',
    'declaration_confirmed',
    'terms_agreed',
    'marketing_opt_in',
    'accepted_at',
];
}
