@extends('layouts.app')

@section('content')
    <style>
        .audience-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .audience-card {
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            padding: 14px;
            background: linear-gradient(180deg, #ffffff, #f8fbff);
        }
        .audience-card strong {
            display: block;
            margin-bottom: 6px;
        }
        .audience-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }
    </style>

    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>Broadcast Notifications</h1>
                    <p>Send announcement-style dashboard notifications to the right LMS audience.</p>
                </div>
            </div>
        </section>

        <section class="card">
            <form method="POST" action="{{ route('broadcast-notifications.store') }}" class="stack form-premium">
                @csrf
                <div class="form-grid">
                    <div class="field">
                        <label>Audience</label>
                        <select name="audience" required>
                            @foreach ($audienceOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('audience') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Course</label>
                        <select name="course_id">
                            <option value="">Optional unless sending to one course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" maxlength="120" required>
                    </div>
                </div>
                <div class="field">
                    <label>Message</label>
                    <textarea name="message" rows="6" maxlength="1200" placeholder="Write the message that should appear on the dashboard notification cards." required>{{ old('message') }}</textarea>
                </div>
                <div class="actions-row">
                    <button class="btn" type="submit">Send Broadcast</button>
                </div>
            </form>
        </section>

        <section class="card">
            <div class="page-head">
                <div>
                    <h2>Audience Guide</h2>
                    <p>Pick the audience that best matches the update you want to push.</p>
                </div>
            </div>
            <div class="audience-grid">
                @foreach ($audienceOptions as $value => $label)
                    <article class="audience-card">
                        <strong>{{ $label }}</strong>
                        <p>
                            @if ($value === 'course_students')
                                Best for class-specific reminders, assignment updates, or schedule changes.
                            @elseif ($value === 'students')
                                Useful for course updates, deadlines, and platform notices for learners.
                            @elseif ($value === 'trainers')
                                Good for teaching updates, review requests, and operational announcements.
                            @elseif ($value === 'admins')
                                Use for admin-only coordination across super admin and admin accounts.
                            @elseif ($value === 'demo_users')
                                Fits demo access reminders and sample task follow-up messages.
                            @else
                                Sends a platform-wide message to every active account in the LMS.
                            @endif
                        </p>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection
