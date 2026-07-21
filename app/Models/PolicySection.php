<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicySection extends Model
{
    //
    protected $fillable = [
        'policy_id',
        'section_key',
        'title',
        'body',
        'sort_order',
    ];
}
