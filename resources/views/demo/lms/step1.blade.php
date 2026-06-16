@extends('demo.layout')

@section('title', 'Step 1 — Start Your Learning Journey')


<style>
    /* ── Reset & base ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .journey-wrap {
        min-height: 100vh;
        background: linear-gradient(135deg, #f0eeff 0%, #e8f0ff 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem 1rem 3rem;
    }

    /* ── Page header ── */
    .page-hero {
        text-align: center;
        margin-bottom: 2rem;
    }
    .page-hero h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e1b4b;
        letter-spacing: -0.5px;
    }
    .page-hero p {
        font-size: 1rem;
        color: #6b7280;
        margin-top: .4rem;
    }

    /* ── Step progress bar ── */
    .step-progress {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 4px;
    }
    .sp-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 500;
        color: #9ca3af;
    }
    .sp-item.done { color: #059669; }
    .sp-item.active { color: #534ab7; }
    .sp-dot {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: #e5e7eb;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #9ca3af;
        flex-shrink: 0;
    }
    .sp-item.done .sp-dot { background: #d1fae5; color: #059669; }
    .sp-item.active .sp-dot { background: #534ab7; color: #fff; }
    .sp-line { width: 40px; height: 2px; background: #e5e7eb; border-radius: 2px; }
    .sp-line.done { background: #059669; }

    /* ── Main card layout ── */
    .journey-card {
        width: 100%;
        max-width: 1200px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(83,74,183,.10);
        display: grid;
        grid-template-columns: 220px 1fr;
        overflow: hidden;
    }

    /* ── Bitmoji sidebar ── */
    .bitmoji-panel {
        background: linear-gradient(180deg, #eeedfe 0%, #e0dbff 100%);
        padding: 2rem 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        border-right: 1px solid #d4cef7;
    }
    .bit-avatar-wrap {
        position: relative;
        width: 110px; height: 110px;
    }
    .bit-avatar-wrap img {
        width: 110px; height: 110px;
        border-radius: 50%;
        border: 3px solid #a89ef0;
        object-fit: cover;
        background: #d4cef7;
    }
    .bit-online {
        position: absolute;
        bottom: 6px; right: 6px;
        width: 14px; height: 14px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    .bit-bubble {
        background: #fff;
        border: 1px solid #d4cef7;
        border-radius: 14px;
        padding: .75rem 1rem;
        font-size: 12.5px;
        line-height: 1.55;
        color: #374151;
        position: relative;
        text-align: center;
    }
    .bit-bubble::before {
        content: '';
        position: absolute;
        top: -9px; left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-bottom-color: #d4cef7;
    }
    .bit-bubble::after {
        content: '';
        position: absolute;
        top: -7px; left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-bottom-color: #fff;
    }
    .bit-steps {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: .5rem;
    }
    .bs-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.5px;
        color: #6b7280;
        padding: 6px 10px;
        border-radius: 8px;
        transition: all .2s;
    }
    .bs-item.active {
        background: rgba(83,74,183,.12);
        color: #534ab7;
        font-weight: 600;
    }
    .bs-item.done {
        color: #059669;
    }
    .bs-item i { font-size: 14px; }

    /* ── Form panel ── */
    .form-panel {
        padding: 2rem 2.25rem;
        overflow-y: auto;
    }
    .form-step-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eeedfe;
        color: #534ab7;
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: .75rem;
    }
    .pulse-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #534ab7;
        animation: pulse 1.4s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .35; transform: scale(.65); }
    }
    .form-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: .25rem;
    }
    .form-title em { color: #534ab7; font-style: normal; }
    .form-subtitle {
        font-size: .875rem;
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    /* ── Section divider ── */
    .sec-head {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #534ab7;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: .75rem;
        margin-top: 1.25rem;
    }
    .sec-head:first-of-type { margin-top: 0; }
    .sec-head::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #ebe9ff;
    }

    /* ── Form rows ── */
    .f-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }
    .f-row.single { grid-template-columns: 1fr; }
    .fgroup { display: flex; flex-direction: column; gap: 5px; }
    .flabel {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .flabel i { color: #7f77dd; font-size: 14px; }
    .finput {
        height: 40px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 0 12px;
        font-size: 13.5px;
        color: #111827;
        background: #fafaf9;
        outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
        width: 100%;
    }
    .finput:focus {
        border-color: #7f77dd;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(127,119,221,.15);
    }
    .finput.is-invalid {
        border-color: #ef4444;
        background: #fff8f8;
    }
    .finput.is-valid {
        border-color: #22c55e;
        background: #f0fdf4;
    }
    select.finput { cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpath stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px;
    }
    .field-error {
        font-size: 11.5px;
        color: #ef4444;
        display: none;
        align-items: center;
        gap: 4px;
    }
    .field-error.show { display: flex; }
    .field-error i { font-size: 13px; }

    /* Laravel validation errors */
    .server-error {
        font-size: 11.5px;
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    /* ── Card selectors (type / level / course) ── */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(148px, 1fr));
        gap: 10px;
        margin-bottom: 4px;
    }
    .sel-card {
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        cursor: pointer;
        background: #fafaf9;
        transition: all .18s;
        display: flex;
        flex-direction: column;
        gap: 5px;
        user-select: none;
    }
    .sel-card:hover { border-color: #a89ef0; background: #f5f3ff; }
    .sel-card.selected { border-color: #534ab7; background: #eeedfe; }
    .sel-card.selected .sc-label { color: #3c3489; }
    .sel-card i { font-size: 22px; color: #7f77dd; }
    .sel-card.selected i { color: #3c3489; }
    .sc-label { font-size: 13px; font-weight: 600; color: #374151; }
    .sc-desc { font-size: 11px; color: #9ca3af; line-height: 1.4; }
    .sel-card.selected .sc-desc { color: #6d5fba; }
    .sel-card input[type=radio] { display: none; }

    .placeholder-hint {
        font-size: 13px;
        color: #9ca3af;
        padding: 14px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: 1.5px dashed #e5e7eb;
        border-radius: 12px;
        background: #fafaf9;
    }

    /* ── Tip box ── */
    .tip-box {
        background: #f5f3ff;
        border-left: 4px solid #7f77dd;
        border-radius: 0 10px 10px 0;
        padding: .65rem 1rem;
        font-size: 12.5px;
        color: #534ab7;
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin: 1.25rem 0 1rem;
    }
    .tip-box i { flex-shrink: 0; margin-top: 1px; font-size: 15px; }

    /* ── Action buttons ── */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: .75rem;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
    }
    .btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        height: 42px; padding: 0 20px;
        border: 1.5px solid #d1d5db;
        border-radius: 10px;
        background: #fff;
        font-size: 13.5px; font-weight: 500; color: #374151;
        cursor: pointer; text-decoration: none;
        transition: all .15s;
    }
    .btn-back:hover { background: #f9fafb; border-color: #9ca3af; }
    .btn-next {
        display: inline-flex; align-items: center; gap: 8px;
        height: 42px; padding: 0 28px;
        background: #534ab7;
        border: none; border-radius: 10px;
        font-size: 13.5px; font-weight: 600; color: #fff;
        cursor: pointer;
        transition: all .15s;
        box-shadow: 0 4px 12px rgba(83,74,183,.3);
    }
    .btn-next:hover { background: #3c3489; box-shadow: 0 6px 16px rgba(83,74,183,.4); }
    .btn-next:active { transform: scale(.97); }
    .btn-next:disabled { opacity: .65; cursor: not-allowed; transform: none; }

    /* ── Loading spinner ── */
    .spinner {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Responsive ── */
    @media (max-width: 700px) {
        .journey-card { grid-template-columns: 1fr; }
        .bitmoji-panel { flex-direction: row; flex-wrap: wrap; justify-content: center; padding: 1.25rem 1rem; }
        .bit-steps { display: none; }
        .f-row { grid-template-columns: 1fr; }
        .form-panel { padding: 1.25rem; }
    }
</style>


@section('content')
<div class="journey-wrap">

    {{-- Page hero --}}
    <div class="page-hero">
        <h1>🚀 Start Your Learning Journey</h1>
        <p>Tell us about yourself and we'll personalise your demo experience.</p>
    </div>


    {{-- Main card --}}
    <div class="journey-card">

        {{-- Bitmoji sidebar --}}
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

        {{-- Form --}}
        <div class="form-panel">
            <div class="form-step-badge">
                <span class="pulse-dot"></span>
                Step 1 of 5 — Onboarding
            </div>
            <h1 class="form-title">Build Your <em>Learning Journey</em></h1>
            <p class="form-subtitle">Complete this quick form to unlock your personalised demo.</p>

            {{-- Laravel server-side errors global --}}
            @if ($errors->any())
                <div id="server-errors" style="display:none">
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('lms.step1.store') }}" method="POST" id="onboardForm" novalidate>
                @csrf

                {{-- ── Personal info ── --}}
                <div class="sec-head"><i class="fas fa-id-card"></i> Personal info</div>
                <div class="f-row">
                    <div class="fgroup">
                        <label class="flabel" for="full_name">
                            <i class="fas fa-user"></i> Full name
                        </label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            class="finput @error('full_name') is-invalid @enderror"
                            placeholder="e.g. Riya Sharma"
                            value="{{ old('full_name') }}"
                            autocomplete="name"
                        >
                        <span class="field-error" id="e_full_name">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="e_full_name_msg">Full name is required</span>
                        </span>
                        @error('full_name')
                            <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="fgroup">
                        <label class="flabel" for="email">
                            <i class="fas fa-envelope"></i> Email address
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="finput @error('email') is-invalid @enderror"
                            placeholder="e.g. riya@email.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                        >
                        <span class="field-error" id="e_email">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="e_email_msg">Valid email address required</span>
                        </span>
                        @error('email')
                            <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="f-row">
                    <div class="fgroup">
                        <label class="flabel" for="contact">
                            <i class="fas fa-mobile-alt"></i> Mobile number
                        </label>
                        <input
                            type="tel"
                            id="contact"
                            name="contact"
                            class="finput @error('contact') is-invalid @enderror"
                            placeholder="e.g. 9876543210"
                            value="{{ old('contact') }}"
                            maxlength="10"
                            autocomplete="tel"
                        >
                        <span class="field-error" id="e_contact">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="e_contact_msg">Valid 10-digit number required</span>
                        </span>
                        @error('contact')
                            <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="fgroup">
                        <label class="flabel" for="education_level">
                            <i class="fas fa-graduation-cap"></i> Education level
                        </label>
                        <select
                            id="education_level"
                            name="education_level"
                            class="finput @error('education_level') is-invalid @enderror"
                        >
                            <option value="">Select education level</option>
                            @foreach ($educationLevels as $level)
                                <option value="{{ $level->id }}" {{ old('education_level') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="field-error" id="e_education_level">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Please select your education level</span>
                        </span>
                        @error('education_level')
                            <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- ── Course selection ── --}}
                <div class="sec-head" style="margin-top:1.5rem"><i class="fas fa-compass"></i> Course selection</div>

                {{-- Interest area --}}
                <div class="fgroup" style="margin-bottom:14px">
                    <label class="flabel" for="interest_area">
                        <i class="fas fa-crosshairs"></i> Interest area
                    </label>
                    <select
                        id="interest_area"
                        name="interest_area"
                        class="finput @error('interest_area') is-invalid @enderror"
                    >
                        <option value="">Select your area of interest</option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                {{ old('interest_area') == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="field-error" id="e_interest_area">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Please select an interest area</span>
                    </span>
                    @error('interest_area')
                        <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Course type cards --}}
                <div class="fgroup" style="margin-bottom:14px">
                    <label class="flabel">
                        <i class="fas fa-certificate"></i> Course type
                    </label>
                    <div id="type-cards-wrap">
                        <div class="placeholder-hint">
                            <i class="fas fa-hand-point-up"></i>
                            Select an interest area to see course types
                        </div>
                    </div>
                    <input type="hidden" name="course_type_id" id="course_type_id" value="{{ old('course_type_id') }}">
                    <span class="field-error" id="e_course_type">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Please select a course type</span>
                    </span>
                    @error('course_type_id')
                        <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Skill level cards --}}
                <div class="fgroup" style="margin-bottom:14px">
                    <label class="flabel">
                        <i class="fas fa-chart-line"></i> Skill level
                    </label>
                    <div id="level-cards-wrap">
                        <div class="placeholder-hint">
                            <i class="fas fa-hand-point-up"></i>
                            Select a course type first
                        </div>
                    </div>
                    <input type="hidden" name="course_level_id" id="course_level_id" value="{{ old('course_level_id') }}">
                    <span class="field-error" id="e_course_level">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Please select a skill level</span>
                    </span>
                    @error('course_level_id')
                        <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Preferred course cards --}}
                <div class="fgroup" style="margin-bottom:4px">
                    <label class="flabel">
                        <i class="fas fa-book-open"></i> Preferred course
                    </label>
                    <div id="course-cards-wrap">
                        <div class="placeholder-hint">
                            <i class="fas fa-hand-point-up"></i>
                            Select a skill level to see available courses
                        </div>
                    </div>
                    <input type="hidden" name="preferred_course" id="preferred_course" value="{{ old('preferred_course') }}">
                    <span class="field-error" id="e_preferred_course">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Please select a preferred course</span>
                    </span>
                    @error('preferred_course')
                        <span class="server-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Tip --}}
                <div class="tip-box">
                    <i class="fas fa-lightbulb"></i>
                    <span>Choose your course carefully — your entire demo session will be tailored to your selection!</span>
                </div>

                {{-- Actions --}}
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ══════════════════════════════════════════
       1. SHOW SERVER-SIDE ERRORS VIA SWEETALERT
    ══════════════════════════════════════════ */
    @if ($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Please fix the following errors',
        html: `{!! implode('<br>', $errors->all()) !!}`,
        confirmButtonColor: '#534ab7',
        confirmButtonText: 'Got it',
        customClass: { popup: 'swal-rounded' }
    });
    @endif

    @if (session('success'))
    Swal.fire({
        icon: 'success',
        title: 'All done!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#534ab7',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
    });
    @endif

    @if (session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Something went wrong',
        text: '{{ session('error') }}',
        confirmButtonColor: '#534ab7'
    });
    @endif

    /* ══════════════════════════════════════════
       2. BITMOJI GUIDE MESSAGES
    ══════════════════════════════════════════ */
    const bitMsg = document.getElementById('bitMsg');
    const bsItems = [document.getElementById('bs0'), document.getElementById('bs1'),
                     document.getElementById('bs2'), document.getElementById('bs3'),
                     document.getElementById('bs4')];

    const messages = {
        name:     '👤 Great! Now enter your email address.',
        email:    '📧 Perfect! Now enter your mobile number.',
        contact:  '📱 Great! Select your education level next.',
        edu:      '🎓 Excellent! Now choose your area of interest.',
        interest: '🎯 Nice! Select the course type that suits you.',
        type:     '🏅 Now pick your skill level.',
        level:    '📚 Almost there! Choose your preferred course.',
        course:   '🚀 You\'re all set! Hit "Start my journey" to continue.'
    };

    function setGuide(msgKey, stepIdx) {
        if (messages[msgKey]) bitMsg.textContent = messages[msgKey];
        bsItems.forEach((el, i) => {
            el.classList.remove('active', 'done');
            if (i < stepIdx) el.classList.add('done');
            else if (i === stepIdx) el.classList.add('active');
        });
    }

    /* ══════════════════════════════════════════
       3. INLINE VALIDATION HELPERS
    ══════════════════════════════════════════ */
    function showErr(id) {
        const el = document.getElementById('e_' + id);
        if (el) el.classList.add('show');
        const inp = document.getElementById(id) || document.querySelector('[name="' + id + '"]');
        if (inp) inp.classList.add('is-invalid');
    }
    function hideErr(id) {
        const el = document.getElementById('e_' + id);
        if (el) el.classList.remove('show');
        const inp = document.getElementById(id) || document.querySelector('[name="' + id + '"]');
        if (inp) { inp.classList.remove('is-invalid'); inp.classList.add('is-valid'); }
    }
    function clearValid(id) {
        const inp = document.getElementById(id) || document.querySelector('[name="' + id + '"]');
        if (inp) inp.classList.remove('is-valid');
    }

    /* Live field focus / guide */
    const fieldGuides = [
        { id: 'full_name', msg: 'name', step: 0 },
        { id: 'email',     msg: 'email', step: 0 },
        { id: 'contact',   msg: 'contact', step: 0 },
        { id: 'education_level', msg: 'edu', step: 0 },
    ];
    fieldGuides.forEach(({ id, msg, step }) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('focus', () => setGuide(msg, step));
        el.addEventListener('input', () => { clearValid(id); hideErr(id); });
        el.addEventListener('change', () => { clearValid(id); hideErr(id); });
    });

    /* ══════════════════════════════════════════
       4. CASCADING SELECTORS
          interest_area → course_type → course_level → course
    ══════════════════════════════════════════ */
    const typeWrap   = document.getElementById('type-cards-wrap');
    const levelWrap  = document.getElementById('level-cards-wrap');
    const courseWrap = document.getElementById('course-cards-wrap');
    const hidType    = document.getElementById('course_type_id');
    const hidLevel   = document.getElementById('course_level_id');
    const hidCourse  = document.getElementById('preferred_course');

    const TYPE_ICONS = {
        'Basic': 'fas fa-seedling',
        'Professional': 'fas fa-briefcase',
    };
    const LEVEL_ICONS = {
        'Beginner': 'fas fa-leaf',
        'Intermediate': 'fas fa-fire',
        'Advanced': 'fas fa-rocket',
    };

    function resetAfter(from) {
        if (from <= 0) { // reset types
            typeWrap.innerHTML  = '<div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select an interest area to see course types</div>';
            hidType.value = '';
        }
        if (from <= 1) {
            levelWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select a course type first</div>';
            hidLevel.value = '';
        }
        if (from <= 2) {
            courseWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-hand-point-up"></i> Select a skill level to see available courses</div>';
            hidCourse.value = '';
        }
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
                ${item.desc ? `<div class="sc-desc">${item.desc}</div>` : ''}
            `;
            card.addEventListener('click', () => {
                grid.querySelectorAll('.sel-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                hiddenInput.value = item.id;
                hideErr(hiddenInput.name);
                onSelect(item);
            });
            // Restore old selection
            if (hiddenInput.value == item.id) {
                card.classList.add('selected');
            }
            grid.appendChild(card);
        });
        container.innerHTML = '';
        container.appendChild(grid);
    }

    /* Step A: Interest area changed → fetch course types */
    document.getElementById('interest_area').addEventListener('change', async function () {
        const catId = this.value;
        hideErr('interest_area');
        setGuide('interest', 1);
        resetAfter(0);

        if (!catId) return;

        typeWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-spinner fa-spin"></i> Loading course types…</div>';

        try {
            const res = await fetch(`/api/demo/course-types?category_id=${catId}`);
            const types = await res.json();

            if (!types.length) {
                typeWrap.innerHTML = '<div class="placeholder-hint">No course types available</div>';
                return;
            }

            makeCardGrid(typeWrap, types.map(t => ({
                id: t.id,
                label: t.name,
                icon: TYPE_ICONS[t.name] || 'fas fa-book',
                desc: t.name === 'Basic' ? 'Short intro course (5–45 hrs)' : 'In-depth program (90–150 hrs)'
            })), hidType, (item) => {
                setGuide('type', 2);
                loadLevels(item.id);
            });

        } catch (e) {
            typeWrap.innerHTML = '<div class="placeholder-hint" style="color:#ef4444"><i class="fas fa-exclamation-triangle"></i> Failed to load course types</div>';
        }
    });

    /* Step B: Course type selected → fetch levels */
    async function loadLevels(typeId) {
        resetAfter(1);
        levelWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-spinner fa-spin"></i> Loading skill levels…</div>';

        try {
            const res = await fetch(`/api/demo/course-levels?type_id=${typeId}`);
            const levels = await res.json();

            if (!levels.length) {
                levelWrap.innerHTML = '<div class="placeholder-hint">No skill levels available</div>';
                return;
            }

            makeCardGrid(levelWrap, levels.map(l => ({
                id: l.id,
                label: l.name,
                icon: LEVEL_ICONS[l.name] || 'fas fa-chart-bar',
                desc: ''
            })), hidLevel, (item) => {
                setGuide('level', 3);
                loadCourses(document.getElementById('interest_area').value, typeId, item.id);
            });

        } catch (e) {
            levelWrap.innerHTML = '<div class="placeholder-hint" style="color:#ef4444"><i class="fas fa-exclamation-triangle"></i> Failed to load skill levels</div>';
        }
    }

    /* Step C: Level selected → fetch courses */
    async function loadCourses(categoryId, typeId, levelId) {
        resetAfter(2);
        courseWrap.innerHTML = '<div class="placeholder-hint"><i class="fas fa-spinner fa-spin"></i> Loading courses…</div>';

        try {
            const res = await fetch(`/api/demo/courses?category_id=${categoryId}&type_id=${typeId}&level_id=${levelId}`);
            const courses = await res.json();

            if (!courses.length) {
                courseWrap.innerHTML = '<div class="placeholder-hint">No courses available for this selection</div>';
                return;
            }

            makeCardGrid(courseWrap, courses.map(c => ({
                id: c.id,
                label: c.title,
                icon: 'fas fa-book-open',
                desc: c.duration_hours ? `${c.duration_hours} hrs` : ''
            })), hidCourse, (item) => {
                setGuide('course', 4);
                bitMsg.textContent = `🎯 Great choice! "${item.label}" is one of our most popular courses!`;
            });

        } catch (e) {
            courseWrap.innerHTML = '<div class="placeholder-hint" style="color:#ef4444"><i class="fas fa-exclamation-triangle"></i> Failed to load courses</div>';
        }
    }

    /* ══════════════════════════════════════════
       5. FORM VALIDATION ON SUBMIT
    ══════════════════════════════════════════ */
    document.getElementById('onboardForm').addEventListener('submit', function (e) {
        e.preventDefault();

        let valid = true;
        const errors = [];

        /* Full name */
        const name = document.getElementById('full_name').value.trim();
        if (!name) {
            showErr('full_name'); valid = false;
            errors.push('Full name is required.');
        } else { hideErr('full_name'); }

        /* Email */
        const email = document.getElementById('email').value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showErr('email'); valid = false;
            errors.push('A valid email address is required.');
        } else { hideErr('email'); }

        /* Phone */
        const phone = document.getElementById('contact').value.trim();
        if (!/^\d{10}$/.test(phone)) {
            showErr('contact'); valid = false;
            errors.push('Mobile number must be exactly 10 digits.');
        } else { hideErr('contact'); }

        /* Education level */
        const edu = document.getElementById('education_level').value;
        if (!edu) {
            showErr('education_level'); valid = false;
            errors.push('Please select your education level.');
        } else { hideErr('education_level'); }

        /* Interest area */
        const interest = document.getElementById('interest_area').value;
        if (!interest) {
            showErr('interest_area'); valid = false;
            errors.push('Please select an interest area.');
        } else { hideErr('interest_area'); }

        /* Course type */
        if (!hidType.value) {
            showErr('course_type'); valid = false;
            errors.push('Please select a course type.');
        } else { hideErr('course_type'); }

        /* Skill level */
        if (!hidLevel.value) {
            showErr('course_level'); valid = false;
            errors.push('Please select a skill level.');
        } else { hideErr('course_level'); }

        /* Preferred course */
        if (!hidCourse.value) {
            showErr('preferred_course'); valid = false;
            errors.push('Please select a preferred course.');
        } else { hideErr('preferred_course'); }

        /* Show SweetAlert if errors */
        if (!valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Almost there!',
                html: `<ul style="text-align:left;padding-left:1.2rem;line-height:1.9">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                confirmButtonColor: '#534ab7',
                confirmButtonText: 'Fix these',
                customClass: { popup: 'swal-rounded' }
            });
            return;
        }

        /* Confirm before submitting */
        Swal.fire({
            icon: 'question',
            title: 'Ready to start?',
            html: `<p style="color:#374151">You've selected <strong>${document.getElementById('interest_area').selectedOptions[0]?.text}</strong>.<br>Shall we continue to your demo?</p>`,
            showCancelButton: true,
            confirmButtonColor: '#534ab7',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '<i class="fas fa-arrow-right"></i> Yes, continue!',
            cancelButtonText: 'Review again',
        }).then(result => {
            if (result.isConfirmed) {
                /* Loading state */
                const btn = document.getElementById('submitBtn');
                document.getElementById('btnLabel').textContent = 'Setting up your journey…';
                document.getElementById('btnArrow').style.display = 'none';
                document.getElementById('btnSpinner').style.display = 'inline-block';
                btn.disabled = true;
                document.getElementById('onboardForm').submit();
            }
        });
    });

    /* ══════════════════════════════════════════
       6. AUTO-SAVE TO localStorage
    ══════════════════════════════════════════ */
    ['full_name', 'email', 'contact'].forEach(name => {
        const el = document.getElementById(name);
        if (!el) return;
        const saved = localStorage.getItem('lms_' + name);
        if (saved && !el.value) el.value = saved;
        el.addEventListener('input', () => localStorage.setItem('lms_' + name, el.value));
    });

    /* ══════════════════════════════════════════
       7. RESTORE STATE ON BACK (old() values)
    ══════════════════════════════════════════ */
    const oldCategoryId = '{{ old('interest_area') }}';
    const oldTypeId     = '{{ old('course_type_id') }}';
    const oldLevelId    = '{{ old('course_level_id') }}';
    const oldCourseId   = '{{ old('preferred_course') }}';

    if (oldCategoryId) {
        document.getElementById('interest_area').value = oldCategoryId;
        document.getElementById('interest_area').dispatchEvent(new Event('change'));
    }

});
</script>

<style>
/* SweetAlert customisation */
.swal-rounded { border-radius: 16px !important; }
.swal2-confirm, .swal2-cancel { border-radius: 10px !important; font-size: 14px !important; }
</style>
@endsection