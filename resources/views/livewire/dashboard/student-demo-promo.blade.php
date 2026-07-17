<div class="d-promo d-promo--{{ $hasDemo ? 'upsell' : 'demo' }}">

    @if (!$hasDemo)
        {{-- ── STATE 1: No demo submitted yet → promote the ₹999 demo ── --}}
        <div class="d-promo-glow"></div>

        <div class="d-promo-body">
            <span class="d-promo-badge">🎯 New Here?</span>

            <h2 class="d-promo-title">Try a Live Demo Class for Just ₹{{ number_format($demoFee) }}</h2>

            <p class="d-promo-text">
                Get hands-on with a real session before you commit — live trainer interaction,
                actual course content, and zero long-term pressure.
            </p>

            <ul class="d-promo-features">
                <li><span class="d-promo-tick">✓</span> One live session with an expert trainer</li>
                <li><span class="d-promo-tick">✓</span> Real curriculum, not a watered-down preview</li>
                <li><span class="d-promo-tick">✓</span> Demo fee adjusted against your course fee later</li>
            </ul>

            <div class="d-promo-cta-row">
                <div class="d-promo-price">
                    <span class="d-promo-price-amount">₹{{ number_format($demoFee) }}</span>
                    {{-- <span class="d-promo-price-note">one-time demo fee</span> --}}
                </div>

                <a class="d-hero-btn" href="{{ route('lms.choose-type') }}">
                    Start Demo Now →
                </a>
            </div>
        </div>

    @else
        {{-- ── STATE 2: Demo already submitted → promote the full course ── --}}
        <div class="d-promo-glow d-promo-glow--gold"></div>

        <div class="d-promo-body">
            <span class="d-promo-badge d-promo-badge--gold">
                {{ $demo->isCompleted() ? '✅ Demo Completed' : '🕒 Demo Submitted' }}
            </span>

            <h2 class="d-promo-title">
                Loved {{ $demo->course->title ?? 'the Demo' }}? Unlock the Full Course
            </h2>

            <p class="d-promo-text">
                You've already experienced how we teach. Enroll now to get full access to the
                curriculum, assignments, live quizzes, and your completion certificate.
            </p>

            <ul class="d-promo-features">
                <li><span class="d-promo-tick">✓</span> Full syllabus &amp; downloadable resources</li>
                <li><span class="d-promo-tick">✓</span> Certificate of completion</li>
                <li><span class="d-promo-tick">✓</span> Lifetime access to session recordings</li>
            </ul>

            <div class="d-promo-cta-row">
                <div class="d-promo-price">
                    @if (!empty($demo->course->price))
                        <span class="d-promo-price-amount">₹{{ number_format($demo->course->price) }}</span>
                        <span class="d-promo-price-note">full course access</span>
                    @else
                        <span class="d-promo-price-note">Limited seats for this batch</span>
                    @endif
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a class="d-btn d-btn-ghost" href="{{ route('student.courses') }}">
                        View Course Details
                    </a>
                    <a class="d-hero-btn" href="{{ route('student.courses')}}">
                        Enroll Now →
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .d-promo {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        padding: 26px 28px;
        margin-bottom: 20px;
        border: 1px solid rgba(255,255,255,0.08);
       background: linear-gradient(135deg, rgb(18 21 149 / 14%), rgb(18 18 205 / 75%));
    }

    .d-promo--upsell {
        background: linear-gradient(135deg, rgba(234,179,8,0.14), rgba(20,20,30,0.4));
    }

    .d-promo-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99,102,241,0.35), transparent 70%);
        pointer-events: none;
    }

    .d-promo-glow--gold {
        background: radial-gradient(circle, rgba(234,179,8,0.30), transparent 70%);
    }

    .d-promo-body { position: relative; z-index: 1; }

    .d-promo-badge {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .02em;
        padding: 5px 12px;
        border-radius: 999px;
        /* color: #a5b4fc; */
        background: rgba(99,102,241,0.16);
        margin-bottom: 12px;
    }

    .d-promo-badge--gold {
        color: #fcd34d;
        background: rgba(234,179,8,0.16);
    }

    .d-promo-title {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.25;
        color: var(--text);
    }

    .d-promo-text {
        margin: 0 0 16px;
        max-width: 620px;
        font-size: 14px;
        line-height: 1.6;
        color: var(--text2);
    }

    .d-promo-features {
        list-style: none;
        margin: 0 0 20px;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .d-promo-features li {
        font-size: 13px;
        color: var(--text2);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .d-promo-tick {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 11px;
        color: #fff;
        background: #22c55e;
        flex-shrink: 0;
    }

    .d-promo-cta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .d-promo-price { display: flex; flex-direction: column; }

    .d-promo-price-amount {
        font-size: 22px;
        font-weight: 800;
        color: var(--text);
    }

    .d-promo-price-note {
        font-size: 12px;
        color: var(--text3);
    }

    @media (max-width: 640px) {
        .d-promo { padding: 20px; }
        .d-promo-title { font-size: 20px; }
        .d-promo-cta-row { flex-direction: column; align-items: flex-start; }
    }
</style>