@extends('demo.layout')

@section('title', 'Step 1 — Start Your Learning Journey')

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .journey-wrap {
        min-height: 100vh;
        background:
            radial-gradient(circle at 8% 8%, color-mix(in srgb, var(--brand-primary) 16%, transparent), transparent 40%),
            radial-gradient(circle at 92% 88%, color-mix(in srgb, var(--brand-accent) 18%, transparent), transparent 42%),
            linear-gradient(135deg, var(--bg) 0%, var(--bg2) 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem 1rem 3rem;
    }

    .page-hero { text-align: center; margin-bottom: 1.75rem; max-width: 560px; }
    .page-hero .hero-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: color-mix(in srgb, var(--brand-primary) 14%, transparent);
        color: var(--brand-primary);
        font-size: 11.5px; font-weight: 700; letter-spacing: .04em;
        padding: 5px 13px; border-radius: 999px; margin-bottom: .75rem;
    }
    .page-hero h1 {
        font-size: 2.1rem; font-weight: 800;
        color: var(--text-main); letter-spacing: -0.03em; line-height: 1.15;
    }
    .page-hero h1 em {
        font-style: normal;
        background: linear-gradient(120deg, var(--brand-primary), var(--brand-accent));
        -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .page-hero p { font-size: .95rem; color: var(--text-muted); margin-top: .5rem; }

    .journey-card {
        width: 100%; max-width: 1180px;
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        display: grid; grid-template-columns: 250px 1fr; overflow: hidden;
    }

    /* ============================================================
       AVATAR PICKER — the one signature element of this redesign.
       Not a fixed guide image: the student picks who represents
       them, sticker-style, with a playful pop on select.
    ============================================================ */
    .bitmoji-panel {
        background: linear-gradient(180deg, var(--bg2) 0%, var(--bg) 100%);
        padding: 1.75rem 1.25rem;
        display: flex; flex-direction: column; align-items: center; gap: 1rem;
        border-right: 1px solid var(--line);
    }

    .avatar-hero {
        position: relative; width: 96px; height: 96px;
    }
    .avatar-hero img {
        width: 96px; height: 96px; border-radius: 50%;
        border: 3px solid var(--bg-card);
        box-shadow: 0 0 0 3px var(--brand-primary);
        object-fit: cover; background: var(--line);
        transition: transform .25s cubic-bezier(.34,1.56,.64,1);
    }
    .avatar-hero.just-picked img { transform: scale(1.08) rotate(-3deg); }
    .avatar-hero .avatar-online {
        position: absolute; bottom: 4px; right: 4px;
        width: 15px; height: 15px; background: var(--brand-green);
        border-radius: 50%; border: 2.5px solid var(--bg-card);
    }

    .bit-bubble {
        background: var(--bg-card); border: 1px solid var(--line);
        border-radius: 14px; padding: .7rem .95rem;
        font-size: 12.5px; line-height: 1.5; color: var(--text); position: relative; text-align: center;
    }
    .bit-bubble::before {
        content: ''; position: absolute; top: -9px; left: 50%; transform: translateX(-50%);
        border: 5px solid transparent; border-bottom-color: var(--line);
    }
    .bit-bubble::after {
        content: ''; position: absolute; top: -7px; left: 50%; transform: translateX(-50%);
        border: 5px solid transparent; border-bottom-color: var(--bg-card);
    }

    .avatar-picker-label {
        font-size: 11px; font-weight: 700; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: .06em; margin-top: .25rem; text-align: center;
    }
    .avatar-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;
        width: 100%;
    }
    .avatar-option {
        width: 100%; aspect-ratio: 1; border-radius: 50%;
        border: 2.5px solid transparent; cursor: pointer; overflow: hidden;
        background: var(--bg-card); padding: 2px;
        transition: transform .15s ease, border-color .15s ease;
    }
    .avatar-option img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: block; }
    .avatar-option:hover { transform: translateY(-2px) scale(1.05); }
    .avatar-option.selected { border-color: var(--brand-primary); }

    .bit-steps { width: 100%; display: flex; flex-direction: column; gap: 4px; margin-top: .25rem; }
    .bs-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 11.5px; color: var(--text-muted);
        padding: 6px 10px; border-radius: var(--radius-sm); transition: all .2s;
    }
    .bs-item.active {
        background: color-mix(in srgb, var(--brand-primary) 12%, transparent);
        color: var(--brand-primary); font-weight: 600;
    }
    .bs-item.done { color: var(--brand-green); }
    .bs-item i { font-size: 14px; }

    /* Form panel */
    .form-panel { padding: 2rem 2.25rem; overflow-y: auto; }
    .form-step-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: color-mix(in srgb, var(--brand-primary) 12%, transparent);
        color: var(--brand-primary);
        font-size: 11.5px; font-weight: 600;
        padding: 4px 12px; border-radius: 20px; margin-bottom: .75rem;
    }
    .pulse-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--brand-primary);
        animation: pulse 1.4s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .35; transform: scale(.65); }
    }
    .form-title { font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-bottom: .25rem; letter-spacing: -0.02em; }
    .form-title em { font-style: normal; color: var(--brand-primary); }
    .form-subtitle { font-size: .875rem; color: var(--text-muted); margin-bottom: 1.5rem; }

    .sec-head {
        display: flex; align-items: center; gap: 8px;
        font-size: 11px; font-weight: 700; color: var(--brand-primary);
        text-transform: uppercase; letter-spacing: .07em;
        margin-bottom: .75rem; margin-top: 1.25rem;
    }
    .sec-head:first-of-type { margin-top: 0; }
    .sec-head::after { content: ''; flex: 1; height: 1px; background: var(--line); }

    .f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .fgroup { display: flex; flex-direction: column; gap: 5px; }
    .flabel { font-size: 12px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 5px; }
    .flabel i { color: var(--brand-primary); font-size: 14px; }
    .finput {
        height: 40px; border: 1.5px solid var(--input-border);
        border-radius: var(--radius-sm); padding: 0 12px;
        font-size: 13.5px; color: var(--text-main);
        background: var(--input-bg); outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s; width: 100%;
    }
    .finput:focus {
        border-color: var(--input-focus);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--input-focus) 18%, transparent);
    }
    .finput.is-invalid { border-color: var(--danger); background: color-mix(in srgb, var(--danger) 5%, var(--bg-card)); }
    .finput.is-valid   { border-color: var(--brand-green); background: color-mix(in srgb, var(--brand-green) 5%, var(--bg-card)); }
    select.finput {
        cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpath stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px;
    }
    .field-error { font-size: 11.5px; color: var(--danger); display: none; align-items: center; gap: 4px; }
    .field-error.show { display: flex; }
    .field-error i { font-size: 13px; }
    .server-error { font-size: 11.5px; color: var(--danger); display: flex; align-items: center; gap: 4px; margin-top: 2px; }

    /* Interest area — a fun single-select card grid instead of a plain dropdown */
    .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-bottom: 4px; }
    .sel-card {
        border: 1.5px solid var(--line); border-radius: var(--radius-sm);
        padding: 14px; cursor: pointer; background: var(--bg-card2);
        transition: all .18s; display: flex; flex-direction: column; gap: 5px; user-select: none;
    }
    .sel-card:hover { border-color: var(--border); background: var(--bg2); transform: translateY(-1px); }
    .sel-card.selected {
        border-color: var(--brand-primary);
        background: color-mix(in srgb, var(--brand-primary) 10%, var(--bg-card));
        box-shadow: 0 4px 14px color-mix(in srgb, var(--brand-primary) 22%, transparent);
    }
    .sel-card.selected .sc-label { color: var(--brand-primary); }
    .sel-card i { font-size: 22px; color: var(--brand-primary); }
    .sel-card.selected i { color: var(--brand-primary); }
    .sc-label { font-size: 13px; font-weight: 700; color: var(--text); }

    .tip-box {
        background: color-mix(in srgb, var(--brand-accent) 12%, var(--bg-card));
        border-left: 4px solid var(--brand-accent);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        padding: .65rem 1rem; font-size: 12.5px; color: var(--text);
        display: flex; align-items: flex-start; gap: 7px; margin: 1.25rem 0 1rem;
    }
    .tip-box i { flex-shrink: 0; margin-top: 1px; font-size: 15px; color: var(--brand-accent); }

    .form-actions {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: .75rem; padding-top: 1rem; border-top: 1px solid var(--line);
    }
    .btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        height: 42px; padding: 0 20px;
        border: 1.5px solid var(--line); border-radius: var(--radius-sm);
        background: var(--bg-card); font-size: 13.5px; font-weight: 500; color: var(--text);
        cursor: pointer; text-decoration: none; transition: all .15s;
    }
    .btn-back:hover { background: var(--bg2); border-color: var(--text-muted); }
    .btn-next {
        display: inline-flex; align-items: center; gap: 8px;
        height: 42px; padding: 0 28px;
        background: linear-gradient(120deg, var(--brand-primary), color-mix(in srgb, var(--brand-primary) 70%, var(--brand-accent)));
        border: none; border-radius: var(--radius-sm);
        font-size: 13.5px; font-weight: 700; color: #fff; cursor: pointer; transition: all .15s;
        box-shadow: 0 4px 14px color-mix(in srgb, var(--brand-primary) 40%, transparent);
    }
    .btn-next:hover  { filter: brightness(1.06); box-shadow: 0 6px 18px color-mix(in srgb, var(--brand-primary) 50%, transparent); }
    .btn-next:active { transform: scale(.97); }
    .btn-next:disabled { opacity: .65; cursor: not-allowed; transform: none; }

    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
        border-radius: 50%; animation: spin .7s linear infinite; display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 700px) {
        .journey-card { grid-template-columns: 1fr; }
        .bitmoji-panel { padding: 1.25rem 1rem; }
        .avatar-grid { grid-template-columns: repeat(5, 1fr); max-width: 280px; }
        .bit-steps { display: none; }
        .f-row { grid-template-columns: 1fr; }
        .form-panel { padding: 1.25rem; }
    }
