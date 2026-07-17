@extends('layouts.auth')
@section('title', 'Academic Mantra LMS Login | Student Portal Access Now')
@section('meta_description', 'Log in to your Academic Mantra LMS account to access your courses, internships, assignments, learning dashboard, and student resources securely.')
@section('content')
<div class="shell">

    {{-- LEFT: FORM PANEL --}}
    <section class="form-panel">

        <div class="brand-mini">
            <img src="{{ asset('theme/images/logo.png') }}" alt="Academic Mantra" loading="lazy">
        </div>

        <div class="form-header">
            <h2>👋 Welcome Back</h2>
            <p>
                Sign in to access your dashboard, continue your learning journey,
                manage assignments, join live training sessions, and track your progress.
            </p>
        </div>

        {{-- Error box --}}
        <div class="error-box" id="errorBox" style="display:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <circle cx="12" cy="16" r="0.5" fill="currentColor"/>
            </svg>
            <span id="errorMsg">These credentials do not match our records.</span>
        </div>

        <form id="loginForm" data-url="{{ route('login.attempt') }}" novalidate>
            @csrf

            @if ($errors->any())
                <div class="error-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <circle cx="12" cy="16" r="0.5" fill="currentColor"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Email --}}
            <div class="field">
                <label for="email">Email address</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email"
                        placeholder="you@academicmantra.com"
                        value="{{ old('email') }}"
                        autocomplete="email" required>
                </div>
            </div>

            {{-- Password --}}
            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password" required>
                    <button type="button" class="eye-btn" id="eyeBtn" aria-label="Show password">
                        <svg id="eyeOpen" width="17" height="17" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeClosed" width="17" height="17" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember + Forgot --}}
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1"
                    {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Remember me</label>
                  <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
            </div>
             
            {{-- Submit --}}
            <button type="submit" class="btn-login" id="submitBtn">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                <div class="spinner"></div>
                Sign in to Dashboard
            </button>
        </form>

       
        

    </section>

    {{-- RIGHT: HERO SIDE --}}
    <section class="hero-side">

        <div class="hero-bg"></div>

        <div class="hero-content">

            <span class="live-pill">● Live Training Active</span>

            <h3>
                Train <span>Live.</span><br>
                Build Skills.<br>
                Launch Careers.
            </h3>

            <p>No Classroom Theory — Only Hands-On Learning &amp; Real Industry Exposure.</p>

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

        <img class="bitmoji"
            src="{{ asset('theme/images/bitmoji.png') }}"
            alt="Academic Mantra"
            title="Bitmoji Academic Mantra"
            loading="lazy">

    </section>

</div>
@endsection