<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavPermissionSeeder extends Seeder
{
    /**
     * All roles in the system.
     */
    protected array $roles = ['superadmin', 'admin', 'manager_hr', 'it', 'trainer', 'student'];

    public function run(): void
    {
        DB::table('role_permissions')->truncate();
        DB::table('nav_items')->truncate();

        // module_key => [label, icon, route, sort_order]
        $modules = [
            'dashboard'    => ['Dashboard', 'ti ti-home', 'admin.dashboard', 1],
            'users'        => ['Students / Users', 'ti ti-users', 'admin.users.index', 2],
            'courses'      => ['Courses & Curriculum', 'ti ti-book', 'admin.courses.index', 3],
            'demos'        => ['Demo Requests', 'ti ti-player-play', 'admin.demos.index', 4],
            'payments'     => ['Payments & Invoices', 'ti ti-receipt', 'admin.payments.index', 5],
            'certificates' => ['Certificates', 'ti ti-certificate', 'admin.certificates.index', 6],
            'email'        => ['Email Integration', 'ti ti-mail', 'admin.email.index', 7],
            'reports'      => ['Reports & Analytics', 'ti ti-chart-bar', 'admin.reports.index', 8],
            'hr'           => ['HR Module', 'ti ti-briefcase', 'admin.hr.index', 9],
            'settings'     => ['Settings', 'ti ti-settings', 'admin.settings.index', 10],
            'nav_items'    => ['Navigation Builder', 'ti ti-list', 'admin.nav-items.index', 11],
            'permissions'  => ['Roles & Permissions', 'ti ti-shield-lock', 'admin.permissions.index', 12],
        ];

        $now = now();
        $navRows = [];
        foreach ($modules as $key => [$label, $icon, $route, $sort]) {
            $navRows[] = [
                'parent_id'  => null,
                'module_key' => $key,
                'label'      => $label,
                'icon'       => $icon,
                'route'      => $route,
                'sort_order' => $sort,
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('nav_items')->insert($navRows);

        // Default role -> module full-access map. Trim per-role below to match your real matrix.
        $fullAccess = ['dashboard', 'users', 'courses', 'demos', 'payments', 'certificates', 'email', 'reports', 'hr', 'settings', 'nav_items', 'permissions'];

        $roleAccess = [
            'superadmin'  => $fullAccess,
            'admin'       => ['dashboard', 'users', 'courses', 'demos', 'payments', 'certificates', 'email', 'reports', 'nav_items', 'permissions'],
            'manager_hr'  => ['dashboard', 'hr', 'reports'],
            'it'          => ['dashboard', 'email', 'settings', 'reports'],
            'trainer'     => ['dashboard', 'courses'],
            'student'     => ['dashboard'],
        ];

        $permRows = [];
        foreach ($roleAccess as $role => $allowedModules) {
            foreach (array_keys($modules) as $key) {
                $allowed = in_array($key, $allowedModules, true);
                $permRows[] = [
                    'role'       => $role,
                    'module_key' => $key,
                    'can_view'   => $allowed ? 1 : 0,
                    'can_create' => $allowed && $role !== 'student' ? 1 : 0,
                    'can_edit'   => $allowed && $role !== 'student' ? 1 : 0,
                    'can_delete' => $allowed && in_array($role, ['superadmin', 'admin'], true) ? 1 : 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('role_permissions')->insert($permRows);
    }
}
