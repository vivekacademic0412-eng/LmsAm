{{-- dashboard/partials/_recommended-courses.blade.php
     Required vars: $recommendedCourses, $accentClass, $courseIcons --}}

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