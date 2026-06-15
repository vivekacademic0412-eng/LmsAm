@extends('demo.layout')

@section('title', 'Welcome – Start Your Journey')

{{-- @section('extra-styles') --}}

{{-- @endsection --}}

@section('bitmoji-message',
    '😊 Hi! Fill in your details below and I\'ll personalize your learning journey just for
    you!')
@section('bitmoji-emoji', '👋')

@section('content')
    <!-- TOP TRUST BAR -->


    <div class="welcome-grid">


        {{-- ── Left: Hero Panel ── --}}
        <div class="welcome-hero" style="position:relative; overflow:visible; padding-top:32px">

            {{-- ── Naina bitmoji sitting on top edge ── --}}
            <div class="bm-sitter" id="bmSitter" onclick="waveHello()" title="Click me!">
            </div>
            {{-- ── original hero content ── --}}
            <div class="bitmoji-section--2">
                <div class="bm-speech" id="bmSpeech">Hi! I'm Acedmic Mantra 👋 Let's start!</div>
                <img src="{{ asset('theme/images/hii-bitmoji.png') }}" class="hero-bitmoji" alt="Guide">
            </div>
            <h1 class="hero-title">
                Stop Scrolling.<br>
                Start Building Your Future
            </h1>
            <p class="hero-desc">
                Learn through live sessions, real projects, mentorship, AI tools and practical industry experience. .
            </p>

            <div class="stat-pills">
                <div class="stat-pill">
                    <div class="icon" style="background:rgba(108,63,245,0.2); color:var(--brand-primary)">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span>5-step guided journey</span>
                </div>
                <div class="stat-pill">
                    <div class="icon" style="background:rgba(107,203,119,0.2); color:var(--brand-green)">
                        <i class="fas fa-video"></i>
                    </div>
                    <span>Watch &amp; learn with demo videos</span>
                </div>
                <div class="stat-pill">
                    <div class="icon" style="background:rgba(255,107,107,0.2); color:var(--brand-secondary)">
                        <i class="fas fa-medal"></i>
                    </div>
                    <span>Earn your first certificate</span>
                </div>
                <div class="stat-pill">
                    <div class="icon" style="background:rgba(255,217,61,0.2); color:var(--brand-accent)">
                        <i class="fas fa-users"></i>
                    </div>
                    <span>Join 12,400+ active learners</span>
                </div>
            </div>
        </div>

        {{-- ── Right: Form ── --}}
        <div class="card">
            <div class="step-badge">
                <div class="dot-pulse"></div>
                Step 1 of 5 — Onboarding
            </div>
            <h1>Let's Build Your <em>Learning Journey</em></h1>
            <p class="subtitle">Complete this quick form and unlock your personalized demo.</p>

            <form action="{{ route('lms.step1.store') }}" method="POST" id="onboardForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user" style="margin-right:5px; color:var(--brand-primary)"></i> Full
                            Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. Arjun Sharma"
                            value="{{ old('full_name') }}">
                        <p class="field-tip"><i class="fas fa-info-circle"></i> Your name personalizes the experience</p>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope" style="margin-right:5px; color:var(--brand-primary)"></i> Email
                        </label>
                        <input type="text" name="email" class="form-control" placeholder="you@email.com "
                            value="{{ old('email') }}">
                        <p class="field-tip"><i class="fas fa-info-circle"></i> Enter correctly to get updates</p>
                    </div>

                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-phone" style="margin-right:5px; color:var(--brand-primary)"></i> Contact
                            no
                        </label>
                        <input type="text" name="contact" class="form-control" placeholder=" 98XXXXXX"
                            value="{{ old('contact') }}">
                        <p class="field-tip"><i class="fas fa-info-circle"></i> Enter correctly to get updates</p>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-graduation-cap" style="margin-right:5px; color:var(--brand-primary)"></i>
                            Education Level</label>

                        <select name="education_level" class="form-control">
                            <option value="">Select your level...</option>

                            @foreach ($educationLevels as $level)
                                <option value="{{ $level->id }}"
                                    {{ old('education_level') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-compass" style="margin-right:5px; color:var(--brand-primary)"></i>
                        Interest Area
                    </label>

                    <select name="interest_area" id="interest_area" class="form-control">
                        <option value="">Select Interest Area</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <i class="fas fa-book-open" style="margin-right:5px; color:var(--brand-primary)"></i>
                        Preferred Course
                    </label>

                    <div id="course-cards" class="course-cards">
                        <p>Select an Interest Area first</p>
                    </div>

                    @error('preferred_course')
                        <p class="field-tip" style="color:var(--brand-secondary)">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="tip-box">
                    <strong>🤖 Guide Tip:</strong> Choose your course carefully — your entire demo session will be tailored
                    to your selection!
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">
                    <span id="btnText">Start My Demo Journey</span>
                    <i class="fas fa-arrow-right" id="btnIcon"></i>
                </button>
            </form>
        </div>

        <div class="review-section">

            <div class="review-heading">

                <h2>
                    ⭐ What Learners Say
                </h2>

                <p>
                    Real feedback from our learners
                </p>

            </div>


            <div class="review-carousel" id="reviewCarousel">

                @forelse($feedbacks as $feedback)

                    <div class="review-card">

                        <div class="quote">
                            “
                        </div>

                        <p>

                            {{ $feedback->message }}

                        </p>


                        <div class="stars">

                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $feedback->rating)
                                    ⭐
                                @else
                                    ☆
                                @endif
                            @endfor

                        </div>


                        <div class="user">

                            <div class="avatar">

                                {{ strtoupper(substr($feedback->user->name ?? 'U', 0, 1)) }}

                            </div>


                            <div>

                                <strong>

                                    {{ $feedback->user->name ?? 'Learner' }}

                                </strong>

                                <span>

                                    {{ $feedback->course->title ?? 'Student' }}

                                </span>

                            </div>

                        </div>


                        @if ($feedback->liked_tags)
                            <div class="tags">

                                @foreach ($feedback->liked_tags as $tag)
                                    <span>

                                        {{ $tag }}

                                    </span>
                                @endforeach

                            </div>
                        @endif


                        @if ($feedback->would_recommend)
                            <div class="recommend">

                                ✅ Recommended

                            </div>
                        @endif

                    </div>

                @empty

                    <div class="review-card">

                        <p>

                            No reviews available yet.

                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        const slider =
            document.getElementById(
                'reviewCarousel'
            );

        let scroll = 0;

        setInterval(() => {

            scroll += 340;

            if (
                scroll >=
                slider.scrollWidth -
                slider.clientWidth
            ) {
                scroll = 0;
            }

            slider.scrollTo({
                left: scroll,
                behavior: 'smooth'
            });

        }, 3000);

        document.addEventListener('DOMContentLoaded', function() {

            const interestArea = document.getElementById('interest_area');
            const courseCards = document.getElementById('course-cards');

            interestArea.addEventListener('change', async function() {

                const categoryId = this.value;

                courseCards.innerHTML = '<p>Loading courses...</p>';

                try {

                    const response = await fetch(`/category-courses/${categoryId}`);
                    const courses = await response.json();

                    let html = '';

                    courses.forEach(course => {

                        html += `
    <div>
        <input
            type="radio"
            class="course-option"
            name="preferred_course"
            id="course_${course.id}"
            value="${course.id}"
        >

        <label class="course-card-label" for="course_${course.id}">
            <span class="icon">📚</span>
            <span>${course.title}</span>
        </label>
    </div>
`;
                    });

                    courseCards.innerHTML = html || '<p>No courses available</p>';

                } catch (error) {
                    console.error(error);
                    courseCards.innerHTML = '<p>Failed to load courses</p>';
                }
            });

        });



        // Show relevant field tips on focus
        document.querySelectorAll('.form-control').forEach(el => {
            el.addEventListener('focus', () => {
                el.style.background = 'rgba(108,63,245,0.08)';
            });
            el.addEventListener('blur', () => {
                el.style.background = 'var(--bg-card2)';
            });
        });

        // Course selection updates bitmoji message
        document.addEventListener('change', function(e) {

            if (e.target.classList.contains('course-option')) {

                const radio = e.target;
                const label = radio.closest('div')
                    .querySelector('.course-card-label span:last-child')
                    .textContent;



                document.getElementById('bitmojiMsg').textContent =
                    `🎯 Great choice! "${label}" is one of our most popular courses!`;

                document.getElementById('bitmojiMsg').style.display = 'block';
            }
        });
        // Button loading state
        document.getElementById('onboardForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.style.opacity = '0.7';
            document.getElementById('btnText').textContent = 'Setting up your journey...';
            document.getElementById('btnIcon').className = 'fas fa-spinner fa-spin';
        });

        // Auto-save to localStorage
        document.querySelectorAll('input[type=text]').forEach(input => {
            const saved = localStorage.getItem('lms_' + input.name);
            if (saved) input.value = saved;
            input.addEventListener('input', () => localStorage.setItem('lms_' + input.name, input.value));
        });
    </script>
@endsection
