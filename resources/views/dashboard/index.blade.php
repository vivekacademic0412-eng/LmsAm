@extends('layouts.app')

@php
    $roleLabels = \App\Models\User::roleOptions();
    $accentClass = [
        'blue' => 'accent-blue',
        'green' => 'accent-green',
        'violet' => 'accent-violet',
        'orange' => 'accent-orange',
        'red' => 'accent-red',
        'teal' => 'accent-teal',
    ];
    $courseIcons = ['DS', 'UX', 'FN', 'WB', 'CL', 'AI'];
    $allCoursesRoute =
        $user->role === \App\Models\User::ROLE_STUDENT ? route('student.courses') : route('courses.index');
    $isStudent = $dashboardMode === 'student';
    $isTrainer = $dashboardMode === 'trainer';
    $learningTitle = $isStudent ? 'My Learning' : ($isTrainer ? 'Assigned Learning' : 'Course Snapshot');
    $learningSubtitle = $isStudent
        ? 'Track your progress and continue your training path.'
        : ($isTrainer
            ? 'Track assigned learners and completion.'
            : 'Monitor catalog activity with role-safe access.');
    $learningActionLabel = $isStudent ? 'View all courses →' : 'Open catalog →';
    $heroKicker = $isStudent
        ? 'Continue learning'
        : ($user->role === \App\Models\User::ROLE_SUPERADMIN
            ? ''
            : 'Dashboard overview');
    $heroResumeRoute = route('panel.' . $user->role);
    if ($isStudent && !empty($studentResumeItem) && !empty($studentResumeItem['route'])) {
        $heroResumeRoute = $studentResumeItem['route'];
    } elseif (!empty($heroCourse) && !empty($heroCourse['resume_route'])) {
        $heroResumeRoute = $heroCourse['resume_route'];
    } elseif (!empty($heroCourse['course_id'])) {
        $heroResumeRoute =
            $user->role === \App\Models\User::ROLE_STUDENT
                ? route('student.courses.show', $heroCourse['course_id'])
                : route('courses.show', $heroCourse['course_id']);
    }
@endphp

