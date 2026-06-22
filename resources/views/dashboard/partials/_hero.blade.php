{{-- dashboard/partials/_hero.blade.php --}}
@php
    $heroThumb = $heroCourse['thumbnail_url'] ?? '';
@endphp

<section
    class="d-hero{{ $heroThumb ? ' with-image' : '' }}"
    @if ($heroThumb) style="background-image: url('{{ $heroThumb }}');" @endif
>
    @if ($heroThumb)
        <div class="d-hero-overlay"></div>
    @endif

    <div class="d-hero-inner">
        @if ($heroKicker !== '')
            <div class="d-hero-kicker">{{ $heroKicker }}</div>
        @endif

        <h1 class="d-hero-title">{{ $heroCourse['title'] ?? 'Learning Dashboard' }}</h1>
        <p class="d-hero-meta">{{ $heroCourse['provider'] ?? 'LMS Academy' }}</p>

        <a class="d-hero-btn" href="{{ $heroResumeRoute }}">
            <span>Continue</span>
            <span style="font-size:16px;">→</span>
        </a>

        <p class="d-hero-sub" style="margin-top:10px;">
            {{ $heroCourse['progress_percent'] ?? 0 }}% complete
            &middot;
            {{ $heroCourse['hours_done'] ?? 0 }}h of {{ $heroCourse['hours_total'] ?? 0 }}h
        </p>
    </div>

    <div class="d-hero-ring">
        <b>{{ $heroCourse['progress_percent'] ?? 0 }}%</b>
        <span>Done</span>
    </div>
</section>