{{-- dashboard/partials/_student.blade.php --}}
@php $isStudent = true; $isTrainer = false; @endphp

<div class="d-student-cols">

    {{-- ── MAIN COLUMN ──────────────────────────────────────────── --}}
    <div class="d-student-main">

        {{-- Resume panel --}}
        <article class="d-resume">
            @if (!empty($studentResumeItem))
                <div class="d-resume-head">
                    <div>
                        <div class="d-continue-cta" style="margin-bottom:10px;">↩ Resume Last Lesson</div>
                        <h2 class="d-resume-title">{{ $studentResumeItem['item_title'] }}</h2>
                        <p class="d-resume-note">
                            {{ $studentResumeItem['item_type'] }} in {{ $studentResumeItem['course_title'] }}.
                            Jump back in exactly where you stopped.
                        </p>
                    </div>
                    <a class="d-hero-btn" href="{{ $studentResumeItem['route'] }}">Resume Now →</a>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:7px;align-items:center;">
                    <span class="d-pill">{{ $studentResumeItem['course_title'] }}</span>
                    <span class="d-pill d-pill-soft">{{ $studentResumeItem['item_type'] }}</span>

                    @if (($studentResumeItem['pending_tasks_count'] ?? 0) > 0)
                        <span class="d-pill d-pill-gold">
                            {{ $studentResumeItem['pending_tasks_count'] }}
                            task{{ $studentResumeItem['pending_tasks_count'] === 1 ? '' : 's' }} pending
                        </span>
                    @endif

                    @if (($studentResumeItem['live_quizzes_count'] ?? 0) > 0)
                        <span class="d-pill">
                            {{ $studentResumeItem['live_quizzes_count'] }}
                            live quiz{{ $studentResumeItem['live_quizzes_count'] === 1 ? '' : 'zes' }}
                        </span>
                    @endif
                </div>

                <div class="d-resume-stats">
                    <div class="d-resume-stat">
                        <span>Course Progress</span>
                        <strong>{{ $studentResumeItem['progress_percent'] ?? 0 }}%</strong>
                    </div>
                    <div class="d-resume-stat">
                        <span>Hours Done</span>
                        <strong>{{ $studentResumeItem['hours_done'] ?? 0 }}h</strong>
                    </div>
                    <div class="d-resume-stat">
                        <span>Hours Total</span>
                        <strong>{{ $studentResumeItem['hours_total'] ?? 0 }}h</strong>
                    </div>
                </div>

            @else
            <div>
    <div class="d-continue-cta" style="margin-bottom:10px;">
        🎓 Welcome to Academic Mantra LMS
    </div>

    <h2 class="d-resume-title">
        Start Your Learning Journey Today
    </h2>

    <p class="d-resume-note">
        Explore industry-focused courses, learn from expert mentors, complete real-world projects, and build the skills employers are looking for. Your enrolled courses and learning progress will appear here automatically.
    </p>

    <a class="d-hero-btn" href="{{ route('student.courses') }}" style="margin-top:14px;display:inline-flex;">
        Explore Courses →
    </a>