</style>

@section('content')
{{--
    Pre-compute restore values once in PHP so JS can read them reliably
    on ANY request type (fresh GET after Back, failed-validation redirect,
    or first visit). Priority: old() [failed validation] → DB record.
--}}
@php
    $rv = [
        'full_name'        => old('full_name',        $existingDemoUser?->full_name          ?? ''),
        'email'            => old('email',             $existingDemoUser?->email              ?? ''),
        'contact'          => old('contact',           $existingDemoUser?->phone              ?? ''),
        'education_level'  => old('education_level',   $existingDemoUser?->education_level_id ?? ''),
        'interest_area'    => old('interest_area',     $existingDemoUser?->interest_area_id   ?? session('lms_interest', '')),
        'avatar'           => old('avatar',             $existingDemoUser?->avatar             ?? ''),
    ];

    // Small, friendly avatar set — swap these paths for your own asset set.
    // Nothing here is forced on the student; it's their pick.
    $avatarOptions = [
            'https://api.dicebear.com/9.x/adventurer/svg?seed=1',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=2',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=3',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=4',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=5',
            'https://api.dicebear.com/9.x/adventurer/svg?seed=6',
            'https://api.dicebear.com/9.x/fun-emoji/svg?seed=1',
            'https://api.dicebear.com/9.x/fun-emoji/svg?seed=2',
    ];
    $defaultAvatar = $rv['avatar'] ?: $avatarOptions[0];
