<div class="demo-detail-page">
    <div class="dd-header">
        <div>
            <h1>My Demo Submission</h1>
            <p>Review your demo details and download your certificate once approved.</p>
        </div>

        @php
            $statusClass = match($demo->status) {
                'approved', 'completed' => 'badge-success',
                'rejected' => 'badge-danger',
                default => 'badge-warning',
            };
        @endphp
        <span class="dd-status-badge {{ $statusClass }}">
            {{ ucfirst($demo->status) }}
        </span>
    </div>

    <div class="dd-grid">
        {{-- ── Demo Info Card ── --}}
        <div class="dd-card">
            <h3><i class="fas fa-chalkboard-teacher"></i> Demo Details</h3>
            <div class="dd-row">
                <span>Topic</span>
                <strong>{{ $demo->demo_topic ?? '—' }}</strong>
            </div>
            <div class="dd-row">
                <span>Course</span>
                <strong>{{ $demo->course->title ?? '—' }}</strong>
            </div>
            <div class="dd-row">
                <span>Demo Date</span>
                <strong>{{ optional($demo->demo_date)->format('d M Y') ?? '—' }}</strong>
            </div>
            <div class="dd-row">
                <span>Submitted On</span>
                <strong>{{ $demo->created_at->format('d M Y, h:i A') }}</strong>
            </div>
            @if ($demo->score)
                <div class="dd-row">
                    <span>Score</span>
                    <strong>{{ $demo->score }}</strong>
                </div>
            @endif
        </div>

        {{-- ── Candidate Info Card ── --}}
        <div class="dd-card">
            <h3><i class="fas fa-user"></i> Candidate</h3>
            <div class="dd-row">
                <span>Name</span>
                <strong>{{ $this->person->full_name ?? '—' }}</strong>
            </div>
            <div class="dd-row">
                <span>Email</span>
                <strong>{{ $this->person->email ?? '—' }}</strong>
            </div>
            <div class="dd-row">
                <span>Contact</span>
                <strong>{{ $this->person->phone ?? '—' }}</strong>
            </div>
        </div>

        {{-- ── Feedback Card ── --}}
        @if ($demo->feedback)
            <div class="dd-card dd-full">
                <h3><i class="fas fa-comment-dots"></i> Mentor Feedback</h3>
                <p class="dd-feedback">{{ $demo->feedback }}</p>
            </div>
        @endif
    </div>

    {{-- ── Certificate Section ── --}}
    <div class="dd-cert-card">
        @if (in_array($demo->status, ['approved', 'completed']))
            <div class="dd-cert-icon"><i class="fas fa-award"></i></div>
            <div class="dd-cert-body">
                <h3>Certificate Ready</h3>
                <p>Your demo has been approved. Download your certificate below.</p>
            </div>
            <a href="{{ route('certificate.download', $demo->id) }}" target="_blank" class="dd-cert-btn">
                <i class="fas fa-download"></i> Download Certificate
            </a>
        @else
            <div class="dd-cert-icon dd-cert-pending"><i class="fas fa-hourglass-half"></i></div>
            <div class="dd-cert-body">
                <h3>Certificate Pending</h3>
                <p>Your certificate will unlock once your demo is reviewed and approved.</p>
            </div>
        @endif
    </div>
</div>

<style>
    .demo-detail-page { padding: 24px; color: var(--text); margin: 0 auto; }

    .dd-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .dd-header h1 { font-size: 24px; font-weight: 700; color: var(--text-main); }
    .dd-header p { color: var(--text-muted); font-size: 14px; margin-top: 4px; }

    .dd-status-badge {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        height: fit-content;
    }
    .badge-success { background: rgba(22,163,74,.12); color: var(--success); }
    .badge-danger  { background: rgba(220,38,38,.12); color: var(--danger); }
    .badge-warning { background: rgba(217,119,6,.12); color: var(--warning); }

    .dd-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .dd-card {
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm);
        padding: 20px;
    }
    .dd-full { grid-column: 1 / -1; }

    .dd-card h3 {
        font-size: 14px;
        color: var(--brand-primary);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dd-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid var(--line);
        font-size: 13.5px;
    }
    .dd-row:last-child { border-bottom: none; }
    .dd-row span { color: var(--text-muted); }
    .dd-row strong { color: var(--text-main); text-align: right; }

    .dd-feedback { color: var(--text-muted); line-height: 1.7; font-size: 13.5px; }

    .dd-cert-card {
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow-card);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }

    .dd-cert-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(240, 179, 90, .15);
        color: var(--brand-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .dd-cert-pending { background: rgba(90,113,138,.12); color: var(--text-muted); }

    .dd-cert-body { flex: 1; min-width: 200px; }
    .dd-cert-body h3 { color: var(--text-main); font-size: 16px; margin-bottom: 2px; }
    .dd-cert-body p { color: var(--text-muted); font-size: 13.5px; }

    .dd-cert-btn {
        background: var(--brand-primary);
        color: #fff;
        padding: 12px 22px;
        border-radius: var(--radius-xs);
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform .15s, box-shadow .15s;
    }
    .dd-cert-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px var(--primary-glow); }
</style>