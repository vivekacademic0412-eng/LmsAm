<x-demo.header/>
<link rel="stylesheet" href="{{ asset('theme/css/login.css') }}">
<button class="theme-btn" id="themeBtn" aria-label="Toggle dark mode" title="Toggle dark mode">
    <svg class="sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="2.2" stroke-linecap="round">
        <circle cx="12" cy="12" r="5" />
        <line x1="12" y1="1" x2="12" y2="3" />
        <line x1="12" y1="21" x2="12" y2="23" />
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
        <line x1="1" y1="12" x2="3" y2="12" />
        <line x1="21" y1="12" x2="23" y2="12" />
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
    </svg>
    <svg class="moon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="2.2" stroke-linecap="round">
        <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z" />
    </svg>
</button>

<div class="shell">
    {{-- @yield('content') --}}
    <section class="form-panel">
        <div class="brand-mini">
            <img src="{{ asset('theme/images/logo.png') }}" alt="Academic Mantra" title=" Acedmic Mantra" loading="lazy">
        </div>
        <div class="form-header">
            <h2>
                👋 Welcome Back
            </h2>
            <p>
                Sign in to access your dashboard, continue your learning journey,
                manage assignments, join live training sessions, and track your progress.
            </p>
        </div>
        <!-- Error placeholder (shown via JS demo) -->
        <div class="error-box" id="errorBox" style="display:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <circle cx="12" cy="16" r="0.5" fill="currentColor" />
            </svg>
            <span id="errorMsg">These credentials do not match our records.</span>
        </div>
        <form id="loginForm" data-url="{{ route('login.attempt') }}" novalidate>
            @csrf
            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
            <!-- Email -->
            <div class="field">
                <label for="email">Email address</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" placeholder="you@academicmantra.com"
                        autocomplete="email" required>
                </div>
            </div>

            <!-- Password -->
            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Enter your password"
                        autocomplete="current-password" required>
                    <button type="button" class="eye-btn" id="eyeBtn" aria-label="Show password">
                        <!-- EYE OPEN -->
                        <svg id="eyeOpen" width="17" height="17" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <!-- EYE CLOSED -->
                        <svg id="eyeClosed" width="17" height="17" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember + Forgot -->
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Remember me</label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login" id="submitBtn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    <polyline points="10 17 15 12 10 7" />
                    <line x1="15" y1="12" x2="3" y2="12" />
                </svg>
                Sign in to Dashboard
            </button>
        </form>

        <div class="divider">
            <hr><span>Secure Access For</span>
            <hr>
        </div>

        <div class="role-row">
            <span class="role-badge super">Super Admin</span>
            <span class="role-badge admin">Admin</span>
            <span class="role-badge trainer">Trainer</span>
            <span class="role-badge learner">Learner</span>
        </div>
    </section>
    <section class="hero-side">

        <div class="hero-bg"></div>

        <div class="hero-content">

            <span class="live-pill mb-4">
                ● Live Training Active
            </span>

            <h3>
                Train Live.
                Build Skills.
                Launch Careers.

            </h3>

            <p>

                No Classroom Theory — Only Hands-On Learning & Real Industry Exposure.

            </p>

            <div class="stats">

                <div>
                    <strong>150+</strong>
                    <span>Courses</span>
                </div>

                <div>
                    <strong>2500+</strong>
                    <span>Learners</span>
                </div>

                <div>
                    <strong>99%</strong>
                    <span>Placement</span>
                </div>

            </div>

        </div>

        <!-- YOUR BITMOJI -->

        <img class="bitmoji" src="{{ asset('theme/images/bitmoji.png') }}" alt="Academic Mantra" title="Bitmoji Acedmic Mantra" loading="lazy">

    </section>
</div>

<script src="{{ asset('theme/js/login.js') }}" defer></script>
<x-demo.footer />
