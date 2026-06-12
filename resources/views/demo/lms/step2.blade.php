@extends('demo.layout')


@section('title', 'Demo Session – Watch & Learn')

@section('bitmoji-message',
    '🎥 Watch the demo carefully! Pay attention to the techniques used — you\'ll recreate
    something similar soon!')
@section('bitmoji-emoji', '🤩')

@section('content')
    <div class="step-badge">
        <div class="dot-pulse"></div>
        Step 2 of 5 — Demo Session
    </div>
    <div class="course-info-bar">
        <div>
            <h1>Your <em>Demo</em> Session</h1>

            @if ($video)
                <h3 class="demo-video-title mb-2">
                    {{ $video->title }}
                </h3>

                <p class="subtitle mb-0">
                    {{ $video->description }}
                </p>
            @endif
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <div class="course-tag">
                <i class="fas fa-play-circle"></i>
                {{ session('lms_course_label', 'Web Development') }}
            </div>

            <div class="tutor-tag">
                <div class="tutor-avatar">🧑</div>
                <span>by Riya Kapoor, Student</span>
            </div>
        </div>
    </div>

    <div class="card">
        {{-- ── Video Player ── --}}

        @if ($video)
            <div class="video-section">
                <div class="video-wrap">

                    <video id="demoVideo" width="100%" controls controlsList="nodownload">
                        <source src="{{ asset('storage/' . $video->file_path) }}" type="{{ $video->file_mime }}">
                    </video>

                </div>

                <div class="watch-progress-wrap">
                    <div class="watch-progress-label">
                        <span>Video Progress</span>
                        <span class="progress-pct" id="progressPct">0% watched</span>
                    </div>
                    <div class="watch-bar-bg">
                        <div class="watch-bar-fill" id="watchFill"></div>
                    </div>
                </div>

            </div>
        @else
            <div class="alert alert-warning">
                No demo video available for this course.
            </div>
        @endif
       

            {{-- ── What to Focus On ── --}}
            <p
                style="font-family:'Sora',sans-serif; font-size:0.8rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:12px;">
                <i class="fas fa-eye" style="color:var(--brand-primary); margin-right:6px"></i> Watch For These Points
            </p>
            <div class="watch-checklist">
                <div class="checklist-item" id="chk1">
                    <div class="check-icon"><i class="fas fa-check"></i></div>
                    <div class="check-label">
                        <strong>Introduction & Topic Explanation</strong>
                        How the student introduces the topic clearly and confidently
                    </div>
                </div>
                <div class="checklist-item" id="chk2">
                    <div class="check-icon"><i class="fas fa-check"></i></div>
                    <div class="check-label">
                        <strong>Practical Demonstration</strong>
                        Hands-on walkthrough of the concept or skill being taught
                    </div>
                </div>
                <div class="checklist-item" id="chk3">
                    <div class="check-icon"><i class="fas fa-check"></i></div>
                    <div class="check-label">
                        <strong>Summary & Key Takeaways</strong>
                        How they wrap up and reinforce the learning in under 60 seconds
                    </div>
                </div>
            </div>

            {{-- ── CTA Section ── --}}
            <div class="cta-locked" id="ctaLock">
                <i class="fas fa-lock"></i>
                <span>Watch at least <strong>70% of the video</strong> to unlock the next step</span>
            </div>

            <form action="{{ route('lms.step2.store') }}" method="POST">
                @csrf
                <input type="hidden" name="video_watched" id="videoWatchedInput" value="0">
                <div class="btn-group">
                    <a href="{{ route('lms.step1') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn-primary" id="continueBtn" disabled
                        style="opacity:0.5; cursor:not-allowed">
                        <span>I've Watched the Demo</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    @endsection

    {{-- @section('scripts')
        <script>
            // Remove overlay on click
            document.getElementById('videoOverlay').addEventListener('click', function() {
                this.classList.add('hidden');
                simulateProgress();
            });

            // Simulate watch progress (in real app, use YouTube IFrame API)
            let progress = 0;
            let interval;

            function simulateProgress() {
                interval = setInterval(() => {
                    progress = Math.min(progress + 1.2, 100);
                    updateProgress(progress);
                    if (progress >= 70) unlockCta();
                    if (progress >= 100) clearInterval(interval);
                }, 600);
            }

            function updateProgress(p) {
                const pct = Math.round(p);
                document.getElementById('watchFill').style.width = pct + '%';
                document.getElementById('progressPct').textContent = pct + '% watched';
                document.getElementById('videoWatchedInput').value = pct;

                // Auto-check items
                if (p >= 30) checkItem('chk1');
                if (p >= 60) checkItem('chk2');
                if (p >= 90) checkItem('chk3');

                // Bitmoji messages
                if (p === 25) showBitmoji('🎥 Great start! Keep watching...');
                if (p === 50) showBitmoji('🧠 You\'re halfway! Pay attention to the demo technique.');
                if (p === 75) showBitmoji('🚀 Almost done! The summary section is coming up — key part!');
            }

            function checkItem(id) {
                document.getElementById(id).classList.add('checked');
            }

            function unlockCta() {
                const lock = document.getElementById('ctaLock');
                lock.classList.add('unlocked');
                lock.innerHTML = '<i class="fas fa-unlock"></i> <span>✅ Video progress sufficient — you can continue!</span>';
                const btn = document.getElementById('continueBtn');
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                showBitmoji('🎉 Awesome! You can now continue to the next step!');
            }

            function showBitmoji(msg) {
                const el = document.getElementById('bitmojiMsg');
                el.textContent = msg;
                el.style.display = 'block';
                setTimeout(() => el.style.display = 'none', 5000);
            }

            // Restore progress if returning
            const saved = parseInt(localStorage.getItem('lms_video_progress') || '0');
            if (saved > 0) {
                updateProgress(saved);
                progress = saved;
                if (saved >= 70) unlockCta();
            }
            window.addEventListener('beforeunload', () => {
                localStorage.setItem('lms_video_progress', progress);
            });
        </script>
    @endsection --}}
    @section('scripts')
<script>
const video = document.getElementById('demoVideo');

// safe PHP → JS
let progress = @json($video_details->progress_demo ?? 0);
let unlocked = false;

if (video) {

    video.addEventListener('timeupdate', function () {

        if (!video.duration) return;

        progress = (video.currentTime / video.duration) * 100;

        updateProgress(progress);

        if (progress >= 70 && !unlocked) {
            unlockCta();
            unlocked = true;
        }
    });

    video.addEventListener('ended', function () {
        progress = 100;
        updateProgress(100);

        if (!unlocked) {
            unlockCta();
            unlocked = true;
        }
    });
}

function updateProgress(p) {
    const pct = Math.round(p);

    document.getElementById('watchFill').style.width = pct + '%';
    document.getElementById('progressPct').textContent = pct + '% watched';
    document.getElementById('videoWatchedInput').value = pct;

    if (pct >= 30) checkItem('chk1');
    if (pct >= 60) checkItem('chk2');
    if (pct >= 90) checkItem('chk3');
}

function checkItem(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('checked');
}


function unlockCta() {

    const lock = document.getElementById('ctaLock');

    if (lock) {
        lock.classList.add('unlocked');
        lock.innerHTML =
            '<i class="fas fa-unlock"></i> <span>✅ Video progress sufficient — you can continue!</span>';
    }

    const btn = document.getElementById('continueBtn');

    if (btn) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    }

    showBitmoji('🎉 Awesome! You can now continue to the next step!');
}

function showBitmoji(msg) {

    const el = document.getElementById('bitmojiMsg');

    if (!el) return;

    el.textContent = msg;
    el.style.display = 'block';

    setTimeout(() => {
        el.style.display = 'none';
    }, 5000);
}
  </script>
  @endsection