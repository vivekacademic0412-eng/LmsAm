{{--
    Enrollment promo card — shows a different pitch depending on where the
    student is in the funnel:
      - hasn't tried anything yet   -> push a free demo class
      - tried the demo, no purchase -> push converting to the full course
      - already purchased           -> hidden (nothing to promote)

    Expected variables (pass ?? false / '#' from the controller so this
    partial never breaks if they're not wired up yet):
      $studentHasDemo        bool
      $studentHasPaidCourse  bool
      $demoRoute             string
      $enrollRoute           string
      $promoCourseTitle      string|null  (course to push on the "upgrade" state)
--}}
@php
    $studentHasDemo       = $studentHasDemo ?? false;
    $studentHasPaidCourse = $studentHasPaidCourse ?? false;
    $demoRoute            = $demoRoute ?? '#';
    $enrollRoute          = $enrollRoute ?? '#';
    $promoCourseTitle     = $promoCourseTitle ?? 'your next course';
@endphp

@unless($studentHasPaidCourse === true && $studentHasDemo === true && false)
    {{-- outer @unless kept false-guarded on purpose: real hide condition is below --}}
@endunless

@if(!$studentHasPaidCourse)
    <article class="d-promo {{ $studentHasDemo ? 'd-promo--upgrade' : 'd-promo--demo' }}">
        <div class="d-promo__glow"></div>

        <div class="d-promo__body">
            @if($studentHasDemo)
                <span class="d-promo__eyebrow">🎉 You've completed a demo class</span>
                <h3 class="d-promo__title">Loved the demo? Let's make it official.</h3>
                <p class="d-promo__text">
                    Unlock the full curriculum for <strong>{{ $promoCourseTitle }}</strong> —
                    live sessions, projects, mentor support, and a certificate on completion.
                </p>
                <div class="d-promo__actions">
                    <a href="{{ $enrollRoute }}" class="d-hero-btn">Enroll Now →</a>
                    <a href="{{ route('student.courses') }}" class="d-promo__link">Browse all courses</a>
                </div>
            @else
                <span class="d-promo__eyebrow">👋 New here?</span>
                <h3 class="d-promo__title">Try a free demo class — no cost, no commitment.</h3>
                <p class="d-promo__text">
                    Sit in on a live session, meet a mentor, and see exactly how the course works
                    before you decide to enroll.
                </p>
                <div class="d-promo__actions">
                    <a href="{{ $demoRoute }}" class="d-hero-btn">Book Free Demo →</a>
                    <a href="{{ route('student.courses') }}" class="d-promo__link">Explore courses</a>
                </div>
            @endif
        </div>

        <div class="d-promo__badge">
            {{ $studentHasDemo ? '🚀' : '🎓' }}
        </div>
    </article>

    @push('styles')
        <style>
            .d-promo {
                position: relative;
                overflow: hidden;
                border-radius: 16px;
                padding: 26px 28px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                color: #fff;
            }

            .d-promo--demo {
                background: linear-gradient(135deg, #0947a8 0%, #1a5fd6 60%, #0a2f7a 100%);
            }

            .d-promo--upgrade {
                background: linear-gradient(135deg, #b45309 0%, #d97706 55%, #92400e 100%);
            }

            .d-promo__glow {
                position: absolute;
                top: -80px;
                right: -60px;
                width: 260px;
                height: 260px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(255,255,255,.18) 0%, transparent 70%);
                pointer-events: none;
            }

            .d-promo__body {
                position: relative;
                z-index: 1;
                max-width: 620px;
            }

            .d-promo__eyebrow {
                display: inline-block;
                font-size: .78rem;
                font-weight: 700;
                letter-spacing: .04em;
                background: rgba(255,255,255,.16);
                border: 1px solid rgba(255,255,255,.25);
                padding: .3rem .8rem;
                border-radius: 999px;
                margin-bottom: 10px;
            }

            .d-promo__title {
                margin: 0 0 8px;
                font-size: clamp(1.1rem, 2.2vw, 1.4rem);
                font-weight: 800;
                line-height: 1.3;
            }

            .d-promo__text {
                margin: 0 0 16px;
                font-size: .92rem;
                line-height: 1.65;
                color: rgba(255,255,255,.85);
            }

            .d-promo__actions {
                display: flex;
                align-items: center;
                gap: 16px;
                flex-wrap: wrap;
            }

            .d-promo__link {
                color: #fff;
                font-size: .85rem;
                font-weight: 600;
                text-decoration: underline;
                text-underline-offset: 3px;
                opacity: .85;
            }

            .d-promo__link:hover { opacity: 1; }

            .d-promo__badge {
                position: relative;
                z-index: 1;
                flex-shrink: 0;
                width: 64px;
                height: 64px;
                border-radius: 18px;
                background: rgba(255,255,255,.14);
                border: 1px solid rgba(255,255,255,.25);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
            }

            @media (max-width: 640px) {
                .d-promo { flex-direction: column; align-items: flex-start; text-align: left; }
                .d-promo__badge { display: none; }
            }
        </style>
    @endpush
@endif