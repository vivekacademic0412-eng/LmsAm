{{-- resources/views/demo/lms/choose-type.blade.php --}}
@extends('demo.layout')
@section('title', ' Register for AI Training & Internships | Academic Mantra Services')
@section('meta_description',
    'Create your Academic Mantra LMS account to enroll in AI-integrated training, live
    internships, certifications, and access your student dashboard.')
 
        {{-- <link rel="stylesheet" href="{{ asset('css/demo-lms-landing.css') }}"> --}}
        {{-- <link rel="stylesheet" href="{{ asset('css/demo-register.css') }}"> --}}
        {{-- Animate.css for SweetAlert transitions --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <style>
            /* ═══════════════════════════════════════════════════════════════
           LMS Landing / Hero Section — demo-lms-landing.css
           Namespaced under .lms-landing so nothing here leaks into other
           pages. Pair with demo-register.css (dreg-* classes) for the form.
        ═══════════════════════════════════════════════════════════════ */

            .lms-landing {
                --lms-navy: #0e1f5c;
                --lms-navy-deep: #0a1642;
                --lms-blue: #2952e3;
                --lms-blue-light: #4d6bef;
                --lms-red: #ef3f4f;
                --lms-text: #101828;
                --lms-text-muted: #5b6472;
                --lms-card-bg: #ffffff;
                --lms-card-shadow: 0 20px 50px rgba(20, 33, 90, .18), 0 4px 14px rgba(20, 33, 90, .08);

                min-height: 100vh;
                background: linear-gradient(135deg, #eef1fc 0%, #c4d1f7 42%, #6f89e0 78%, #5573dd 100%);
                padding-bottom: 3rem;
            }

            [data-theme="dark"] .lms-landing {
                --lms-text: #eef2ff;
                --lms-text-muted: #a9b3d6;
                --lms-card-bg: #131c3a;
                background: linear-gradient(135deg, #0a1030 0%, #101a44 42%, #1a2b6b 78%, #223a8c 100%);
            }

            .hero-section {
                /* max-width: 1280px; */
                margin: 0 auto;
                display: grid;
                grid-template-columns: 1.05fr .95fr;
                align-items: center;
                gap: 3rem;
                /* padding: 3.5rem 2rem 2rem; */
                min-height: calc(100vh - 140px);
            }

            /* ── Left column ─────────────────────────────────────────────── */
            .hero-left {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            /* Back link — deliberately quiet: no border, no fill, just a hint
           of motion on hover. It sits above everything else so it reads as
           navigation, not a call-to-action. */
            .hero-back-link {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                margin-bottom: 1.5rem;
                padding: .3rem 0;
                font-size: .875rem;
                font-weight: 600;
                color: var(--lms-text-muted);
                text-decoration: none;
                transition: color .15s ease, transform .15s ease;
            }

            .hero-back-link svg {
                transition: transform .15s ease;
            }

            .hero-back-link:hover {
                color: var(--lms-blue);
            }

            .hero-back-link:hover svg {
                transform: translateX(-3px);
            }

            .logo-tag {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                background: var(--lms-navy);
                color: #fff;
                font-weight: 800;
                font-size: 1.05rem;
                letter-spacing: .03em;
                padding: .55rem 1.25rem;
                border-radius: 999px;
                box-shadow: 0 10px 24px rgba(14, 31, 92, .35);
                margin-bottom: 1.75rem;
            }

            .logo-tag__dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: var(--lms-red);
                box-shadow: 0 0 0 0 rgba(239, 63, 79, .6);
                animation: lms-dot-pulse 1.8s ease-out infinite;
            }

            @keyframes lms-dot-pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(239, 63, 79, .55);
                }

                70% {
                    box-shadow: 0 0 0 9px rgba(239, 63, 79, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(239, 63, 79, 0);
                }
            }

            .hero-left h1 {
                font-size: clamp(2.1rem, 3.6vw, 3.1rem);
                font-weight: 800;
                line-height: 1.15;
                color: var(--lms-text);
                margin: 0 0 1.25rem;
            }

            .hero-left h1 span {
                background: linear-gradient(90deg, var(--lms-blue), var(--lms-blue-light));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            .hero-left p {
                font-size: 1.05rem;
                line-height: 1.7;
                color: var(--lms-text-muted);
                max-width: 46ch;
                margin: 0 0 2rem;
            }

            /* ── CTA buttons ──────────────────────────────────────────────── */
            .hero-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: .9rem;
                margin-bottom: 2rem;
            }

            .btn-primary-custom,
            .btn-outline-custom {
                display: inline-flex;
                align-items: center;
                gap: .5rem;
                padding: .85rem 1.6rem;
                border-radius: 999px;
                font-weight: 700;
                font-size: .9375rem;
                text-decoration: none;
                transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
                border: 1.5px solid transparent;
            }

            .btn-primary-custom {
                background: var(--lms-navy);
                color: #fff;
                box-shadow: 0 10px 22px rgba(14, 31, 92, .3);
            }

            .btn-primary-custom:hover {
                transform: translateY(-2px);
                box-shadow: 0 14px 28px rgba(14, 31, 92, .38);
                color: #fff;
            }

            .btn-outline-custom {
                background: rgba(255, 255, 255, .55);
                border-color: var(--lms-navy);
                color: var(--lms-navy);
                backdrop-filter: blur(4px);
            }

            .btn-outline-custom:hover {
                background: #fff;
                transform: translateY(-2px);
                color: var(--lms-navy);
            }

            /* ── Feature pills ────────────────────────────────────────────── */
            .hero-features {
                display: flex;
                flex-wrap: wrap;
                gap: .65rem;
            }

            .feature-pill {
                background: rgba(14, 31, 92, .85);
                color: #fff;
                font-size: .8125rem;
                font-weight: 600;
                padding: .5rem 1.1rem;
                border-radius: 999px;
                box-shadow: 0 6px 14px rgba(14, 31, 92, .18);
                transition: transform .15s ease, background .15s ease;
            }

            .feature-pill:hover {
                transform: translateY(-2px);
                background: var(--lms-navy);
            }

            /* ── Right column: registration card ─────────────────────────── */
            .hero-right {
                /* background: var(--lms-card-bg); */
                border-radius: 22px;
                /* box-shadow: var(--lms-card-shadow); */
                padding: 2.25rem 2.25rem 2rem;
                width: 100%;
                /* max-width: 480px; */
                margin-left: auto;
            }

            /* ── Responsive ───────────────────────────────────────────────── */
            @media (max-width: 1024px) {
                .hero-section {
                    grid-template-columns: 1fr;
                    gap: 2.5rem;
                    padding: 2.5rem 1.5rem;
                    min-height: auto;
                }

                .hero-left {
                    align-items: center;
                    text-align: center;
                }

                .hero-left p {
                    max-width: 56ch;
                }

                .hero-buttons,
                .hero-features {
                    justify-content: center;
                }

                .hero-right {
                    max-width: 560px;
                    margin: 0 auto;
                }
            }

            @media (max-width: 560px) {
                .hero-section {
                    padding: 2rem 1rem;
                }

                .hero-left h1 {
                    font-size: 1.85rem;
                }

                .hero-buttons {
                    flex-direction: column;
                    width: 100%;
                }

                .btn-primary-custom,
                .btn-outline-custom {
                    justify-content: center;
                    width: 100%;
                }

                .hero-right {
                    padding: 1.75rem 1.25rem 1.5rem;
                    border-radius: 18px;
                }

                .hero-back-link {
                    margin-bottom: 1.1rem;
                }
            }
        </style>
 

@section('content')
    <div class="lms-landing">

        <section class="hero-section">
            <div class="hero-left">

                {{-- ── Back link: standalone, quiet, first thing in the column ── --}}
                <a href="https://www.academicmantraservices.com" class="hero-back-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.4">
                        <path d="M19 12H5M11 18l-6-6 6-6" />
                    </svg>
                    Back to Home
                </a>

                <div class="logo-tag">
                    <span class="logo-tag__dot"></span>
                    LIVE
                </div>

                <h1>
                    Welcome to <span>LIVE Skills Training Programs</span>
                </h1>
                <p>
                    We're excited to have you here! Explore your live demo session,
                    interact with expert mentors, discover career-focused courses,
                    and experience how practical learning can transform your future.
                </p>

                <div class="hero-buttons">
                    <a href="https://www.academicmantraservices.com/it-training-program" class="btn-primary-custom">
                        Explore Courses
                    </a>
                    <a href="https://www.academicmantraservices.com/reviews" class="btn-outline-custom">
                        Student Success Stories
                    </a>
                </div>

                <div class="hero-features">
                    <div class="feature-pill">Live Classes</div>
                    <div class="feature-pill">Expert Trainers</div>
                    <div class="feature-pill">Hands-on Learning</div>
                    <div class="feature-pill">Career Growth</div>
                </div>
            </div>

            {{-- ── Right: Registration Form ── --}}
            <div class="welcome-hero hero-right">
                @livewire('demo.demo-register')
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
@endpush
