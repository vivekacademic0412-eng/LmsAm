<?php

namespace App\Livewire\Admin;

use App\Models\Module;
use App\Models\ModuleCategory;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RolePermissionManager extends Component
{
    public array $roles = ['superadmin', 'admin', 'manager_hr', 'it', 'trainer', 'student'];

    public string $activeRole = 'admin';

    /** @var array<int, array{view:bool,create:bool,edit:bool,delete:bool}> keyed by module_id, for the active role */
    public array $permissions = [];

    public bool $dirty = false;

    public function mount(): void
    {
        $this->loadPermissionsFor($this->activeRole);
    }

    public function render()
    {
        $tree = ModuleCategory::orderBy('sort_order')->with(['modules.children'])->get();

        return view('livewire.admin.role-permission-manager', [
            'tree' => $tree,
        ]);
    }

    public function switchRole(string $role): void
    {
        if ($this->dirty && ! confirm_discard()) {
            // no-op placeholder; front-end confirm() handles the prompt, see blade wire:click.prevent guard
        }

        $this->activeRole = $role;
        $this->loadPermissionsFor($role);
        $this->dirty = false;
    }

    public function toggle(int $moduleId, string $action): void
    {
        $current = $this->permissions[$moduleId][$action] ?? false;
        $this->permissions[$moduleId][$action] = ! $current;

        // Turning any create/edit/delete on implies view must be on too.
        if ($action !== 'view' && $this->permissions[$moduleId][$action]) {
            $this->permissions[$moduleId]['view'] = true;
        }
        // Turning view off clears the rest — no point creating/editing something you can't see.
        if ($action === 'view' && ! $this->permissions[$moduleId]['view']) {
            $this->permissions[$moduleId]['create'] = false;
            $this->permissions[$moduleId]['edit']   = false;
            $this->permissions[$moduleId]['delete'] = false;
        }

        $this->dirty = true;
    }

    public function toggleAllForModule(int $moduleId, bool $value): void
    {
        $this->permissions[$moduleId] = [
            'view'   => $value,
            'create' => $value,
            'edit'   => $value,
            'delete' => $value,
        ];
        $this->dirty = true;
    }

    public function save(): void
    {
        if ($this->activeRole === '') {
            $this->addError('activeRole', 'Choose a role first.');
            return;
        }

        $now = now();
        $rows = [];
        foreach ($this->permissions as $moduleId => $flags) {
            $rows[] = [
                'role'       => $this->activeRole,
                'module_id'  => $moduleId,
                'can_view'   => $flags['view']   ?? 0,
                'can_create' => $flags['create'] ?? 0,
                'can_edit'   => $flags['edit']   ?? 0,
                'can_delete' => $flags['delete'] ?? 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($rows)) {
            DB::table('role_permissions')->upsert(
                $rows,
                ['role', 'module_id'],
                ['can_view', 'can_create', 'can_edit', 'can_delete', 'updated_at']
            );
        }

        $this->dirty = false;
        session()->flash('success', 'Permissions saved for "' . $this->activeRole . '".');
    }

    protected function loadPermissionsFor(string $role): void
    {
        $moduleIds = Module::pluck('id');

        $existing = RolePermission::where('role', $role)->get()->keyBy('module_id');

        $this->permissions = [];
        foreach ($moduleIds as $id) {
            $row = $existing->get($id);
            $this->permissions[$id] = [
                'view'   => (bool) ($row->can_view ?? false),
                'create' => (bool) ($row->can_create ?? false),
                'edit'   => (bool) ($row->can_edit ?? false),
                'delete' => (bool) ($row->can_delete ?? false),
            ];
        }
    }
}

if (! function_exists('confirm_discard')) {
    function confirm_discard(): bool
    {
        return true; // server has no access to browser confirm(); guarded client-side instead
    }
}