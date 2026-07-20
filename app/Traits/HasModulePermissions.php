<?php

namespace App\Traits;

use App\Models\RolePermission;

/**
 * Add to App\Models\User:  use HasModulePermissions;
 * Usage:  $user->hasPermission('courses', 'edit')
 */
trait HasModulePermissions
{
    public function hasPermission(string $moduleKey, string $action = 'view'): bool
    {
        $superadmin = defined(static::class . '::ROLE_SUPERADMIN') ? static::ROLE_SUPERADMIN : 'superadmin';

        if ($this->role === $superadmin) {
            return true; // superadmin always has full access
        }

        return RolePermission::allow($this->role, $moduleKey, $action);
    }
}