@extends('layouts.app')
@section('title', 'Student Feedback')
@section('content')
    <div class="d-root">

        <div class="d-admin-hero mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 d-hero-inner">

                <div>


                    <h2 class="mt-3 mb-2 d-hero-title">
                        Student Feedback
                    </h2>

                    <p class=" mb-0 d-hero-meta">
                        All emoji reactions and reviews from demo submissions
                    </p>

                </div>

            </div>

        </div>
        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        {{-- Stats Row --}}
        <div class="d-stats-grid" style="margin-bottom:20px">
            <div class="d-stat">
                <div class="d-stat-icon" style="background:rgba(108,63,245,.2);color:#A78BFA"><i
                        class="fas fa-comments"></i></div>
                <div class="stat-num">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Responses</div>
            </div>
            <div class="d-stat">
                <div class="d-stat-icon" style="background:rgba(255,217,61,.15);color:#FFD93D"><i class="fas fa-star"></i>
                </div>
                <div class="stat-num">{{ $stats['avg_rating'] ?: '—' }}</div>
                <div class="stat-label">Avg Rating</div>
            </div>
            <div class="d-stat">
                <div class="d-stat-icon" style="background:rgba(107,203,119,.15);color:#6BCB77"><i
                        class="fas fa-thumbs-up"></i></div>
                <div class="d-stat-num">{{ $stats['recommend'] }}</div>
                <div class="d-stat-meta">Would Recommend</div>
            </div>
        </div>

        {{-- Emoji breakdown --}}
        @if ($stats['by_emoji']->count())
            <div class="panel" style="margin-bottom:20px">
                <div class="panel-head"><span>😊 Emoji Reaction Breakdown</span></div>
                <div style="display:flex; gap:12px; flex-wrap:wrap">
                    @foreach ($stats['by_emoji'] as $e)
                        <div
                            style="background:var(--card2);border:1px solid var(--border);border-radius:14px;padding:14px 20px;text-align:center;min-width:100px">
                            <div style="font-size:2rem">{{ $e->emoji_reaction }}</div>
                            <div style="font-family:'Sora',sans-serif;font-size:.8rem;font-weight:700;margin-top:4px">
                                {{ $e->emoji_label }}</div>
                            <div style="font-size:1.4rem;font-weight:800;color:#A78BFA;font-family:'Sora',sans-serif">
                                {{ $e->count }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Feedback list --}}
        @forelse($feedbacks as $fb)
            <div class="list-card" style="margin-bottom:14px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;flex-wrap:wrap">
                    <div style="font-size:2rem">{{ $fb->emoji_reaction }}</div>
                    <div class="flex-1">
                        <div class="fw">{{ $fb->user?->name ?? 'Unknown' }}</div>
                        <div class="muted-sm">{{ $fb->user?->email }} &nbsp;|&nbsp; {{ $fb->course?->name ?? 'N/A' }}</div>
                    </div>
                    <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                        <span class="badge badge-purple">{{ $fb->emoji_label }}</span>
                        @if ($fb->rating)
                            <span class="badge badge-yellow">⭐ {{ $fb->rating }}/5</span>
                        @endif
                        @if ($fb->would_recommend !== null)
                            <span class="badge {{ $fb->would_recommend ? 'badge-green' : 'badge-red' }}">
                                {{ $fb->would_recommend ? '👍 Recommends' : '👎 Not Recommending' }}
                            </span>
                        @endif
                        <span class="muted-sm">{{ $fb->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Aspect ratings --}}
                @if ($fb->content_rating || $fb->clarity_rating || $fb->support_rating)
                    <div style="display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap">
                        @if ($fb->content_rating)
                            <div style="font-size:.78rem;color:var(--muted)">📚 Content: <strong
                                    style="color:var(--text-main)">{{ $fb->content_rating }}/5</strong></div>
                        @endif
                        @if ($fb->clarity_rating)
                            <div style="font-size:.78rem;color:var(--muted)">🔊 Clarity: <strong
                                    style="color:var(--text-main)">{{ $fb->clarity_rating }}/5</strong></div>
                        @endif
                        @if ($fb->support_rating)
                            <div style="font-size:.78rem;color:var(--muted)">🤝 Support: <strong
                                    style="color:var(--text-main)">{{ $fb->support_rating }}/5</strong></div>
                        @endif
                    </div>
                @endif

                {{-- Tags --}}
                @if ($fb->liked_tags)
                    <div style="margin-bottom:8px">
                        <span
                            style="font-size:.72rem;color:#6BCB77;font-weight:700;text-transform:uppercase;letter-spacing:.06em">Liked:
                        </span>
                        @foreach ($fb->liked_tags as $tag)
                            <span
                                style="background:rgba(107,203,119,.1);border:1px solid rgba(107,203,119,.25);color:#6BCB77;border-radius:100px;padding:2px 9px;font-size:.75rem;margin-right:5px">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif
                @if ($fb->improve_tags)
                    <div style="margin-bottom:8px">
                        <span
                            style="font-size:.72rem;color:#FF9F43;font-weight:700;text-transform:uppercase;letter-spacing:.06em">Improve:
                        </span>
                        @foreach ($fb->improve_tags as $tag)
                            <span
                                style="background:rgba(255,159,67,.1);border:1px solid rgba(255,159,67,.25);color:#FF9F43;border-radius:100px;padding:2px 9px;font-size:.75rem;margin-right:5px">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- Message --}}
                @if ($fb->message)
                    <div
                        style="background:rgba(108,63,245,.06);border-left:3px solid rgba(108,63,245,.4);border-radius:0 10px 10px 0;padding:10px 14px;font-size:.85rem;color:var(--text-muted);font-style:italic;margin-top:4px">
                        "{{ $fb->message }}"
                    </div>
                @endif
            </div>
        @empty
            <div class="empty-state">
                <div style="font-size:2.5rem;margin-bottom:12px">💬</div>
                <p>No feedback submitted yet.</p>
            </div>
        @endforelse

        <div style="margin-top:16px">{{ $feedbacks->links() }}</div>
    </div>
@endsection
