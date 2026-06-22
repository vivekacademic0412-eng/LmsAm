{{-- dashboard/partials/_video-slider.blade.php
     Required vars: $videos, $type ('feature'|'review'), $badgePrefix, $emptyLabel, $emptyTitle, $emptyDesc --}}
@php $count = $videos->count(); @endphp

<section class="d-card">
    <div class="d-section-head" style="margin-bottom:16px;">
        <div>
            @if ($type === 'feature')
                <h2>Feature Videos</h2>
            @else
                <h2>Reviews</h2>
            @endif
        </div>
    </div>

    <div
        class="d-video-slider {{ $type === 'review' ? 'd-review-slider' : '' }}"
        data-demo-video-slider
    >
        <div class="d-video-viewport">
            <div class="d-video-track" data-demo-video-track>

                @forelse ($videos as $index => $video)
                    <article
                        class="d-video-slide {{ $index === 0 ? 'active' : '' }}"
                        data-demo-video-slide
                        aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
                    >
                        <div class="d-demo-video">
                            <div class="d-demo-cover">
                                <span class="d-demo-badge">
                                    {{ $badgePrefix }} {{ str_pad((string) ($video->position ?? $index + 1), 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <h3>{{ $video->title ?: ($type === 'feature' ? 'Feature Video' : 'Learner Review') }}</h3>
                                <p>{{ $video->description ?: ($type === 'feature'
                                    ? 'See how our learning platform works in minutes.'
                                    : 'YouTube review videos added in the admin panel will appear here.') }}</p>

                                @if ($type === 'feature')
                                    <div class="d-demo-chips">
                                        <span class="d-demo-chip">Position {{ $video->position ?? $index + 1 }}</span>
                                        <span class="d-demo-chip">{{ $count }} video{{ $count === 1 ? '' : 's' }} live</span>
                                    </div>
                                    <a class="d-demo-play" href="{{ route('demo-feature-video.show', $video) }}" target="_blank" rel="noopener">
                                        <span>▶</span> Open Full Video
                                    </a>
                                @else
                                    <div class="d-review-actions">
                                        <div class="d-review-chips">
                                            <span class="d-demo-chip">Position {{ $video->position ?? $index + 1 }}</span>
                                            <span class="d-demo-chip">YouTube review</span>
                                            <span class="d-demo-chip">{{ $count }} live</span>
                                        </div>
                                        <a class="d-demo-play" href="{{ $video->watch_url }}" target="_blank" rel="noopener">
                                            <span>▶</span> Watch on YouTube
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="d-demo-thumb">
                                @if ($type === 'feature')
                                    <video controls preload="metadata" controlslist="nodownload" playsinline>
                                        <source
                                            src="{{ route('demo-feature-video.show', $video) }}"
                                            type="{{ $video->file_mime ?: 'video/mp4' }}"
                                        >
                                    </video>
                                @else
                                    <iframe
                                        src="{{ $video->embed_url }}&enablejsapi=1"
                                        title="{{ $video->title ?: 'Demo Review Video' }}"
                                        loading="lazy"
                                        referrerpolicy="strict-origin-when-cross-origin"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                        data-demo-youtube-embed
                                    ></iframe>
                                @endif
                            </div>
                        </div>
                    </article>

                @empty
                    <div class="d-video-slide active" data-demo-video-slide aria-hidden="false">
                        <div class="d-demo-video">
                            <div class="d-demo-cover">
                                <span class="d-demo-badge">{{ $badgePrefix }}</span>
                                <h3>{{ $emptyTitle }}</h3>
                                <p>{{ $emptyDesc }}</p>
                                <div class="d-demo-chips">
                                    <span class="d-demo-chip">0 {{ $type === 'feature' ? 'videos' : 'reviews' }} live</span>
                                    <span class="d-demo-chip">Slider ready</span>
                                </div>
                            </div>
                            <div class="d-demo-thumb">
                                <div class="d-video-empty">{{ $emptyLabel }}</div>
                            </div>
                        </div>
                    </div>
                @endforelse

            </div>
        </div>

        @if ($count > 1)
            <div class="d-video-nav">
                <div class="d-video-nav-group">
                    <button class="d-video-arrow" type="button" data-demo-video-prev aria-label="Previous">&#8249;</button>
                    <button class="d-video-arrow" type="button" data-demo-video-next aria-label="Next">&#8250;</button>
                </div>
                <div class="d-video-dots">
                    @foreach ($videos as $index => $video)
                        <button
                            class="d-video-dot {{ $index === 0 ? 'active' : '' }}"
                            type="button"
                            data-demo-video-dot="{{ $index }}"
                            aria-label="{{ $badgePrefix }} {{ $index + 1 }}"
                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        ></button>
                    @endforeach
                </div>
                <div class="d-video-counter" data-demo-video-counter>1 / {{ $count }}</div>
            </div>
        @endif
    </div>
</section>