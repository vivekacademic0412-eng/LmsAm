<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HeroSection extends Model
{
    protected $fillable = [
        'logo_path',
        'heading_prefix',
        'heading_highlight',
        'heading_bold',
        'heading_suffix',
        'lede',
        'cta_primary_label',
        'cta_primary_url',
        'cta_secondary_label',
        'cta_secondary_url',
        'mascot_image',
        'guide_tag',
        'guide_name',
        'guide_text',
        'hand_images',
        'is_active',
    ];

    protected $casts = [
        'hand_images' => 'array',
        'is_active' => 'boolean',
    ];

    public function stats(): HasMany
    {
        return $this->hasMany(HeroStat::class)->orderBy('sort_order');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(HeroRating::class)->orderBy('sort_order');
    }
}