@extends('demo.layout')

@section('title', 'Welcome – Start Your Journey')

{{-- @section('extra-styles') --}}

{{-- @endsection --}}

@section('bitmoji-message',
    '😊 Hi! Fill in your details below and I\'ll personalize your learning journey just for
    you!')
@section('bitmoji-emoji', '👋')

@section('content')

    <div class="welcome-grid">

        {{-- ── Left: Hero Panel ── --}}
        {{-- <div class="welcome-hero">
            <div class="hero-icon">🎓</div>
            <div class="hero-title">Welcome to Your<br><em style="color:var(--brand-primary)">Learning Journey</em></div>
            <p class="hero-desc">
                A personalized, guided demo experience designed to take you from zero to your first achievement — step by
                step.
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
        </div> --}}
        {{-- ── Left: Hero Panel ── --}}
        <div class="welcome-hero" style="position:relative; overflow:visible; padding-top:32px">

            {{-- ── Naina bitmoji sitting on top edge ── --}}
            <div class="bm-sitter" id="bmSitter" onclick="waveHello()" title="Click me!">

                {{-- speech bubble --}}
                <div class="bm-speech" id="bmSpeech">Hi! I'm Acedmic Mantra 👋 Let's start!</div>

                <svg width="110" height="140" viewBox="0 0 110 155" xmlns="http://www.w3.org/2000/svg">
                    {{-- sitting legs --}}
                    <rect x="30" y="118" width="16" height="34" rx="8" fill="#4C1D95" />
                    <rect x="64" y="118" width="16" height="34" rx="8" fill="#4C1D95" />
                    <ellipse cx="38" cy="153" rx="12" ry="6" fill="#1a1040" />
                    <ellipse cx="72" cy="153" rx="12" ry="6" fill="#1a1040" />
                    {{-- torso --}}
                    <rect x="28" y="82" width="54" height="44" rx="14" fill="#6C3FF5" />
                    <path d="M44 82 L55 97 L66 82" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    {{-- right arm resting --}}
                    <rect x="82" y="88" width="24" height="12" rx="6" fill="#6C3FF5"
                        transform="rotate(18,82,94)" />
                    <circle cx="102" cy="104" r="9" fill="#F5CBA7" />
                    {{-- left arm waving --}}
                    <g class="wave-arm" id="waveArmG" style="transform-origin:28px 94px">
                        <rect x="4" y="86" width="26" height="12" rx="6" fill="#6C3FF5"
                            transform="rotate(-25,28,92)" />
                        <g transform="translate(-4,62) rotate(-25,28,92)">
                            <circle cx="14" cy="42" r="9" fill="#F5CBA7" />
                            <rect x="5" y="33" width="5" height="12" rx="2.5" fill="#F5CBA7"
                                transform="rotate(-12,7,37)" />
                            <rect x="11" y="31" width="5" height="13" rx="2.5" fill="#F5CBA7" />
                            <rect x="17" y="31" width="5" height="13" rx="2.5" fill="#F5CBA7" />
                            <rect x="23" y="33" width="5" height="12" rx="2.5" fill="#F5CBA7"
                                transform="rotate(12,25,37)" />
                            <rect x="2" y="40" width="5" height="7" rx="2.5" fill="#F5CBA7"
                                transform="rotate(-35,4,43)" />
                        </g>
                    </g>
                    {{-- neck --}}
                    <rect x="46" y="68" width="18" height="18" rx="5" fill="#F5CBA7" />
                    {{-- head --}}
                    <ellipse cx="55" cy="44" rx="32" ry="36" fill="#F5CBA7" />
                    {{-- hair --}}
                    <path d="M23 32 Q22 2 55 0 Q88 2 87 32 Q84 8 55 6 Q26 8 23 32Z" fill="#1a1040" />
                    <path d="M23 32 Q18 55 22 75" fill="none" stroke="#1a1040" stroke-width="10"
                        stroke-linecap="round" />
                    <path d="M87 32 Q92 55 88 75" fill="none" stroke="#1a1040" stroke-width="10"
                        stroke-linecap="round" />
                    <rect x="70" y="10" width="12" height="5" rx="2.5" fill="#FF6B9D" />
                    <circle cx="76" cy="12" r="4" fill="#FFB3D1" />
                    {{-- ears + earrings --}}
                    <ellipse cx="22" cy="44" rx="7" ry="10" fill="#F5CBA7" />
                    <ellipse cx="88" cy="44" rx="7" ry="10" fill="#F5CBA7" />
                    <ellipse cx="22" cy="44" rx="4" ry="6" fill="#F0A88A" />
                    <ellipse cx="88" cy="44" rx="4" ry="6" fill="#F0A88A" />
                    <circle cx="22" cy="52" r="3" fill="#FFD93D" />
                    <circle cx="88" cy="52" r="3" fill="#FFD93D" />
                    {{-- eyebrows --}}
                    <path d="M38 26 Q46 20 54 24" fill="none" stroke="#1a1040" stroke-width="2.2"
                        stroke-linecap="round" />
                    <path d="M56 24 Q64 20 72 26" fill="none" stroke="#1a1040" stroke-width="2.2"
                        stroke-linecap="round" />
                    {{-- eyes --}}
                    <ellipse cx="46" cy="40" rx="9" ry="10" fill="white" />
                    <ellipse cx="64" cy="40" rx="9" ry="10" fill="white" />
                    <circle cx="47" cy="42" r="4" fill="#3b4fcc" />
                    <circle cx="65" cy="42" r="4" fill="#3b4fcc" />
                    <circle cx="47" cy="42" r="2.5" fill="#0a0a1a" />
                    <circle cx="65" cy="42" r="2.5" fill="#0a0a1a" />
                    <circle cx="49" cy="40" r="1.5" fill="white" />
                    <circle cx="67" cy="40" r="1.5" fill="white" />
                    {{-- blush --}}
                    <ellipse cx="36" cy="50" rx="8" ry="4" fill="#F1948A"
                        opacity=".55" />
                    <ellipse cx="74" cy="50" rx="8" ry="4" fill="#F1948A"
                        opacity=".55" />
                    {{-- nose --}}
                    <path d="M51 54 Q55 60 59 54" fill="none" stroke="#E8A87C" stroke-width="1.5"
                        stroke-linecap="round" />
                    {{-- smile --}}
                    <path d="M40 66 Q55 80 70 66" fill="#E05C73" />
                    <path d="M43 66 Q55 74 67 66 L67 70 Q55 76 43 70Z" fill="white" />
                </svg>
            </div>

            {{-- ── original hero content ── --}}
            <div class="hero-icon">🎓</div>
            <div class="hero-title">Welcome to Your<br><em style="color:var(--brand-primary)">Learning Journey</em></div>
            <p class="hero-desc">
                A personalized, guided demo experience designed to take you from zero to your first achievement — step by
                step.
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
            <h1>Let's Get You <em>Started</em></h1>
            <p class="subtitle">Fill in your details below — takes less than 2 minutes.</p>

            <form action="{{ route('lms.step1.store') }}" method="POST" id="onboardForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user" style="margin-right:5px; color:var(--brand-primary)"></i> Full
                            Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. Arjun Sharma"
                            value="{{ old('full_name') }}" required>
                        <p class="field-tip"><i class="fas fa-info-circle"></i> Your name personalizes the experience</p>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope" style="margin-right:5px; color:var(--brand-primary)"></i> Email
                            /
                            Phone</label>
                        <input type="text" name="email_phone" class="form-control"
                            placeholder="you@email.com or 98XXXXXX" value="{{ old('email_phone') }}" required>
                        <p class="field-tip"><i class="fas fa-info-circle"></i> Enter correctly to get updates</p>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-graduation-cap" style="margin-right:5px; color:var(--brand-primary)"></i>
                        Education Level</label>
                    {{-- <select name="education_level" class="form-control" required>
                        <option value="" disabled {{ old('education_level') ? '' : 'selected' }}>Select your level...
                        </option>
                        <option value="10th" {{ old('education_level') == '10th' ? 'selected' : '' }}>10th Pass</option>
                        <option value="12th" {{ old('education_level') == '12th' ? 'selected' : '' }}>12th Pass</option>
                        <option value="graduate" {{ old('education_level') == 'graduate' ? 'selected' : '' }}>Graduate
                        </option>
                        <option value="postgraduate" {{ old('education_level') == 'postgraduate' ? 'selected' : '' }}>Post
                            Graduate</option>
                        <option value="other" {{ old('education_level') == 'other' ? 'selected' : '' }}>Other</option>
                    </select> --}}
                    <select name="education_level" class="form-control" required>
                        <option value="">Select your level...</option>

                        @foreach ($educationLevels as $level)
                            <option value="{{ $level->id }}"
                                {{ old('education_level') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- <div class="form-group">
                <label><i class="fas fa-compass" style="margin-right:5px; color:var(--brand-primary)"></i> Interest Area</label>
                <select name="interest_area" class="form-control" required>
                    <option value="" disabled {{ old('interest_area') ? '' : 'selected' }}>What are you interested in?</option>
                    <option value="tech">Technology &amp; Coding</option>
                    <option value="marketing">Digital Marketing</option>
                    <option value="design">Graphic / UI Design</option>
                    <option value="business">Business &amp; Entrepreneurship</option>
                    <option value="freelancing">Freelancing</option>
                </select>
            </div> --}}
                <div class="form-group">
                    <label>
                        <i class="fas fa-compass" style="margin-right:5px; color:var(--brand-primary)"></i>
                        Interest Area
                    </label>

                    <select name="interest_area" id="interest_area" class="form-control" required>
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
                {{-- <div class="form-group">
                    <label><i class="fas fa-book-open" style="margin-right:5px; color:var(--brand-primary)"></i> Preferred
                        Course</label>
                    <div class="course-cards">
                        @php
                            $courses = [
                                ['value' => 'web-dev', 'icon' => '💻', 'label' => 'Web Development'],
                                ['value' => 'digital-mkt', 'icon' => '📣', 'label' => 'Digital Marketing'],
                                ['value' => 'ui-design', 'icon' => '🎨', 'label' => 'UI/UX Design'],
                                ['value' => 'freelancing', 'icon' => '🚀', 'label' => 'Freelancing'],
                            ];
                        @endphp
                        @foreach ($courses as $c)
                            <div>
                                <input type="radio" class="course-option" name="preferred_course"
                                    id="course_{{ $c['value'] }}" value="{{ $c['value'] }}"
                                    {{ old('preferred_course') == $c['value'] ? 'checked' : '' }}>
                                <label class="course-card-label" for="course_{{ $c['value'] }}">
                                    <span class="icon">{{ $c['icon'] }}</span>
                                    <span>{{ $c['label'] }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('preferred_course')
                        <p class="field-tip" style="color:var(--brand-secondary)"><i class="fas fa-exclamation-circle"></i>
                            {{ $message }}</p>
                    @enderror
                </div> --}}

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

    </div>
@endsection

@section('scripts')
    <script>
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
