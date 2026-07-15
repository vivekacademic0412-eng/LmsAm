<aside class="sidebar" id="sidebar" role="complementary" aria-label="Main navigation">

    {{-- ── Brand ──────────────────────────────────── --}}
    <div class="sb-brand">
        <a href="{{ route('dashboard') }}" class="sb-logo" aria-label="Academic Mantra LMS — Home">
            {{-- <div class="sb-logo-mark" aria-hidden="true">  --}}
               
              
            {{-- </div> --}}
            <div class="sb-brand-text">
               <img src="{{ asset('theme/images/am35.png') }}" alt="LIVE Skills" class="brand-logo"
                    title="LIVE Skills Training Programs" loading="lazy">
            </div>
        </a>
    </div>

    {{-- ── Navigation ──────────────────────────────── --}}
    <nav class="sb-nav" role="navigation" aria-label="Main navigation">

        @php
            $path = request()->path();

            /* ── Helper: is route active ─────────────── */
            $isActive = function (string $routeName) use ($path): bool {
                if (!\Illuminate\Support\Facades\Route::has($routeName)) {
                    return false;
                }
                $routePath = trim(parse_url(route($routeName), PHP_URL_PATH), '/');
                return $path === $routePath || str_starts_with($path, $routePath . '/');
            };

            $routeUrl = fn(string $r) => \Illuminate\Support\Facades\Route::has($r) ? route($r) : '#';
        @endphp

        {{-- ════════════════════════════════════════════
             STUDENT MENU
        ════════════════════════════════════════════ --}}
        @if ($user->role === \App\Models\User::ROLE_STUDENT)

            <div class="sb-section" aria-hidden="true">Overview</div>
            <a href="{{ route('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}"
               {{ $isActive('dashboard') ? 'aria-current=page' : '' }}>
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

            <div class="sb-section" aria-hidden="true">Learning</div>
            <a href="{{ route('student.courses') }}"
               class="sb-item {{ $isActive('student.courses') ? 'active' : '' }}"
               {{ $isActive('student.courses') ? 'aria-current=page' : '' }}>
                <i class="ti ti-books item-icon" aria-hidden="true"></i>
                <span class="item-label">Explore Courses</span>
            </a>
            <a href="{{ route('lms.choose-type') }}"
               class="sb-item {{ $isActive('lms.choose-type') ? 'active' : '' }}"
               {{ $isActive('lms.choose-type') ? 'aria-current=page' : '' }}>
                <i class="ti ti-history item-icon" aria-hidden="true"></i>
                <span class="item-label">Book Demo Session</span>
            </a>
             <a href="{{ route('payments.index') }}"
               class="sb-item {{ $isActive('payments.index') ? 'active' : '' }}"
               {{ $isActive('payments.index') ? 'aria-current=page' : '' }}>
                <i class="ti ti-currency-rupee item-icon" aria-hidden="true"></i>
                <span class="item-label">Payments Transactions</span>
            </a>
             
            {{-- <a href="{{ route('student.certificates') }}"
               class="sb-item {{ $isActive('student.certificates') ? 'active' : '' }}"
               {{ $isActive('student.certificates') ? 'aria-current=page' : '' }}>
                <i class="ti ti-bell item-icon" aria-hidden="true"></i>
               
                <span class="item-label">Latest Updates</span>
            </a> --}}
            {{-- <a href="{{ route('student.certificates') }}"
               class="sb-item {{ $isActive('student.certificates') ? 'active' : '' }}"
               {{ $isActive('student.certificates') ? 'aria-current=page' : '' }}>
                <i class="ti ti-certificate item-icon" aria-hidden="true"></i>
                <span class="item-label">Certificates</span>
            </a> --}}

            <div class="sb-section" aria-hidden="true">Account</div>
            <div class="sb-divider"></div>
            <a href="{{ route('profile.edit') }}"
               class="sb-item {{ $isActive('profile.edit') ? 'active' : '' }}"
               {{ $isActive('profile.edit') ? 'aria-current=page' : '' }}>
                <i class="ti ti-user-circle item-icon" aria-hidden="true"></i>
                <span class="item-label">My Profile</span>
            </a>

        {{-- ════════════════════════════════════════════
             DEMO MENU
        ════════════════════════════════════════════ --}}
        @elseif ($user->role === \App\Models\User::ROLE_DEMO)

            <div class="sb-section" aria-hidden="true">Overview</div>
            <a href="{{ route('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}"
               {{ $isActive('dashboard') ? 'aria-current=page' : '' }}>
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

        {{-- ════════════════════════════════════════════
             TRAINER MENU
        ════════════════════════════════════════════ --}}
        @elseif ($user->role === \App\Models\User::ROLE_TRAINER)

            <div class="sb-section" aria-hidden="true">Overview</div>
            <a href="{{ route('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}"
               {{ $isActive('dashboard') ? 'aria-current=page' : '' }}>
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

            <div class="sb-section" aria-hidden="true">Training</div>
            <a href="{{ route('trainer.submissions') }}"
               class="sb-item {{ $isActive('trainer.submissions') ? 'active' : '' }}"
               {{ $isActive('trainer.submissions') ? 'aria-current=page' : '' }}>
                <i class="ti ti-git-pull-request item-icon" aria-hidden="true"></i>
                <span class="item-label">Review Queue</span>
            </a>
            <a href="{{ route('trainer.courses') }}"
               class="sb-item {{ $isActive('trainer.courses') ? 'active' : '' }}"
               {{ $isActive('trainer.courses') ? 'aria-current=page' : '' }}>
                <i class="ti ti-books item-icon" aria-hidden="true"></i>
                <span class="item-label">All Courses</span>
            </a>

            <div class="sb-section" aria-hidden="true">Students</div>
            <a href="{{ route('trainer.assigned-students') }}"
               class="sb-item {{ $isActive('trainer.assigned-students') ? 'active' : '' }}"
               {{ $isActive('trainer.assigned-students') ? 'aria-current=page' : '' }}>
                <i class="ti ti-users item-icon" aria-hidden="true"></i>
                <span class="item-label">Assigned Students</span>
            </a>
            <a href="{{ route('trainer.progress') }}"
               class="sb-item {{ $isActive('trainer.progress') ? 'active' : '' }}"
               {{ $isActive('trainer.progress') ? 'aria-current=page' : '' }}>
                <i class="ti ti-chart-line item-icon" aria-hidden="true"></i>
                <span class="item-label">Trainer Tracking</span>
            </a>

            <div class="sb-section" aria-hidden="true">Account</div>
            <div class="sb-divider"></div>
            <a href="{{ route('profile.edit') }}"
               class="sb-item {{ $isActive('profile.edit') ? 'active' : '' }}"
               {{ $isActive('profile.edit') ? 'aria-current=page' : '' }}>
                <i class="ti ti-user-circle item-icon" aria-hidden="true"></i>
                <span class="item-label">My Profile</span>
            </a>

        {{-- ════════════════════════════════════════════
             ADMIN / HR / IT / SUPERADMIN MENU
        ════════════════════════════════════════════ --}}
        @else

            {{-- ── Overview ─────────────────────────── --}}
            <div class="sb-section" aria-hidden="true">Overview</div>
            <a href="{{ route('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}"
               {{ $isActive('dashboard') ? 'aria-current=page' : '' }}>
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

            {{-- ── HR Panel ──────────────────────────── --}}
            @if ($user->role === \App\Models\User::ROLE_MANAGER_HR)
                <div class="sb-section" aria-hidden="true">HR</div>
                <a href="{{ route('panel.manager_hr') }}"
                   class="sb-item {{ $isActive('panel.manager_hr') ? 'active' : '' }}"
                   {{ $isActive('panel.manager_hr') ? 'aria-current=page' : '' }}>
                    <i class="ti ti-briefcase item-icon" aria-hidden="true"></i>
                    <span class="item-label">HR Panel</span>
                </a>
            @endif

            {{-- ── IT Panel ──────────────────────────── --}}
            @if ($user->role === \App\Models\User::ROLE_IT)
                <div class="sb-section" aria-hidden="true">IT</div>
                <a href="{{ route('panel.it') }}"
                   class="sb-item {{ $isActive('panel.it') ? 'active' : '' }}"
                   {{ $isActive('panel.it') ? 'aria-current=page' : '' }}>
                    <i class="ti ti-server item-icon" aria-hidden="true"></i>
                    <span class="item-label">IT Panel</span>
                </a>
            @endif

            {{-- ── Courses ───────────────────────────── --}}
            @if (in_array($user->role, [
                    \App\Models\User::ROLE_SUPERADMIN,
                    \App\Models\User::ROLE_ADMIN,
                    \App\Models\User::ROLE_MANAGER_HR,
                    \App\Models\User::ROLE_IT,
                ]))
                @php
                    $courseGroupActive = $isActive('course-categories.index') || $isActive('courses.index');
                @endphp

                <div class="sb-section" aria-hidden="true">Courses</div>

                <div class="sb-group {{ $courseGroupActive ? 'open' : '' }}" id="grp-courses">
                    <button type="button"
                            class="sb-group-trigger {{ $courseGroupActive ? 'has-active' : '' }}"
                            onclick="toggleGroup('grp-courses')"
                            aria-expanded="{{ $courseGroupActive ? 'true' : 'false' }}"
                            aria-controls="sub-courses">
                        <i class="ti ti-school item-icon" aria-hidden="true"></i>
                        <span class="item-label">Courses</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub" id="sub-courses">
                        <div class="sb-sub-inner">
                            <a href="{{ route('course-categories.index') }}"
                               class="sb-sub-item {{ $isActive('course-categories.index') ? 'active' : '' }}"
                               {{ $isActive('course-categories.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-folder" aria-hidden="true"></i>
                                Categories
                            </a>
                            <a href="{{ route('courses.index') }}"
                               class="sb-sub-item {{ $isActive('courses.index') ? 'active' : '' }}"
                               {{ $isActive('courses.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-books" aria-hidden="true"></i>
                                All Courses
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Students + System + Demo (Admin/Superadmin only) ── --}}
            @if (in_array($user->role, [
                    \App\Models\User::ROLE_SUPERADMIN,
                    \App\Models\User::ROLE_ADMIN,
                ]))
                @php
                    $studentGroupActive = $isActive('enrollments.index') || $isActive('submissions.index') || $isActive('payments.index');
                    $sysGroupActive     = $isActive('activity-logs.index') || $isActive('users.index') || $isActive('broadcast-notifications.index');
                    $demoGroupActive    = $isActive('demo-tasks.create-page') || $isActive('demo-tasks.assign-page') || $isActive('demo-feature-video.index') || $isActive('demo-review-videos.index') || $isActive('admin.demo-hero');
                    $demoStagesActive   = $isActive('admin.feedbacks') || $isActive('admin.demo-students') || $isActive('admin.demo-submission-stage');
                @endphp

                {{-- Students --}}
               
                <div class="sb-section" aria-hidden="true">Students</div>

                <div class="sb-group {{ $studentGroupActive ? 'open' : '' }}" id="grp-students">
                    <button type="button"
                            class="sb-group-trigger {{ $studentGroupActive ? 'has-active' : '' }}"
                            onclick="toggleGroup('grp-students')"
                            aria-expanded="{{ $studentGroupActive ? 'true' : 'false' }}"
                            aria-controls="sub-students">
                        <i class="ti ti-users item-icon" aria-hidden="true"></i>
                        <span class="item-label">Students</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub" id="sub-students">
                        <div class="sb-sub-inner">
                            <a href="{{ route('enrollments.index') }}"
                               class="sb-sub-item {{ $isActive('enrollments.index') ? 'active' : '' }}"
                               {{ $isActive('enrollments.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-clipboard-list" aria-hidden="true"></i>
                                Enrollments
                            </a>
                            <a href="{{ route('submissions.index') }}"
                               class="sb-sub-item {{ $isActive('submissions.index') ? 'active' : '' }}"
                               {{ $isActive('submissions.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-git-pull-request" aria-hidden="true"></i>
                                Submission Review
                            </a>
                             <a href="{{ route('payments.index') }}"
                               class="sb-sub-item {{ $isActive('payments.index') ? 'active' : '' }}"
                               {{ $isActive('payments.index') ? 'aria-current=page' : '' }}>
                               <i class="ti ti-currency-rupee item-icon" aria-hidden="true"></i>
                               Payments Transactions
                            </a>
                        </div>
                    </div>
                </div>

                {{-- System --}}
                <div class="sb-section" aria-hidden="true">System</div>

                <div class="sb-group {{ $sysGroupActive ? 'open' : '' }}" id="grp-system">
                    <button type="button"
                            class="sb-group-trigger {{ $sysGroupActive ? 'has-active' : '' }}"
                            onclick="toggleGroup('grp-system')"
                            aria-expanded="{{ $sysGroupActive ? 'true' : 'false' }}"
                            aria-controls="sub-system">
                        <i class="ti ti-settings item-icon" aria-hidden="true"></i>
                        <span class="item-label">System</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub" id="sub-system">
                        <div class="sb-sub-inner">
                            <a href="{{ route('activity-logs.index') }}"
                               class="sb-sub-item {{ $isActive('activity-logs.index') ? 'active' : '' }}"
                               {{ $isActive('activity-logs.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-activity" aria-hidden="true"></i>
                                Activity Logs
                            </a>
                            <a href="{{ route('users.index') }}"
                               class="sb-sub-item {{ $isActive('users.index') ? 'active' : '' }}"
                               {{ $isActive('users.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-shield-check" aria-hidden="true"></i>
                                User Control
                            </a>
                            <a href="{{ route('broadcast-notifications.index') }}"
                               class="sb-sub-item {{ $isActive('broadcast-notifications.index') ? 'active' : '' }}"
                               {{ $isActive('broadcast-notifications.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-speakerphone" aria-hidden="true"></i>
                                Broadcast
                            </a>
                             <a href="{{ route('admin.brocheres') }}"
                               class="sb-sub-item {{ $isActive('admin.brocheres') ? 'active' : '' }}"
                               {{ $isActive('admin.brocheres') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-speakerphone" aria-hidden="true"></i>
                                Brochure
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Demo Tasks --}}
                <div class="sb-section" aria-hidden="true">Demo</div>

                <div class="sb-group {{ $demoGroupActive ? 'open' : '' }}" id="grp-demo-tasks">
                    <button type="button"
                            class="sb-group-trigger {{ $demoGroupActive ? 'has-active' : '' }}"
                            onclick="toggleGroup('grp-demo-tasks')"
                            aria-expanded="{{ $demoGroupActive ? 'true' : 'false' }}"
                            aria-controls="sub-demo-tasks">
                        <i class="ti ti-device-desktop item-icon" aria-hidden="true"></i>
                        <span class="item-label">Demo Tasks</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub" id="sub-demo-tasks">
                        <div class="sb-sub-inner">
                            <a href="{{ route('demo-tasks.create-page') }}"
                               class="sb-sub-item {{ $isActive('demo-tasks.create-page') ? 'active' : '' }}"
                               {{ $isActive('demo-tasks.create-page') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-plus-circle" aria-hidden="true"></i>
                                Create Task
                            </a>
                            <a href="{{ route('demo-tasks.assign-page') }}"
                               class="sb-sub-item {{ $isActive('demo-tasks.assign-page') ? 'active' : '' }}"
                               {{ $isActive('demo-tasks.assign-page') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-user-check" aria-hidden="true"></i>
                                Assign Task
                            </a>
                            <a href="{{ route('demo-feature-video.index') }}"
                               class="sb-sub-item {{ $isActive('demo-feature-video.index') ? 'active' : '' }}"
                               {{ $isActive('demo-feature-video.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-video" aria-hidden="true"></i>
                                Feature Video
                            </a>
                            <a href="{{ route('admin.demo-hero') }}"
                               class="sb-sub-item {{ $isActive('admin.demo-hero') ? 'active' : '' }}"
                               {{ $isActive('admin.demo-hero') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-video" aria-hidden="true"></i>
                                Hero Section -Banner
                            </a>
                            <a href="{{ route('demo-review-videos.index') }}"
                               class="sb-sub-item {{ $isActive('demo-review-videos.index') ? 'active' : '' }}"
                               {{ $isActive('demo-review-videos.index') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-star" aria-hidden="true"></i>
                                Reviews
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Demo Stages --}}
                <div class="sb-group {{ $demoStagesActive ? 'open' : '' }}" id="grp-demo-stages">
                    <button type="button"
                            class="sb-group-trigger {{ $demoStagesActive ? 'has-active' : '' }}"
                            onclick="toggleGroup('grp-demo-stages')"
                            aria-expanded="{{ $demoStagesActive ? 'true' : 'false' }}"
                            aria-controls="sub-demo-stages">
                        <i class="ti ti-layers-subtract item-icon" aria-hidden="true"></i>
                        <span class="item-label">Demo Stages</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub" id="sub-demo-stages">
                        <div class="sb-sub-inner">
                            <a href="{{ route('admin.demo-students') }}"
                               class="sb-sub-item {{ $isActive('admin.demo-students') ? 'active' : '' }}"
                               {{ $isActive('admin.demo-students') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-users" aria-hidden="true"></i>
                                Students
                            </a>
                            <a href="{{ route('admin.demo-submission-stage') }}"
                               class="sb-sub-item {{ $isActive('admin.demo-submission-stage') ? 'active' : '' }}"
                               {{ $isActive('admin.demo-submission-stage') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-git-merge" aria-hidden="true"></i>
                                Submission Stage
                            </a>
                            <a href="{{ route('admin.feedbacks') }}"
                               class="sb-sub-item {{ $isActive('admin.feedbacks') ? 'active' : '' }}"
                               {{ $isActive('admin.feedbacks') ? 'aria-current=page' : '' }}>
                                <i class="ti ti-message-dots" aria-hidden="true"></i>
                                Feedback
                            </a>
                        </div>
                    </div>
                </div>

            @endif

            {{-- ── Account (all admin/hr/it roles) ─── --}}
            <div class="sb-section" aria-hidden="true">Account</div>
            <div class="sb-divider"></div>
            <a href="{{ route('profile.edit') }}"
               class="sb-item {{ $isActive('profile.edit') ? 'active' : '' }}"
               {{ $isActive('profile.edit') ? 'aria-current=page' : '' }}>
                <i class="ti ti-user-circle item-icon" aria-hidden="true"></i>
                <span class="item-label">My Profile</span>
            </a>

        @endif

    </nav>

    {{-- ── Bottom User Card ────────────────────────── --}}
    <div class="sb-bottom">

        <a href="{{ route('profile.edit') }}"
           class="sb-user"
           aria-label="Go to profile — {{ $user->name }}">
            <div class="sb-avatar" aria-hidden="true">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}"
                         alt="{{ $user->name }}"
                         style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="sb-user-info">
                <div class="sb-user-name">{{ $user->name }}</div>
                <div class="sb-user-role">{{ $roleLabels[$user->role] ?? 'User' }}</div>
            </div>
            <i class="ti ti-chevron-right sb-user-chevron" aria-hidden="true"></i>
        </a>

        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="sb-logout" aria-label="Sign out of Academic Mantra LMS">
                <i class="ti ti-logout" aria-hidden="true"></i>
                <span>Sign Out</span>
            </button>
        </form>

    </div>

</aside>

{{-- ── Sidebar collapse toggle (desktop) ──────── --}}
<button class="sb-toggle"
        id="sbToggleBtn"
        aria-label="Collapse sidebar"
        aria-expanded="true"
        aria-controls="sidebar"
        onclick="toggleSidebar()">
    <i class="ti ti-chevron-left"></i>
</button>

{{-- ── Mobile overlay ──────────────────────────── --}}
<div class="sb-overlay"
     id="sbOverlay"
     aria-hidden="true"
     onclick="closeMobileSidebar()"></div>