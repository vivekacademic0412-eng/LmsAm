<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroRating extends Model
{
    protected $fillable = ['hero_section_id', 'score', 'label', 'sort_order'];
}