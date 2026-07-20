<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavPermissionSeeder extends Seeder
{
    /**
     * All roles in the system (matches App\Models\User::ROLE_* constants).
     */
    protected array $roles = [
        'superadmin',
        'admin',
        'manager_hr',
        'it',
        'trainer',
        'student',
        'demo',
    ];

    public function run(): void
    {
        DB::table('role_permissions')->truncate();
        DB::table('nav_items')->truncate();

        $now = now();

        /**
         * ─────────────────────────────────────────────
         * NAV TREE
         * ─────────────────────────────────────────────
         * Each entry:
         *   key         => unique module_key
         *   label       => display text
         *   icon        => icon class
         *   route       => named route (or null for static/group items)
         *   sort        => sort order
         *   roles       => roles allowed to VIEW this item
         *   children    => nested items (same shape), only for group headers
         *
         * This mirrors resources/views/layouts/partials/sidebar.blade.php exactly.
         */
        $tree = [

            // ── Shared: Dashboard (all roles) ─────────────
            [
                'key'   => 'dashboard',
                'label' => 'Dashboard',
                'icon'  => 'ti ti-layout-dashboard',
                'route' => 'dashboard',
                'sort'  => 1,
                'roles' => ['superadmin', 'admin', 'manager_hr', 'it', 'trainer', 'student', 'demo'],
            ],

            // ── Student: Learning ──────────────────────────
            [
                'key'   => 'student_courses',
                'label' => 'Explore Courses',
                'icon'  => 'ti ti-books',
                'route' => 'student.courses',
                'sort'  => 2,
                'roles' => ['student'],
            ],
            [
                'key'   => 'lms_choose_type',
                'label' => 'Book Demo Session',
                'icon'  => 'ti ti-history',
                'route' => 'lms.choose-type',
                'sort'  => 3,
                'roles' => ['student'],
            ],
            [
                'key'   => 'student_payments',
                'label' => 'Payments Transactions',
                'icon'  => 'ti ti-currency-rupee',
                'route' => 'payments.index',
                'sort'  => 4,
                'roles' => ['student'],
            ],
            [
                'key'   => 'student_certificates',
                'label' => 'Certificates',
                'icon'  => 'fa-solid fa-certificate',
                'route' => null, // placeholder link (#) in blade
                'sort'  => 5,
                'roles' => ['student'],
            ],

            // ── HR Panel ────────────────────────────────────
            [
                'key'   => 'panel_manager_hr',
                'label' => 'HR Panel',
                'icon'  => 'ti ti-briefcase',
                'route' => 'panel.manager_hr',
                'sort'  => 6,
                'roles' => ['manager_hr'],
            ],

            // ── IT Panel ────────────────────────────────────
            [
                'key'   => 'panel_it',
                'label' => 'IT Panel',
                'icon'  => 'ti ti-server',
                'route' => 'panel.it',
                'sort'  => 7,
                'roles' => ['it'],
            ],

            // ── Courses (group) ─────────────────────────────
            [
                'key'      => 'courses_group',
                'label'    => 'Courses',
                'icon'     => 'ti ti-school',
                'route'    => null,
                'sort'     => 8,
                'roles'    => ['superadmin', 'admin', 'manager_hr', 'it'],
                'children' => [
                    [
                        'key'   => 'course_categories',
                        'label' => 'Categories',
                        'icon'  => 'ti ti-folder',
                        'route' => 'course-categories.index',
                        'sort'  => 1,
                        'roles' => ['superadmin', 'admin', 'manager_hr', 'it'],
                    ],
                    [
                        'key'   => 'courses_index',
                        'label' => 'All Courses',
                        'icon'  => 'ti ti-books',
                        'route' => 'courses.index',
                        'sort'  => 2,
                        'roles' => ['superadmin', 'admin', 'manager_hr', 'it'],
                    ],
                ],
            ],

            // ── Students (group, admin/superadmin only) ─────
            [
                'key'      => 'students_group',
                'label'    => 'Students',
                'icon'     => 'ti ti-users',
                'route'    => null,
                'sort'     => 9,
                'roles'    => ['superadmin', 'admin'],
                'children' => [
                    [
                        'key'   => 'enrollments_index',
                        'label' => 'Enrollments',
                        'icon'  => 'ti ti-clipboard-list',
                        'route' => 'enrollments.index',
                        'sort'  => 1,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'submissions_index',
                        'label' => 'Submission Review',
                        'icon'  => 'ti ti-git-pull-request',
                        'route' => 'submissions.index',
                        'sort'  => 2,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'admin_payments',
                        'label' => 'Payments Transactions',
                        'icon'  => 'ti ti-currency-rupee',
                        'route' => 'payments.index',
                        'sort'  => 3,
                        'roles' => ['superadmin', 'admin'],
                    ],
                ],
            ],

            // ── System (group, admin/superadmin only) ───────
            [
                'key'      => 'system_group',
                'label'    => 'System',
                'icon'     => 'ti ti-settings',
                'route'    => null,
                'sort'     => 10,
                'roles'    => ['superadmin', 'admin'],
                'children' => [
                    [
                        'key'   => 'activity_logs',
                        'label' => 'Activity Logs',
                        'icon'  => 'ti ti-activity',
                        'route' => 'activity-logs.index',
                        'sort'  => 1,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'users_index',
                        'label' => 'User Control',
                        'icon'  => 'ti ti-shield-check',
                        'route' => 'users.index',
                        'sort'  => 2,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'broadcast_notifications',
                        'label' => 'Broadcast',
                        'icon'  => 'ti ti-speakerphone',
                        'route' => 'broadcast-notifications.index',
                        'sort'  => 3,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'admin_brocheres',
                        'label' => 'Brochure',
                        'icon'  => 'ti ti-speakerphone',
                        'route' => 'admin.brocheres',
                        'sort'  => 4,
                        'roles' => ['superadmin', 'admin'],
                    ],
                ],
            ],

            // ── Demo Tasks (group, admin/superadmin only) ───
            [
                'key'      => 'demo_tasks_group',
                'label'    => 'Demo Tasks',
                'icon'     => 'ti ti-device-desktop',
                'route'    => null,
                'sort'     => 11,
                'roles'    => ['superadmin', 'admin'],
                'children' => [
                    [
                        'key'   => 'demo_tasks_create',
                        'label' => 'Create Task',
                        'icon'  => 'ti ti-plus-circle',
                        'route' => 'demo-tasks.create-page',
                        'sort'  => 1,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'demo_tasks_assign',
                        'label' => 'Assign Task',
                        'icon'  => 'ti ti-user-check',
                        'route' => 'demo-tasks.assign-page',
                        'sort'  => 2,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'demo_feature_video',
                        'label' => 'Feature Video',
                        'icon'  => 'ti ti-video',
                        'route' => 'demo-feature-video.index',
                        'sort'  => 3,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'admin_demo_hero',
                        'label' => 'Hero Section - Banner',
                        'icon'  => 'ti ti-video',
                        'route' => 'admin.demo-hero',
                        'sort'  => 4,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'demo_review_videos',
                        'label' => 'Reviews',
                        'icon'  => 'ti ti-star',
                        'route' => 'demo-review-videos.index',
                        'sort'  => 5,
                        'roles' => ['superadmin', 'admin'],
                    ],
                ],
            ],

            // ── Demo Stages (group, admin/superadmin only) ──
            [
                'key'      => 'demo_stages_group',
                'label'    => 'Demo Stages',
                'icon'     => 'ti ti-layers-subtract',
                'route'    => null,
                'sort'     => 12,
                'roles'    => ['superadmin', 'admin'],
                'children' => [
                    [
                        'key'   => 'admin_demo_students',
                        'label' => 'Students',
                        'icon'  => 'ti ti-users',
                        'route' => 'admin.demo-students',
                        'sort'  => 1,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'admin_demo_submission_stage',
                        'label' => 'Submission Stage',
                        'icon'  => 'ti ti-git-merge',
                        'route' => 'admin.demo-submission-stage',
                        'sort'  => 2,
                        'roles' => ['superadmin', 'admin'],
                    ],
                    [
                        'key'   => 'admin_feedbacks',
                        'label' => 'Feedback',
                        'icon'  => 'ti ti-message-dots',
                        'route' => 'admin.feedbacks',
                        'sort'  => 3,
                        'roles' => ['superadmin', 'admin'],
                    ],
                ],
            ],

            // ── Trainer ──────────────────────────────────────
            [
                'key'   => 'trainer_submissions',
                'label' => 'Review Queue',
                'icon'  => 'ti ti-git-pull-request',
                'route' => 'trainer.submissions',
                'sort'  => 13,
                'roles' => ['trainer'],
            ],
            [
                'key'   => 'trainer_courses',
                'label' => 'All Courses',
                'icon'  => 'ti ti-books',
                'route' => 'trainer.courses',
                'sort'  => 14,
                'roles' => ['trainer'],
            ],
            [
                'key'   => 'trainer_assigned_students',
                'label' => 'Assigned Students',
                'icon'  => 'ti ti-users',
                'route' => 'trainer.assigned-students',
                'sort'  => 15,
                'roles' => ['trainer'],
            ],
            [
                'key'   => 'trainer_progress',
                'label' => 'Trainer Tracking',
                'icon'  => 'ti ti-chart-line',
                'route' => 'trainer.progress',
                'sort'  => 16,
                'roles' => ['trainer'],
            ],

            // ── Account (all roles) ──────────────────────────
            [
                'key'   => 'profile_edit',
                'label' => 'My Profile',
                'icon'  => 'ti ti-user-circle',
                'route' => 'profile.edit',
                'sort'  => 17,
                'roles' => ['superadmin', 'admin', 'manager_hr', 'it', 'trainer', 'student', 'demo'],
            ],
        ];

        // Flatten tree -> [ [row_data, roles, children], ... ] and insert with parent_id resolution.
        $moduleRoleMap = [];

        foreach ($tree as $item) {
            $parentId = DB::table('nav_items')->insertGetId([
                'parent_id'  => null,
                'module_key' => $item['key'],
                'label'      => $item['label'],
                'icon'       => $item['icon'],
                'route'      => $item['route'],
                'sort_order' => $item['sort'],
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $moduleRoleMap[$item['key']] = $item['roles'];

            if (!empty($item['children'])) {
                foreach ($item['children'] as $child) {
                    DB::table('nav_items')->insert([
                        'parent_id'  => $parentId,
                        'module_key' => $child['key'],
                        'label'      => $child['label'],
                        'icon'       => $child['icon'],
                        'route'      => $child['route'],
                        'sort_order' => $child['sort'],
                        'status'     => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $moduleRoleMap[$child['key']] = $child['roles'];
                }
            }
        }

        // ── role_permissions ────────────────────────────────
        $permRows = [];

        foreach ($moduleRoleMap as $moduleKey => $allowedRoles) {
            foreach ($this->roles as $role) {
                $canView = in_array($role, $allowedRoles, true);

                $permRows[] = [
                    'role'       => $role,
                    'module_key' => $moduleKey,
                    'can_view'   => $canView ? 1 : 0,
                    'can_create' => $canView && !in_array($role, ['student', 'demo', 'trainer'], true) ? 1 : 0,
                    'can_edit'   => $canView && !in_array($role, ['student', 'demo', 'trainer'], true) ? 1 : 0,
                    'can_delete' => $canView && in_array($role, ['superadmin', 'admin'], true) ? 1 : 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('role_permissions')->insert($permRows);
    }
}