{{-- dashboard/partials/_learning-grid.blade.php
     Required vars: $learningItems, $learningTitle, $learningSubtitle, $learningActionLabel,
                    $allCoursesRoute, $accentClass, $courseIcons,
                    $isStudent, $isTrainer, $assignedCourseIds --}}

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
                $isAssigned = !$isTrainer
                    || in_array($item['course_id'] ?? 0, $assignedCourseIds, true);

                $itemRoute = !empty($item['course_id'])
                    ? ($isStudent
                        ? ($item['resume_route'] ?? route('student.courses.show', $item['course_id']))
                        : route('courses.show', $item['course_id']))
                    : $allCoursesRoute;

                $thumb    = $item['thumbnail_url'] ?? '';
                $topClass = $thumb ? '' : ($accentClass[$item['accent']] ?? 'accent-blue');
                $progress = min(100, (int) $item['progress_percent']);
            @endphp

            @if ($isTrainer && !$isAssigned)
                <article class="d-course-card disabled">
            @else
                <a href="{{ $itemRoute }}" class="d-course-card">
            @endif

                <div
                    class="d-course-top {{ $topClass }}"
                    @if ($thumb) style="background-image:url('{{ $thumb }}')" @endif
                >
                    <div class="d-icon-box">{{ $courseIcons[$index % count($courseIcons)] }}</div>
                    <span class="d-badge">{{ $progress }}%</span>
                    @if ($isTrainer && !$isAssigned)
                        <span class="d-course-lock">🔒 Locked</span>
                    @endif
                </div>

                <div class="d-course-body">
                    <span class="d-pill">{{ $item['category'] }}</span>
                    <h3>{{ $item['title'] }}</h3>
                    <p class="d-course-meta">{{ $item['provider'] }}</p>
                    <div class="d-bar-track">
                        <div
                            class="d-bar-val {{ $accentClass[$item['accent']] ?? 'accent-blue' }}"
                            style="width:{{ $progress }}%"
                        ></div>
                    </div>
                    <div class="d-course-foot">
                        <span>{{ $item['hours_done'] }}h / {{ $item['hours_total'] }}h</span>
                        <span>{{ $progress }}%</span>
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