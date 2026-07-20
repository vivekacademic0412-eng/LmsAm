<?php

namespace App\Traits;

use App\Models\RolePermission;

/**
 * Add to App\Models\User:  use HasModulePermissions;
 *
 * Usage:
 *   $user->hasPermission('courses', 'edit')
 *   $user->can('view', 'demos')   // if you also wire a Gate, see routes/admin.php note
 */
trait HasModulePermissions
{
    public function hasPermission(string $moduleKey, string $action = 'view'): bool
    {
        // superadmin always has full access
        if ($this->role === (defined(static::class . '::ROLE_SUPERADMIN') ? static::ROLE_SUPERADMIN : 'superadmin')) {
            return true;
        }

        return RolePermission::allow($this->role, $moduleKey, $action);
    }
}
