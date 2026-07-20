<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleCategory;
use App\Models\Module;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        RolePermission::truncate();
        Module::truncate();
        ModuleCategory::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = [
            'superadmin',
            'admin',
            'manager_hr',
            'it',
            'trainer',
            'student',
            'demo',
        ];

        /**
         * ─────────────────────────────────────────────
         * SIDEBAR DEFINITION
         * ─────────────────────────────────────────────
         * Mirrors resources/views/layouts/partials/sidebar.blade.php exactly.
         * Each module carries a `roles` array — the roles that can actually
         * SEE that item in the blade (drives can_view below). Omit `roles`
         * to default to all roles.
         */
        $sidebar = [

            [
                'category' => 'Overview',
                'icon'     => 'fas fa-chart-line',
                'modules'  => [
                    [
                        'key'   => 'dashboard',
                        'label' => 'Dashboard',
                        'icon'  => 'ti ti-layout-dashboard',
                        'route' => 'dashboard',
                        'roles' => ['superadmin', 'admin', 'manager_hr', 'it', 'trainer', 'student', 'demo'],
                    ],
                ],
            ],

            // ── Student only ─────────────────────────────
            [
                'category' => 'Learning',
                'icon'     => 'fas fa-graduation-cap',
                'modules'  => [
                    [
                        'key'   => 'student-courses',
                        'label' => 'Explore Courses',
                        'icon'  => 'ti ti-books',
                        'route' => 'student.courses',
                        'roles' => ['student'],
                    ],
                    [
                        'key'   => 'lms-choose-type',
                        'label' => 'Book Demo Session',
                        'icon'  => 'ti ti-history',
                        'route' => 'lms.choose-type',
                        'roles' => ['student'],
                    ],
                    [
                        'key'   => 'student-payments',
                        'label' => 'Payments Transactions',
                        'icon'  => 'ti ti-currency-rupee',
                        'route' => 'payments.index',
                        'roles' => ['student'],
                    ],
                    [
                        'key'   => 'student-certificates',
                        'label' => 'Certificates',
                        'icon'  => 'fa-solid fa-certificate',
                        'route' => null, // href="#" in blade — no route yet
                        'roles' => ['student'],
                    ],
                ],
            ],

            // ── HR only ───────────────────────────────────
            [
                'category' => 'HR',
                'icon'     => 'fas fa-briefcase',
                'modules'  => [
                    [
                        'key'   => 'panel-manager-hr',
                        'label' => 'HR Panel',
                        'icon'  => 'ti ti-briefcase',
                        'route' => 'panel.manager_hr',
                        'roles' => ['manager_hr'],
                    ],
                ],
            ],

            // ── IT only ───────────────────────────────────
            [
                'category' => 'IT',
                'icon'     => 'fas fa-server',
                'modules'  => [
                    [
                        'key'   => 'panel-it',
                        'label' => 'IT Panel',
                        'icon'  => 'ti ti-server',
                        'route' => 'panel.it',
                        'roles' => ['it'],
                    ],
                ],
            ],

            // ── superadmin / admin / manager_hr / it ───────
            [
                'category' => 'Courses',
                'icon'     => 'fas fa-book',
                'modules'  => [
                    [
                        'key'   => 'courses',
                        'label' => 'Courses',
                        'icon'  => 'ti ti-school',
                        'route' => null,
                        'roles' => ['superadmin', 'admin', 'manager_hr', 'it'],
                        'children' => [
                            [
                                'key'   => 'course-categories',
                                'label' => 'Categories',
                                'icon'  => 'ti ti-folder',
                                'route' => 'course-categories.index',
                                'roles' => ['superadmin', 'admin', 'manager_hr', 'it'],
                            ],
                            [
                                'key'   => 'all-courses',
                                'label' => 'All Courses',
                                'icon'  => 'ti ti-books',
                                'route' => 'courses.index',
                                'roles' => ['superadmin', 'admin', 'manager_hr', 'it'],
                            ],
                        ],
                    ],
                ],
            ],

            // ── superadmin / admin only ─────────────────────
            [
                'category' => 'Students',
                'icon'     => 'fas fa-users',
                'modules'  => [
                    [
                        'key'   => 'students',
                        'label' => 'Students',
                        'icon'  => 'ti ti-users',
                        'route' => null,
                        'roles' => ['superadmin', 'admin'],
                        'children' => [
                            [
                                'key'   => 'enrollments',
                                'label' => 'Enrollments',
                                'icon'  => 'ti ti-clipboard-list',
                                'route' => 'enrollments.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'submissions',
                                'label' => 'Submission Review',
                                'icon'  => 'ti ti-git-pull-request',
                                'route' => 'submissions.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'admin-payments',
                                'label' => 'Payments Transactions',
                                'icon'  => 'ti ti-currency-rupee',
                                'route' => 'payments.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'category' => 'System',
                'icon'     => 'fas fa-cogs',
                'modules'  => [
                    [
                        'key'   => 'system',
                        'label' => 'System',
                        'icon'  => 'ti ti-settings',
                        'route' => null,
                        'roles' => ['superadmin', 'admin'],
                        'children' => [
                            [
                                'key'   => 'activity-logs',
                                'label' => 'Activity Logs',
                                'icon'  => 'ti ti-activity',
                                'route' => 'activity-logs.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'users',
                                'label' => 'User Control',
                                'icon'  => 'ti ti-shield-check',
                                'route' => 'users.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'broadcast-notifications',
                                'label' => 'Broadcast',
                                'icon'  => 'ti ti-speakerphone',
                                'route' => 'broadcast-notifications.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'brochure',
                                'label' => 'Brochure',
                                'icon'  => 'ti ti-speakerphone',
                                'route' => 'admin.brocheres',
                                'roles' => ['superadmin', 'admin'],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'category' => 'Demo',
                'icon'     => 'fas fa-desktop',
                'modules'  => [
                    [
                        'key'   => 'demo-tasks',
                        'label' => 'Demo Tasks',
                        'icon'  => 'ti ti-device-desktop',
                        'route' => null,
                        'roles' => ['superadmin', 'admin'],
                        'children' => [
                            [
                                'key'   => 'demo-tasks-create',
                                'label' => 'Create Task',
                                'icon'  => 'ti ti-plus-circle',
                                'route' => 'demo-tasks.create-page',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'demo-tasks-assign',
                                'label' => 'Assign Task',
                                'icon'  => 'ti ti-user-check',
                                'route' => 'demo-tasks.assign-page',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'demo-feature-video',
                                'label' => 'Feature Video',
                                'icon'  => 'ti ti-video',
                                'route' => 'demo-feature-video.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'demo-hero',
                                'label' => 'Hero Section - Banner',
                                'icon'  => 'ti ti-video',
                                'route' => 'admin.demo-hero',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'demo-review-videos',
                                'label' => 'Reviews',
                                'icon'  => 'ti ti-star',
                                'route' => 'demo-review-videos.index',
                                'roles' => ['superadmin', 'admin'],
                            ],
                        ],
                    ],

                    [
                        'key'   => 'demo-stages',
                        'label' => 'Demo Stages',
                        'icon'  => 'ti ti-layers-subtract',
                        'route' => null,
                        'roles' => ['superadmin', 'admin'],
                        'children' => [
                            [
                                'key'   => 'demo-students',
                                'label' => 'Students',
                                'icon'  => 'ti ti-users',
                                'route' => 'admin.demo-students',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'demo-submission-stage',
                                'label' => 'Submission Stage',
                                'icon'  => 'ti ti-git-merge',
                                'route' => 'admin.demo-submission-stage',
                                'roles' => ['superadmin', 'admin'],
                            ],
                            [
                                'key'   => 'demo-feedbacks',
                                'label' => 'Feedback',
                                'icon'  => 'ti ti-message-dots',
                                'route' => 'admin.feedbacks',
                                'roles' => ['superadmin', 'admin'],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Trainer only ────────────────────────────────
            [
                'category' => 'Training',
                'icon'     => 'fas fa-chalkboard-teacher',
                'modules'  => [
                    [
                        'key'   => 'trainer-submissions',
                        'label' => 'Review Queue',
                        'icon'  => 'ti ti-git-pull-request',
                        'route' => 'trainer.submissions',
                        'roles' => ['trainer'],
                    ],
                    [
                        'key'   => 'trainer-courses',
                        'label' => 'All Courses',
                        'icon'  => 'ti ti-books',
                        'route' => 'trainer.courses',
                        'roles' => ['trainer'],
                    ],
                    [
                        'key'   => 'trainer-assigned-students',
                        'label' => 'Assigned Students',
                        'icon'  => 'ti ti-users',
                        'route' => 'trainer.assigned-students',
                        'roles' => ['trainer'],
                    ],
                    [
                        'key'   => 'trainer-progress',
                        'label' => 'Trainer Tracking',
                        'icon'  => 'ti ti-chart-line',
                        'route' => 'trainer.progress',
                        'roles' => ['trainer'],
                    ],
                ],
            ],

            // ── All roles ────────────────────────────────────
            [
                'category' => 'Account',
                'icon'     => 'fas fa-user-circle',
                'modules'  => [
                    [
                        'key'   => 'profile',
                        'label' => 'My Profile',
                        'icon'  => 'ti ti-user-circle',
                        'route' => 'profile.edit',
                        'roles' => ['superadmin', 'admin', 'manager_hr', 'it', 'trainer', 'student', 'demo'],
                    ],
                ],
            ],

        ];

        foreach ($sidebar as $catIndex => $catData) {

            $category = ModuleCategory::create([
                'name'       => $catData['category'],
                'slug'       => Str::slug($catData['category']),
                'icon'       => $catData['icon'],
                'sort_order' => $catIndex + 1,
                'status'     => true,
            ]);

            foreach ($catData['modules'] as $moduleIndex => $moduleData) {

                $module = Module::create([
                    'category_id' => $category->id,
                    'parent_id'   => null,
                    'module_key'  => $moduleData['key'],
                    'label'       => $moduleData['label'],
                    'icon'        => $moduleData['icon'] ?? null,
                    'route'       => $moduleData['route'] ?? null,
                    'sort_order'  => $moduleIndex + 1,
                    'status'      => true,
                ]);

                $this->syncPermissions($module, $roles, $moduleData['roles'] ?? $roles);

                if (!empty($moduleData['children'])) {

                    foreach ($moduleData['children'] as $childIndex => $childData) {

                        $child = Module::create([
                            'category_id' => $category->id,
                            'parent_id'   => $module->id,
                            'module_key'  => $childData['key'],
                            'label'       => $childData['label'],
                            'icon'        => $childData['icon'] ?? null,
                            'route'       => $childData['route'] ?? null,
                            'sort_order'  => $childIndex + 1,
                            'status'      => true,
                        ]);

                        $this->syncPermissions($child, $roles, $childData['roles'] ?? $roles);
                    }
                }
            }
        }
    }

    /**
     * Create one RolePermission row per role for the given module,
     * with can_view reflecting the roles actually allowed to see it
     * in the sidebar (not blanket-true like a generic seeder).
     */
    protected function syncPermissions(Module $module, array $allRoles, array $allowedRoles): void
    {
        foreach ($allRoles as $role) {

            $canView = in_array($role, $allowedRoles, true);

            RolePermission::create([
                'role'       => $role,
                'module_id'  => $module->id,
                'module_key' => $module->module_key,
                'can_view'   => $canView,
                'can_create' => $canView && in_array($role, ['superadmin', 'admin'], true),
                'can_edit'   => $canView && in_array($role, ['superadmin', 'admin'], true),
                'can_delete' => $canView && $role === 'superadmin',
            ]);
        }
    }
}