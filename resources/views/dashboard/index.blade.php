

{{-- @extends('layouts.app')

@section('content')

    <!-- Stats -->
     <section aria-label="Key metrics">
        <div class="stats-grid">
            <article class="stat-card">
                <div class="stat-icon blue" aria-hidden="true"><i class="ti ti-users"></i></div>
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value">1,284</div>
                    <div class="stat-change up"><i class="ti ti-trending-up" aria-hidden="true"></i> +12%
                        this month</div>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon green" aria-hidden="true"><i class="ti ti-school"></i></div>
                <div>
                    <div class="stat-label">Active Courses</div>
                    <div class="stat-value">47</div>
                    <div class="stat-change up"><i class="ti ti-trending-up" aria-hidden="true"></i> +3 this
                        week</div>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon amber" aria-hidden="true"><i class="ti ti-git-pull-request"></i></div>
                <div>
                    <div class="stat-label">Pending Submissions</div>
                    <div class="stat-value">24</div>
                    <div class="stat-change down"><i class="ti ti-alert-triangle" aria-hidden="true"></i>
                        Needs review</div>
                </div>
            </article>
            <article class="stat-card">
                <div class="stat-icon purple" aria-hidden="true"><i class="ti ti-certificate"></i></div>
                <div>
                    <div class="stat-label">Certificates Issued</div>
                    <div class="stat-value">328</div>
                    <div class="stat-change up"><i class="ti ti-trending-up" aria-hidden="true"></i> +18 this
                        week</div>
                </div>
            </article>
        </div>
    </section>

    <!-- Content grid -->
    <div class="content-grid">

        <!-- Recent enrollments table -->
        <section aria-label="Recent enrollments">
            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">Recent Enrollments</h2>
                    <a href="/enrollments" class="card-link">View all <i class="ti ti-arrow-right"
                            aria-hidden="true"></i></a>
                </div>
                <div style="overflow-x:auto">
                    <table class="data-table" aria-label="Recent student enrollments">
                        <thead>
                            <tr>
                                <th scope="col">Student</th>
                                <th scope="col">Course</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="sb-avatar"
                                            style="width:28px;height:28px;font-size:10px;flex-shrink:0">PS
                                        </div>
                                        Priya Sharma
                                    </div>
                                </td>
                                <td>Web Development</td>
                                <td>Jun 22, 2026</td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="sb-avatar"
                                            style="width:28px;height:28px;font-size:10px;flex-shrink:0">RK
                                        </div>
                                        Rahul Kumar
                                    </div>
                                </td>
                                <td>Python Basics</td>
                                <td>Jun 21, 2026</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="sb-avatar"
                                            style="width:28px;height:28px;font-size:10px;flex-shrink:0">AV
                                        </div>
                                        Ananya Verma
                                    </div>
                                </td>
                                <td>UI/UX Design</td>
                                <td>Jun 20, 2026</td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="sb-avatar"
                                            style="width:28px;height:28px;font-size:10px;flex-shrink:0">MS
                                        </div>
                                        Mohit Singh
                                    </div>
                                </td>
                                <td>Data Science</td>
                                <td>Jun 19, 2026</td>
                                <td><span class="badge badge-danger">Suspended</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="sb-avatar"
                                            style="width:28px;height:28px;font-size:10px;flex-shrink:0">NP
                                        </div>
                                        Neha Patel
                                    </div>
                                </td>
                                <td>Digital Marketing</td>
                                <td>Jun 18, 2026</td>
                                <td><span class="badge badge-info">Reviewing</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Right column -->
        <div style="display:flex;flex-direction:column;gap:20px">

            <!-- Course progress -->
            <section aria-label="Course progress">
                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title">Course Progress</h2>
                        <a href="/courses" class="card-link">All courses</a>
                    </div>
                    <div class="card-body">
                        <div class="progress-wrap">
                            <div class="progress-meta">
                                <span class="progress-label">Web Development</span>
                                <span class="progress-val">78%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-bar" style="width:78%" role="progressbar" aria-valuenow="78"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="progress-wrap">
                            <div class="progress-meta">
                                <span class="progress-label">Python Basics</span>
                                <span class="progress-val">61%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-bar" style="width:61%" role="progressbar" aria-valuenow="61"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="progress-wrap">
                            <div class="progress-meta">
                                <span class="progress-label">UI/UX Design</span>
                                <span class="progress-val">45%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-bar" style="width:45%" role="progressbar" aria-valuenow="45"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="progress-wrap">
                            <div class="progress-meta">
                                <span class="progress-label">Data Science</span>
                                <span class="progress-val">92%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-bar" style="width:92%" role="progressbar" aria-valuenow="92"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick actions -->
            <section aria-label="Quick actions">
                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title">Quick Actions</h2>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <button class="qa-btn" aria-label="Add new student">
                                <i class="ti ti-user-plus" aria-hidden="true"></i>
                                Add Student
                            </button>
                            <button class="qa-btn" aria-label="Create new course">
                                <i class="ti ti-book-plus" aria-hidden="true"></i>
                                New Course
                            </button>
                            <button class="qa-btn" aria-label="Send broadcast notification">
                                <i class="ti ti-speakerphone" aria-hidden="true"></i>
                                Broadcast
                            </button>
                            <button class="qa-btn" aria-label="View activity logs">
                                <i class="ti ti-activity" aria-hidden="true"></i>
                                Activity Logs
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent activity -->
            <section aria-label="Recent activity">
                <div class="card">
                    <div class="card-head">
                        <h2 class="card-title">Recent Activity</h2>
                    </div>
                    <div class="card-body" style="padding-top:8px">
                        <div class="activity-item">
                            <div class="activity-dot" style="background:var(--success)" aria-hidden="true">
                            </div>
                            <div>
                                <div class="activity-text">Priya Sharma enrolled in <strong>Web
                                        Development</strong></div>
                                <div class="activity-time">2 min ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot" style="background:var(--warning)" aria-hidden="true">
                            </div>
                            <div>
                                <div class="activity-text">Submission by Rahul Kumar marked
                                    <strong>pending</strong>
                                </div>
                                <div class="activity-time">15 min ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot" style="background:var(--brand-secondary)" aria-hidden="true">
                            </div>
                            <div>
                                <div class="activity-text">Certificate issued to <strong>Ananya Verma</strong>
                                </div>
                                <div class="activity-time">1 hour ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot" style="background:var(--info)" aria-hidden="true">
                            </div>
                            <div>
                                <div class="activity-text">New course <strong>AI Fundamentals</strong> added
                                </div>
                                <div class="activity-time">3 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

@endsection --}}
@extends('layouts.app')

