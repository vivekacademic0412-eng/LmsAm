{{-- FILE: resources/views/demo/lms/step2.blade.php --}}
@extends('demo.layout')
@section('title', 'Demo Session – Watch & Learn')
@section('bitmoji-message', '🎥 Watch carefully! Pay attention to techniques — you\'ll recreate something similar next!')

@section('content')

<div class="explore-banner">
  <div class="banner-icon">🗺️</div>
  <div class="banner-text">
    <div class="step-badge" style="margin-bottom:10px">
      <div class="dot-pulse"></div>
      Step 2 of 5 — Demo Session
    </div>
    <h1>Your <em>Demo</em> Session</h1>

    @if($video)
      <h3 class="demo-video-title mb-2">{{ $video->title }}</h3>
      <p class="subtitle mb-0">{{ $video->description }}</p>
    @endif

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px">
      <div class="course-tag">
        <i class="fas fa-play-circle"></i>
        {{ session('lms_course_label', 'Your Course') }}
      </div>
      <div class="tutor-tag">
        <div class="tutor-avatar">🧑</div>
        <span>by Academic Mantra</span>
      </div>
    </div>
  </div>
</div>

<div class="card">

  {{-- ── Video Player ── --}}
  @if($video)
    <div class="video-section">
      <div class="video-wrap">
        <video id="demoVideo" width="100%" controls controlsList="nodownload" preload="metadata">
          <source src="{{ asset('storage/' . $video->file_path) }}" type="{{ $video->file_mime }}">
          Your browser does not support the video tag.
        </video>
      </div>

      <div class="watch-progress-wrap">
        <div class="watch-progress-label">
          <span><i class="fas fa-chart-line"></i> Video Progress</span>
          <span class="progress-pct" id="progressPct">
            {{ (int) ($video_details->progress_demo ?? 0) }}% watched
          </span>
        </div>
        <div class="watch-bar-bg">
          <div class="watch-bar-fill" id="watchFill"
               style="width:{{ (int) ($video_details->progress_demo ?? 0) }}%"></div>
        </div>

        @if(($video_details->progress_demo ?? 0) > 0)
          <p style="font-size:11px;color:var(--text-muted);margin-top:8px">
            <i class="fas fa-history"></i>
            Resumed — you previously watched <strong>{{ (int) $video_details->progress_demo }}%</strong> of this video.
          </p>
        @endif
      </div>
    </div>
  @else
    <div class="alert-info"><i class="fas fa-info-circle"></i> No demo video is available for this course yet. You can continue to the next step.</div>
  @endif

  {{-- ── Watch Checklist ── --}}
  <div class="section-title" style="margin-top:4px">
    <i class="fas fa-eye" style="color:var(--primary)"></i> Watch For These Key Points
  </div>
  <div class="watch-checklist">
    <div class="checklist-item" id="chk1">
      <div class="check-icon"><i class="fas fa-check"></i></div>
      <div class="check-label">
        <strong>Introduction &amp; Topic Explanation</strong>
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
        <strong>Summary &amp; Key Takeaways</strong>
        How they wrap up and reinforce the learning in under 60 seconds
      </div>
    </div>
  </div>

  {{-- ── CTA Lock — server already knows whether it's unlocked ── --}}
  @php $alreadyUnlocked = ($video_details->progress_demo ?? 0) >= 70; @endphp

  <div class="cta-locked {{ $alreadyUnlocked ? 'unlocked' : '' }}" id="ctaLock">
    @if($alreadyUnlocked)
      <i class="fas fa-unlock"></i>
      <span>✅ Progress sufficient — you can continue!</span>
    @else
      <i class="fas fa-lock"></i>
      <span>Watch at least <strong>70% of the video</strong> to unlock the next step</span>
    @endif
  </div>

  <form action="{{ route('lms.step2.store') }}" method="POST">
    @csrf
    <input type="hidden" name="video_watched" id="videoWatchedInput"
           value="{{ (int) ($video_details->progress_demo ?? 0) }}">

    <div class="btn-group">
      <a href="{{ route('lms.step1') }}" class="btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <button type="submit" class="btn-primary" id="continueBtn"
        {{ $alreadyUnlocked ? '' : 'disabled' }}
        style="{{ $alreadyUnlocked ? '' : 'opacity:.5;cursor:not-allowed' }}">
        <span>I've Watched the Demo</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </div>
  </form>

</div>
@endsection

@section('scripts')
<script>
const video = document.getElementById('demoVideo');

/* ── Starting point comes from the DB, never from the player ── */
let progress = @json((float) ($video_details->progress_demo ?? 0));
let unlocked = progress >= 70;

/* Pre-checked items + pre-applied bar already rendered server-side above,
   but re-apply via JS too in case checklist needs the checked class on load */
applyChecklist(progress);

if (video) {

  /* Jump playback to where the learner left off, so "resume" is real,
     not just a displayed number */
  video.addEventListener('loadedmetadata', function () {
    if (progress > 0 && progress < 100 && video.duration) {
      video.currentTime = (progress / 100) * video.duration;
    }
  });

  video.addEventListener('timeupdate', function () {
    if (!video.duration) return;

    const livePct = (video.currentTime / video.duration) * 100;

    /* 🔒 KEY FIX: progress can only go UP, never down.
       Re-watching from 0:00 after reaching 80% must not reset it. */
    if (livePct > progress) {
      progress = livePct;
      updateProgress(progress);

      if (progress >= 70 && !unlocked) {
        unlockCta();
        unlocked = true;
      }
    }
  });

  video.addEventListener('ended', function () {
    progress = 100;
    updateProgress(100);
    if (!unlocked) { unlockCta(); unlocked = true; }
  });

  /* Bitmoji milestone messages — only fire once per session */
  let shown = { 25: false, 50: false, 75: false };
  video.addEventListener('timeupdate', function () {
    if (!video.duration) return;
    const pct = Math.round((video.currentTime / video.duration) * 100);
    if (pct >= 25 && !shown[25]) { showBitmoji('🎥 Great start! Keep watching…'); shown[25] = true; }
    if (pct >= 50 && !shown[50]) { showBitmoji('🧠 Halfway! Pay attention to the demo technique.'); shown[50] = true; }
    if (pct >= 75 && !shown[75]) { showBitmoji('🚀 Almost done! The summary section is key!'); shown[75] = true; }
  });
}

function updateProgress(p) {
  const pct = Math.round(p);
  const fill = document.getElementById('watchFill');
  const label = document.getElementById('progressPct');
  const input = document.getElementById('videoWatchedInput');

  if (fill)  fill.style.width = pct + '%';
  if (label) label.textContent = pct + '% watched';
  if (input) input.value = pct;

  applyChecklist(pct);
}

function applyChecklist(pct) {
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
    lock.innerHTML = '<i class="fas fa-unlock"></i> <span>✅ Progress sufficient — you can continue!</span>';
  }
  const btn = document.getElementById('continueBtn');
  if (btn) {
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';
  }
  showBitmoji('🎉 Awesome! You can now continue to the next step!');
}
</script>
@endsection