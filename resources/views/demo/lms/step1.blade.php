@extends('demo.layout')

@section('title', 'Step 1 — Start Your Learning Journey')

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .journey-wrap {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--bg) 0%, var(--bg2) 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem 1rem 3rem;
    }

    .page-hero { text-align: center; margin-bottom: 2rem; }
    .page-hero h1 {
        font-size: 2rem; font-weight: 700;
        color: var(--text-main); letter-spacing: -0.5px;
    }
    .page-hero p { font-size: 1rem; color: var(--text-muted); margin-top: .4rem; }

    .step-progress {
        display: flex; align-items: center; justify-content: center;
        gap: 4px; margin-bottom: 2rem; flex-wrap: wrap;
    }
    .sp-item {
        display: flex; align-items: center; gap: 6px;
        font-size: 12.5px; font-weight: 500; color: var(--text-muted);
    }
    .sp-item.done  { color: var(--brand-green); }
    .sp-item.active{ color: var(--brand-secondary); }
    .sp-dot {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--line);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: var(--text-muted); flex-shrink: 0;
    }
    .sp-item.done  .sp-dot { background: color-mix(in srgb, var(--brand-green) 15%, transparent); color: var(--brand-green); }
    .sp-item.active .sp-dot { background: var(--brand-secondary); color: #fff; }
    .sp-line { width: 40px; height: 2px; background: var(--line); border-radius: 2px; }
    .sp-line.done  { background: var(--brand-green); }

    .journey-card {
        width: 100%; max-width: 1200px;
        background: var(--bg-card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        display: grid; grid-template-columns: 220px 1fr; overflow: hidden;
    }

    /* Sidebar */
    .bitmoji-panel {
        background: linear-gradient(180deg, var(--bg2) 0%, var(--bg) 100%);
        padding: 2rem 1.25rem;
        display: flex; flex-direction: column; align-items: center; gap: 1rem;
        border-right: 1px solid var(--line);
    }
    .bit-avatar-wrap { position: relative; width: 110px; height: 110px; }
    .bit-avatar-wrap img {
        width: 110px; height: 110px; border-radius: 50%;
        border: 3px solid var(--border); object-fit: cover; background: var(--line);
    }
    .bit-online {
        position: absolute; bottom: 6px; right: 6px;
        width: 14px; height: 14px; background: var(--brand-green);
        border-radius: 50%; border: 2px solid var(--bg-card);
    }
    .bit-bubble {
        background: var(--bg-card); border: 1px solid var(--line);
        border-radius: 14px; padding: .75rem 1rem;
        font-size: 12.5px; line-height: 1.55; color: var(--text); position: relative; text-align: center;
    }
    .bit-bubble::before {
        content: ''; position: absolute; top: -9px; left: 50%; transform: translateX(-50%);
        border: 5px solid transparent; border-bottom-color: var(--line);
    }
    .bit-bubble::after {
        content: ''; position: absolute; top: -7px; left: 50%; transform: translateX(-50%);
        border: 5px solid transparent; border-bottom-color: var(--bg-card);
    }
    .bit-steps { width: 100%; display: flex; flex-direction: column; gap: 4px; margin-top: .5rem; }
    .bs-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 11.5px; color: var(--text-muted);
        padding: 6px 10px; border-radius: var(--radius-sm); transition: all .2s;
    }
    .bs-item.active {
        background: color-mix(in srgb, var(--brand-secondary) 12%, transparent);
        color: var(--brand-secondary); font-weight: 600;
    }
    .bs-item.done { color: var(--brand-green); }
    .bs-item i { font-size: 14px; }

    /* Form panel */
    .form-panel { padding: 2rem 2.25rem; overflow-y: auto; }
    .form-step-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: color-mix(in srgb, var(--brand-secondary) 12%, transparent);
        color: var(--brand-secondary);
        font-size: 11.5px; font-weight: 600;
        padding: 4px 12px; border-radius: 20px; margin-bottom: .75rem;
    }
    .pulse-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--brand-secondary);
        animation: pulse 1.4s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .35; transform: scale(.65); }
    }
    .form-title { font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: .25rem; }
    .form-title em { color: var(--brand-secondary); font-style: normal; }
    .form-subtitle { font-size: .875rem; color: var(--text-muted); margin-bottom: 1.5rem; }

    .sec-head {
        display: flex; align-items: center; gap: 8px;
        font-size: 11px; font-weight: 700; color: var(--brand-secondary);
        text-transform: uppercase; letter-spacing: .07em;
        margin-bottom: .75rem; margin-top: 1.25rem;
    }
    .sec-head:first-of-type { margin-top: 0; }
    .sec-head::after { content: ''; flex: 1; height: 1px; background: var(--line); }

    .f-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .f-row.single { grid-template-columns: 1fr; }
    .fgroup { display: flex; flex-direction: column; gap: 5px; }
    .flabel { font-size: 12px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 5px; }
    .flabel i { color: var(--brand-secondary); font-size: 14px; }
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

    .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(148px, 1fr)); gap: 10px; margin-bottom: 4px; }
    .sel-card {
        border: 1.5px solid var(--line); border-radius: var(--radius-sm);
        padding: 12px 14px; cursor: pointer; background: var(--bg-card2);
        transition: all .18s; display: flex; flex-direction: column; gap: 5px; user-select: none;
    }
    .sel-card:hover { border-color: var(--border); background: var(--bg2); }
    .sel-card.selected { border-color: var(--brand-secondary); background: color-mix(in srgb, var(--brand-secondary) 10%, var(--bg-card)); }
    .sel-card.selected .sc-label { color: var(--brand-primary); }
    .sel-card i { font-size: 22px; color: var(--brand-secondary); }
    .sel-card.selected i { color: var(--brand-primary); }
    .sc-label { font-size: 13px; font-weight: 600; color: var(--text); }
    .sc-desc  { font-size: 11px; color: var(--text-muted); line-height: 1.4; }
    .sel-card.selected .sc-desc { color: var(--brand-secondary); }
    .sel-card input[type=radio] { display: none; }

    .placeholder-hint {
        font-size: 13px; color: var(--text-muted); padding: 14px; text-align: center;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        border: 1.5px dashed var(--line); border-radius: var(--radius-sm); background: var(--bg-card2);
    }

    .tip-box {
        background: color-mix(in srgb, var(--brand-secondary) 8%, var(--bg-card));
        border-left: 4px solid var(--brand-secondary);
        border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
        padding: .65rem 1rem; font-size: 12.5px; color: var(--brand-secondary);
        display: flex; align-items: flex-start; gap: 7px; margin: 1.25rem 0 1rem;
    }
    .tip-box i { flex-shrink: 0; margin-top: 1px; font-size: 15px; }

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
        background: var(--brand-secondary); border: none; border-radius: var(--radius-sm);
        font-size: 13.5px; font-weight: 600; color: #fff; cursor: pointer; transition: all .15s;
        box-shadow: 0 4px 12px color-mix(in srgb, var(--brand-secondary) 35%, transparent);
    }
    .btn-next:hover  { background: var(--brand-primary); box-shadow: 0 6px 16px color-mix(in srgb, var(--brand-secondary) 45%, transparent); }
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
        .bitmoji-panel { flex-direction: row; flex-wrap: wrap; justify-content: center; padding: 1.25rem 1rem; }
        .bit-steps { display: none; }
        .f-row { grid-template-columns: 1fr; }
        .form-panel { padding: 1.25rem; }
    }
