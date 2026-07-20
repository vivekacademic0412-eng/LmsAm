<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavItem extends Model
{
    protected $fillable = [
        'parent_id', 'module_id','module_key', 'label', 'icon', 'route', 'sort_order', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(NavItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NavItem::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * All top-level, active nav items filtered to what $role is allowed to view,
     * ready to hand to the <x-aside-nav/> component.
     */
    public static function forRole(string $role)
    {
        $allowedModules = RolePermission::where('role', $role)
            ->where('can_view', true)
            ->pluck('module_id');
     
        return static::whereNull('parent_id')
            ->where('status', true)
            ->whereIn('module_id', $allowedModules)
            ->orderBy('sort_order')
            ->with(['children' => function ($q) use ($allowedModules) {
                $q->where('status', true)->whereIn('module_id', $allowedModules);
            }])
            ->get();
      
    }
}
