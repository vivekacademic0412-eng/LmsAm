@extends('layouts.app')

@section('content')
    <style>
        .review-page {
            display: grid;
            gap: 18px;
        }
        .review-hero {
            border: 1px solid #d6dfef;
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0) 34%),
                linear-gradient(120deg, #0d4aac 0%, #1a6ed2 58%, #0f8a84 100%);
            color: #fff;
            padding: 28px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            box-shadow: 0 24px 48px rgba(17, 54, 117, 0.18);
        }
        .review-hero h1 {
            margin: 8px 0 10px;
            font-size: 34px;
            line-height: 1.05;
        }
        .review-hero p {
            margin: 0;
            max-width: 70ch;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.92);
        }
        .review-eyebrow,
        .review-position-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .review-hero-actions {
            display: flex;
            justify-content: flex-end;
            align-items: start;
        }
        .review-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }
        .review-stat {
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 14px 16px;
        }
        .review-stat span {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.78);
        }
        .review-stat strong {
            display: block;
            margin-top: 6px;
            font-size: 26px;
            line-height: 1;
        }
        .review-section {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--card);
            padding: 18px;
            box-shadow: 0 14px 30px rgba(18, 42, 86, 0.08);
        }
        .review-section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 12px;
            margin-bottom: 16px;
        }
        .review-section-head h2 {
            margin: 0;
            font-size: 24px;
        }
        .review-section-head p,
        .review-tile-desc,
        .review-empty p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }
        .review-spotlight {
            display: grid;
            grid-template-columns: minmax(280px, 0.9fr) minmax(360px, 1.1fr);
            gap: 16px;
            align-items: stretch;
        }
        .review-spotlight-copy {
            border-radius: 20px;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0) 40%),
                linear-gradient(135deg, #0e4aa8 0%, #134fbc 55%, #0c7f89 100%);
            color: #fff;
            padding: 24px;
            display: grid;
            align-content: center;
            gap: 12px;
            min-height: 100%;
        }
        .review-spotlight-copy h3 {
            margin: 0;
            font-size: 34px;
            line-height: 1.08;
        }
        .review-spotlight-copy p {
            margin: 0;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.92);
        }
        .review-spotlight-meta,
        .review-tile-meta,
        .review-tile-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .review-chip,
        .review-meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 700;
        }
        .review-chip {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }
        .review-meta-chip {
            color: #47607f;
            background: #f3f6fb;
            border: 1px solid #dbe4f0;
        }
        .review-meta-chip--primary {
            color: #1f4fa3;
            background: #edf4ff;
            border-color: #c9daf8;
        }
        .review-preview,
        .review-library-frame {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #d6dfef;
            background: #08152c;
            min-height: 100%;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
        }
        .review-preview iframe {
            width: 100%;
            height: 100%;
            min-height: 320px;
            display: block;
            border: 0;
            background: #08152c;
        }
        .review-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .review-tile {
            border: 1px solid #d8e0ee;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(15, 77, 191, 0.03), rgba(15, 77, 191, 0)), #fff;
            overflow: hidden;
            box-shadow: 0 14px 28px rgba(18, 42, 86, 0.08);
        }
        .review-tile--featured {
            border-color: #b9cef5;
            box-shadow: 0 18px 34px rgba(18, 42, 86, 0.12);
        }
        .review-library-frame {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 0;
            background: #121212;
        }
        .review-library-frame iframe {
            width: 100%;
            height: 100%;
            min-height: 0;
            aspect-ratio: 16 / 9;
            display: block;
            border: 0;
            background: #121212;
        }
        .review-inline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 14px 18px;
            border-top: 1px solid #dbe4f0;
            border-bottom: 1px solid #e8eef7;
            background: #f9fbff;
        }
        .review-inline-actions .btn,
        .review-inline-actions .btn-soft,
        .review-inline-actions .btn-danger {
            flex: 1 1 110px;
            justify-content: center;
            border-radius: 12px;
            padding: 9px 14px;
        }
        .review-tile-body {
            padding: 18px 18px 20px;
            display: grid;
            gap: 10px;
        }
        .review-tile-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
        }
        .review-tile-title-group {
            display: grid;
            gap: 8px;
            min-width: 0;
        }
        .review-tile-top strong {
            font-size: 18px;
            line-height: 1.15;
        }
        .review-uploader {
            margin: 0;
            color: #66758d;
            font-size: 14px;
            line-height: 1.45;
        }
        .review-tile-desc {
            margin: 0;
            color: #5d6d84;
            font-size: 13px;
            line-height: 1.6;
        }
        .review-order-badge {
            display: inline-grid;
            place-content: center;
            min-width: 48px;
            height: 48px;
            padding: 0 10px;
            border-radius: 16px;
            background: #edf4ff;
            color: #104db4;
            font-size: 18px;
            font-weight: 800;
            box-shadow: inset 0 0 0 1px #d0def5;
        }
        .review-tile-actions .btn,
        .review-tile-actions .btn-soft,
        .review-tile-actions .btn-danger {
            border-radius: 12px;
            padding: 8px 14px;
        }
        .review-empty {
            border: 1px dashed #cbd8ea;
            border-radius: 18px;
            padding: 32px 18px;
            text-align: center;
            background: linear-gradient(180deg, rgba(15, 77, 191, 0.03), rgba(15, 77, 191, 0));
            color: #53657d;
        }
        .review-empty h3 {
            margin: 0 0 8px;
        }
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(8, 15, 28, 0.56);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 120;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            width: min(720px, 100%);
            max-height: calc(100vh - 36px);
            overflow: auto;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.25);
        }
        .modal.modal-sm { width: min(420px, 100%); }
        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 18px;
            border-bottom: 1px solid var(--line-soft);
        }
        .modal-head h3 { margin: 0; font-size: 22px; }
        .modal-close {
            border: 0;
            background: transparent;
            color: var(--muted);
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }
        .modal-body { padding: 18px; }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid var(--line-soft);
            padding: 14px 18px;
            gap: 8px;
        }
        .review-form-grid {
            display: grid;
            grid-template-columns: 140px minmax(0, 1fr);
            gap: 14px;
        }
        .field-note {
            margin-top: 6px;
            font-size: 12px;
            color: var(--muted);
        }
        @media (max-width: 960px) {
            .review-hero,
            .review-spotlight,
            .review-form-grid,
            .review-grid {
                grid-template-columns: 1fr;
            }
            .review-hero-actions {
                justify-content: start;
            }
            .review-stat-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="review-page">
        <section class="review-hero">
            <div>
                <span class="review-eyebrow">Admin Demo Media</span>
                <h1>Reviews</h1>
                <p>Manage the YouTube review slider shown below the demo user course section. Position <strong>1</strong> appears first, position <strong>2</strong> appears second, and so on.</p>
                <div class="review-stat-grid">
                    <div class="review-stat">
                        <span>Total Reviews</span>
                        <strong>{{ $videos->count() }}</strong>
                    </div>
                    <div class="review-stat">
                        <span>Next Position</span>
                        <strong>{{ $nextPosition }}</strong>
                    </div>
                    <div class="review-stat">
                        <span>First On Dashboard</span>
                        <strong>{{ $featured?->position ?? '-' }}</strong>
                    </div>
                </div>
            </div>
            <div class="review-hero-actions">
                <button type="button" class="btn" data-modal-open="modal-review-upload">Add Review Video</button>
            </div>
        </section>

        <section class="review-section">
            <div class="review-section-head">
                <div>
                    <h2>All Reviews</h2>
                    <p>Every review video appears below as its own card with preview, position, and direct Edit/Delete actions.</p>
                </div>
            </div>

            @if ($videos->isNotEmpty())
                <div class="review-grid">
                    @foreach ($videos as $video)
                        <article class="review-tile {{ $loop->first ? 'review-tile--featured' : '' }}">
                            <div class="review-library-frame">
                                <iframe
                                    src="{{ $video->embed_url }}"
                                    title="{{ $video->title ?: 'Review Video' }}"
                                    loading="lazy"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                ></iframe>
                            </div>
                            <div class="review-inline-actions">
                                <a class="btn btn-soft" href="{{ $video->watch_url }}" target="_blank" rel="noopener">Open Video</a>
                                <button type="button" class="btn btn-soft" data-modal-open="modal-review-edit-{{ $video->id }}">Edit</button>
                                <button type="button" class="btn btn-danger" data-modal-open="modal-review-delete-{{ $video->id }}">Delete</button>
                            </div>
                            <div class="review-tile-body">
                                <div class="review-tile-top">
                                    <div class="review-tile-title-group">
                                        <strong>{{ $video->title ?: 'For demo' }}</strong>
                                        <p class="review-uploader">{{ $video->uploader?->name ?: 'Added by admin or superadmin' }}</p>
                                    </div>
                                    <span class="review-order-badge">{{ $video->position ?? ($loop->index + 1) }}</span>
                                </div>
                                @if ($video->description)
                                    <p class="review-tile-desc">{{ $video->description }}</p>
                                @endif
                                <div class="review-tile-meta">
                                    @if ($loop->first)
                                        <span class="review-meta-chip review-meta-chip--primary">First On Dashboard</span>
                                    @endif
                                    <span class="review-meta-chip">Position {{ $video->position ?? ($loop->index + 1) }}</span>
                                    <span class="review-meta-chip">Uploaded {{ $video->created_at?->format('M d, Y') }}</span>
                                    <span class="review-meta-chip">YouTube {{ $video->youtube_id }}</span>
                                </div>

                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="review-empty">
                    <h3>No Review Videos Yet</h3>
                    <p>Add your first YouTube review link and set position <strong>1</strong> to make it the first slide on the demo dashboard.</p>
                </div>
            @endif
        </section>
    </div>

    <div class="modal-overlay" id="modal-review-upload" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-head">
                <h3>Add Review Video</h3>
                <button type="button" class="modal-close" data-modal-close="modal-review-upload" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('demo-review-videos.store') }}" class="stack form-premium">
                    @csrf
                    <div class="review-form-grid">
                        <div class="field">
                            <label>Dashboard Position</label>
                            <input type="number" name="position" min="1" step="1" value="{{ old('position', $nextPosition) }}">
                            <div class="field-note">Use unique positions. Review 1 shows first, review 2 shows second.</div>
                        </div>
                        <div class="field">
                            <label>YouTube Video Link</label>
                            <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                    </div>
                    <div class="review-form-grid">
                        <div class="field">
                            <label>Title (optional)</label>
                            <input type="text" name="title" value="{{ old('title') }}" placeholder="Review video title">
                        </div>
                        <div class="field">
                            <label>Description (optional)</label>
                            <textarea name="description" rows="4">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="actions-row">
                        <button class="btn btn-soft" type="submit">Save Review Video</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft" data-modal-close="modal-review-upload">Close</button>
            </div>
        </div>
    </div>

    @foreach ($videos as $video)
        <div class="modal-overlay" id="modal-review-edit-{{ $video->id }}" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Edit Review Video</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-review-edit-{{ $video->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('demo-review-videos.update', $video) }}" class="stack form-premium">
                        @csrf
                        @method('PUT')
                        <div class="review-form-grid">
                            <div class="field">
                                <label>Dashboard Position</label>
                                <input type="number" name="position" min="1" step="1" value="{{ old('position', $video->position) }}">
                                <div class="field-note">Choose where this review should appear in the dashboard slider.</div>
                            </div>
                            <div class="field">
                                <label>YouTube Video Link</label>
                                <input type="url" name="video_url" value="{{ old('video_url', $video->youtube_url) }}">
                            </div>
                        </div>
                        <div class="review-form-grid">
                            <div class="field">
                                <label>Title (optional)</label>
                                <input type="text" name="title" value="{{ old('title', $video->title) }}">
                            </div>
                            <div class="field">
                                <label>Description (optional)</label>
                                <textarea name="description" rows="4">{{ old('description', $video->description) }}</textarea>
                            </div>
                        </div>
                        <div class="actions-row">
                            <button class="btn" type="submit">Update Review Video</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-review-edit-{{ $video->id }}">Close</button>
                </div>
            </div>
        </div>

        <div class="modal-overlay" id="modal-review-delete-{{ $video->id }}" aria-hidden="true">
            <div class="modal modal-sm" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Delete Review Video</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-review-delete-{{ $video->id }}" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <p class="muted">Delete <strong>{{ $video->title ?: 'Review Video' }}</strong> from position <strong>{{ $video->position ?? '-' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-review-delete-{{ $video->id }}">Cancel</button>
                    <form method="POST" action="{{ route('demo-review-videos.destroy', $video) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="{{ asset('js/course-modals.js') }}" defer></script>
@endsection












