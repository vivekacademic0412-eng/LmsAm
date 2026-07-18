{{-- resources/views/demo/lms/choose-type.blade.php --}}
@extends('demo.layout')
@section('title', ' Login or Register for AI Training & Internships | Academic Mantra Services')
@section('meta_description',
    'Log in or create your Academic Mantra LMS account to enroll in AI-integrated training, live
    internships, certifications, and access your student dashboard.')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }

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
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        align-items: center;
        gap: 3rem;
        min-height: calc(100vh - 140px);
        max-width: 1320px;
        padding: 3rem 2rem;
    }

    /* ── Left column ─────────────────────────────────────────────── */
    .hero-left { display: flex; flex-direction: column; align-items: flex-start; }

    .hero-back-link {
        display: inline-flex; align-items: center; gap: .4rem;
        margin-bottom: 1.5rem; padding: .3rem 0;
        font-size: .875rem; font-weight: 600;
        color: var(--lms-text-muted); text-decoration: none;
        transition: color .15s ease, transform .15s ease;
    }
    .hero-back-link svg { transition: transform .15s ease; }
    .hero-back-link:hover { color: var(--lms-blue); }
    .hero-back-link:hover svg { transform: translateX(-3px); }

    .logo-tag {
        display: inline-flex; align-items: center; gap: .5rem;
        background: var(--lms-navy); color: #fff;
        font-weight: 800; font-size: 1.05rem; letter-spacing: .03em;
        padding: .55rem 1.25rem; border-radius: 999px;
        box-shadow: 0 10px 24px rgba(14, 31, 92, .35);
        margin-bottom: 1.75rem;
    }
    .logo-tag__dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: var(--lms-red);
        animation: lms-dot-pulse 1.8s ease-out infinite;
    }
    @keyframes lms-dot-pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 63, 79, .55); }
        70% { box-shadow: 0 0 0 9px rgba(239, 63, 79, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 63, 79, 0); }
    }

    .hero-left h1 {
        font-size: clamp(2.1rem, 3.6vw, 3.1rem);
        font-weight: 800; line-height: 1.15;
        color: var(--lms-text); margin: 0 0 1.25rem;
    }
    .hero-left h1 span {
        background: linear-gradient(90deg, var(--lms-blue), var(--lms-blue-light));
        -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .hero-left p {
        font-size: 1.05rem; line-height: 1.7;
        color: var(--lms-text-muted); max-width: 46ch; margin: 0 0 2rem;
    }

    .hero-features { display: flex; flex-wrap: wrap; gap: .65rem; }
    .feature-pill {
        background: rgba(14, 31, 92, .85); color: #fff;
        font-size: .8125rem; font-weight: 600;
        padding: .5rem 1.1rem; border-radius: 999px;
        box-shadow: 0 6px 14px rgba(14, 31, 92, .18);
        transition: transform .15s ease, background .15s ease;
    }
    .feature-pill:hover { transform: translateY(-2px); background: var(--lms-navy); }

    /* ── Right column: auth card ─────────────────────────────────── */
    .hero-right {
        background: var(--lms-card-bg);
        border-radius: 22px;
        box-shadow: var(--lms-card-shadow);
        padding: 2rem 2.25rem 2.25rem;
        width: 100%;
        max-width: 480px;
        margin-left: auto;
    }

    /* ── Auth tabs ────────────────────────────────────────────────── */
    .auth-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .4rem;
        background: #eef1fc;
        border-radius: 999px;
        padding: .35rem;
        margin-bottom: 1.75rem;
    }
    [data-theme="dark"] .auth-tabs { background: #0e163a; }
    .auth-tab {
        border: none;
        background: transparent;
        padding: .65rem 1rem;
        border-radius: 999px;
        font-weight: 700;
        font-size: .9rem;
        color: var(--lms-text-muted);
        cursor: pointer;
        transition: background .18s ease, color .18s ease, box-shadow .18s ease;
    }
    .auth-tab.is-active {
        background: var(--lms-navy);
        color: #fff;
        box-shadow: 0 8px 18px rgba(14, 31, 92, .3);
    }

    /* ── Shared success/error boxes used by both forms ─────────────── */
    .dreg-error-box {
        background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
        padding: 16px; border-radius: 10px; margin-bottom: 20px; font-size: .9rem;
    }

    /* ── Success screen (registration) ───────────────────────────── */
    .dreg-done { text-align: center; padding: .5rem 0 .25rem; }
    .dreg-done__icon {
        width: 56px; height: 56px; margin: 0 auto 1.1rem;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; background: #dcfce7; color: #16a34a;
    }
    .dreg-done__title { font-size: 1.4rem; font-weight: 800; color: var(--lms-text, #101828); margin: 0 0 .4rem; }
    .dreg-done__sub { font-size: .95rem; color: var(--lms-text-muted, #5b6472); margin: 0 0 1.3rem; }
    .dreg-done__notice {
        display: flex; gap: .7rem; text-align: left;
        background: #FFF7ED; border: 1px solid #FED7AA; color: #7C2D12;
        padding: 14px 16px; border-radius: 10px; font-size: .875rem; line-height: 1.5;
        margin-bottom: 1.1rem;
    }
    .dreg-done__notice svg { flex-shrink: 0; margin-top: 2px; }
    .dreg-done__resend-msg {
        font-size: .85rem; border-radius: 8px; padding: .6rem .8rem; margin-bottom: 1rem;
    }
    .dreg-done__resend-msg.is-ok { background: #ecfdf5; color: #166534; border: 1px solid #86efac; }
    .dreg-done__resend-msg.is-warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    .dreg-resend-btn {
        width: 100%; padding: .85rem 1.4rem; border-radius: 999px; border: 1.5px solid var(--lms-navy, #0e1f5c);
        background: transparent; color: var(--lms-navy, #0e1f5c); font-weight: 700; font-size: .9375rem;
        cursor: pointer; transition: background .15s ease, color .15s ease;
    }
    .dreg-resend-btn:hover { background: var(--lms-navy, #0e1f5c); color: #fff; }
    .dreg-resend-btn:disabled { opacity: .6; cursor: not-allowed; }
    .dreg-done__login-link {
        display: block; margin-top: 1.1rem; font-size: .875rem; font-weight: 600;
        color: var(--lms-blue, #2952e3); text-decoration: none;
    }
    .dreg-done__login-link:hover { text-decoration: underline; }

    /* ── Login-form extras ───────────────────────────────────────── */
    .dreg-login-row {
        display: flex; align-items: center; justify-content: space-between;
        margin: -.5rem 0 1.25rem; font-size: .85rem;
    }
    .dreg-remember { display: flex; align-items: center; gap: .4rem; color: var(--lms-text-muted, #5b6472); }
    .dreg-forgot-link { color: var(--lms-blue, #2952e3); text-decoration: none; font-weight: 600; }
    .dreg-forgot-link:hover { text-decoration: underline; }

    /* ── Responsive ───────────────────────────────────────────────── */
    @media (max-width: 1024px) {
        .hero-section { grid-template-columns: 1fr; gap: 2.5rem; padding: 2.5rem 1.5rem; min-height: auto; }
        .hero-left { align-items: center; text-align: center; }
        .hero-left p { max-width: 56ch; }
        .hero-features { justify-content: center; }
        .hero-right { max-width: 560px; margin: 0 auto; }
    }

    @media (max-width: 560px) {
        .hero-section { padding: 2rem 1rem; }
        .hero-left h1 { font-size: 1.85rem; }
        .hero-right { padding: 1.6rem 1.25rem 1.75rem; border-radius: 18px; }
        .hero-back-link { margin-bottom: 1.1rem; }
        .auth-tab { font-size: .82rem; padding: .6rem .5rem; }
    }
</style>

@section('content')
    <div class="lms-landing" x-data="{ tab: 'register' }" @switch-to-login.window="tab = 'login'">

        <section class="hero-section">
            <div class="hero-left">

                <a href="https://www.academicmantraservices.com" class="hero-back-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path d="M19 12H5M11 18l-6-6 6-6" />
                    </svg>
                    Back to Home
                </a>

                <div class="logo-tag">
                    <span class="logo-tag__dot"></span>
                    LIVE
                </div>

                <h1>Welcome to <span>LIVE Skills Training Programs</span></h1>
                <p>
                    We're excited to have you here! Explore your live demo session,
                    interact with expert mentors, discover career-focused courses,
                    and experience how practical learning can transform your future.
                </p>

                <div class="hero-features">
                    <div class="feature-pill">Live Classes</div>
                    <div class="feature-pill">Expert Trainers</div>
                    <div class="feature-pill">Hands-on Learning</div>
                    <div class="feature-pill">Career Growth</div>
                </div>
            </div>

            {{-- ── Right: Merged Login / Register card ── --}}
            <div class="welcome-hero hero-right">

                <div class="auth-tabs" role="tablist">
                    <button type="button" role="tab" class="auth-tab" :class="{ 'is-active': tab === 'login' }"
                        @click="tab = 'login'">
                        Log In
                    </button>
                    <button type="button" role="tab" class="auth-tab" :class="{ 'is-active': tab === 'register' }"
                        @click="tab = 'register'">
                        Register
                    </button>
                </div>

                <div x-show="tab === 'login'" x-cloak>
                    @livewire('demo.demo-login')
                </div>

                <div x-show="tab === 'register'" x-cloak>
                    @livewire('demo.demo-register')
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
@endpush