@php
    use App\Models\User;
    use App\Models\CourseSessionItem;

    $roleLabels    = User::roleOptions();
    $isStudent     = $dashboardMode === 'student';
    $isTrainer     = $dashboardMode === 'trainer';
    $isAdmin       = in_array($user->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);
    $isDemo        = $dashboardMode === 'demo';

    $accentClass = [
        'blue'   => 'accent-blue',
        'green'  => 'accent-green',
        'violet' => 'accent-violet',
        'orange' => 'accent-orange',
        'red'    => 'accent-red',
        'teal'   => 'accent-teal',
    ];
    $courseIcons = ['DS', 'UX', 'FN', 'WB', 'CL', 'AI'];

    $allCoursesRoute = $user->role === User::ROLE_STUDENT
        ? route('student.courses')
        : route('courses.index');

    $learningTitle = $isStudent ? 'My Learning'
        : ($isTrainer ? 'Assigned Learning' : 'Course Snapshot');

    $learningSubtitle = $isStudent
        ? 'Track your progress and continue your training path.'
        : ($isTrainer
            ? 'Track assigned learners and completion.'
            : 'Monitor catalog activity with role-safe access.');

    $learningActionLabel = $isStudent ? 'View all courses →' : 'Open catalog →';

    $heroKicker = $isStudent ? 'Continue learning'
        : ($user->role === User::ROLE_SUPERADMIN ? '' : 'Dashboard overview');

    $heroResumeRoute = route('panel.' . $user->role);
    if ($isStudent && !empty($studentResumeItem['route'])) {
        $heroResumeRoute = $studentResumeItem['route'];
    } elseif (!empty($heroCourse['resume_route'])) {
        $heroResumeRoute = $heroCourse['resume_route'];
    } elseif (!empty($heroCourse['course_id'])) {
        $heroResumeRoute = $user->role === User::ROLE_STUDENT
            ? route('student.courses.show', $heroCourse['course_id'])
            : route('courses.show', $heroCourse['course_id']);
    }
