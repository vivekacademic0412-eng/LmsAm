<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroStat extends Model
{
    protected $fillable = ['hero_section_id', 'number', 'label', 'sort_order'];
}