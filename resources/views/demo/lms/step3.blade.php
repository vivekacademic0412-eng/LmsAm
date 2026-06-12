@extends('demo.layout')


@section('title', 'Create Your Demo')


@section('bitmoji-message',
    '🧠 Now it\'s YOUR turn! Don\'t worry — just explain what you understood in your own words.
    You\'ve got this!')
@section('bitmoji-emoji', '💪')

@section('content')
    <div class="step-badge">
        <div class="dot-pulse"></div>
        Step 3 of 5 — Create Your Demo
    </div>

    <h1>Create Your <em>First Demo</em></h1>
    <p class="subtitle">Record a short video showing what you've learned. No perfection required — just your understanding!
    </p>

    <div class="card">

        {{-- ── Instruction Steps ── --}}
        <div class="section-title"><i class="fas fa-list-check" style="color:var(--brand-primary)"></i> What You Need To Do
        </div>
        <div class="mini-steps">
            <div class="mini-step">
                <div class="mini-step-num">1</div>
                <div class="mini-step-text">
                    <strong>Pick your topic below</strong>
                    <span>Choose a topic from {{ session('lms_course_label', 'your selected course') }} to demo</span>
                </div>
            </div>
            <div class="mini-step">
                <div class="mini-step-num">2</div>
                <div class="mini-step-text">
                    <strong>Record your video (1–5 minutes)</strong>
                    <span>Use screen recorder, phone camera, or the tool below — whatever works for you</span>
                </div>
            </div>
            <div class="mini-step">
                <div class="mini-step-num">3</div>
                <div class="mini-step-text">
                    <strong>Write a short description</strong>
                    <span>2–3 lines explaining what you demonstrated in your video</span>
                </div>
            </div>
            <div class="mini-step">
                <div class="mini-step-num">4</div>
                <div class="mini-step-text">
                    <strong>Upload &amp; submit</strong>
                    <span>Hit submit and we'll review your demo and move you forward!</span>
                </div>
            </div>
        </div>

        {{-- ── Recommended Tool ── --}}
        <div class="section-title"><i class="fas fa-tools" style="color:var(--brand-primary)"></i> Recommended Recording
            Tool</div>
        <div class="tool-card">
            <div class="tool-icon">🎬</div>
            <div class="tool-info">
                <h3>Screen + Camera Recorder</h3>
                <p>Free, browser-based — no download needed. Record your screen or webcam in one click.</p>
            </div>
            <a href="https://screenapp.io" target="_blank" class="btn-tool">
                <i class="fas fa-external-link-alt"></i> Open Tool
            </a>
        </div>

        <form action="{{ route('lms.step3.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
            @csrf

            {{-- ── Topic Field ── --}}
            <div class="form-group">
                <label><i class="fas fa-lightbulb" style="color:var(--brand-primary); margin-right:5px"></i> Demo
                    Topic</label>
                <input type="text" name="demo_topic" id="demoTopic" class="form-control"
                    placeholder="e.g. How to create a landing page in HTML..." value="{{ old('demo_topic') }}">
                @error('demo_topic')
                    <p class="field-tip" style="color:var(--brand-secondary)"><i class="fas fa-exclamation-circle"></i>
                        {{ $message }}</p>
                @enderror
                <div class="topic-suggestions">
                    @php
                        $courseId = session('lms_course_id');

                        $topics = \App\Models\Course::where('category_id', $courseId)->pluck('title')->toArray();
                    @endphp
                    @foreach ($topics as $t)
                        <div class="topic-pill" onclick="setTopic(this)">{{ $t }}</div>
                    @endforeach
                </div>
            </div>

            {{-- ── Description Field ── --}}
            <div class="form-group">
                <label><i class="fas fa-align-left" style="color:var(--brand-primary); margin-right:5px"></i> What You
                    Learned / What You Demonstrated</label>
                <textarea name="demo_description" id="demoDesc" class="form-control"
                    placeholder="Describe what you explained in your video. For example: 'I demonstrated how to create a responsive navbar using HTML and CSS Flexbox. I explained the mobile-first approach...'"
                    maxlength="600">{{ old('demo_description') }}</textarea>
                <div class="char-count"><span id="charCount">0</span>/600 characters</div>
                @error('demo_description')
                    <p class="field-tip" style="color:var(--brand-secondary)"><i class="fas fa-exclamation-circle"></i>
                        {{ $message }}</p>
                @enderror
            </div>

            {{-- ── Video Upload ── --}}
            <div class="form-group">
                <label><i class="fas fa-video" style="color:var(--brand-primary); margin-right:5px"></i> Upload Your Demo
                    Video</label>
                <div class="upload-zone" id="uploadZone">
                    <input type="file" name="demo_video" id="videoFile" accept="video/*">
                    <div class="upload-icon">🎥</div>
                    <div class="upload-title">Drop your video here or <span style="color:var(--brand-primary)">browse</span>
                    </div>
                    <div class="upload-sub">Your recorded demo video (any format)</div>
                    <div class="upload-formats">
                        <span class="fmt-tag">MP4</span>
                        <span class="fmt-tag">MOV</span>
                        <span class="fmt-tag">AVI</span>
                        <span class="fmt-tag">WEBM</span>
                        <span class="fmt-tag">Max 200MB</span>
                    </div>
                </div>
                <div class="upload-preview" id="uploadPreview">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong id="uploadFilename" style="font-size:0.88rem; color:var(--text-main)">video.mp4</strong>
                        <span style="font-size:0.78rem; color:var(--text-muted); display:block">Ready to submit</span>
                    </div>
                </div>
                @error('demo_video')
                    <p class="field-tip" style="color:var(--brand-secondary)"><i class="fas fa-exclamation-circle"></i>
                        {{ $message }}</p>
                @enderror
            </div>

            <div class="tip-box">
                <strong>🤖 Guide Tip:</strong> Don't overthink it! Even a 1-minute video explaining the concept in your own
                words is perfect. We're evaluating understanding, not production quality.
            </div>

            <div class="btn-group">
                <a href="{{ route('lms.step2') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn-primary" id="submitDemoBtn">
                    <span id="submitBtnText">Submit My Demo</span>
                    <i class="fas fa-paper-plane" id="submitBtnIcon"></i>
                </button>
            </div>
        </form>

    </div>