@endphp

@section('content')

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <div class="d-root">
        <div class="d-page">
            <div class="d-grid {{ $isStudent ? 'student-mode' : '' }}">

                {{-- ── STUDENT HERO ────────────────────────────────────────── --}}
                @if ($isStudent && !empty($heroCourse))
                    @include('dashboard.partials._hero', [
                        'heroCourse'      => $heroCourse,
                        'heroKicker'      => $heroKicker,
                        'heroResumeRoute' => $heroResumeRoute,
                    ])
                @endif

                {{-- ── DEMO DASHBOARD ─────────────────────────────────────── --}}
                @if ($isDemo)
                    @include('dashboard.partials._demo', [
                        'notifications'    => $notifications,
                        'demoFeatureVideos'=> $demoFeatureVideos,
                        'demoAssignments'  => $demoAssignments,
                        'demoCategories'   => $demoCategories,
                        'demoReviewVideos' => $demoReviewVideos,
                    ])

                {{-- ── ADMIN / SUPERADMIN ──────────────────────────────────── --}}
                @elseif ($isAdmin)
                    @include('dashboard.partials._admin', [
                        'panelDescription' => $panelDescription,
                        'overviewCards'    => $overviewCards,
                    ])
                @endif

                {{-- ── SHARED SECTIONS (non-demo) ─────────────────────────── --}}
                @if (!$isDemo)
                    @if ($isStudent)
                        @include('dashboard.partials._student', [
                            'studentResumeItem'          => $studentResumeItem ?? null,
                            'studentRecentSubmissions'   => $studentRecentSubmissions,
                            'studentCertificates'        => $studentCertificates,
                            'studentPendingActionSummary'=> $studentPendingActionSummary,
                            'studentPendingActionItems'  => $studentPendingActionItems,
                            'learningItems'              => $learningItems,
                            'recommendedCourses'         => $recommendedCourses,
                            'quickActions'               => $quickActions,
                            'notifications'              => $notifications,
                            'skillProgress'              => $skillProgress,
                            'topics'                     => $topics,
                            'accentClass'                => $accentClass,
                            'courseIcons'                => $courseIcons,
                            'allCoursesRoute'            => $allCoursesRoute,
                            'learningTitle'              => $learningTitle,
                            'learningSubtitle'           => $learningSubtitle,
                            'learningActionLabel'        => $learningActionLabel,
                            'assignedCourseIds'          => $assignedCourseIds ?? [],
                        ])
                    @else
                        @include('dashboard.partials._trainer-admin-shared', [
                            'learningItems'       => $learningItems,
                            'recommendedCourses'  => $recommendedCourses,
                            'quickActions'        => $quickActions,
                            'notifications'       => $notifications,
                            'skillProgress'       => $skillProgress,
                            'topics'              => $topics,
                            'accentClass'         => $accentClass,
                            'courseIcons'         => $courseIcons,
                            'allCoursesRoute'     => $allCoursesRoute,
                            'learningTitle'       => $learningTitle,
                            'learningSubtitle'    => $learningSubtitle,
                            'learningActionLabel' => $learningActionLabel,
                            'assignedCourseIds'   => $assignedCourseIds ?? [],
                            'isTrainer'           => $isTrainer,
                        ])
                    @endif
                @endif

            </div>{{-- /d-grid --}}
        </div>{{-- /d-page --}}
    </div>{{-- /d-root --}}

    @if ($isDemo)
        <script src="{{ asset('js/student-courses.js') }}" defer></script>
    @endif

@endsection