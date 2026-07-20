<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = [
        'role', 'module_id', 'can_view', 'can_create', 'can_edit', 'can_delete',
    ];

    protected $casts = [
        'can_view'   => 'boolean',
        'can_create' => 'boolean',
        'can_edit'   => 'boolean',
        'can_delete' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public static function allow(string $role, string $moduleKey, string $action): bool
    {
        $row = static::where('role', $role)
            ->whereHas('module', fn ($q) => $q->where('module_key', $moduleKey))
            ->first();

        if (! $row) {
            return false;
        }

        return match ($action) {
            'view'   => $row->can_view,
            'create' => $row->can_create,
            'edit'   => $row->can_edit,
            'delete' => $row->can_delete,
            default  => false,
        };
    }
}