<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicySection extends Model
{
    use HasFactory;

    protected $fillable = ['policy_id', 'section_key', 'title', 'body', 'sort_order'];

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }
}