@endphp

<div class="journey-wrap">

    <div class="page-hero mt-4">
        <span class="hero-badge"><i class="fas fa-sparkles"></i> Let's get you set up</span>
        <h1>Build Your <em>Learning Journey</em></h1>
        <p>Pick your vibe, tell us who you are, and we'll queue up a demo made for you.</p>
    </div>

    <div class="journey-card">

        <aside class="bitmoji-panel" aria-label="Choose your avatar">
            <div class="avatar-hero" id="avatarHero">
                <img id="avatarHeroImg" src="{{ asset('theme/images/avatars/' . $defaultAvatar . '.png') }}" alt="Your chosen avatar">
                <span class="avatar-online" title="Online"></span>
            </div>
            <div class="bit-bubble" id="bitMsg">
                👋 Pick an avatar that feels like you, then fill in your details!
            </div>

            <div class="avatar-picker-label">Choose your avatar</div>
            <div class="avatar-grid" id="avatarGrid">
                @foreach ($avatarOptions as $avatar)
                    <div class="avatar-option {{ $defaultAvatar === $avatar ? 'selected' : '' }}" data-avatar="{{ $avatar }}">
                        <img src="{{ $avatar }}" alt="Avatar option">
                    </div>
                @endforeach
            </div>
            <input type="hidden" name="avatar" id="avatarInput" value="{{ $defaultAvatar }}">

            <div class="bit-steps">
                <div class="bs-item active" id="bs0"><i class="fas fa-user"></i> Personal info</div>
                <div class="bs-item" id="bs1"><i class="fas fa-compass"></i> Interest area</div>
            </div>
        </aside>

        <div class="form-panel">
            <div class="form-step-badge">
                <span class="pulse-dot"></span>
                Step 1 of 4 — Onboarding
            </div>
            <h1 class="form-title">Tell Us <em>About You</em></h1>
            <p class="form-subtitle">Two quick sections and we'll queue up your demo video.</p>

            @if ($errors->any())
                <div id="server-errors" style="display:none">
                    @foreach ($errors->all() as $error)<span>{{ $error }}</span>@endforeach
                </div>
            @endif

            <form action="{{ route('lms.step1.store') }}" method="POST" id="onboardForm" novalidate>
                @csrf
                <input type="hidden" name="avatar" id="avatarInputForm" value="{{ $defaultAvatar }}">

                <div class="sec-head"><i class="fas fa-id-card"></i> Personal info</div>
                <div class="f-row">
                    <div class="fgroup">
                        <label class="flabel" for="full_name"><i class="fas fa-user"></i> Full name</label>
                        <input type="text" id="full_name" name="full_name"
                            class="finput @error('full_name') is-invalid @enderror"
                            placeholder="e.g. Riya Sharma"
                            value="{{ $rv['full_name'] }}"
                            autocomplete="name">
                        <span class="field-error" id="e_full_name">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Full name is required</span>
                        </span>
                        @error('full_name')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="fgroup">
                        <label class="flabel" for="email"><i class="fas fa-envelope"></i> Email address</label>
                        <input type="email" id="email" name="email"
                            class="finput @error('email') is-invalid @enderror"
                            placeholder="e.g. riya@email.com"
                            value="{{ $rv['email'] }}"
                            autocomplete="email">
                        <span class="field-error" id="e_email">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Valid email address required</span>
                        </span>
                        @error('email')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="f-row">
                    <div class="fgroup">
                        <label class="flabel" for="contact"><i class="fas fa-mobile-alt"></i> Mobile number</label>
                        <input type="tel" id="contact" name="contact"
                            class="finput @error('contact') is-invalid @enderror"
                            placeholder="e.g. 9876543210"
                            value="{{ $rv['contact'] }}"
                            maxlength="10" autocomplete="tel">
                        <span class="field-error" id="e_contact">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Valid 10-digit number required</span>
                        </span>
                        @error('contact')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="fgroup">
                        <label class="flabel" for="education_level"><i class="fas fa-graduation-cap"></i> Education level</label>
                        <select id="education_level" name="education_level"
                            class="finput @error('education_level') is-invalid @enderror">
                            <option value="">Select education level</option>
                            @foreach ($educationLevels as $level)
                                <option value="{{ $level->id }}" {{ $rv['education_level'] == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="field-error" id="e_education_level">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Please select your education level</span>
                        </span>
                        @error('education_level')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="sec-head" style="margin-top:1.5rem"><i class="fas fa-compass"></i> What are you into?</div>

                <div class="fgroup" style="margin-bottom:4px">
                    <div id="interest-cards-wrap">
                        <div class="card-grid" id="interestGrid">
                            @foreach ($categories as $category)
                                <div class="sel-card {{ (string) $rv['interest_area'] === (string) $category->id ? 'selected' : '' }}"
                                     data-id="{{ $category->id }}">
                                    <i class="fas fa-book"></i>
                                    <div class="sc-label">{{ $category->name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="interest_area" id="interest_area" value="{{ $rv['interest_area'] }}">
                    <span class="field-error" id="e_interest_area">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Please pick an interest area</span>
                    </span>
                    @error('interest_area')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                </div>

                <div class="tip-box">
                    <i class="fas fa-lightbulb"></i>
                    <span>We'll match your demo video to whatever you pick here — no extra steps needed.</span>
                </div>

                <div class="form-actions">
                    <a href="{{ route('lms.landing') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn-next" id="submitBtn">
                        <span id="btnLabel">Start my journey</span>
                        <div class="spinner" id="btnSpinner"></div>
                        <i class="fas fa-arrow-right" id="btnArrow"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if ($errors->any())
    Swal.fire({
        icon: 'error', title: 'Please fix the following errors',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonColor: 'var(--brand-primary)',
        confirmButtonText: 'Got it', customClass: { popup: 'swal-rounded' }
    });
    @endif
    @if (session('error'))
    Swal.fire({
        icon: 'error', title: 'Something went wrong', text: '{{ session('error') }}',
        confirmButtonColor: 'var(--brand-primary)'
    });
    @endif

    /* ── Bitmoji-style guide messages ── */
    const bitMsg = document.getElementById('bitMsg');
    const bsItems = [0, 1].map(i => document.getElementById('bs' + i));
    const messages = {
        avatar:   '✨ Nice pick! Now fill in your details below.',
        name:     '👤 Great! Now enter your email address.',
        email:    '📧 Perfect! Now enter your mobile number.',
        contact:  '📱 Great! Select your education level next.',
        edu:      '🎓 Excellent! Now tell us what you\'re into.',
        interest: '🎯 Awesome pick! Hit "Start my journey" when ready.',
    };
    function setGuide(msgKey, stepIdx) {
        if (messages[msgKey]) bitMsg.textContent = messages[msgKey];
        bsItems.forEach((el, i) => {
            el.classList.remove('active', 'done');
            if (i < stepIdx) el.classList.add('done');
            else if (i === stepIdx) el.classList.add('active');
        });
    }

    function showErr(id) {
        document.getElementById('e_' + id)?.classList.add('show');
        const inp = document.getElementById(id) || document.querySelector('[name="' + id + '"]');
        inp?.classList.add('is-invalid');
    }
    function hideErr(id) {
        document.getElementById('e_' + id)?.classList.remove('show');
        const inp = document.getElementById(id) || document.querySelector('[name="' + id + '"]');
        if (inp) { inp.classList.remove('is-invalid'); inp.classList.add('is-valid'); }
    }
    function clearValid(id) {
        const inp = document.getElementById(id) || document.querySelector('[name="' + id + '"]');
        inp?.classList.remove('is-valid');
    }

    [['full_name', 'name', 0], ['email', 'email', 0], ['contact', 'contact', 0], ['education_level', 'edu', 0]]
        .forEach(([id, msg, step]) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('focus',  () => setGuide(msg, step));
            el.addEventListener('input',  () => { clearValid(id); hideErr(id); });
            el.addEventListener('change', () => { clearValid(id); hideErr(id); });
        });

    /* ── Avatar picker ── */
    const avatarGrid    = document.getElementById('avatarGrid');
    const avatarHero     = document.getElementById('avatarHero');
    const avatarHeroImg = document.getElementById('avatarHeroImg');
    const avatarInputs  = [document.getElementById('avatarInput'), document.getElementById('avatarInputForm')];

    avatarGrid.querySelectorAll('.avatar-option').forEach(opt => {
        opt.addEventListener('click', () => {
            avatarGrid.querySelectorAll('.avatar-option').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');

            const chosen = opt.dataset.avatar;
            const newSrc = opt.querySelector('img').src;

            avatarInputs.forEach(inp => { if (inp) inp.value = chosen; });
            avatarHeroImg.src = newSrc;

            avatarHero.classList.add('just-picked');
            setTimeout(() => avatarHero.classList.remove('just-picked'), 300);

            setGuide('avatar', 0);
        });
    });

    /* ── Interest area — single-select card grid ── */
    const interestGrid  = document.getElementById('interestGrid');
    const interestInput = document.getElementById('interest_area');

    interestGrid.querySelectorAll('.sel-card').forEach(card => {
        card.addEventListener('click', () => {
            interestGrid.querySelectorAll('.sel-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            interestInput.value = card.dataset.id;
            hideErr('interest_area');
            setGuide('interest', 1);
        });
    });

    /* ── Auto-save personal fields to localStorage for convenience ── */
    ['full_name', 'email', 'contact'].forEach(name => {
        const el = document.getElementById(name);
        if (!el) return;
        const saved = localStorage.getItem('lms_' + name);
        if (saved && !el.value) el.value = saved;
        el.addEventListener('input', () => localStorage.setItem('lms_' + name, el.value));
    });

    /* ── Submit validation ── */
    document.getElementById('onboardForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let valid = true;
        const errors = [];

        const checks = [
            [() => !document.getElementById('full_name').value.trim(), 'full_name', 'Full name is required.'],
            [() => !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(document.getElementById('email').value.trim()), 'email', 'A valid email address is required.'],
            [() => !/^\d{10}$/.test(document.getElementById('contact').value.trim()), 'contact', 'Mobile number must be exactly 10 digits.'],
            [() => !document.getElementById('education_level').value, 'education_level', 'Please select your education level.'],
            [() => !interestInput.value, 'interest_area', 'Please pick an interest area.'],
        ];
        checks.forEach(([test, id, msg]) => {
            if (test()) { showErr(id); valid = false; errors.push(msg); }
            else hideErr(id);
        });

        if (!valid) {
            Swal.fire({
                icon: 'warning', title: 'Almost there!',
                html: `<ul style="text-align:left;padding-left:1.2rem;line-height:1.9">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                confirmButtonColor: 'var(--brand-primary)', confirmButtonText: 'Fix these',
                customClass: { popup: 'swal-rounded' }
            });
            return;
        }

        const btn = document.getElementById('submitBtn');
        document.getElementById('btnLabel').textContent = 'Setting up your journey…';
        document.getElementById('btnArrow').style.display = 'none';
        document.getElementById('btnSpinner').style.display = 'inline-block';
        btn.disabled = true;
        document.getElementById('onboardForm').submit();
    });
});
</script>

<style>
.swal-rounded { border-radius: 16px !important; }
.swal2-confirm, .swal2-cancel { border-radius: 10px !important; font-size: 14px !important; }
</style>
@endsection