@extends('layouts.auth')
@section('title', 'Academic Mantra LMS Login | Student Portal Access Now')
@section('meta_description', 'Log in to your Academic Mantra LMS account to access your courses, internships, assignments, learning dashboard, and student resources securely.')
@section('content')

    <button type="button" class="theme-btn" id="themeBtn" aria-label="Toggle dark mode">
        <svg class="sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
        </svg>
        <svg class="moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
        </svg>
    </button>

    <div class="shell">

        <section class="form-panel">
            <div class="brand-mini">
                <img src="{{ asset('theme/images/am35.png') }}" alt="Academic Mantra" title="logo" >
                <span>Learning Management System</span>
            </div>

            <span class="welcome-tag">Welcome back</span>

            <div class="form-header">
                <h1>Sign in to your<br>classroom</h1>
                <p>Pick up your courses, assignments, and live sessions right where you left off.</p>
            </div>

            <div class="error-box" id="errorBox" style="display:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <circle cx="12" cy="16" r="0.5" fill="currentColor" />
                </svg>
                <span id="errorMsg">These credentials do not match our records.</span>
            </div>

            <form id="loginForm" data-url="{{ route('login.attempt') }}" novalidate>
                @csrf
                @if ($errors->any())
                    <div class="error-box">
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="field">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <span class="input-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" placeholder="you@academicmantra.com" autocomplete="email" required>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon" aria-hidden="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                        <button type="button" class="eye-btn" id="eyeBtn" aria-label="Show password">
                            <svg id="eyeOpen" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg id="eyeClosed" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Remember me</label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>
                   <a href="{{ route('lms.demo') }}" class="register">Register</a>
                <button type="submit" class="btn-login" id="submitBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                    Sign in to dashboard
                </button>
            </form>

            <div class="divider"><hr><span>Secure access for</span><hr></div>

           
        </section>

        <section class="hero-side">
            <div class="hero-glow" aria-hidden="true"></div>

            <div class="hero-top mb-2">
                <span class="live-pill mb-1"><span class="live-dot"></span> 1,240 learners online now</span>
                <h2>One platform.<br><em>Four roles.</em><br>Endless progress.</h2>
                <p>Super admins, admins, trainers and learners each get a dashboard built for exactly what they need to do next.</p>
            </div>

            <div class="orbit-wrap" aria-hidden="true">
                <div class="orbit-ring"></div>
                <div class="orbit-ring inner"></div>

                <div class="orbit-track">
                    <div class="orbit-node n1"><span>Super<br>Admin</span></div>
                    <div class="orbit-node n2"><span>Learner</span></div>
                </div>
                <div class="orbit-track reverse">
                    <div class="orbit-node n3"><span>Trainer</span></div>
                    <div class="orbit-node n4"><span>Admin</span></div>
                </div>

                <div class="orbit-core">AM</div>
            </div>

            <div class="stats">
                <div><strong>18.4k</strong><span>Learners</span></div>
                <div><strong>540+</strong><span>Courses</span></div>
                <div><strong>97%</strong><span>Satisfaction</span></div>
            </div>
        </section>

    </div>

@endsection
<script src="{{ asset('theme/js/login.js') }}" defer></script>