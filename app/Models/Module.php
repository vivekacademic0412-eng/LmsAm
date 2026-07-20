<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'category_id', 'parent_id', 'module_key', 'label', 'icon', 'route', 'sort_order', 'status',
    ];

    protected $casts = ['status' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(ModuleCategory::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id')->orderBy('sort_order');
    }

    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'module_id');
    }

    /**
     * Full tree — categories, each with top-level modules and their child modules —
     * pruned to only what $role is allowed to view. Used by the sidebar.
     */
    public static function treeForRole(string $role)
    {
        $allowedModuleIds = RolePermission::where('role', $role)
            ->where('can_view', true)
            ->pluck('module_id');

        return ModuleCategory::where('status', true)
            ->orderBy('sort_order')
            ->with(['modules' => function ($q) use ($allowedModuleIds) {
                $q->where('status', true)
                  ->whereIn('id', $allowedModuleIds)
                  ->with(['children' => function ($cq) use ($allowedModuleIds) {
                      $cq->where('status', true)->whereIn('id', $allowedModuleIds);
                  }]);
            }])
            ->get()
            ->filter(fn ($category) => $category->modules->isNotEmpty())
            ->values();
    }
}