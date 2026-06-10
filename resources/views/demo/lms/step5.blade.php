@extends('demo.layout')


@section('title', 'Explore More Courses')


@section('bitmoji-message', '🌟 You can be the next success story! Explore more courses and keep your momentum going!')
@section('bitmoji-emoji', '🚀')

@section('content')

{{-- ── Banner ── --}}
<div class="explore-banner">
    <div class="banner-icon">🗺️</div>
    <div class="banner-text">
        <div class="step-badge" style="margin-bottom:10px">
            <div class="dot-pulse"></div>
            Step 5 of 5 — Keep Growing!
        </div>
        <h1>Recommended <em>Next Paths</em></h1>
        <p>Based on your interest in <strong>{{ session('lms_course_label', 'your course') }}</strong> — here's what top learners explore next.</p>
    </div>
</div>

{{-- ── Course Recommendations ── --}}
<div class="section-header">
    <h2>📚 Next Level Courses</h2>
    <a href="#" class="see-all">View All →</a>
</div>
<div class="courses-grid">
    @forelse($courses as $c)
        <div class="course-card">

            {{-- thumbnail: uses DB column, falls back to emoji from category --}}
            <div class="course-thumb"
                 style="background: linear-gradient(135deg,#1E1B3A,#2D1B69)">
                @if($c->thumbnail)
                    <img src="{{ Storage::url($c->thumbnail) }}"
                         alt="{{ $c->title }}"
                         style="width:100%;height:100%;object-fit:cover">
                @else
                    {{ $c->category->emoji ?? '📚' }}
                @endif
            </div>

            <div class="course-body">
                <div class="course-tag"
                     style="color:{{ $c->category->tag_color ?? '#A78BFA' }}">
                    {{ $c->category->name ?? 'Course' }}
                </div>

                <div class="course-name">
                    {{ $c->title }}
                    @if($c->is_popular ?? false)
                        <span class="badge-popular">🔥 Popular</span>
                    @endif
                </div>

                <div class="course-meta">
                    <div class="star-row">
                        @for($i = 0; $i < 5; $i++)
                            <span class="star">★</span>
                        @endfor
                        <span style="margin-left:4px">
                            {{ number_format($c->rating ?? 4.5, 1) }}
                        </span>
                    </div>
                    <span>👥 {{ number_format($c->total_students ?? 0) }}</span>
                    <span>⏱ {{ $c->duration_hours ?? 0 }} hrs</span>
                </div>

               {{-- <a href="{{ route('lms.course.show', $c->slug) }}"
                   class="btn-enroll">
                    Enroll Now →
                </a>  --}}
            </div>
        </div>

    @empty
        {{-- fallback: no courses found for this interest --}}
        <div style="grid-column:1/-1; text-align:center;
                    padding:40px 20px; color:var(--text-muted)">
            <div style="font-size:2rem; margin-bottom:10px">🔍</div>
            <p>No courses found for your selected interest.</p>
            <a href="{{ route('lms.step1') }}" class="btn-secondary"
               style="display:inline-flex; margin-top:14px">
                ← Change Interest
            </a>
        </div>
    @endforelse
</div>


{{-- ── Student Demo Gallery ── --}}
{{-- <div class="social-section">
    <div class="section-header">
        <h2>👥 What Other Learners Created</h2>
        <a href="#" class="see-all">See All Projects →</a>
    </div>
    <div class="student-demos">
        @php
            $demos = [
                ['emoji'=>'💻','name'=>'Rahul M.','course'=>'Web Dev','color'=>'#2D1B69'],
                ['emoji'=>'📊','name'=>'Priya S.','course'=>'Marketing','color'=>'#3B1A1A'],
                ['emoji'=>'🎨','name'=>'Aryan K.','course'=>'UI Design','color'=>'#1A1B3B'],
                ['emoji'=>'🚀','name'=>'Sneha T.','course'=>'Freelancing','color'=>'#1A2B1A'],
                ['emoji'=>'📱','name'=>'Dev R.','course'=>'Web Dev','color'=>'#1B2B3B'],
                ['emoji'=>'🎯','name'=>'Nisha P.','course'=>'Marketing','color'=>'#2B1B2B'],
            ];
        @endphp
        @foreach($demos as $d)
            <div>
                <div class="demo-thumb">
                    <div class="demo-thumb-bg" style="background:linear-gradient(160deg,{{ $d['color'] }},#0D0B1F)">{{ $d['emoji'] }}</div>
                    <div class="demo-play-overlay">
                        <div class="play-circle"><i class="fas fa-play"></i></div>
                    </div>
                </div>
                <div class="demo-info">
                    <div class="demo-name">{{ $d['name'] }}</div>
                    <div class="demo-course">{{ $d['course'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div> --}}

{{-- ── Testimonials ── --}}
<div class="section-header" style="margin-top:24px">
    <h2>💬 Student Reviews</h2>
</div>

<div class="testimonials">
    @forelse($reviews as $r)
        <div class="testimonial">
            <div class="t-avatar" style="background:rgba(108,63,245,0.1)">
                {{ mb_strtoupper(mb_substr($r->user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <p class="t-quote">"{{ $r->message }}"</p>
                <div class="t-name">{{ $r->user->name ?? 'Anonymous' }}</div>
                <div class="t-course">
                    {{ $r->course->name ?? 'General' }}
                    <span style="margin-left:6px">{{ $r->emoji_reaction }}</span>
                    @if($r->rating)
                        &nbsp;⭐ {{ $r->rating }}/5
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p style="color:var(--text-muted); font-size:.88rem; text-align:center; padding:24px 0">
            No reviews yet — be the first to share your experience!
        </p>
    @endforelse
</div>

{{-- ── Final CTA ── --}}
<div class="cta-strip">
    <div style="font-size:2.5rem; margin-bottom:12px">🏆</div>
    <h2>You Can Be the <em>Next Success Story!</em></h2>
    <p>Join thousands of learners who transformed their skills into real careers and income.</p>
    <div class="btn-group" style="justify-content:center">
        <a href="#" class="btn-primary" style="width:auto">
            <i class="fas fa-play-circle"></i> Continue Learning
        </a>
        <a href="#" class="btn-secondary" style="width:auto">
            <i class="fas fa-trophy"></i> View More Courses
        </a>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Show bitmoji on load
    setTimeout(() => {
        document.getElementById('bitmojiMsg').style.display = 'block';
    }, 1200);

    // Course card hover: update bitmoji
    document.querySelectorAll('.course-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            const name = card.querySelector('.course-name').textContent.trim().replace('🔥 Popular','');
            const el = document.getElementById('bitmojiMsg');
            el.textContent = `👀 "${name}" — great next step for you!`;
            el.style.display = 'block';
        });
    });
</script>
@endsection