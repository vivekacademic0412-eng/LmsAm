@extends('layouts.app')

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Task & Quiz Items</h1>
                    <p>{{ $course->title }}</p>
                </div>
                <div class="actions-row">
                    <a class="btn btn-soft" href="{{ route('trainer.submissions') }}">Review Queue</a>
                    <a class="btn btn-soft" href="{{ route('trainer.progress') }}">Back to Progress</a>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Week</th>
                        <th>Session</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>Week {{ $item->session?->week?->week_number }}</td>
                            <td>Session {{ $item->session?->session_number }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ strtoupper($item->item_type) }}</td>
                            <td>
                                @if ($item->item_type === \App\Models\CourseSessionItem::TYPE_QUIZ)
                                    {{ $item->is_live ? 'Live' : 'Closed' }}
                                @else
                                    Ready
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-soft" href="{{ route('trainer.items.submissions', $item) }}">View Submissions</a>
                                @if ($item->item_type === \App\Models\CourseSessionItem::TYPE_QUIZ)
                                    <form method="POST" action="{{ route('trainer.items.quiz-live', $item) }}" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-soft" type="submit">{{ $item->is_live ? 'Close Quiz' : 'Go Live' }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No task or quiz items found for this course.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