@section('content')
 @if (session()->has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    </script>
@endif
    <div class="d-root">
        <div class="d-page">
            <div class="d-grid {{ $isStudent ? 'student-mode' : '' }}">

                {{-- ── STUDENT HERO ─────────────────────────────────────────── --}}
                @if ($isStudent && !empty($heroCourse))
                    @php
                        $heroThumb = $heroCourse['thumbnail_url'] ?? '';
                        $heroBg = $heroThumb ? "url('{$heroThumb}')" : '';
                    @endphp
                    <section class="d-hero{{ $heroThumb ? ' with-image' : '' }}"
                        @if ($heroThumb) style="background-image:{{ $heroBg }};" @endif>
                        @if ($heroThumb)
                            <div class="d-hero-overlay"></div>
                        @endif
                        <div class="d-hero-inner">
                            @if ($heroKicker !== '')
                                <div class="d-hero-kicker">{{ $heroKicker }}</div>
                            @endif
                            <h1 class="d-hero-title">{{ $heroCourse['title'] ?? 'Learning Dashboard' }}</h1>
                            <p class="d-hero-meta">{{ $heroCourse['provider'] ?? 'LMS Academy' }}</p>
                            <a class="d-hero-btn" href="{{ $heroResumeRoute }}">
                                <span>Continue</span>
                                <span style="font-size:16px;">→</span>
                            </a>
                            <p class="d-hero-sub" style="margin-top:10px;">
                                {{ $heroCourse['progress_percent'] ?? 0 }}% complete &middot;
                                {{ $heroCourse['hours_done'] ?? 0 }}h of {{ $heroCourse['hours_total'] ?? 0 }}h
                            </p>
                        </div>
                        <div class="d-hero-ring">
                            <b>{{ $heroCourse['progress_percent'] ?? 0 }}%</b>
                            <span>Done</span>
                        </div>
                    </section>
                @endif

                {{-- ── DEMO MODE ────────────────────────────────────────────── --}}
                @if ($dashboardMode === 'demo')
                    @php $demoVideoCount = $demoFeatureVideos->count(); @endphp

                    <section class="d-card">
                        <div class="d-section-head">
                            <div>
                                <h2>Welcome, Demo User</h2>
                                <p>Explore the demo experience, browse every feature video, and complete your tasks.</p>
                            </div>
                        </div>
                    </section>

                    @if ($notifications->isNotEmpty())
                        <section class="d-card">
                            <div class="d-section-head" style="margin-bottom:16px;">
                                <div>
                                    <h2>Notifications</h2>
                                    <p>Announcements and updates sent to your demo account appear here.</p>
                                </div>
                            </div>
                            @include('dashboard-notification-feed-v2', ['notifications' => $notifications])
                        </section>
                    @endif

                    {{-- Feature video slider --}}
                    <section class="d-video-slider" data-demo-video-slider>
                        <div class="d-video-viewport">
                            <div class="d-video-track" data-demo-video-track>
                                @forelse ($demoFeatureVideos as $index => $video)
                                    <article class="d-video-slide {{ $index === 0 ? 'active' : '' }}" data-demo-video-slide
                                        aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                                        <div class="d-demo-video">
                                            <div class="d-demo-cover">
                                                <span class="d-demo-badge">Feature Video
                                                    {{ str_pad((string) ($video->position ?? $index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                                <h3>{{ $video->title ?: 'Feature Video' }}</h3>
                                                <p>{{ $video->description ?: 'See how our learning platform works in minutes.' }}
                                                </p>
                                                <div class="d-demo-chips">
                                                    <span class="d-demo-chip">Position
                                                        {{ $video->position ?? $index + 1 }}</span>
                                                    <span class="d-demo-chip">{{ $demoVideoCount }}
                                                        video{{ $demoVideoCount === 1 ? '' : 's' }} live</span>
                                                </div>
                                                <a class="d-demo-play"
                                                    href="{{ route('demo-feature-video.show', $video) }}" target="_blank"
                                                    rel="noopener">
                                                    <span>▶</span> Open Full Video
                                                </a>
                                            </div>
                                            <div class="d-demo-thumb">
                                                <video controls preload="metadata" controlslist="nodownload" playsinline>
                                                    <source src="{{ route('demo-feature-video.show', $video) }}"
                                                        type="{{ $video->file_mime ?: 'video/mp4' }}">
                                                </video>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <div class="d-video-slide active" data-demo-video-slide aria-hidden="false">
                                        <div class="d-demo-video">
                                            <div class="d-demo-cover">
                                                <span class="d-demo-badge">Feature Video</span>
                                                <h3>Videos Coming Soon</h3>
                                                <p>Feature videos added here will appear automatically for demo users.</p>
                                                <div class="d-demo-chips">
                                                    <span class="d-demo-chip">0 videos live</span>
                                                    <span class="d-demo-chip">Slider ready</span>
                                                </div>
                                            </div>
                                            <div class="d-demo-thumb">
                                                <div class="d-video-empty">FEATURE VIDEO</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @if ($demoVideoCount > 1)
                            <div class="d-video-nav">
                                <div class="d-video-nav-group">
                                    <button class="d-video-arrow" type="button" data-demo-video-prev
                                        aria-label="Previous">&#8249;</button>
                                    <button class="d-video-arrow" type="button" data-demo-video-next
                                        aria-label="Next">&#8250;</button>
                                </div>
                                <div class="d-video-dots">
                                    @foreach ($demoFeatureVideos as $index => $video)
                                        <button class="d-video-dot {{ $index === 0 ? 'active' : '' }}" type="button"
                                            data-demo-video-dot="{{ $index }}"
                                            aria-label="Video {{ $index + 1 }}"
                                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
                                    @endforeach
                                </div>
                                <div class="d-video-counter" data-demo-video-counter>1 / {{ $demoVideoCount }}</div>
                            </div>
                        @endif
                    </section>

                    {{-- Demo tasks
                    <section class="d-card">
                        <div class="d-section-head" style="margin-bottom:16px;">
                            <div>
                              <h2> Tasks</h2>
                               
                                <p>Download resources, review submissions, and upload your answer.</p>
                            </div>
                        </div>
                        <div class="d-demo-grid">
                            @forelse ($demoAssignments as $row)
                                @php
                                    $task = $row['task'];
                                    $submission = $row['submission'];
                                    $assignment = $row['assignment'];
                                    $canDownload = !empty($task?->resource_file_path);
                                    $hasTools = !empty($task?->ai_video_url);
                                @endphp
                                <div class="d-demo-task">
                                    @if (!empty($task?->task_video_path))
                                        <div class="d-task-video">
                                            <div class="d-task-video-note">Watch this task video first, then complete the
                                                task below.</div>
                                            <video controls preload="metadata" controlslist="nodownload" playsinline>
                                                <source src="{{ route('demo-tasks.video', $task) }}"
                                                    type="{{ $task->task_video_mime ?: 'video/mp4' }}">
                                            </video>
                                        </div>
                                    @endif
                                    <strong>{{ $task?->title ?? 'Demo Task' }}</strong>
                                    <div class="d-demo-task-meta">
                                        {{ $task?->description ?? 'Complete this task to see how submissions work.' }}
                                    </div>
                                    @if ($canDownload || $hasTools)
                                        <div class="d-task-actions">
                                            @if ($canDownload)
                                                <a class="d-btn d-btn-ghost d-btn-sm"
                                                    href="{{ route('demo-tasks.download', $task) }}">Download Resource</a>
                                            @endif
                                            @if ($hasTools)
                                                <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $task->ai_video_url }}"
                                                    target="_blank" rel="noopener">Tools</a>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($assignment)
                                        <form method="POST" action="{{ route('demo-assignments.submit', $assignment) }}"
                                            enctype="multipart/form-data" class="d-submit-panel">
                                            @csrf
                                            <div class="d-submit-block">
                                                <h4>Your Answer</h4>
                                                <textarea name="answer_text" rows="4" placeholder="Type your answer here..."></textarea>
                                                <div class="d-muted" style="font-size:12px;">Write a short response, then
                                                    upload a supporting document if needed.</div>
                                            </div>
                                            <div class="d-submit-block">
                                                <h4>Upload Document</h4>
                                                <input type="file" name="submission_file" accept="*/*">
                                                <div class="d-muted" style="font-size:12px;">PDF, DOCX, PPT, ZIP, video, or
                                                    image.</div>
                                            </div>
                                            <div class="d-task-actions">
                                                <button class="d-btn d-btn-primary d-btn-sm" type="submit">Submit Demo
                                                    Task</button>
                                                @if ($submission && $submission->file_path)
                                                    <a class="d-btn d-btn-ghost d-btn-sm"
                                                        href="{{ route('demo-tasks.submissions.download', $submission) }}">Download
                                                        Your File</a>
                                                @endif
                                            </div>
                                            @if ($submission)
                                                <div class="d-submit-preview">
                                                    <strong>Last Submission</strong>
                                                    @if ($submission->answer_text)
                                                        <p>{{ $submission->answer_text }}</p>
                                                    @else
                                                        <p>No text answer submitted.</p>
                                                    @endif
                                                    <div class="d-submit-file">
                                                        <span>{{ $submission->file_name ?: 'No file uploaded' }}</span>
                                                        @if ($submission->file_path)
                                                            <a class="d-btn d-btn-ghost d-btn-sm"
                                                                href="{{ route('demo-tasks.submissions.download', $submission) }}">Download</a>
                                                        @endif
                                                    </div>
                                                    <span class="d-muted" style="font-size:12px;">Last submitted
                                                        {{ optional($submission->submitted_at)->diffForHumans() }}</span>
                                                </div>
                                            @endif
                                        </form>
                                    @else
                                        <div class="d-sub-empty">
                                            This task was uploaded by admin.
                                            @if ($canDownload)
                                                You can access the resource above, but
                                            @else
                                            @endif
                                            Submission is disabled until it is assigned to you.
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="d-muted">No demo tasks available yet.</p>
                            @endforelse
                        </div>
                    </section> --}}
                    {{-- <livewire:student.demo-tasks :demo-assignments="$demoAssignments" /> --}}
                    {{-- Demo Practice Tasks --}}
                    <section class="d-card">
                        <div class="d-section-head" style="margin-bottom:20px;">
                            <div>
                                <h2>🎓 Demo Practice Tasks</h2>
                                <p>
                                    Welcome to your practice workspace. These demo tasks are designed to help you
                                    understand how learning materials, assignments, submissions, and file uploads
                                    work on the platform before you start real coursework.
                                </p>
                            </div>
                        </div>

                        <div class="d-demo-intro">
                            <div class="d-demo-icon">🚀</div>
                            <div>
                                <h3>Getting Started</h3>
                                <p>
                                    Watch the instructional video, download the provided resources,
                                    complete the task, and submit your answer. This is a safe practice
                                    environment where you can explore the platform workflow with confidence.
                                </p>
                            </div>
                        </div>

                        <div class="d-demo-grid">
                            @forelse ($demoAssignments as $row)
                                @php
                                    $task = $row['task'];
                                    $submission = $row['submission'];
                                    $assignment = $row['assignment'];

                                    $canDownload = !empty($task?->resource_file_path);
                                    $hasTools = !empty($task?->ai_video_url);
                                @endphp

                                <div class="d-demo-task">

                                    @if (!empty($task?->task_video_path))
                                        <div class="d-task-video">
                                            <div class="d-task-video-note">
                                                📹 Watch this short guide first, then complete the practice task below.
                                            </div>

                                            <video controls preload="metadata" controlslist="nodownload" playsinline>
                                                <source src="{{ route('demo-tasks.video', $task) }}"
                                                    type="{{ $task->task_video_mime ?: 'video/mp4' }}">
                                            </video>
                                        </div>
                                    @endif

                                    <strong>
                                        {{ $task?->title ?? 'Practice Assignment' }}
                                    </strong>

                                    <div class="d-demo-task-meta">
                                        {{ $task?->description ??
                                            'Complete this practice activity to learn how assignment submissions, document uploads, and learning resources work within the platform.' }}
                                    </div>

                                    @if ($canDownload || $hasTools)
                                        <div class="d-task-actions">

                                            @if ($canDownload)
                                                <a class="d-btn d-btn-ghost d-btn-sm"
                                                    href="{{ route('demo-tasks.download', $task) }}">
                                                    📥 Download Resource
                                                </a>
                                            @endif

                                            @if ($hasTools)
                                                <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $task->ai_video_url }}"
                                                    target="_blank" rel="noopener">
                                                    🛠 Learning Tools
                                                </a>
                                            @endif

                                        </div>
                                    @endif

                                    @if ($assignment)
                                        <form method="POST" action="{{ route('demo-assignments.submit', $assignment) }}"
                                            enctype="multipart/form-data" class="d-submit-panel">

                                            @csrf

                                            <div class="d-submit-block">
                                                <h4>✍️ Your Answer</h4>

                                                <textarea name="answer_text" rows="4"
                                                    placeholder="Write your answer, explanation, observations, or solution here..."></textarea>

                                                <div class="d-muted" style="font-size:12px;">
                                                    Submit your response just like you would for a real assignment.
                                                </div>
                                            </div>

                                            <div class="d-submit-block">
                                                <h4>📎 Upload Supporting File</h4>

                                                <input type="file" name="submission_file" accept="*/*">

                                                <div class="d-muted" style="font-size:12px;">
                                                    Accepted formats: PDF, DOCX, PPT, ZIP, Images, Videos and other
                                                    supporting files.
                                                </div>
                                            </div>

                                            <div class="d-task-actions">

                                                <button class="d-btn d-btn-primary d-btn-sm" type="submit">
                                                    🚀 Submit Practice Task
                                                </button>

                                                @if ($submission && $submission->file_path)
                                                    <a class="d-btn d-btn-ghost d-btn-sm"
                                                        href="{{ route('demo-tasks.submissions.download', $submission) }}">
                                                        📥 Download Your File
                                                    </a>
                                                @endif

                                            </div>

                                            @if ($submission)
                                                <div class="d-submit-preview">

                                                    <strong>✅ Your Latest Submission</strong>

                                                    @if ($submission->answer_text)
                                                        <p>{{ $submission->answer_text }}</p>
                                                    @else
                                                        <p>No written answer submitted yet.</p>
                                                    @endif

                                                    <div class="d-submit-file">

                                                        <span>
                                                            {{ $submission->file_name ?: 'No file uploaded' }}
                                                        </span>

                                                        @if ($submission->file_path)
                                                            <a class="d-btn d-btn-ghost d-btn-sm"
                                                                href="{{ route('demo-tasks.submissions.download', $submission) }}">
                                                                Download
                                                            </a>
                                                        @endif

                                                    </div>

                                                    <span class="d-muted" style="font-size:12px;">
                                                        Submitted
                                                        {{ optional($submission->submitted_at)->diffForHumans() }}
                                                    </span>

                                                </div>
                                            @endif

                                        </form>
                                    @else
                                        <div class="d-sub-empty">
                                            <strong>📚 Practice Resource Available</strong>

                                            <p style="margin-top:10px;">
                                                This task is currently available for learning and exploration.
                                            </p>

                                            @if ($canDownload)
                                                <p>
                                                    Download the provided resource to understand the assignment
                                                    structure and workflow.
                                                </p>
                                            @endif

                                            <p>
                                                Submission functionality will become available once this task
                                                has been assigned to your account.
                                            </p>
                                        </div>
                                    @endif

                                </div>

                            @empty

                                <div class="d-sub-empty">
                                    <strong>🎯 No Practice Tasks Available Yet</strong>

                                    <p style="margin-top:10px;">
                                        Demo tasks will appear here once they are published by the administrator.
                                        Please check back later.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </section>


                    {{-- Browse Courses --}}
                    <section class="d-card">
                        <div class="d-section-head" style="margin-bottom:16px;">
                            <div>
                                <h2>Browse Courses</h2>
                                <p>Select a main category, then choose a subcategory to filter courses.</p>
                            </div>
                        </div>
                        <div class="d-tab-row" id="categoryTabs" style="margin-bottom:6px;">
                            @foreach ($demoCategories as $index => $category)
                                <button class="d-tab-btn tab-btn main-tab  {{ $index === 0 ? 'active' : '' }}"
                                    type="button" data-tab="{{ $category->id }}">
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                        @foreach ($demoCategories as $index => $category)
                            @php
                                $tabCourses = $category->courses
                                    ->concat($category->children->flatMap->courses)
                                    ->unique('id')
                                    ->values();
                            @endphp
                            <div class="d-tab-panel {{ $index === 0 ? 'active' : '' }}"
                                data-tab-panel="{{ $category->id }}">
                                <div class="d-subtab-label">Subcategories</div>
                                <div class="d-subtab-row" data-subtabs>
                                    <button class="d-subtab-btn active" type="button" data-subtab="all">All</button>
                                    @foreach ($category->children as $child)
                                        <button class="d-subtab-btn" type="button"
                                            data-subtab="{{ $child->id }}">{{ $child->name }}</button>
                                    @endforeach
                                </div>
                                <div class="d-course-tile-grid">
                                    @forelse ($tabCourses as $course)
                                        @php
                                            $thumb = $course->thumbnail_url ?: '';
                                            $bg = $thumb
                                                ? "url('{$thumb}')"
                                                : 'linear-gradient(120deg,#1c5fca,#3aa77a)';
                                            $catName =
                                                $course->subcategory?->name ??
                                                ($course->category?->name ?? $category->name);
                                            $subId = $course->subcategory?->id
                                                ? (string) $course->subcategory->id
                                                : 'none';
                                        @endphp
                                        <div class="d-course-tile" data-subcat="{{ $subId }}">
                                            <div class="d-course-tile-top" style="background-image:{{ $bg }};">
                                                <strong>{{ $course->title }}</strong>
                                            </div>
                                            <div class="d-course-tile-body">
                                                <div class="d-muted" style="font-size:12px;">{{ $catName }}</div>
                                                <span class="d-badge-lock">🔒 Locked</span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="d-muted">No courses in this category.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </section>

                    {{-- Reviews slider --}}
                    @php $demoReviewCount = $demoReviewVideos->count(); @endphp
                    <section class="d-card">
                        <div class="d-section-head" style="margin-bottom:16px;">
                            <div>
                                <h2>Reviews</h2>
                            </div>
                        </div>
                        <div class="d-video-slider d-review-slider" data-demo-video-slider>
                            <div class="d-video-viewport">
                                <div class="d-video-track" data-demo-video-track>
                                    @forelse ($demoReviewVideos as $index => $video)
                                        <article class="d-video-slide {{ $index === 0 ? 'active' : '' }}"
                                            data-demo-video-slide aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                                            <div class="d-demo-video">
                                                <div class="d-demo-cover">
                                                    <span class="d-demo-badge">Review
                                                        {{ str_pad((string) ($video->position ?? $index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                                    <h3>{{ $video->title ?: 'Learner Review' }}</h3>
                                                    <p>{{ $video->description ?: 'YouTube review videos added in the admin panel will appear here.' }}
                                                    </p>
                                                    <div class="d-review-actions">
                                                        <div class="d-review-chips">
                                                            <span class="d-demo-chip">Position
                                                                {{ $video->position ?? $index + 1 }}</span>
                                                            <span class="d-demo-chip">YouTube review</span>
                                                            <span class="d-demo-chip">{{ $demoReviewCount }} live</span>
                                                        </div>
                                                        <a class="d-demo-play" href="{{ $video->watch_url }}"
                                                            target="_blank" rel="noopener">
                                                            <span>▶</span> Watch on YouTube
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="d-demo-thumb">
                                                    <iframe src="{{ $video->embed_url }}&enablejsapi=1"
                                                        title="{{ $video->title ?: 'Demo Review Video' }}" loading="lazy"
                                                        referrerpolicy="strict-origin-when-cross-origin"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                        allowfullscreen data-demo-youtube-embed></iframe>
                                                </div>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="d-video-slide active" data-demo-video-slide aria-hidden="false">
                                            <div class="d-demo-video">
                                                <div class="d-demo-cover">
                                                    <span class="d-demo-badge">Reviews</span>
                                                    <h3>Review Videos Coming Soon</h3>
                                                    <p>Once admin adds YouTube review videos, they'll appear here in this
                                                        slider.</p>
                                                    <div class="d-demo-chips">
                                                        <span class="d-demo-chip">0 reviews live</span>
                                                        <span class="d-demo-chip">YouTube slider ready</span>
                                                    </div>
                                                </div>
                                                <div class="d-demo-thumb">
                                                    <div class="d-video-empty">DEMO REVIEWS</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @if ($demoReviewCount > 1)
                                <div class="d-video-nav">
                                    <div class="d-video-nav-group">
                                        <button class="d-video-arrow" type="button" data-demo-video-prev
                                            aria-label="Previous">&#8249;</button>
                                        <button class="d-video-arrow" type="button" data-demo-video-next
                                            aria-label="Next">&#8250;</button>
                                    </div>
                                    <div class="d-video-dots">
                                        @foreach ($demoReviewVideos as $index => $video)
                                            <button class="d-video-dot {{ $index === 0 ? 'active' : '' }}" type="button"
                                                data-demo-video-dot="{{ $index }}"
                                                aria-label="Review {{ $index + 1 }}"
                                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
                                        @endforeach
                                    </div>
                                    <div class="d-video-counter" data-demo-video-counter>1 / {{ $demoReviewCount }}</div>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- ── ADMIN / SUPERADMIN ───────────────────────────────────── --}}
                @elseif (in_array($user->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true))
                    <section class="d-admin-hero">
                        <h3>Admin Panel</h3>
                        <p>{{ $panelDescription }}</p>
                    </section>

                    <div class="d-stats-grid">
                        @foreach ($overviewCards as $i => $card)
                            @php
                                $icons = ['g', 't', 'o', ''];
                                $cls = $icons[$i % 4] ?? '';
                            @endphp
                            <div class="d-stat">
                                <div class="d-stat-icon {{ $cls }}">{{ $card['code'] }}</div>
                                <div>
                                    <b>{{ $card['value'] }}{{ $card['suffix'] ?? '' }}</b>
                                    <span>{{ $card['label'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @endif

                {{-- ── SHARED (non-demo) ────────────────────────────────────── --}}
                @if ($dashboardMode !== 'demo')

                    @if ($isStudent)
                        <div class="d-student-cols">
                            {{-- ── MAIN COLUMN ─────────────────────── --}}
                            <div class="d-student-main">

                                {{-- Resume panel --}}
                                <article class="d-resume">
                                    @if (!empty($studentResumeItem))
                                        <div class="d-resume-head">
                                            <div>
                                                <div class="d-continue-cta" style="margin-bottom:10px;">↩ Resume Last
                                                    Lesson</div>
                                                <h2 class="d-resume-title">{{ $studentResumeItem['item_title'] }}</h2>
                                                <p class="d-resume-note">
                                                    {{ $studentResumeItem['item_type'] }} in
                                                    {{ $studentResumeItem['course_title'] }}.
                                                    Jump back in exactly where you stopped.
                                                </p>
                                            </div>
                                            <a class="d-hero-btn" href="{{ $studentResumeItem['route'] }}">Resume Now
                                                →</a>
                                        </div>
                                        <div style="display:flex;flex-wrap:wrap;gap:7px;align-items:center;">
                                            <span class="d-pill">{{ $studentResumeItem['course_title'] }}</span>
                                            <span class="d-pill d-pill-soft">{{ $studentResumeItem['item_type'] }}</span>
                                            @if (($studentResumeItem['pending_tasks_count'] ?? 0) > 0)
                                                <span
                                                    class="d-pill d-pill-gold">{{ $studentResumeItem['pending_tasks_count'] }}
                                                    task{{ $studentResumeItem['pending_tasks_count'] === 1 ? '' : 's' }}
                                                    pending</span>
                                            @endif
                                            @if (($studentResumeItem['live_quizzes_count'] ?? 0) > 0)
                                                <span class="d-pill">{{ $studentResumeItem['live_quizzes_count'] }} live
                                                    quiz{{ $studentResumeItem['live_quizzes_count'] === 1 ? '' : 'zes' }}</span>
                                            @endif
                                        </div>
                                        <div class="d-resume-stats">
                                            <div class="d-resume-stat"><span>Course
                                                    Progress</span><strong>{{ $studentResumeItem['progress_percent'] ?? 0 }}%</strong>
                                            </div>
                                            <div class="d-resume-stat"><span>Hours
                                                    Done</span><strong>{{ $studentResumeItem['hours_done'] ?? 0 }}h</strong>
                                            </div>
                                            <div class="d-resume-stat"><span>Hours
                                                    Total</span><strong>{{ $studentResumeItem['hours_total'] ?? 0 }}h</strong>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <div class="d-continue-cta" style="margin-bottom:10px;">↩ Resume Last Lesson
                                            </div>
                                            <h2 class="d-resume-title">Your learning space is ready</h2>
                                            <p class="d-resume-note">Once you enroll in courses and open lessons, your
                                                exact resume link will appear here automatically.</p>
                                            <a class="d-hero-btn" href="{{ route('student.courses') }}"
                                                style="margin-top:14px;display:inline-flex;">Open My Courses →</a>
                                        </div>
                                    @endif
                                </article>

                                {{-- Learning Hub label + courses --}}
                                <div class="d-col-group">
                                    <span class="d-col-label">Learning Hub</span>

                    @endif

                    {{-- ── LEARNING GRID ──────────────────────────────────────── --}}
                    <section class="d-card">
                        <div class="d-section-head">
                            <div>
                                <h2>{{ $learningTitle }}</h2>
                                <p>{{ $learningSubtitle }}</p>
                            </div>
                            <a class="d-section-link" href="{{ $allCoursesRoute }}">{{ $learningActionLabel }}</a>
                        </div>
                        <div class="d-learning-grid">
                            @forelse ($learningItems as $index => $item)
                                @php
                                    $itemRoute = !empty($item['course_id'])
                                        ? ($isStudent
                                            ? $item['resume_route'] ?? route('student.courses.show', $item['course_id'])
                                            : route('courses.show', $item['course_id']))
                                        : $allCoursesRoute;
                                    $isAssigned =
                                        !$isTrainer ||
                                        in_array($item['course_id'] ?? 0, $assignedCourseIds ?? [], true);
                                    $thumb = $item['thumbnail_url'] ?? '';
                                    $topClass = $thumb ? '' : $accentClass[$item['accent']] ?? 'accent-blue';
                                @endphp
                                @if ($isTrainer && !$isAssigned)
                                    <article class="d-course-card disabled">
                                    @else
                                        <a href="{{ $itemRoute }}" class="d-course-card">
                                @endif
                                <div class="d-course-top {{ $topClass }}"
                                    @if ($thumb) style="background-image:url('{{ $thumb }}')" @endif>
                                    <div class="d-icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                                    <span class="d-badge">{{ min(100, (int) $item['progress_percent']) }}%</span>
                                    @if ($isTrainer && !$isAssigned)
                                        <span class="d-course-lock">🔒 Locked</span>
                                    @endif
                                </div>
                                <div class="d-course-body">
                                    <span class="d-pill">{{ $item['category'] }}</span>
                                    <h3>{{ $item['title'] }}</h3>
                                    <p class="d-course-meta">{{ $item['provider'] }}</p>
                                    <div class="d-bar-track">
                                        <div class="d-bar-val {{ $accentClass[$item['accent']] ?? 'accent-blue' }}"
                                            style="width:{{ min(100, (int) $item['progress_percent']) }}%"></div>
                                    </div>
                                    <div class="d-course-foot">
                                        <span>{{ $item['hours_done'] }}h / {{ $item['hours_total'] }}h</span>
                                        <span>{{ $item['progress_percent'] }}%</span>
                                    </div>
                                    @if ($isStudent)
                                        <span class="d-continue-cta">Continue →</span>
                                    @endif
                                </div>
                                @if ($isTrainer && !$isAssigned)
                                    </article>
                                @else
                                    </a>
                                @endif
                            @empty
                                <article class="d-course-card">
                                    <div class="d-course-body">
                                        <h3>No learning data yet</h3>
                                        <p class="d-course-meta">No assigned or enrolled courses found.</p>
                                    </div>
                                </article>
                            @endforelse
                        </div>
                    </section>

                    @if ($isStudent)
                        {{-- My Submissions --}}
                        <section class="d-card">
                            <div class="d-section-head">
                                <div>
                                    <h2>My Submissions</h2>
                                    <p>Your latest task and quiz submissions with quick access to the lesson.</p>
                                </div>
                                <a class="d-section-link" href="{{ route('student.history') }}">Open history →</a>
                            </div>
                            <div class="d-submission-grid" style="margin-top:14px;">
                                @forelse ($studentRecentSubmissions as $submission)
                                    <article class="d-submission d-submission--{{ $submission['status_tone'] }}">
                                        <div class="d-submission-head">
                                            <div>
                                                <strong>{{ $submission['title'] }}</strong>
                                                <div class="d-submission-meta">{{ $submission['course_title'] }} &middot;
                                                    {{ $submission['submitted_at_human'] ?: 'Recently submitted' }}</div>
                                            </div>
                                            <div class="d-sub-badges">
                                                <span class="d-status-badge">{{ $submission['submission_type'] }}</span>
                                                <span class="d-status-badge">{{ $submission['status_label'] }}</span>
                                            </div>
                                        </div>
                                        @if (!empty($submission['answer_text']))
                                            <div class="d-answer-box">
                                                <h4>Answer</h4>
                                                <div class="d-answer-text">
                                                    {{ \Illuminate\Support\Str::limit($submission['answer_text'], 180) }}
                                                </div>
                                            </div>
                                        @endif
                                        @if (!empty($submission['file_name']))
                                            <div class="d-doc-box"
                                                style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;">
                                                <span
                                                    style="color:var(--text2);font-size:13px;">{{ $submission['file_name'] }}</span>
                                                @if (!empty($submission['download_route']))
                                                    <a class="d-btn d-btn-ghost d-btn-sm"
                                                        href="{{ $submission['download_route'] }}">Download</a>
                                                @endif
                                            </div>
                                        @endif
                                        @if (!empty($submission['review_notes']))
                                            <div class="d-answer-box">
                                                <h4>Review Notes</h4>
                                                <div class="d-answer-text">
                                                    {{ \Illuminate\Support\Str::limit($submission['review_notes'], 180) }}
                                                </div>
                                            </div>
                                        @endif
                                        <div style="display:flex;gap:7px;flex-wrap:wrap;">
                                            <a class="d-btn d-btn-ghost d-btn-sm"
                                                href="{{ $submission['open_route'] }}">Open Lesson</a>
                                        </div>
                                    </article>
                                @empty
                                    <div class="d-sub-empty">No submissions yet. When you submit a task or quiz, it will
                                        appear here.</div>
                                @endforelse
                            </div>
                        </section>

                        {{-- Certificates --}}
                        <section class="d-card">
                            <div class="d-section-head">
                                <div>
                                    <h2>Certificates</h2>
                                    <p>Download PDF or SVG certificates for fully completed courses.</p>
                                </div>
                                <a class="d-section-link" href="{{ route('student.certificates') }}">View all →</a>
                            </div>
                            <div class="d-cert-grid" style="margin-top:14px;">
                                @forelse ($studentCertificates as $certificate)
                                    <article class="d-cert">
                                        <div class="d-cert-top">
                                            <span class="d-pill">{{ $certificate['category'] }}</span>
                                            <span class="d-cert-code">{{ $certificate['certificate_code'] }}</span>
                                        </div>
                                        <div>
                                            <h4>{{ $certificate['course_title'] }}</h4>
                                            <p class="d-cert-meta">
                                                Issued {{ $certificate['issued_at_human'] }}
                                                &middot; {{ $certificate['hours_total'] }}h
                                                &middot; {{ $certificate['trainer_name'] }}
                                            </p>
                                        </div>
                                        <div class="d-cert-actions">
                                            <a class="d-btn d-btn-ghost d-btn-sm"
                                                href="{{ $certificate['course_route'] }}">Open Course</a>
                                            <a class="d-btn d-btn-primary d-btn-sm"
                                                href="{{ $certificate['download_pdf_route'] }}">PDF</a>
                                            <a class="d-btn d-btn-ghost d-btn-sm"
                                                href="{{ $certificate['download_svg_route'] }}">SVG</a>
                                        </div>
                                    </article>
                                @empty
                                    <div class="d-sub-empty">Complete a course to unlock your first certificate.</div>
                                @endforelse
                            </div>
                        </section>

            </div>{{-- /Learning Hub col-group --}}

            {{-- Discover More --}}
            <div class="d-col-group">
                <span class="d-col-label">Discover More</span>
                <section class="d-card">
                    <div class="d-section-head">
                        <div>
                            <h2>Recommended Courses</h2>
                            <p>Available courses from your LMS catalog.</p>
                        </div>
                        <a class="d-section-link" href="{{ route('courses.index') }}">Browse all →</a>
                    </div>
                    <div class="d-recommend-grid" style="margin-top:14px;">
                        @forelse ($recommendedCourses as $index => $course)
                            @php $tone = array_keys($accentClass)[$index % count($accentClass)]; @endphp
                            <article class="d-recommend">
                                <div class="d-recommend-top {{ $accentClass[$tone] }}">
                                    <div class="d-icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                                </div>
                                <div class="d-recommend-body">
                                    <span class="d-pill">{{ $course['category'] }}</span>
                                    <h4>{{ $course['title'] }}</h4>
                                    <p class="d-recommend-meta">By {{ $course['provider'] }}</p>
                                    <div class="d-recommend-foot">
                                        <span>{{ $course['hours'] }}h total</span>
                                        <a class="d-mini-btn" href="{{ route('courses.show', $course['id']) }}">View
                                            →</a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <article class="d-recommend">
                                <div class="d-recommend-body">
                                    <h4>No courses available</h4>
                                </div>
                            </article>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>{{-- /d-student-main --}}

        {{-- ── SIDE COLUMN ──────────────────────── --}}
        <aside class="d-student-side">

            {{-- Pending actions queue --}}
            <article class="d-queue">
                <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:10px;">
                    <div>
                        <h3 style="margin:0;font-size:20px;font-weight:700;color:var(--text);">Pending Tasks &amp; Live
                            Quizzes</h3>
                        <p style="margin:5px 0 0;color:var(--text3);font-size:13px;">Your most important actions at a
                            glance.</p>
                    </div>
                    <a class="d-section-link" href="{{ route('student.courses') }}">Open courses →</a>
                </div>
                <div class="d-queue-summary">
                    <div class="d-queue-box"><span>Pending
                            Tasks</span><strong>{{ $studentPendingActionSummary['tasks'] ?? 0 }}</strong></div>
                    <div class="d-queue-box"><span>Live
                            Quizzes</span><strong>{{ $studentPendingActionSummary['live_quizzes'] ?? 0 }}</strong></div>
                    <div class="d-queue-box"><span>Total
                            Actions</span><strong>{{ $studentPendingActionSummary['total'] ?? 0 }}</strong></div>
                </div>
                <div class="d-queue-list">
                    @forelse ($studentPendingActionItems as $actionItem)
                        <a href="{{ $actionItem['route'] }}" class="d-queue-item">
                            <div class="d-queue-item-top">
                                <strong>{{ $actionItem['item_title'] }}</strong>
                                <span
                                    class="d-queue-tag {{ $actionItem['item_type'] === \App\Models\CourseSessionItem::TYPE_QUIZ ? 'd-queue-tag--quiz' : 'd-queue-tag--task' }}">
                                    {{ $actionItem['item_type_label'] }}
                                </span>
                            </div>
                            <p>{{ $actionItem['course_title'] }}</p>
                            <div class="d-queue-meta">
                                Week {{ $actionItem['week_number'] ?: '-' }} / Session
                                {{ $actionItem['session_number'] ?: '-' }}
                                &middot; {{ $actionItem['status_label'] }}
                            </div>
                        </a>
                    @empty
                        <div class="d-sub-empty">No pending tasks or live quizzes. Nicely caught up! ✓</div>
                    @endforelse
                </div>
            </article>

            {{-- Action Center --}}
            <div class="d-col-group">
                <span class="d-col-label">Action Center</span>

                <section class="d-card d-card-sm">
                    <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Quick Actions</h3>
                    <p style="margin:0 0 14px;color:var(--text3);font-size:13px;">Most-used student shortcuts in one place.
                    </p>
                    <div class="d-qa-grid">
                        @foreach ($quickActions as $action)
                            <a class="d-qa-link" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
                        @endforeach
                    </div>
                </section>

                @if ($notifications->isNotEmpty())
                    <section class="d-card d-card-sm">
                        <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Notifications</h3>
                        @include('dashboard-notification-feed-v2', ['notifications' => $notifications])
                    </section>
                @endif

                <article class="d-card d-card-sm">
                    <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Skill Progress</h3>
                    @forelse ($skillProgress as $index => $skill)
                        <div class="d-skill-row">
                            <div class="d-skill-label">
                                <span>{{ $skill['skill'] }}</span>
                                <span style="color:var(--brand-300);font-weight:700;">{{ $skill['progress'] }}%</span>
                            </div>
                            <div class="d-bar-track">
                                <div class="d-bar-val {{ $accentClass[array_keys($accentClass)[$index % count($accentClass)]] }}"
                                    style="width:{{ $skill['progress'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="d-muted" style="margin:0;font-size:13px;">No skill progress available.</p>
                    @endforelse
                </article>

                <article class="d-card d-card-sm">
                    <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Browse by Topic</h3>
                    <div class="d-topic-grid">
                        @forelse ($topics as $topic)
                            <a href="{{ route('courses.index') }}" class="d-topic">
                                <div class="d-topic-bullet">{{ strtoupper(substr($topic['name'], 0, 2)) }}</div>
                                <div>
                                    <strong>{{ $topic['name'] }}</strong>
                                    <p>{{ number_format($topic['count']) }} courses</p>
                                </div>
                            </a>
                        @empty
                            <p class="d-muted" style="margin:0;font-size:13px;">No topics found.</p>
                        @endforelse
                    </div>
                </article>
            </div>
        </aside>
    </div>{{-- /d-student-cols --}}
@else
    {{-- ── TRAINER / ADMIN non-student layout ── --}}
    <section class="d-card">
        <h2 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Quick Actions</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach ($quickActions as $action)
                <a class="d-btn d-btn-ghost" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
            @endforeach
        </div>
    </section>

    @if ($notifications->isNotEmpty())
        <section class="d-card">
            <div class="d-section-head" style="margin-bottom:16px;">
                <div>
                    <h2>Notifications</h2>
                    <p>Latest LMS updates for your account.</p>
                </div>
            </div>
            @include('dashboard-notification-feed-v2', ['notifications' => $notifications])
        </section>
    @endif

    <div class="d-split">
        <article class="d-panel-box">
            <h3>Skill Progress</h3>
            @forelse ($skillProgress as $index => $skill)
                <div class="d-skill-row">
                    <div class="d-skill-label">
                        <span>{{ $skill['skill'] }}</span>
                        <span style="color:var(--brand-300);font-weight:700;">{{ $skill['progress'] }}%</span>
                    </div>
                    <div class="d-bar-track">
                        <div class="d-bar-val {{ $accentClass[array_keys($accentClass)[$index % count($accentClass)]] }}"
                            style="width:{{ $skill['progress'] }}%"></div>
                    </div>
                </div>
            @empty
                <p class="d-muted" style="margin:0;font-size:13px;">No skill progress available.</p>
            @endforelse
        </article>

        <article class="d-panel-box">
            <h3>Browse by Topic</h3>
            <div class="d-topic-grid">
                @forelse ($topics as $topic)
                    <a href="{{ route('courses.index') }}" class="d-topic">
                        <div class="d-topic-bullet">{{ strtoupper(substr($topic['name'], 0, 2)) }}</div>
                        <div>
                            <strong>{{ $topic['name'] }}</strong>
                            <p>{{ number_format($topic['count']) }} courses</p>
                        </div>
                    </a>
                @empty
                    <p class="d-muted" style="margin:0;font-size:13px;">No topics found.</p>
                @endforelse
            </div>
        </article>
    </div>

    <section>
        <div class="d-section-head" style="margin-bottom:14px;">
            <div>
                <h2>Recommended Courses</h2>
                <p>Available courses from your LMS catalog.</p>
            </div>
            <a class="d-section-link" href="{{ route('courses.index') }}">Browse all →</a>
        </div>
        <div class="d-recommend-grid">
            @forelse ($recommendedCourses as $index => $course)
                @php $tone = array_keys($accentClass)[$index % count($accentClass)]; @endphp
                <article class="d-recommend">
                    <div class="d-recommend-top {{ $accentClass[$tone] }}">
                        <div class="d-icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                    </div>
                    <div class="d-recommend-body">
                        <span class="d-pill">{{ $course['category'] }}</span>
                        <h4>{{ $course['title'] }}</h4>
                        <p class="d-recommend-meta">By {{ $course['provider'] }}</p>
                        <div class="d-recommend-foot">
                            <span>{{ $course['hours'] }}h total</span>
                            <a class="d-mini-btn" href="{{ route('courses.show', $course['id']) }}">View →</a>
                        </div>
                    </div>
                </article>
            @empty
                <article class="d-recommend">
                    <div class="d-recommend-body">
                        <h4>No courses available</h4>
                    </div>
                </article>
            @endforelse
        </div>
    </section>
    @endif

    @endif {{-- end non-demo --}}

    </div>{{-- /d-grid --}}
    </div>{{-- /d-page --}}
    </div>{{-- /d-root --}}

    @if ($dashboardMode === 'demo')
        <script src="{{ asset('js/student-courses.js') }}" defer></script>
    @endif

@endsection

   

