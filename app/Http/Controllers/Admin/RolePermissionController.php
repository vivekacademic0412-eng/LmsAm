<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavItem;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    protected array $roles = ['superadmin', 'admin', 'manager_hr', 'it', 'trainer', 'student'];

    public function index()
    {
        $modules = NavItem::orderBy('sort_order')->get(['module_key', 'label']);

        $existing = RolePermission::all()
            ->groupBy('role')
            ->map(fn ($rows) => $rows->keyBy('module_key'));

        return view('permissions.index', [
            'roles'    => $this->roles,
            'modules'  => $modules,
            'existing' => $existing,
        ]);
    }

    /**
     * Bulk-save the whole matrix in one request — one query per role via upsert, no N+1.
     */
    public function update(Request $request)
    {
        $payload = $request->validate([
            'permissions'                     => 'required|array',
            'permissions.*.role'              => 'required|string',
            'permissions.*.module_key'        => 'required|string',
            'permissions.*.can_view'          => 'nullable|boolean',
            'permissions.*.can_create'        => 'nullable|boolean',
            'permissions.*.can_edit'          => 'nullable|boolean',
            'permissions.*.can_delete'        => 'nullable|boolean',
        ]);

        $now = now();
        $rows = array_map(function ($row) use ($now) {
            return [
                'role'       => $row['role'],
                'module_key' => $row['module_key'],
                'can_view'   => $row['can_view']   ?? 0,
                'can_create' => $row['can_create'] ?? 0,
                'can_edit'   => $row['can_edit']   ?? 0,
                'can_delete' => $row['can_delete'] ?? 0,
                'updated_at' => $now,
                'created_at' => $now,
            ];
        }, $payload['permissions']);

        DB::table('role_permissions')->upsert(
            $rows,
            ['role', 'module_key'],
            ['can_view', 'can_create', 'can_edit', 'can_delete', 'updated_at']
        );

        return back()->with('success', 'Permissions updated.');
    }
    public function Setting()
    {
        

        return view('backend.admin.onbording.index');
    }
}
