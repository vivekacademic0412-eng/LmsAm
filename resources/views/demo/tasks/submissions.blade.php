@extends('layouts.app')

@section('content')
    <style>
        .submissions-page { display: grid; gap: 16px; }
        .submissions-hero {
            background: linear-gradient(120deg, #0f4dbf 0%, #1d6ed0 100%);
            color: #fff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 18px 40px rgba(15, 55, 120, 0.18);
        }
        .submissions-hero h1 { margin: 0; font-size: 30px; line-height: 1.1; }
        .submissions-hero p { margin: 8px 0 0; opacity: 0.92; }
        .submissions-stats { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
        .submissions-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            font-size: 12px;
            font-weight: 700;
        }
        .submission-list { display: grid; gap: 14px; }
        .submission-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.06);
            display: grid;
            gap: 12px;
        }
        .submission-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            align-items: start;
        }
        .submission-title { display: grid; gap: 6px; }
        .submission-title strong { font-size: 18px; }
        .submission-badges { display: flex; flex-wrap: wrap; gap: 8px; }
        .submission-body {
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
        }
        .submission-panel {
            border: 1px solid #dce4f1;
            border-radius: 12px;
            background: #f8fbff;
            padding: 14px;
            display: grid;
            gap: 8px;
        }
        .submission-panel.alt { background: #fff; }
        .submission-panel h4 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6c7c94;
        }
        .submission-answer {
            white-space: pre-wrap;
            line-height: 1.6;
            color: #31415b;
            font-size: 14px;
        }
        .submission-file {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }
        .submission-file .name {
            color: #425066;
            font-size: 13px;
            word-break: break-word;
        }
        @media (max-width: 900px) {
            .submission-body { grid-template-columns: 1fr; }
        }
    </style>

    <div class="submissions-page">
        <section class="submissions-hero">
            <h1>User Submissions</h1>
            <p>Review demo user answers and uploaded documents in one place.</p>
            <div class="submissions-stats">
                <span class="submissions-pill">{{ $submissions->total() }} total submissions</span>
                <span class="submissions-pill">{{ $submissions->count() }} on this page</span>
                @if (!empty($selectedUser))
                    <span class="submissions-pill">Filtered user: {{ $selectedUser->name }}</span>
                    <a class="submissions-pill" href="{{ route('demo-tasks.submissions-page') }}">Clear filter</a>
                @endif
            </div>
        </section>

        @if ($submissions->isNotEmpty())
            <div class="submission-list">
                @foreach ($submissions as $submission)
                    <article class="submission-card">
                        <div class="submission-head">
                            <div class="submission-title">
                                <strong>{{ $submission->assignment?->demoTask?->title ?? 'Demo Task' }}</strong>
                                <div class="submission-badges">
                                    <span class="demo-status">Demo user: {{ $submission->assignment?->user?->name ?? '-' }}</span>
                                    <span class="demo-status">Submitted: {{ $submission->submitted_at?->format('M d, Y h:i A') ?? '-' }}</span>
                                </div>
                            </div>
                            @if ($submission->file_path)
                                <a class="btn btn-soft" href="{{ route('demo-tasks.submissions.download', $submission) }}">Download Document</a>
                            @endif
                        </div>

                        <div class="submission-body">
                            <div class="submission-panel">
                                <h4>Answer</h4>
                                @if ($submission->answer_text)
                                    <div class="submission-answer">{{ $submission->answer_text }}</div>
                                @else
                                    <div class="muted">No text answer provided.</div>
                                @endif
                            </div>
                            <div class="submission-panel alt">
                                <h4>Uploaded Document</h4>
                                <div class="submission-file">
                                    <div class="name">{{ $submission->file_name ?: 'No uploaded document.' }}</div>
                                    @if ($submission->file_path)
                                        <a class="btn btn-soft" href="{{ route('demo-tasks.submissions.download', $submission) }}">Download</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top: 8px;">
                {{ $submissions->links() }}
            </div>
        @else
            <div class="card">
                <div class="demo-empty">No demo submissions available yet.</div>
            </div>
        @endif
    </div>
@endsection

