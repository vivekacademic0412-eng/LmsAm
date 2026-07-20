<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavItem extends Model
{
    protected $fillable = ['parent_id', 'module_key', 'label', 'icon', 'route', 'sort_order', 'status'];

    public function children()
    {
        return $this->hasMany(NavItem::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Permissions for this item, matched by module_key (not a strict FK —
     * role_permissions.module_key is a plain string column, same as nav_items.module_key).
     */
    public function permissions()
    {
        return $this->hasMany(RolePermission::class, 'module_key', 'module_key');
    }

    /**
     * Top-level items visible to $role, with only the children that role
     * can also view, both ordered by sort_order.
     */
    public function scopeForRole($query, string $role)
    {
        return $query
            ->whereNull('parent_id')
            ->where('status', 1)
            ->whereHas('permissions', fn ($q) => $q->where('role', $role)->where('can_view', true))
            ->with(['children' => function ($q) use ($role) {
                $q->where('status', 1)
                  ->whereHas('permissions', fn ($qq) => $qq->where('role', $role)->where('can_view', true))
                  ->orderBy('sort_order');
            }])
            ->orderBy('sort_order');
    }
}