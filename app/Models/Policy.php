<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'version', 'title', 'is_active', 'published_at'];

    protected $casts = [
        'is_active'    => 'boolean',
        'published_at' => 'datetime',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PolicySection::class)->orderBy('sort_order');
    }
}