</style>

@section('content')
{{--
    Pre-compute restore values once in PHP so JS can read them reliably
    on ANY request type (fresh GET after Back, failed-validation redirect,
    or first visit). Priority: old() [failed validation] → DB record → session.
--}}
@php
    $rv = [
        'full_name'        => old('full_name',        $existingDemoUser?->full_name           ?? ''),
        'email'            => old('email',             $existingDemoUser?->email               ?? ''),
        'contact'          => old('contact',           $existingDemoUser?->phone               ?? ''),
        'education_level'  => old('education_level',   $existingDemoUser?->education_level_id  ?? ''),
        'interest_area'    => old('interest_area',     $existingDemoUser?->interest_area_id    ?? session('lms_interest',         '')),
        'course_type_id'   => old('course_type_id',    session('lms_course_type_id',           '')),
        'course_level_id'  => old('course_level_id',   session('lms_course_level_id',          '')),
        'preferred_course' => old('preferred_course',  $existingDemoUser?->preferred_course_id ?? session('lms_preferred_course', '')),
    ];
@endphp

<div class="journey-wrap">
    <div class="stepper">@include('demo.stepper')</div>
   
    <div class="page-hero mt-4">
        <h1> Start Your Learning Journey</h1>
        <p>Tell us about yourself and we'll personalise your demo experience.</p>
    </div>

    <div class="journey-card">

        <aside class="bitmoji-panel" aria-label="Guide assistant">
            <div class="bit-avatar-wrap">
                <img src="{{ asset('theme/images/guide-step-1.png') }}" alt="Your learning guide">
                <span class="bit-online" title="Online"></span>
            </div>
            <div class="bit-bubble" id="bitMsg">
                👋 Hi! Fill in your details below and I'll personalise your learning journey just for you!
            </div>
            <div class="bit-steps">
                <div class="bs-item active" id="bs0"><i class="fas fa-user"></i> Personal info</div>
                <div class="bs-item" id="bs1"><i class="fas fa-compass"></i> Interest area</div>
                <div class="bs-item" id="bs2"><i class="fas fa-certificate"></i> Course type</div>
                <div class="bs-item" id="bs3"><i class="fas fa-chart-bar"></i> Skill level</div>
                <div class="bs-item" id="bs4"><i class="fas fa-book"></i> Choose course</div>
            </div>
        </aside>

        <div class="form-panel">
            <div class="form-step-badge">
                <span class="pulse-dot"></span>
                Step 1 of 5 — Onboarding
            </div>
            <h1 class="form-title">Build Your <em>Learning Journey</em></h1>
            <p class="form-subtitle">Complete this quick form to unlock your personalised demo.</p>

            @if ($errors->any())
                <div id="server-errors" style="display:none">
                    @foreach ($errors->all() as $error)<span>{{ $error }}</span>@endforeach
                </div>
            @endif

            <form action="{{ route('lms.step1.store') }}" method="POST" id="onboardForm" novalidate>
                @csrf

                <div class="sec-head"><i class="fas fa-id-card"></i> Personal info</div>
                <div class="f-row">
                    <div class="fgroup">
                        <label class="flabel" for="full_name"><i class="fas fa-user"></i> Full name</label>
                        <input type="text" id="full_name" name="full_name"
                            class="finput @error('full_name') is-invalid @enderror"
                            placeholder="e.g. Riya Sharma"
                            value="{{ $existingDemoUser['name'] ?? $rv['full_name'] }}"
                            autocomplete="name">
                        <span class="field-error" id="e_full_name">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="e_full_name_msg">Full name is required</span>
                        </span>
                        @error('full_name')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="fgroup">
                        <label class="flabel" for="email"><i class="fas fa-envelope"></i> Email address</label>
                        <input type="email" id="email" name="email"
                            class="finput @error('email') is-invalid @enderror"
                            placeholder="e.g. riya@email.com"
                            value="{{$existingDemoUser['email'] ?? $rv['email'] }}"
                            autocomplete="email">
                        <span class="field-error" id="e_email">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="e_email_msg">Valid email address required</span>
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
                            <span id="e_contact_msg">Valid 10-digit number required</span>
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

                <div class="sec-head" style="margin-top:1.5rem"><i class="fas fa-compass"></i> Course selection</div>

                <div class="fgroup" style="margin-bottom:14px">
                    <label class="flabel" for="interest_area"><i class="fas fa-crosshairs"></i> Interest area</label>
                    <select id="interest_area" name="interest_area"
                        class="finput @error('interest_area') is-invalid @enderror">
                        <option value="">Select your area of interest</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $rv['interest_area'] == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="field-error" id="e_interest_area">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Please select an interest area</span>
                    </span>
                    @error('interest_area')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                </div>

                <div class="fgroup" style="margin-bottom:14px">
                    <label class="flabel"><i class="fas fa-certificate"></i> Course type</label>
                    <div id="type-cards-wrap">
                        <div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select an interest area to see course types</div>
                    </div>
                    <input type="hidden" name="course_type_id" id="course_type_id" value="{{ $rv['course_type_id'] }}">
                    <span class="field-error" id="e_course_type">
                        <i class="fas fa-exclamation-circle"></i><span>Please select a course type</span>
                    </span>
                    @error('course_type_id')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                </div>

                <div class="fgroup" style="margin-bottom:14px">
                    <label class="flabel"><i class="fas fa-chart-line"></i> Skill level</label>
                    <div id="level-cards-wrap">
                        <div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select a course type first</div>
                    </div>
                    <input type="hidden" name="course_level_id" id="course_level_id" value="{{ $rv['course_level_id'] }}">
                    <span class="field-error" id="e_course_level">
                        <i class="fas fa-exclamation-circle"></i><span>Please select a skill level</span>
                    </span>
                    @error('course_level_id')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                </div>

                <div class="fgroup" style="margin-bottom:4px">
                    <label class="flabel"><i class="fas fa-book-open"></i> Preferred course</label>
                    <div id="course-cards-wrap">
                        <div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select a skill level to see available courses</div>
                    </div>
                    <input type="hidden" name="preferred_course" id="preferred_course" value="{{ $rv['preferred_course'] }}">
                    <span class="field-error" id="e_preferred_course">
                        <i class="fas fa-exclamation-circle"></i><span>Please select a preferred course</span>
                    </span>
                    @error('preferred_course')<span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>@enderror
                </div>

                <div class="tip-box">
                    <i class="fas fa-lightbulb"></i>
                    <span>Choose your course carefully — your entire demo session will be tailored to your selection!</span>
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

    /* ── 1. SERVER-SIDE ERROR / SUCCESS ALERTS ── */
    @if ($errors->any())
    Swal.fire({
        icon: 'error', title: 'Please fix the following errors',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonColor: 'var(--brand-secondary)',
        confirmButtonText: 'Got it', customClass: { popup: 'swal-rounded' }
    });
    @endif
    @if (session('success'))
    Swal.fire({
        icon: 'success', title: 'All done!', text: '{{ session('success') }}',
        confirmButtonColor: 'var(--brand-secondary)',
        timer: 2500, timerProgressBar: true, showConfirmButton: false
    });
    @endif
    @if (session('error'))
    Swal.fire({
        icon: 'error', title: 'Something went wrong', text: '{{ session('error') }}',
        confirmButtonColor: 'var(--brand-secondary)'
    });
    @endif

    /* ── 2. BITMOJI GUIDE ── */
    const bitMsg = document.getElementById('bitMsg');
    const bsItems = [0,1,2,3,4].map(i => document.getElementById('bs' + i));
    const messages = {
        name: '👤 Great! Now enter your email address.',
        email: '📧 Perfect! Now enter your mobile number.',
        contact: '📱 Great! Select your education level next.',
        edu: '🎓 Excellent! Now choose your area of interest.',
        interest: '🎯 Nice! Select the course type that suits you.',
        type: '🏅 Now pick your skill level.',
        level: '📚 Almost there! Choose your preferred course.',
        course: '🚀 You\'re all set! Hit "Start my journey" to continue.'
    };
    function setGuide(msgKey, stepIdx) {
        if (messages[msgKey]) bitMsg.textContent = messages[msgKey];
        bsItems.forEach((el, i) => {
            el.classList.remove('active', 'done');
            if (i < stepIdx) el.classList.add('done');
            else if (i === stepIdx) el.classList.add('active');
        });
    }

    /* ── 3. INLINE VALIDATION HELPERS ── */
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

    [['full_name','name',0],['email','email',0],['contact','contact',0],['education_level','edu',0]]
        .forEach(([id, msg, step]) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('focus',  () => setGuide(msg, step));
            el.addEventListener('input',  () => { clearValid(id); hideErr(id); });
            el.addEventListener('change', () => { clearValid(id); hideErr(id); });
        });

    /* ── 4. CASCADING CARD SELECTORS ── */
    const typeWrap   = document.getElementById('type-cards-wrap');
    const levelWrap  = document.getElementById('level-cards-wrap');
    const courseWrap = document.getElementById('course-cards-wrap');
    const hidType    = document.getElementById('course_type_id');
    const hidLevel   = document.getElementById('course_level_id');
    const hidCourse  = document.getElementById('preferred_course');

    const TYPE_ICONS  = { 'Basic': 'fas fa-seedling', 'Professional': 'fas fa-briefcase' };
    const LEVEL_ICONS = { 'Beginner': 'fas fa-leaf', 'Intermediate': 'fas fa-fire', 'Advanced': 'fas fa-rocket' };

    function resetAfter(from) {
        if (from <= 0) { typeWrap.innerHTML  = '<div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select an interest area to see course types</div>';  hidType.value = ''; }
        if (from <= 1) { levelWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select a course type first</div>';                    hidLevel.value = ''; }
        if (from <= 2) { courseWrap.innerHTML= '<div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select a skill level to see available courses</div>'; hidCourse.value = ''; }
    }

    function makeCardGrid(container, items, hiddenInput, onSelect) {
        const grid = document.createElement('div');
        grid.className = 'card-grid';
        items.forEach(item => {
            const card = document.createElement('div');
            card.className = 'sel-card';
            card.dataset.id = item.id;
            card.innerHTML = `
                <i class="${item.icon}" aria-hidden="true"></i>
                <div class="sc-label">${item.label}</div>
                ${item.desc ? `<div class="sc-desc">${item.desc}</div>` : ''}`;
            card.addEventListener('click', () => {
                grid.querySelectorAll('.sel-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                hiddenInput.value = item.id;
                hideErr(hiddenInput.name);
                onSelect(item);
            });
            if (String(hiddenInput.value) === String(item.id)) card.classList.add('selected');
            grid.appendChild(card);
        });
        container.innerHTML = '';
        container.appendChild(grid);
    }

    document.getElementById('interest_area').addEventListener('change', async function () {
        const catId = this.value;
        hideErr('interest_area');
        setGuide('interest', 1);
        resetAfter(0);
        if (!catId) return;
        typeWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-spinner fa-spin"></i> Loading course types…</div>';
        try {
            const types = await fetch(`/api/demo/course-types?category_id=${catId}`).then(r => r.json());
            if (!types.length) { typeWrap.innerHTML = '<div class="placeholder-hint">No course types available</div>'; return; }
            makeCardGrid(typeWrap, types.map(t => ({
                id: t.id, label: t.name,
                icon: TYPE_ICONS[t.name] || 'fas fa-book',
                desc: t.name === 'Basic' ? 'Short intro course (5–45 hrs)' : 'In-depth program (90–150 hrs)'
            })), hidType, item => { setGuide('type', 2); loadLevels(item.id); });
        } catch {
            typeWrap.innerHTML = '<div class="placeholder-hint" style="color:var(--danger)"><i class="fas fa-exclamation-triangle"></i> Failed to load course types</div>';
        }
    });

    async function loadLevels(typeId) {
        resetAfter(1);
        levelWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-spinner fa-spin"></i> Loading skill levels…</div>';
        try {
            const levels = await fetch(`/api/demo/course-levels?type_id=${typeId}`).then(r => r.json());
            if (!levels.length) { levelWrap.innerHTML = '<div class="placeholder-hint">No skill levels available</div>'; return; }
            makeCardGrid(levelWrap, levels.map(l => ({
                id: l.id, label: l.name,
                icon: LEVEL_ICONS[l.name] || 'fas fa-chart-bar', desc: ''
            })), hidLevel, item => {
                setGuide('level', 3);
                loadCourses(document.getElementById('interest_area').value, typeId, item.id);
            });
        } catch {
            levelWrap.innerHTML = '<div class="placeholder-hint" style="color:var(--danger)"><i class="fas fa-exclamation-triangle"></i> Failed to load skill levels</div>';
        }
    }

    async function loadCourses(categoryId, typeId, levelId) {
        resetAfter(2);
        courseWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-spinner fa-spin"></i> Loading courses…</div>';
        try {
            const courses = await fetch(`/api/demo/courses?category_id=${categoryId}&type_id=${typeId}&level_id=${levelId}`).then(r => r.json());
            if (!courses.length) { courseWrap.innerHTML = '<div class="placeholder-hint">No courses available for this selection</div>'; return; }
            makeCardGrid(courseWrap, courses.map(c => ({
                id: c.id, label: c.title,
                icon: 'fas fa-book-open',
                desc: c.duration_hours ? `${c.duration_hours} hrs` : ''
            })), hidCourse, item => {
                setGuide('course', 4);
                bitMsg.textContent = `🎯 Great choice! "${item.label}" is one of our most popular courses!`;
            });
        } catch {
            courseWrap.innerHTML = '<div class="placeholder-hint" style="color:var(--danger)"><i class="fas fa-exclamation-triangle"></i> Failed to load courses</div>';
        }
    }

    /* ── 5. FORM SUBMIT VALIDATION ── */
    document.getElementById('onboardForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let valid = true;
        const errors = [];

        const checks = [
            [() => !document.getElementById('full_name').value.trim(), 'full_name', 'Full name is required.'],
            [() => !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(document.getElementById('email').value.trim()), 'email', 'A valid email address is required.'],
            [() => !/^\d{10}$/.test(document.getElementById('contact').value.trim()), 'contact', 'Mobile number must be exactly 10 digits.'],
            [() => !document.getElementById('education_level').value, 'education_level', 'Please select your education level.'],
            [() => !document.getElementById('interest_area').value, 'interest_area', 'Please select an interest area.'],
            [() => !hidType.value,   'course_type',      'Please select a course type.'],
            [() => !hidLevel.value,  'course_level',     'Please select a skill level.'],
            [() => !hidCourse.value, 'preferred_course', 'Please select a preferred course.'],
        ];
        checks.forEach(([test, id, msg]) => {
            if (test()) { showErr(id); valid = false; errors.push(msg); }
            else hideErr(id);
        });

        if (!valid) {
            Swal.fire({
                icon: 'warning', title: 'Almost there!',
                html: `<ul style="text-align:left;padding-left:1.2rem;line-height:1.9">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                confirmButtonColor: 'var(--brand-secondary)', confirmButtonText: 'Fix these',
                customClass: { popup: 'swal-rounded' }
            });
            return;
        }

        Swal.fire({
            icon: 'question', title: 'Ready to start?',
            html: `<p style="color:var(--text)">You've selected <strong>${document.getElementById('interest_area').selectedOptions[0]?.text}</strong>.<br>Shall we continue to your demo?</p>`,
            showCancelButton: true,
            confirmButtonColor: 'var(--brand-secondary)', cancelButtonColor: 'var(--text-muted)',
            confirmButtonText: '<i class="fas fa-arrow-right"></i> Yes, continue!',
            cancelButtonText: 'Review again',
        }).then(result => {
            if (result.isConfirmed) {
                const btn = document.getElementById('submitBtn');
                document.getElementById('btnLabel').textContent = 'Setting up your journey…';
                document.getElementById('btnArrow').style.display = 'none';
                document.getElementById('btnSpinner').style.display = 'inline-block';
                btn.disabled = true;
                document.getElementById('onboardForm').submit();
            }
        });
    });

    /* ── 6. AUTO-SAVE TO localStorage ── */
    ['full_name', 'email', 'contact'].forEach(name => {
        const el = document.getElementById(name);
        if (!el) return;
        const saved = localStorage.getItem('lms_' + name);
        if (saved && !el.value) el.value = saved;
        el.addEventListener('input', () => localStorage.setItem('lms_' + name, el.value));
    });

    /* ── 7. RESTORE CASCADE FROM PHP-ECHOED VALUES ──
       These come from $rv (computed in @php above), which already
       merged old() / DB record / session in the right priority order.
       The hidden inputs are pre-filled server-side; we just need to
       drive the AJAX cascade so the card grids render and show the
       correct selection visually.
    ── */
    const restoreCategoryId = '{{ $rv['interest_area'] }}';
    const restoreTypeId     = '{{ $rv['course_type_id'] }}';
    const restoreLevelId    = '{{ $rv['course_level_id'] }}';
    const restoreCourseId   = '{{ $rv['preferred_course'] }}';

    function waitForCards(container, targetId) {
        return new Promise(resolve => {
            if (!targetId) { resolve(false); return; }
            let tries = 0;
            const iv = setInterval(() => {
                const hasCards   = container.querySelector('.sel-card');
                const isLoading  = container.querySelector('.fa-spinner');
                if (hasCards && !isLoading) { clearInterval(iv); resolve(true); return; }
                if (++tries >= 30)          { clearInterval(iv); resolve(false); }
            }, 100);
        });
    }

    function clickCard(wrap, id) {
        const card = id ? wrap.querySelector(`.sel-card[data-id="${id}"]`) : null;
        if (card) { card.click(); return true; }
        return false;
    }

    async function restoreFullSelection() {
        if (!restoreCategoryId) return;

        // Stage 1 — set the interest area dropdown (already set by server-side `selected`,
        // but we still need to kick off the type-cards fetch).
        document.getElementById('interest_area').value = restoreCategoryId;

        if (!restoreTypeId) {
            // User only filled personal info last time — load types so they can continue.
            document.getElementById('interest_area').dispatchEvent(new Event('change'));
            return;
        }

        // Fetch types manually (same logic as the change handler) so we can await it.
        typeWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-spinner fa-spin"></i> Loading course types…</div>';
        try {
            const types = await fetch(`/api/demo/course-types?category_id=${restoreCategoryId}`).then(r => r.json());
            if (!types.length) { typeWrap.innerHTML = '<div class="placeholder-hint">No course types available</div>'; return; }

            makeCardGrid(typeWrap, types.map(t => ({
                id: t.id, label: t.name,
                icon: TYPE_ICONS[t.name] || 'fas fa-book',
                desc: t.name === 'Basic' ? 'Short intro course (5–45 hrs)' : 'In-depth program (90–150 hrs)'
            })), hidType, item => { setGuide('type', 2); loadLevels(item.id); });

            // Click the previously chosen type — its onSelect calls loadLevels().
            if (!clickCard(typeWrap, restoreTypeId)) return;

            // Wait for level cards to appear, then click the right one.
            if (!(await waitForCards(levelWrap, restoreLevelId))) return;
            if (!clickCard(levelWrap, restoreLevelId)) return;

            // Wait for course cards, then click the right one.
            if (!(await waitForCards(courseWrap, restoreCourseId))) return;
            clickCard(courseWrap, restoreCourseId);

        } catch {
            typeWrap.innerHTML = '<div class="placeholder-hint" style="color:var(--danger)"><i class="fas fa-exclamation-triangle"></i> Failed to restore your previous selection</div>';
        }
    }

    restoreFullSelection();
});
</script>

<style>
.swal-rounded { border-radius: 16px !important; }
.swal2-confirm, .swal2-cancel { border-radius: 10px !important; font-size: 14px !important; }
</style>
@endsection