@endsection
@section('scripts')
<script>
// ── Topic pill ──────────────────────────────────────────────
function setTopic(el) {
    document.getElementById('demoTopic').value = el.textContent;
    document.querySelectorAll('.topic-pill').forEach(p => {
        p.style.borderColor = 'var(--border)';
        p.style.color = '';
    });
    el.style.borderColor = 'var(--brand-primary)';
    el.style.color = '#A78BFA';
}

// ── Char counter ────────────────────────────────────────────
const desc = document.getElementById('demoDesc');
if (desc) {
    desc.addEventListener('input', () => {
        document.getElementById('charCount').textContent = desc.value.length;
    });
    document.getElementById('charCount').textContent = desc.value.length;
}

// ── Upload preview ──────────────────────────────────────────
document.getElementById('videoFile').addEventListener('change', function () {
    if (this.files[0]) {
        if (this.files[0].size > 512 * 1024 * 1024) {
            alert('Max file size is 500MB');
            this.value = '';
            return;
        }
        document.getElementById('uploadFilename').textContent = this.files[0].name;
        document.getElementById('uploadPreview').classList.add('visible');
        document.getElementById('uploadZone').style.borderColor = 'var(--brand-green)';
        showBitmoji('📤 Video selected! Fill in the description and hit submit!');
    }
});

// ── Drag & drop ─────────────────────────────────────────────
const zone = document.getElementById('uploadZone');
zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('videoFile').files = dt.files;
        document.getElementById('videoFile').dispatchEvent(new Event('change'));
    }
});

// ── Bitmoji helper ──────────────────────────────────────────
function showBitmoji(msg) {
    const el = document.getElementById('bitmojiMsg');
    if (!el) return;
    el.textContent = msg;
    el.style.display = 'block';
    setTimeout(() => el.style.display = 'none', 5000);
}

// ── Clear all inline validation errors ─────────────────────
function clearErrors() {
    document.querySelectorAll('.lms-field-error').forEach(el => el.remove());
    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('input-error'));
}

// ── Show a validation error under the right element ─────────
function showError(fieldName, message) {
    // Walk up from the named input to its nearest .form-group
    const input = document.querySelector(`[name="${fieldName}"]`);
    // For file input it lives inside .upload-zone inside .form-group
    const group = input
        ? (input.closest('.form-group') ?? input.parentElement)
        : null;

    if (!group) return;

    const p = document.createElement('p');
    p.className = 'field-tip lms-field-error';
    p.style.color = 'var(--brand-secondary)';
    p.style.marginTop = '6px';
    p.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    group.appendChild(p);

    if (input) input.classList.add('input-error');
}

// ── SINGLE submit handler ───────────────────────────────────
document.getElementById('createForm').addEventListener('submit', function (e) {
    e.preventDefault();   // ← always prevent default; we handle redirect in JS

    clearErrors();

    const btn      = document.getElementById('submitDemoBtn');
    const btnText  = document.getElementById('submitBtnText');
    const btnIcon  = document.getElementById('submitBtnIcon');

    // Loading state
    btn.disabled       = true;
    btn.style.opacity  = '0.75';
    btnText.textContent = 'Uploading…';
    btnIcon.className   = 'fas fa-spinner fa-spin';

    const formData   = new FormData(this);
    const csrfToken  = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch("{{ route('lms.step3.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':     csrfToken,
            'X-Requested-With': 'XMLHttpRequest',   // ← tells Laravel it's AJAX
            'Accept':           'application/json',
        },
        body: formData,
    })
    .then(async response => {
        const data = await response.json();

        if (response.ok && data.status) {
            // ✅ Success — redirect to step 4
            btnText.textContent = 'Redirecting…';
            window.location.href = data.redirect_url;
            return;
        }

        // ❌ Reset button
        btn.disabled       = false;
        btn.style.opacity  = '1';
        btnText.textContent = 'Submit My Demo';
        btnIcon.className   = 'fas fa-paper-plane';

        if (response.status === 422 && data.errors) {
            // Show each validation error under the correct field
            Object.entries(data.errors).forEach(([field, messages]) => {
                showError(field, messages[0]);
            });
            // Scroll to first error
            const firstErr = document.querySelector('.lms-field-error');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // General server error
        showBitmoji('❌ ' + (data.message || 'Something went wrong. Try again.'));
    })
    .catch(err => {
        console.error('AJAX Error:', err);
        btn.disabled       = false;
        btn.style.opacity  = '1';
        btnText.textContent = 'Submit My Demo';
        btnIcon.className   = 'fas fa-paper-plane';
        showBitmoji('❌ Network error. Check your connection and try again.');
    });
});
</script>
@endsection
