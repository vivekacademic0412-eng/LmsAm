@extends('layouts.auth')
@section('title', 'Forgot Password | Academic Mantra LMS')
@section('meta_description', 'Reset your Academic Mantra LMS password to regain access to your courses, assignments, and dashboard.')
@section('content')

    

    <div class="shell">

        <section class="form-panel">
            <a href="{{ route('login') }}" class="hero-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                    <path d="M19 12H5M11 18l-6-6 6-6" />
                </svg>
                Back to Login
            </a>

            <div class="brand-mini">
                <img src="{{ asset('theme/images/am35.png') }}" alt="Academic Mantra" title="logo">
                <span>Learning Management System</span>
            </div>

            <span class="welcome-tag">Reset your password</span>

            <div class="form-header">
                <h1>Forgot your<br>password?</h1>
                <p>Enter the email address on your account and we'll send you a link to reset it.</p>
            </div>

            @livewire('auth.forgot-password')

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

