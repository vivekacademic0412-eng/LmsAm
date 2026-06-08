
<aside class="sidebar" id="mainSidebar">

    {{-- ── Brand ──────────────────────────────────── --}}
    <div class="sb-brand">
        <a href="{{ route('dashboard') }}" class="sb-logo">
            <div class="sb-logo-mark">A</div>
            <div>
                <div class="sb-logo-text">Academic Mantra</div>
                <div class="sb-logo-sub">LMS Platform</div>
            </div>
        </a>
    </div>

    {{-- ── Navigation ──────────────────────────────── --}}
    <nav class="sb-nav" role="navigation" aria-label="Main navigation">

        @php
            $path = request()->path();

            /* ── Icon map ─────────────────────────────── */
            $icons = [
                'dashboard'                      => 'fa-solid  fa-layout-dashboard',
                'student.courses'                => 'fa-solid fa-books',
                'student.history'                => 'fa-solid fa-history',
                'student.certificates'           => 'fa-solid fa-certificate',
                'profile.edit'                   => 'fa-solid fa-user-circle',
                'trainer.submissions'            => 'fa-solid fa-git-pull-request',
                'trainer.courses'                => 'fa-solid fa-books',
                'trainer.assigned-students'      => 'fa-solid fa-users',
                'trainer.progress'               => 'fa-solid fa-chart-line',
                'panel.manager_hr'               => 'fa-solid fa-briefcase',
                'panel.it'                       => 'fa-solid fa-server',
                'course-categories.index'        => 'fa-solid fa-folder',
                'courses.index'                  => 'fa-solid fa-school',
                'enrollments.index'              => 'fa-solid fa-clipboard-list',
                'submissions.index'              => 'fa-solid fa-git-pull-request',
                'activity-logs.index'            => 'fa-solid fa-activity',
                'users.index'                    => 'fa-solid fa-shield-check',
                'broadcast-notifications.index'  => 'fa-solid fa-speakerphone',
                'demo-tasks.create-page'         => 'fa-solid fa-plus-circle',
                'demo-tasks.assign-page'         => 'fa-solid fa-user-check',
                'demo-feature-video.index'       => 'fa-solid fa-video',
                'demo-review-videos.index'       => 'fa-solid fa-star',
            ];

            /* ── helper: is route active ─────────────── */
            $isActive = function(string $routeName) use ($path): bool {
                if (! \Illuminate\Support\Facades\Route::has($routeName)) return false;
                $routePath = trim(parse_url(route($routeName), PHP_URL_PATH), '/');
                return $path === $routePath || str_starts_with($path, $routePath.'/');
            };

            $routeUrl = fn(string $r) =>
                \Illuminate\Support\Facades\Route::has($r) ? route($r) : '#';
        @endphp

        {{-- ════════════════════════════════════════════
             STUDENT MENU
        ════════════════════════════════════════════ --}}
        @if($user->role === \App\Models\User::ROLE_STUDENT)

            <div class="sb-section">Overview</div>
            <a href="{{ $routeUrl('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

            <div class="sb-section">Learning</div>
            <a href="{{ $routeUrl('student.courses') }}"
               class="sb-item {{ $isActive('student.courses') ? 'active' : '' }}">
                <i class="ti ti-books item-icon" aria-hidden="true"></i>
                <span class="item-label">My Courses</span>
            </a>
            <a href="{{ $routeUrl('student.history') }}"
               class="sb-item {{ $isActive('student.history') ? 'active' : '' }}">
                <i class="ti ti-history item-icon" aria-hidden="true"></i>
                <span class="item-label">History</span>
            </a>
            <a href="{{ $routeUrl('student.certificates') }}"
               class="sb-item {{ $isActive('student.certificates') ? 'active' : '' }}">
                <i class="ti ti-certificate item-icon" aria-hidden="true"></i>
                <span class="item-label">Certificates</span>
            </a>

            <div class="sb-section">Account</div>
            <a href="{{ $routeUrl('profile.edit') }}"
               class="sb-item {{ $isActive('profile.edit') ? 'active' : '' }}">
                <i class="ti ti-user-circle item-icon" aria-hidden="true"></i>
                <span class="item-label">My Profile</span>
            </a>

        {{-- ════════════════════════════════════════════
             DEMO MENU
        ════════════════════════════════════════════ --}}
        @elseif($user->role === \App\Models\User::ROLE_DEMO)

            <div class="sb-section">Overview</div>
            <a href="{{ route('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

        {{-- ════════════════════════════════════════════
             TRAINER MENU
        ════════════════════════════════════════════ --}}
        @elseif($user->role === \App\Models\User::ROLE_TRAINER)

            <div class="sb-section">Overview</div>
            <a href="{{ $routeUrl('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

            <div class="sb-section">Training</div>
            <a href="{{ $routeUrl('trainer.submissions') }}"
               class="sb-item {{ $isActive('trainer.submissions') ? 'active' : '' }}">
                <i class="ti ti-git-pull-request item-icon" aria-hidden="true"></i>
                <span class="item-label">Review Queue</span>
                {{-- dynamic badge could go here --}}
            </a>
            <a href="{{ $routeUrl('trainer.courses') }}"
               class="sb-item {{ $isActive('trainer.courses') ? 'active' : '' }}">
                <i class="ti ti-books item-icon" aria-hidden="true"></i>
                <span class="item-label">All Courses</span>
            </a>

            <div class="sb-section">Students</div>
            <a href="{{ $routeUrl('trainer.assigned-students') }}"
               class="sb-item {{ $isActive('trainer.assigned-students') ? 'active' : '' }}">
                <i class="ti ti-users item-icon" aria-hidden="true"></i>
                <span class="item-label">Assigned Students</span>
            </a>
            <a href="{{ $routeUrl('trainer.progress') }}"
               class="sb-item {{ $isActive('trainer.progress') ? 'active' : '' }}">
                <i class="ti ti-chart-line item-icon" aria-hidden="true"></i>
                <span class="item-label">Trainer Tracking</span>
            </a>

            <div class="sb-section">Account</div>
            <a href="{{ $routeUrl('profile.edit') }}"
               class="sb-item {{ $isActive('profile.edit') ? 'active' : '' }}">
                <i class="ti ti-user-circle item-icon" aria-hidden="true"></i>
                <span class="item-label">My Profile</span>
            </a>

        {{-- ════════════════════════════════════════════
             ADMIN / HR / IT / SUPERADMIN MENU
        ════════════════════════════════════════════ --}}
        @else

            {{-- Overview --}}
            <div class="sb-section">Overview</div>
            <a href="{{ $routeUrl('dashboard') }}"
               class="sb-item {{ $isActive('dashboard') ? 'active' : '' }}">
                <i class="ti ti-layout-dashboard item-icon" aria-hidden="true"></i>
                <span class="item-label">Dashboard</span>
            </a>

            {{-- HR Panel --}}
            @if($user->role === \App\Models\User::ROLE_MANAGER_HR)
                <div class="sb-section">HR</div>
                <a href="{{ $routeUrl('panel.manager_hr') }}"
                   class="sb-item {{ $isActive('panel.manager_hr') ? 'active' : '' }}">
                    <i class="ti ti-briefcase item-icon" aria-hidden="true"></i>
                    <span class="item-label">HR Panel</span>
                </a>
            @endif

            {{-- IT Panel --}}
            @if($user->role === \App\Models\User::ROLE_IT)
                <div class="sb-section">IT</div>
                <a href="{{ $routeUrl('panel.it') }}"
                   class="sb-item {{ $isActive('panel.it') ? 'active' : '' }}">
                    <i class="ti ti-server item-icon" aria-hidden="true"></i>
                    <span class="item-label">IT Panel</span>
                </a>
            @endif

            {{-- Courses Section --}}
            @if(in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER_HR, \App\Models\User::ROLE_IT]))

                @php
                    $courseGroupActive =
                        $isActive('course-categories.index') ||
                        $isActive('courses.index');
                @endphp

                <div class="sb-section">Courses</div>

                <div class="sb-group {{ $courseGroupActive ? 'open' : '' }}"
                     data-group="courses">
                    <button type="button"
                            class="sb-group-trigger {{ $courseGroupActive ? 'has-active' : '' }}"
                            aria-expanded="{{ $courseGroupActive ? 'true' : 'false' }}">
                        <i class="ti ti-school item-icon" aria-hidden="true"></i>
                        <span class="item-label">Courses</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub">
                        <div class="sb-sub-inner">
                            <a href="{{ $routeUrl('course-categories.index') }}"
                               class="sb-sub-item {{ $isActive('course-categories.index') ? 'active' : '' }}">
                                <i class="ti ti-folder" aria-hidden="true"></i>
                                Categories
                            </a>
                            <a href="{{ $routeUrl('courses.index') }}"
                               class="sb-sub-item {{ $isActive('courses.index') ? 'active' : '' }}">
                                <i class="ti ti-books" aria-hidden="true"></i>
                                All Courses
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Students Section --}}
            @if(in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN]))

                @php
                    $studentGroupActive =
                        $isActive('enrollments.index') ||
                        $isActive('submissions.index');
                @endphp

                <div class="sb-section">Students</div>

                <div class="sb-group {{ $studentGroupActive ? 'open' : '' }}"
                     data-group="students">
                    <button type="button"
                            class="sb-group-trigger {{ $studentGroupActive ? 'has-active' : '' }}"
                            aria-expanded="{{ $studentGroupActive ? 'true' : 'false' }}">
                        <i class="ti ti-users item-icon" aria-hidden="true"></i>
                        <span class="item-label">Students</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub">
                        <div class="sb-sub-inner">
                            <a href="{{ $routeUrl('enrollments.index') }}"
                               class="sb-sub-item {{ $isActive('enrollments.index') ? 'active' : '' }}">
                                <i class="ti ti-clipboard-list" aria-hidden="true"></i>
                                Enrollments
                            </a>
                            <a href="{{ $routeUrl('submissions.index') }}"
                               class="sb-sub-item {{ $isActive('submissions.index') ? 'active' : '' }}">
                                <i class="ti ti-git-pull-request" aria-hidden="true"></i>
                                Submission Review
                            </a>
                        </div>
                    </div>
                </div>

                {{-- System --}}
                <div class="sb-section">System</div>

                @php
                    $sysGroupActive =
                        $isActive('activity-logs.index') ||
                        $isActive('users.index') ||
                        $isActive('broadcast-notifications.index');
                @endphp

                <div class="sb-group {{ $sysGroupActive ? 'open' : '' }}"
                     data-group="system">
                    <button type="button"
                            class="sb-group-trigger {{ $sysGroupActive ? 'has-active' : '' }}"
                            aria-expanded="{{ $sysGroupActive ? 'true' : 'false' }}">
                        <i class="ti ti-settings item-icon" aria-hidden="true"></i>
                        <span class="item-label">System</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub">
                        <div class="sb-sub-inner">
                            <a href="{{ $routeUrl('activity-logs.index') }}"
                               class="sb-sub-item {{ $isActive('activity-logs.index') ? 'active' : '' }}">
                                <i class="ti ti-activity" aria-hidden="true"></i>
                                Activity Logs
                            </a>
                            <a href="{{ $routeUrl('users.index') }}"
                               class="sb-sub-item {{ $isActive('users.index') ? 'active' : '' }}">
                                <i class="ti ti-shield-check" aria-hidden="true"></i>
                                User Control
                            </a>
                            <a href="{{ $routeUrl('broadcast-notifications.index') }}"
                               class="sb-sub-item {{ $isActive('broadcast-notifications.index') ? 'active' : '' }}">
                                <i class="ti ti-speakerphone" aria-hidden="true"></i>
                                Broadcast
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Demo Tasks --}}
                @php
                    $demoGroupActive =
                        $isActive('demo-tasks.create-page') ||
                        $isActive('demo-tasks.assign-page') ||
                        $isActive('demo-feature-video.index') ||
                        $isActive('demo-review-videos.index');
                @endphp

                <div class="sb-section">Demo</div>

                <div class="sb-group {{ $demoGroupActive ? 'open' : '' }}"
                     data-group="demo">
                    <button type="button"
                            class="sb-group-trigger {{ $demoGroupActive ? 'has-active' : '' }}"
                            aria-expanded="{{ $demoGroupActive ? 'true' : 'false' }}">
                        <i class="ti ti-device-desktop item-icon" aria-hidden="true"></i>
                        <span class="item-label">Demo Tasks</span>
                        <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="sb-sub">
                        <div class="sb-sub-inner">
                            <a href="{{ $routeUrl('demo-tasks.create-page') }}"
                               class="sb-sub-item {{ $isActive('demo-tasks.create-page') ? 'active' : '' }}">
                                <i class="ti ti-plus-circle" aria-hidden="true"></i>
                                Create Task
                            </a>
                            <a href="{{ $routeUrl('demo-tasks.assign-page') }}"
                               class="sb-sub-item {{ $isActive('demo-tasks.assign-page') ? 'active' : '' }}">
                                <i class="ti ti-user-check" aria-hidden="true"></i>
                                Assign Task
                            </a>
                            <a href="{{ $routeUrl('demo-feature-video.index') }}"
                               class="sb-sub-item {{ $isActive('demo-feature-video.index') ? 'active' : '' }}">
                                <i class="ti ti-video" aria-hidden="true"></i>
                                Feature Video
                            </a>
                            <a href="{{ $routeUrl('demo-review-videos.index') }}"
                               class="sb-sub-item {{ $isActive('demo-review-videos.index') ? 'active' : '' }}">
                                <i class="ti ti-star" aria-hidden="true"></i>
                                Reviews
                            </a>
                        </div>
                    </div>
                </div>

            @endif

            {{-- Account --}}
            <div class="sb-divider"></div>
            <a href="{{ $routeUrl('profile.edit') }}"
               class="sb-item {{ $isActive('profile.edit') ? 'active' : '' }}">
                <i class="ti ti-user-circle item-icon" aria-hidden="true"></i>
                <span class="item-label">My Profile</span>
            </a>

        @endif

    </nav>

    {{-- ── Bottom User Card ────────────────────────── --}}
    <div class="sb-bottom">

        <a href="{{ route('profile.edit') }}" class="sb-user" style="text-decoration:none">
            <div class="sb-avatar">
                @if($avatarUrl)
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
            <button type="submit" class="sb-logout">
                <i class="ti ti-logout" aria-hidden="true"></i>
                Sign Out
            </button>
        </form>

    </div>

</aside>
