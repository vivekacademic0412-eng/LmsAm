@extends('layouts.app')

@section('content')
    <style>
        .student-day-card {
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(20, 95, 209, 0.03), rgba(20, 95, 209, 0));
            padding: 14px;
        }
        .secure-link {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        .secure-link:hover { text-decoration: underline; }
    </style>
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>{{ $enrollment->course->title }}</h1>
                    <p>Progress auto-mark is enabled for this enrolled course. Completed {{ $completedItems }} / {{ $totalItems }} items.</p>
                </div>
            </div>
        </section>

        @foreach ($enrollment->course->weeks as $week)
            <section class="student-day-card">
                <div class="page-head">
                    <h2>Week {{ $week->week_number }}: {{ $week->title }}</h2>
                </div>
                @foreach ($week->sessions as $session)
                    <div class="mt-8">
                        <h3 class="mt-0">Session {{ $session->session_number }}: {{ $session->title }}</h3>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Resource Type</th>
                                    <th>Content</th>
                                    <th>URL</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($session->items as $item)
                                    <tr>
                                        <td>{{ strtoupper(str_replace('_', ' ', $item->item_type)) }}</td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->resource_type ?: '-' }}</td>
                                        <td>{{ $item->content ?: '-' }}</td>
                                        <td>
                                            @if ($item->hasPrivateCloudinaryAsset())
                                                <a href="{{ route('course-session-items.media.view', $item) }}" class="secure-link">Open Secure Viewer</a>
                                            @elseif ($item->resource_url && !in_array($item->resource_type, ['video', 'ppt', 'video_or_ppt'], true))
                                                <a href="{{ $item->resource_url }}" class="secure-link" target="_blank" rel="noopener">Open Link</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </section>
        @endforeach
    </div>
@endsection
