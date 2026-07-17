@extends('layouts.auth')
@section('title', 'Reset Password | Academic Mantra LMS')
@section('meta_description', 'Set a new password for your Academic Mantra LMS account.')

{{-- Uses the same shell/hero layout + CSS as login and forgot-password:
     theme.css, auth-eye-fix.css, auth-responsive.css --}}


@section('content')

   
    <div class="shell ">

        {{-- ── Left: form ── --}}
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

            <span class="welcome-tag">Almost there</span>

            <div class="form-header">
                <h1>Set a new<br>password</h1>
                <p>Choose a strong password you haven't used before on this account.</p>
            </div>

            @livewire('auth.reset-password', ['token' => $token, 'email' => $email ?? request('email')])

        </section>

        {{-- ── Right: friendly avatar illustration ── --}}
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

    {{-- <script src="{{ asset('theme/js/password-toggle.js') }}" defer></script> --}}

@endsection
