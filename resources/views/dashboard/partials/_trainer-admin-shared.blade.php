{{-- dashboard/partials/_trainer-admin-shared.blade.php --}}

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

@include('dashboard.partials._learning-grid', [
    'learningItems'       => $learningItems,
    'learningTitle'       => $learningTitle,
    'learningSubtitle'    => $learningSubtitle,
    'learningActionLabel' => $learningActionLabel,
    'allCoursesRoute'     => $allCoursesRoute,
    'accentClass'         => $accentClass,
    'courseIcons'         => $courseIcons,
    'isStudent'           => false,
    'isTrainer'           => $isTrainer,
    'assignedCourseIds'   => $assignedCourseIds,
])

<div class="d-split">
    @include('dashboard.partials._skills-topics', [
        'skillProgress' => $skillProgress,
        'topics'        => $topics,
        'accentClass'   => $accentClass,
        'wrapperClass'  => 'd-panel-box',
    ])
</div>

<section>
    @include('dashboard.partials._recommended-courses', [
        'recommendedCourses' => $recommendedCourses,
        'accentClass'        => $accentClass,
        'courseIcons'        => $courseIcons,
    ])
</section>