</div>
            @endif
        </article>

        {{-- Learning Hub --}}
        <div class="d-col-group">
            {{-- <span class="d-col-label">Learning Hub</span> --}}

            {{-- @include('dashboard.partials._learning-grid', [
                'learningItems'       => $learningItems,
                'learningTitle'       => $learningTitle,
                'learningSubtitle'    => $learningSubtitle,
                'learningActionLabel' => $learningActionLabel,
                'allCoursesRoute'     => $allCoursesRoute,
                'accentClass'         => $accentClass,
                'courseIcons'         => $courseIcons,
                'isStudent'           => true,
                'isTrainer'           => false,
                'assignedCourseIds'   => $assignedCourseIds,
            ]) --}}

            {{-- My Submissions --}}
            {{-- <section class="d-card">
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
                                    <div class="d-submission-meta">
                                        {{ $submission['course_title'] }}
                                        &middot;
                                        {{ $submission['submitted_at_human'] ?: 'Recently submitted' }}
                                    </div>
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
                                <div class="d-doc-box" style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span style="color:var(--text2);font-size:13px;">{{ $submission['file_name'] }}</span>
                                    @if (!empty($submission['download_route']))
                                        <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $submission['download_route'] }}">Download</a>
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
                                <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $submission['open_route'] }}">Open Lesson</a>
                            </div>
                        </article>
                    @empty
                        <div class="d-sub-empty">
                            No submissions yet. When you submit a task or quiz, it will appear here.
                        </div>
                    @endforelse
                </div>
            </section> --}}

            {{-- Certificates --}}
            {{-- <section class="d-card">
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
                                <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $certificate['course_route'] }}">Open Course</a>
                                <a class="d-btn d-btn-primary d-btn-sm" href="{{ $certificate['download_pdf_route'] }}">PDF</a>
                                <a class="d-btn d-btn-ghost d-btn-sm" href="{{ $certificate['download_svg_route'] }}">SVG</a>
                            </div>
                        </article>
                    @empty
                        <div class="d-sub-empty">Complete a course to unlock your first certificate.</div>
                    @endforelse
                </div>
            </section> --}}
        </div>{{-- /Learning Hub col-group --}}

        {{-- Discover More --}}
        <div class="d-col-group">
            <span class="d-col-label">Discover More</span>
            <section class="d-card">
                @include('dashboard.partials._recommended-courses', [
                    'recommendedCourses' => $recommendedCourses,
                    'accentClass'        => $accentClass,
                    'courseIcons'        => $courseIcons,
                ])
            </section>
        </div>

    </div>{{-- /d-student-main --}}

    {{-- ── SIDE COLUMN ──────────────────────────────────────────── --}}
    <aside class="d-student-side">

        {{-- Pending actions queue --}}
        <article class="d-queue">
            <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:10px;">
                <div>
                    <h3 style="margin:0;font-size:20px;font-weight:700;color:var(--text);">
                        Pending Tasks &amp; Live Quizzes
                    </h3>
                    <p style="margin:5px 0 0;color:var(--text3);font-size:13px;">
                        Your most important actions at a glance.
                    </p>
                </div>
                <a class="d-section-link" href="{{ route('student.courses') }}">Open courses →</a>
            </div>

            <div class="d-queue-summary">
                <div class="d-queue-box">
                    <span>Pending Tasks</span>
                    <strong>{{ $studentPendingActionSummary['tasks'] ?? 0 }}</strong>
                </div>
                <div class="d-queue-box">
                    <span>Live Quizzes</span>
                    <strong>{{ $studentPendingActionSummary['live_quizzes'] ?? 0 }}</strong>
                </div>
                <div class="d-queue-box">
                    <span>Total Actions</span>
                    <strong>{{ $studentPendingActionSummary['total'] ?? 0 }}</strong>
                </div>
            </div>

            <div class="d-queue-list">
                @forelse ($studentPendingActionItems as $actionItem)
                    @php
                        $isQuiz = $actionItem['item_type'] === \App\Models\CourseSessionItem::TYPE_QUIZ;
                    @endphp
                    <a href="{{ $actionItem['route'] }}" class="d-queue-item">
                        <div class="d-queue-item-top">
                            <strong>{{ $actionItem['item_title'] }}</strong>
                            <span class="d-queue-tag {{ $isQuiz ? 'd-queue-tag--quiz' : 'd-queue-tag--task' }}">
                                {{ $actionItem['item_type_label'] }}
                            </span>
                        </div>
                        <p>{{ $actionItem['course_title'] }}</p>
                        <div class="d-queue-meta">
                            Week {{ $actionItem['week_number'] ?: '-' }} /
                            Session {{ $actionItem['session_number'] ?: '-' }}
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
            {{-- <span class="d-col-label">Action Center</span>

            <section class="d-card d-card-sm">
                <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Quick Actions</h3>
                <p style="margin:0 0 14px;color:var(--text3);font-size:13px;">Most-used student shortcuts in one place.</p>
                <div class="d-qa-grid">
                    @foreach ($quickActions as $action)
                        <a class="d-qa-link" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
                    @endforeach
                </div>
            </section> --}}

            {{-- @if ($notifications->isNotEmpty())
                <section class="d-card d-card-sm">
                    <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Notifications</h3>
                    @include('dashboard-notification-feed-v2', ['notifications' => $notifications])
                </section>
            @endif --}}

            @include('dashboard.partials._skills-topics', [
                'skillProgress' => $skillProgress,
                'topics'        => $topics,
                'accentClass'   => $accentClass,
                'wrapperClass'  => 'd-card d-card-sm',
            ])

        </div>
    </aside>

</div>{{-- /d-student-cols --}}