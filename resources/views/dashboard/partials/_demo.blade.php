{{-- dashboard/partials/_demo.blade.php --}}

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
@include('dashboard.partials._video-slider', [
    'videos'      => $demoFeatureVideos,
    'type'        => 'feature',
    'badgePrefix' => 'Feature Video',
    'emptyLabel'  => 'FEATURE VIDEO',
    'emptyTitle'  => 'Videos Coming Soon',
    'emptyDesc'   => 'Feature videos added here will appear automatically for demo users.',
])

{{-- Demo Practice Tasks --}}
<section class="d-card">
    <div class="d-section-head" style="margin-bottom:20px;">
        <div>
            <h2>Demo Practice Tasks</h2>
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
                $task       = $row['task'];
                $submission = $row['submission'];
                $assignment = $row['assignment'];
                $canDownload = !empty($task?->resource_file_path);
                $hasTools    = !empty($task?->ai_video_url);
            @endphp

            <div class="d-demo-task">

                @if (!empty($task?->task_video_path))
                    <div class="d-task-video">
                        <div class="d-task-video-note">
                            📹 Watch this short guide first, then complete the practice task below.
                        </div>
                        <video controls preload="metadata" controlslist="nodownload" playsinline>
                            <source
                                src="{{ route('demo-tasks.video', $task) }}"
                                type="{{ $task->task_video_mime ?: 'video/mp4' }}"
                            >
                        </video>
                    </div>
                @endif

                <strong>{{ $task?->title ?? 'Practice Assignment' }}</strong>

                <div class="d-demo-task-meta">
                    {{ $task?->description ?? 'Complete this practice activity to learn how assignment submissions, document uploads, and learning resources work within the platform.' }}
                </div>

                @if ($canDownload || $hasTools)
                    <div class="d-task-actions">
                        @if ($canDownload)
                            <a class="d-btn d-btn-ghost d-btn-sm" href="{{ route('demo-tasks.download', $task) }}">
                                📥 Download Resource
                            </a>
                        @endif
                        @if ($hasTools)
                            <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $task->ai_video_url }}" target="_blank" rel="noopener">
                                🛠 Learning Tools
                            </a>
                        @endif
                    </div>
                @endif

                @if ($assignment)
                    <form
                        method="POST"
                        action="{{ route('demo-assignments.submit', $assignment) }}"
                        enctype="multipart/form-data"
                        class="d-submit-panel"
                    >
                        @csrf

                        <div class="d-submit-block">
                            <h4>✍️ Your Answer</h4>
                            <textarea
                                name="answer_text"
                                rows="4"
                                placeholder="Write your answer, explanation, observations, or solution here..."
                            ></textarea>
                            <div class="d-muted" style="font-size:12px;">
                                Submit your response just like you would for a real assignment.
                            </div>
                        </div>

                        <div class="d-submit-block">
                            <h4>📎 Upload Supporting File</h4>
                            <input type="file" name="submission_file" accept="*/*">
                            <div class="d-muted" style="font-size:12px;">
                                Accepted formats: PDF, DOCX, PPT, ZIP, Images, Videos and other supporting files.
                            </div>
                        </div>

                        <div class="d-task-actions">
                            <button class="d-btn d-btn-primary d-btn-sm" type="submit">
                                🚀 Submit Practice Task
                            </button>
                            @if ($submission?->file_path)
                                <a class="d-btn d-btn-ghost d-btn-sm" href="{{ route('demo-tasks.submissions.download', $submission) }}">
                                    📥 Download Your File
                                </a>
                            @endif
                        </div>

                        @if ($submission)
                            <div class="d-submit-preview">
                                <strong>✅ Your Latest Submission</strong>
                                <p>{{ $submission->answer_text ?: 'No written answer submitted yet.' }}</p>
                                <div class="d-submit-file">
                                    <span>{{ $submission->file_name ?: 'No file uploaded' }}</span>
                                    @if ($submission->file_path)
                                        <a class="d-btn d-btn-ghost d-btn-sm"
                                            href="{{ route('demo-tasks.submissions.download', $submission) }}">
                                            Download
                                        </a>
                                    @endif
                                </div>
                                <span class="d-muted" style="font-size:12px;">
                                    Submitted {{ optional($submission->submitted_at)->diffForHumans() }}
                                </span>
                            </div>
                        @endif
                    </form>
                @else
                    <div class="d-sub-empty">
                        <strong>📚 Practice Resource Available</strong>
                        <p style="margin-top:10px;">
                            This task is currently available for learning and exploration.
                            @if ($canDownload)
                                Download the provided resource to understand the assignment structure and workflow.
                            @endif
                            Submission functionality will become available once this task has been assigned to your account.
                        </p>
                    </div>
                @endif

            </div>
        @empty
            <div class="d-sub-empty">
                <strong>🎯 No Practice Tasks Available Yet</strong>
                <p style="margin-top:10px;">
                    Demo tasks will appear here once they are published by the administrator. Please check back later.
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
            <button
                class="d-tab-btn tab-btn main-tab {{ $index === 0 ? 'active' : '' }}"
                type="button"
                data-tab="{{ $category->id }}"
            >
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

        <div class="d-tab-panel {{ $index === 0 ? 'active' : '' }}" data-tab-panel="{{ $category->id }}">
            <div class="d-subtab-label">Subcategories</div>
            <div class="d-subtab-row" data-subtabs>
                <button class="d-subtab-btn active" type="button" data-subtab="all">All</button>
                @foreach ($category->children as $child)
                    <button class="d-subtab-btn" type="button" data-subtab="{{ $child->id }}">
                        {{ $child->name }}
                    </button>
                @endforeach
            </div>

            <div class="d-course-tile-grid">
                @forelse ($tabCourses as $course)
                    @php
                        $thumb   = $course->thumbnail_url ?: '';
                        $bg      = $thumb ? "url('{$thumb}')" : 'linear-gradient(120deg,#1c5fca,#3aa77a)';
                        $catName = $course->subcategory?->name ?? ($course->category?->name ?? $category->name);
                        $subId   = $course->subcategory?->id ? (string) $course->subcategory->id : 'none';
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
@include('dashboard.partials._video-slider', [
    'videos'      => $demoReviewVideos,
    'type'        => 'review',
    'badgePrefix' => 'Review',
    'emptyLabel'  => 'DEMO REVIEWS',
    'emptyTitle'  => 'Review Videos Coming Soon',
    'emptyDesc'   => 'Once admin adds YouTube review videos, they\'ll appear here in this slider.',
])