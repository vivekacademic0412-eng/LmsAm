{{-- dashboard/partials/_skills-topics.blade.php
     Required vars: $skillProgress, $topics, $accentClass
     Optional: $wrapperClass (defaults to 'd-panel-box') --}}
@php $wrapperClass = $wrapperClass ?? 'd-panel-box'; @endphp

<article class="{{ $wrapperClass }}">
    <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Skill Progress</h3>

    @forelse ($skillProgress as $index => $skill)
        <div class="d-skill-row">
            <div class="d-skill-label">
                <span>{{ $skill['skill'] }}</span>
                <span style="color:var(--brand-300);font-weight:700;">{{ $skill['progress'] }}%</span>
            </div>
            <div class="d-bar-track">
                <div
                    class="d-bar-val {{ $accentClass[array_keys($accentClass)[$index % count($accentClass)]] }}"
                    style="width:{{ $skill['progress'] }}%"
                ></div>
            </div>
        </div>
    @empty
        <p class="d-muted" style="margin:0;font-size:13px;">No skill progress available.</p>
    @endforelse
</article>

<article class="{{ $wrapperClass }}">
    <h3 style="margin:0 0 14px;font-size:20px;font-weight:700;color:var(--text);">Browse by Topic</h3>
    <div class="d-topic-grid">
        @forelse ($topics as $topic)
            <a href="{{ route('student.courses') }}" class="d-topic">
                <div class="d-topic-bullet">{{ strtoupper(substr($topic['name'], 0, 2)) }}</div>
                <div>
                    <strong>{{ $topic['name'] }}</strong>
                    <p>{{ number_format($topic['count']) }} courses</p>
                </div>
            </a>
        @empty
            <p class="d-muted" style="margin:0;font-size:13px;">No topics found.</p>
        @endforelse
    </div>
</article>