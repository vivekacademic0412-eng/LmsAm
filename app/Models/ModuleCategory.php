<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModuleCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'sort_order', 'status'];

    protected $casts = ['status' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (ModuleCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name) . '-' . Str::random(4);
            }
        });
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'category_id')->whereNull('parent_id')->orderBy('sort_order');
    }
}