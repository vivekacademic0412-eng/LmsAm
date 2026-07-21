<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    //
    protected $fillable = [
    'title',
    'slug',
    'version',
    'content',
    'status',
    'published_at',
    
];
public function sections(){
      return $this->hasMany(PolicySection::class,'policy_id')->orderBy('sort_order');
}
}
