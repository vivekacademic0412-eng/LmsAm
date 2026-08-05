{{-- FILE: resources/views/demo/lms/step3.blade.php --}}
@extends('demo.layout')
<style>
    .task-card {
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: var(--shadow-card);
    }

    .task-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px solid var(--line);
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .task-header h4 {
        margin: 0;
        color: var(--brand-primary);
        font-weight: 700;
    }

    .task-header a {
        color: var(--text-main);
    }

    .task-header a:hover {
        color: var(--brand-primary);
    }

    .task-body {
        padding: 20px;
        background: var(--bg-card2);
        border-radius: 12px;
        border: 1px solid var(--line);
        line-height: 1.8;
    }

    .task-header small {
        color: var(--text-muted);
    }
</style>
@section('title', 'Create Your Demo')
@section('bitmoji-message',
    '🧠 Now it\'s YOUR turn! Don\'t worry — just explain what you understood in your own words.
    You\'ve got this!')

@section('content')
    <div class="stepper">@include('demo.stepper')</div>
    <div class="explore-banner">
        <div class="banner-icon">🗺️</div>
        <div class="banner-text">
            <div class="step-badge" style="margin-bottom:10px">
                <div class="dot-pulse"></div>
                Step 3 of 5 — Create Your Demo
            </div>
            <h1>Create Your <em>First Demo</em></h1>
            <p class="subtitle">Record a short video showing what you've learned. No perfection required — just your
                understanding!</p>


        </div>
    </div>


    <div class="card">

        {{-- ── Instruction Steps ── --}}
        <div class="section-title">
            <i class="fas fa-list-check" style="color:var(--brand-primary)"></i>
            Task Submission Process
        </div>

        <div class="mini-steps">

            <div class="mini-step">
                <div class="mini-step-num">1</div>
                <div class="mini-step-text">
                    <strong>Read the Assigned Task Carefully</strong>
                    <span>First, open the assigned task above and read all the instructions before starting your
                        work.</span>
                </div>
            </div>

            <div class="mini-step">
                <div class="mini-step-num">2</div>
                <div class="mini-step-text">
                    <strong>Complete the Task</strong>
                    <span>Create the project exactly as instructed using HTML, CSS, JavaScript, Core PHP, or MySQL
                        (according to the task).</span>
                </div>
            </div>

            <div class="mini-step">
                <div class="mini-step-num">3</div>
                <div class="mini-step-text">
                    <strong>Create a ZIP File</strong>
                    <span>After completing your project, place all project files in one folder and compress it into a ZIP
                        file.</span>
                </div>
            </div>

            <div class="mini-step">
                <div class="mini-step-num">4</div>
                <div class="mini-step-text">
                    <strong>Enter Submission Details</strong>
                    <span>Enter a suitable Project Title and a short description (2–3 lines) explaining what you have
                        completed.</span>
                </div>
            </div>

            <div class="mini-step">
                <div class="mini-step-num">5</div>
                <div class="mini-step-text">
                    <strong>Upload ZIP File</strong>
                    <span>Select your project ZIP file and verify that the correct file has been chosen.</span>
                </div>
            </div>

            <div class="mini-step">
                <div class="mini-step-num">6</div>
                <div class="mini-step-text">
                    <strong>Submit Your Task</strong>
                    <span>Click the <strong>Submit Task</strong> button. Your submission will be sent for review by the
                        trainer.</span>
                </div>
            </div>

        </div>
        {{-- ── Recommended Tool ── --}}
        {{-- <div class="section-title"><i class="fas fa-tools" style="color:var(--brand-primary)"></i> Recommended Recording
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
        </div> --}}

        {{-- ✅ Show a banner if this learner already has a submission for this course --}}
        @if ($existingDemo)
            <div class="alert-info" style="margin-bottom:18px">
                <i class="fas fa-info-circle"></i>
                You already submitted a demo for this course on {{ $existingDemo->created_at->format('d M, Y') }}.
                Re-submitting below will <strong>replace</strong> your previous video and description.
            </div>
        @endif
        @if ($task)

            <div class="task-card">

                <div class="task-header">

                    <div class="w-100">

                        <a class="text-decoration-none d-flex justify-content-between align-items-center">

                            <div>
                                <h4 class="mb-1">
                                    <i class="fas fa-tasks"></i>
                                    {{ $task->title }}
                                </h4>

                                <small class="text-muted">
                                    Assigned on {{ optional($assignment->assigned_at)->format('d M Y, h:i A') }}
                                </small>
                            </div>

                            <i class="fas fa-chevron-down"></i>

                        </a>

                    </div>

                    <span class="badge bg-warning text-dark ms-3">
                        Pending
                    </span>

                </div>

                <div class="collapse mt-3" id="taskDescription">

                    <div class="task-body">

                        {!! nl2br(e($task->description)) !!}

                        @if ($task->resource_url)
                            <div class="mt-3">
                                <a href="{{ $task->resource_url }}" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-link"></i>
                                    Open Resource
                                </a>
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        @endif
        <form action="{{ route('lms.step3.store') }}" method="POST" enctype="multipart/form-data" id="createForm">
            @csrf

            {{-- ── Topic Field — pre-filled from old() OR existing DB record ── --}}
            <div class="form-group">
                <label><i class="fas fa-lightbulb" style="color:var(--brand-primary);margin-right:5px"></i> Demo
                    Task</label>
                <input type="hidden" name="demo_task_assignment_id" value="{{ $assignment->id??''}}">
                <input type="text" name="demo_topic" id="demoTopic" class="form-control"
                    placeholder="e.g. How to create a landing page in HTML..."
                    value="{{ old('demo_topic', $existingDemo->demo_topic ?? '') }}">
                @error('demo_topic')
                    <p class="field-tip" style="color:var(--brand-secondary)"><i class="fas fa-exclamation-circle"></i>
                        {{ $message }}</p>
                @enderror
                {{-- <div class="topic-suggestions">
                    @php
                        $courseId = session('lms_course_id');
                        $topics = \App\Models\Course::where('category_id', $courseId)->pluck('title')->toArray();
                    @endphp
                    @foreach ($topics as $t)
                        <div class="topic-pill" onclick="setTopic(this)">{{ $t }}</div>
                    @endforeach
                </div> --}}
            </div>

            {{-- ── Description Field — pre-filled ── --}}
            <div class="form-group">
                <label><i class="fas fa-align-left" style="color:var(--brand-primary);margin-right:5px"></i> What You
                    Learned / What You Demonstrated</label>
                <textarea name="demo_description" id="demoDesc" class="form-control"
                    placeholder="Describe what you explained in your video. For example: 'I demonstrated how to create a responsive navbar using HTML and CSS Flexbox. I explained the mobile-first approach...'"
                    maxlength="600">{{ old('demo_description', $existingDemo->demo_description ?? '') }}</textarea>
                <div class="char-count"><span id="charCount">0</span>/600 characters</div>
                @error('demo_description')
                    <p class="field-tip" style="color:var(--brand-secondary)"><i class="fas fa-exclamation-circle"></i>
                        {{ $message }}</p>
                @enderror
            </div>

            {{-- ── Video Upload — shows the existing uploaded filename if present ── --}}
            <div class="form-group">
                <label>
                    <i class="fas fa-file-archive" style="color:var(--brand-primary);margin-right:5px"></i>
                    Upload Project ZIP File
                </label>

                <div class="upload-zone" id="uploadZone">
                    <input type="file" name="demo_video" id="videoFile" accept=".zip">

                    <div class="upload-icon"></div>

                    <div class="upload-title">
                        Drop your ZIP file here or
                        <span style="color:var(--brand-primary)">browse</span>
                    </div>

                    <div class="upload-sub">
                        Upload your complete project in a ZIP file.
                    </div>

                    <div class="upload-formats">
                        <span class="fmt-tag">ZIP</span>
                        <span class="fmt-tag">Max 100 MB</span>
                    </div>
                </div>

                <div class="upload-preview {{ $existingDemo && $existingDemo->demo_video ? 'visible' : '' }}"
                    id="uploadPreview">

                    <i class="fas fa-check-circle"></i>

                    <div>
                        <strong id="uploadFilename">
                            {{ $existingDemo && $existingDemo->demo_video ? basename($existingDemo->demo_video) : 'No file selected' }}
                        </strong>

                        <span style="display:block;font-size:13px;color:var(--text-muted)">
                            {{ $existingDemo && $existingDemo->demo_video ? 'Previously uploaded — choose a new ZIP file to replace it.' : '' }}
                        </span>
                    </div>
                </div>

                @error('demo_video')
                    <p class="field-tip text-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>
            {{-- <div class="form-group">
                <label><i class="fas fa-video" style="color:var(--brand-primary);margin-right:5px"></i> Upload Your Demo
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
                        <span class="fmt-tag">Max 500MB</span>
                    </div>
                </div>

               
                <div class="upload-preview {{ $existingDemo && $existingDemo->demo_video ? 'visible' : '' }}"
                    id="uploadPreview">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong id="uploadFilename" style="font-size:0.88rem;color:var(--text-main)">
                            {{ $existingDemo && $existingDemo->demo_video ? basename($existingDemo->demo_video) : 'video.mp4' }}
                        </strong>
                        <span style="font-size:0.78rem;color:var(--text-muted);display:block">
                            {{ $existingDemo && $existingDemo->demo_video ? 'Previously uploaded — choose a new file to replace it' : 'Ready to submit' }}
                        </span>
                    </div>
                </div>

                @error('demo_video')
                    <p class="field-tip" style="color:var(--brand-secondary)"><i class="fas fa-exclamation-circle"></i>
                        {{ $message }}</p>
                @enderror

               
                @if ($existingDemo && $existingDemo->demo_video)
                    <input type="hidden" name="has_existing_video" value="1">
                @endif
            </div> --}}

            <div class="tip-box">
                <strong>🎯 Important:</strong>
                Complete the assigned task first, then compress your project folder into a <strong>.zip</strong> file.
                Upload the ZIP file along with a short description of your work and click <strong>Submit Task</strong>.
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
            document.getElementById('demoTopic').value = el.textContent.trim();
            document.querySelectorAll('.topic-pill').forEach(p => {
                p.style.borderColor = '';
                p.style.color = '';
            });
            el.style.borderColor = 'var(--brand-primary)';
            el.style.color = 'var(--brand-primary)';
        }

        // ── Char counter (init with pre-filled value length too) ─────
        const desc = document.getElementById('demoDesc');
        if (desc) {
            document.getElementById('charCount').textContent = desc.value.length;
            desc.addEventListener('input', () => {
                document.getElementById('charCount').textContent = desc.value.length;
            });
        }

        // ── Upload preview (overrides server-rendered preview on new pick) ──
        document.getElementById('videoFile').addEventListener('change', function() {
            if (!this.files[0]) return;

            if (this.files[0].size > 512 * 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Video Too Large',
                    text: 'Your video file is too large. Please keep it under 500MB.',
                    confirmButtonColor: '#0947a8'
                });
                this.value = '';
                return;
            }

            document.getElementById('uploadFilename').textContent = this.files[0].name;
            document.querySelector('#uploadPreview span').textContent = 'Ready to submit';
            document.getElementById('uploadPreview').classList.add('visible');
            document.getElementById('uploadZone').style.borderColor = 'var(--brand-green)';
            showBitmoji('📤 Video selected! Fill in the description and hit submit!');
        });

        // ── Drag & drop ─────────────────────────────────────────────
        const zone = document.getElementById('uploadZone');
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });
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
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 5000);
        }

        // ── Clear all inline validation errors ─────────────────────
        function clearErrors() {
            document.querySelectorAll('.lms-field-error').forEach(el => el.remove());
            document.querySelectorAll('.form-control').forEach(el => el.classList.remove('input-error'));
        }

        // ── Show a validation error under the right element ─────────
        function showError(fieldName, message) {
            const input = document.querySelector(`[name="${fieldName}"]`);
            const group = input ? (input.closest('.form-group') ?? input.parentElement) : null;
            if (!group) return;

            const p = document.createElement('p');
            p.className = 'field-tip lms-field-error';
            p.style.color = 'var(--brand-secondary)';
            p.style.marginTop = '6px';
            p.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            group.appendChild(p);

            if (input) input.classList.add('input-error');
        }

        // ── Friendly field label for SweetAlert title ───────────────
        const FIELD_LABELS = {
            demo_topic: 'Demo Topic',
            demo_description: 'Description',
            demo_video: 'Demo Video',
        };

        // ── SINGLE submit handler ───────────────────────────────────
        document.getElementById('createForm').addEventListener('submit', function(e) {
            e.preventDefault();

            clearErrors();

            const btn = document.getElementById('submitDemoBtn');
            const btnText = document.getElementById('submitBtnText');
            const btnIcon = document.getElementById('submitBtnIcon');

            // ✅ Quick client-side pre-check so the user gets instant feedback
            // before waiting on the (potentially slow) video upload.
            const topic = document.getElementById('demoTopic').value.trim();
            const description = document.getElementById('demoDesc').value.trim();
            const hasExistingVideo = document.querySelector('[name="has_existing_video"]') !== null;
            const fileChosen = document.getElementById('videoFile').files.length > 0;

            if (!topic) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Title',
                    text: 'Please enter a topic for your demo.',
                    confirmButtonColor: '#0947a8'
                });
                return;
            }
            if (description.length < 30) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Description Too Short',
                    text: 'Please write at least 30 characters describing what you demonstrated.',
                    confirmButtonColor: '#0947a8'
                });
                return;
            }
            if (!fileChosen && !hasExistingVideo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Zip Required',
                    text: 'Please upload your demo zip before submitting.',
                    confirmButtonColor: '#0947a8'
                });
                return;
            }

            // Loading state
            btn.disabled = true;
            btn.style.opacity = '0.75';
            btnText.textContent = 'Uploading…';
            btnIcon.className = 'fas fa-spinner fa-spin';
            showBitmoji('⏳ Uploading your demo… this can take a moment for larger videos!');

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('lms.step3.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(async response => {
                    let data;
                    try {
                        data = await response.json();
                    } catch (parseErr) {
                        // Server returned non-JSON (e.g. a PHP fatal error page) —
                        // don't silently fail, tell the user clearly.
                        throw new Error('Unexpected server response. Please try again.');
                    }

                    if (response.ok && data.status) {
                        btnText.textContent = 'Redirecting…';
                        await Swal.fire({
                            icon: 'success',
                            title: 'Demo Submitted! 🎉',
                            text: data.message || 'Your demo was uploaded successfully.',
                            timer: 1600,
                            timerProgressBar: true,
                            showConfirmButton: false,
                        });
                        window.location.href = data.redirect_url;
                        return;
                    }

                    // ❌ Reset button
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btnText.textContent = 'Submit My Demo';
                    btnIcon.className = 'fas fa-paper-plane';

                    if (response.status === 422 && data.errors) {
                        // Show inline errors under each field
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            showError(field, messages[0]);
                        });

                        // ✅ Also surface the FIRST validation message in a SweetAlert
                        // so it's impossible to miss — especially for the video field,
                        // which sits below the fold on smaller screens.
                        const firstField = Object.keys(data.errors)[0];
                        const firstMessage = data.errors[firstField][0];

                        Swal.fire({
                            icon: 'error',
                            title: FIELD_LABELS[firstField] || 'Validation Error',
                            text: firstMessage,
                            confirmButtonColor: '#0947a8'
                        });

                        const firstErr = document.querySelector('.lms-field-error');
                        if (firstErr) firstErr.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        return;
                    }

                    // General server error (500, etc.)
                    showBitmoji('❌ ' + (data.message || 'Something went wrong. Try again.'));
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: data.message ||
                            'Something went wrong while submitting your demo. Please try again.',
                        confirmButtonColor: '#0947a8'
                    });
                })
                .catch(err => {
                    console.error('AJAX Error:', err);
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btnText.textContent = 'Submit My Demo';
                    btnIcon.className = 'fas fa-paper-plane';

                    showBitmoji('❌ Network error. Check your connection.');
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Could not reach the server. Please check your internet connection and try again.',
                        confirmButtonColor: '#0947a8'
                    });
                });
        });
    </script>
@endsection
