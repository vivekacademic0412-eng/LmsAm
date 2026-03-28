@extends('layouts.app')

@php
    $selectedAudience = old('audience', 'all_active_users');
@endphp

@section('content')
    <style>
        .broadcast-shell {
            display: grid;
            gap: 18px;
        }
        .broadcast-hero {
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(0, 1.3fr) minmax(260px, 0.7fr);
            border: 1px solid #d8e4f5;
            border-radius: 24px;
            padding: 22px;
            background:
                radial-gradient(circle at top left, rgba(20, 95, 209, 0.16), rgba(20, 95, 209, 0) 38%),
                radial-gradient(circle at bottom right, rgba(57, 187, 174, 0.15), rgba(57, 187, 174, 0) 34%),
                linear-gradient(140deg, #ffffff 0%, #f7fbff 48%, #f1f7ff 100%);
            box-shadow: 0 18px 34px rgba(13, 34, 66, 0.08);
        }
        .broadcast-hero-copy {
            display: grid;
            gap: 12px;
            align-content: start;
        }
        .broadcast-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            border-radius: 999px;
            padding: 6px 12px;
            background: rgba(20, 95, 209, 0.1);
            color: #145fd1;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .broadcast-hero h1 {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.02;
            color: #102849;
            max-width: 12ch;
        }
        .broadcast-hero p {
            margin: 0;
            color: #5f7290;
            font-size: 15px;
            line-height: 1.7;
            max-width: 58ch;
        }
        .broadcast-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .broadcast-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 7px 12px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid #d9e5f5;
            color: #2f4f78;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 10px 18px rgba(18, 42, 86, 0.05);
        }
        .broadcast-chip.is-danger {
            color: #b64747;
            border-color: #edc6c6;
            background: #fff4f4;
        }
        .broadcast-stat-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-content: start;
        }
        .broadcast-stat {
            border: 1px solid #d8e4f5;
            border-radius: 18px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.86);
            display: grid;
            gap: 8px;
        }
        .broadcast-stat span {
            color: #6980a3;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .broadcast-stat strong {
            color: #102849;
            font-size: 26px;
            line-height: 1;
        }
        .broadcast-stat p {
            color: #667891;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
        }
        .broadcast-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(0, 1.12fr) minmax(300px, 0.88fr);
            align-items: start;
        }
        .broadcast-panel {
            display: grid;
            gap: 16px;
        }
        .broadcast-panel-head {
            display: grid;
            gap: 6px;
        }
        .broadcast-panel-head h2,
        .broadcast-side-card h2 {
            margin: 0;
            color: #102849;
            font-size: 24px;
            line-height: 1.08;
        }
        .broadcast-panel-head p,
        .broadcast-side-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }
        .broadcast-alert {
            border-radius: 16px;
            padding: 14px 16px;
            border: 1px solid;
            display: grid;
            gap: 6px;
        }
        .broadcast-alert strong {
            font-size: 14px;
        }
        .broadcast-alert p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
        }
        .broadcast-alert--danger {
            background: rgba(197, 58, 58, 0.08);
            color: #b84747;
            border-color: rgba(197, 58, 58, 0.22);
        }
        .broadcast-form {
            border: 1px solid #dbe6f4;
            border-radius: 18px;
            padding: 16px;
            background:
                radial-gradient(circle at top right, rgba(20, 95, 209, 0.08), rgba(20, 95, 209, 0) 34%),
                linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }
        .broadcast-form-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .broadcast-field {
            display: grid;
            gap: 7px;
        }
        .broadcast-field--full {
            grid-column: 1 / -1;
        }
        .broadcast-field-note {
            color: #667891;
            font-size: 12px;
            line-height: 1.55;
        }
        .broadcast-course-field {
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
            border-radius: 16px;
            padding: 10px;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.38);
        }
        .broadcast-course-field.is-active {
            border-color: #b7cff1;
            background: rgba(237, 244, 255, 0.86);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 10px 22px rgba(20, 95, 209, 0.08);
        }
        .broadcast-action-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 6px;
        }
        .broadcast-action-note {
            color: #667891;
            font-size: 12px;
            line-height: 1.6;
            max-width: 48ch;
        }
        .broadcast-send-btn {
            min-width: 172px;
            padding: 12px 18px;
            border-radius: 14px;
            border: 1px solid #0f5dd5;
            background: linear-gradient(135deg, #145fd1 0%, #2b84df 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 14px 24px rgba(20, 95, 209, 0.2);
        }
        .broadcast-send-btn[disabled] {
            opacity: 0.65;
            cursor: not-allowed;
            box-shadow: none;
        }
        .broadcast-side {
            display: grid;
            gap: 18px;
        }
        .broadcast-side-card {
            display: grid;
            gap: 14px;
        }
        .audience-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .audience-card {
            border: 1px solid #d9e4f3;
            border-radius: 18px;
            padding: 15px;
            background:
                linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            display: grid;
            gap: 8px;
            transition: transform 160ms ease, border-color 160ms ease, box-shadow 160ms ease;
        }
        .audience-card:hover {
            transform: translateY(-2px);
            border-color: #bfd3ef;
            box-shadow: 0 12px 24px rgba(18, 42, 86, 0.08);
        }
        .audience-card.is-selected {
            border-color: #9fc1f1;
            background:
                radial-gradient(circle at top right, rgba(20, 95, 209, 0.14), rgba(20, 95, 209, 0) 36%),
                linear-gradient(180deg, #ffffff 0%, #eff6ff 100%);
            box-shadow: 0 14px 26px rgba(20, 95, 209, 0.12);
        }
        .audience-card-head {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 8px;
        }
        .audience-card strong {
            display: block;
            margin: 0;
            color: #102849;
            font-size: 15px;
            line-height: 1.35;
        }
        .audience-card p {
            margin: 0;
            color: #60718d;
            font-size: 13px;
            line-height: 1.65;
        }
        .audience-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            background: #edf4ff;
            color: #235ebd;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .broadcast-tip-list {
            display: grid;
            gap: 10px;
        }
        .broadcast-tip {
            display: grid;
            gap: 4px;
            border: 1px solid #d9e4f3;
            border-radius: 16px;
            padding: 14px;
            background: #fbfdff;
        }
        .broadcast-tip strong {
            color: #102849;
            font-size: 14px;
        }
        .broadcast-tip p {
            margin: 0;
            color: #60718d;
            font-size: 13px;
            line-height: 1.6;
        }
        @media (max-width: 1024px) {
            .broadcast-hero,
            .broadcast-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 720px) {
            .broadcast-form-grid,
            .broadcast-stat-grid {
                grid-template-columns: 1fr;
            }
            .broadcast-hero {
                padding: 18px;
            }
            .broadcast-panel-head h2,
            .broadcast-side-card h2 {
                font-size: 21px;
            }
        }
    </style>

    <div class="broadcast-shell">
        <section class="broadcast-hero">
            <div class="broadcast-hero-copy">
                <span class="broadcast-kicker">Announcement Center</span>
                <h1>Push the right update to the right LMS audience.</h1>
                <p>Use one place to send dashboard-ready notices to students, trainers, admins, IT, Manager / HR, demo users, or one selected course group.</p>
                <div class="broadcast-chip-row">
                    <span class="broadcast-chip">{{ count($audienceOptions) }} audience groups</span>
                    <span class="broadcast-chip">{{ $courses->count() }} courses available for targeting</span>
                    <span class="broadcast-chip {{ $notificationsReady ? '' : 'is-danger' }}">
                        {{ $notificationsReady ? 'Database notifications ready' : 'Notifications table missing' }}
                    </span>
                </div>
            </div>

            <div class="broadcast-stat-grid">
                <article class="broadcast-stat">
                    <span>Audience Groups</span>
                    <strong>{{ count($audienceOptions) }}</strong>
                    <p>Prebuilt recipient segments you can target immediately.</p>
                </article>
                <article class="broadcast-stat">
                    <span>Course Targets</span>
                    <strong>{{ $courses->count() }}</strong>
                    <p>Select a single course when a message should stay class-specific.</p>
                </article>
                <article class="broadcast-stat">
                    <span>Delivery</span>
                    <strong>{{ $notificationsReady ? 'Live' : 'Blocked' }}</strong>
                    <p>{{ $notificationsReady ? 'Messages will appear in dashboard cards and the top-bar bell.' : 'Run the notifications migration before sending anything.' }}</p>
                </article>
                <article class="broadcast-stat">
                    <span>Current Focus</span>
                    <strong>{{ \Illuminate\Support\Str::headline((string) $selectedAudience) }}</strong>
                    <p>The currently selected audience is highlighted in the guide panel.</p>
                </article>
            </div>
        </section>

        @unless ($notificationsReady)
            <section class="card">
                <div class="broadcast-alert broadcast-alert--danger">
                    <strong>Broadcast sending is currently disabled.</strong>
                    <p>The `notifications` table is missing, so the system cannot write dashboard alerts yet. Run the notifications migration first, then reopen this page.</p>
                </div>
            </section>
        @endunless

        <div class="broadcast-grid">
            <section class="card broadcast-panel">
                <div class="broadcast-panel-head">
                    <h2>Compose Broadcast</h2>
                    <p>Keep titles short, make the message action-oriented, and use the course filter only when the notice should stay inside one batch.</p>
                </div>

                <form method="POST" action="{{ route('broadcast-notifications.store') }}" class="broadcast-form stack">
                    @csrf
                    <div class="broadcast-form-grid">
                        <div class="broadcast-field">
                            <label for="broadcastAudience">Audience</label>
                            <select id="broadcastAudience" name="audience" required @disabled(! $notificationsReady)>
                                @foreach ($audienceOptions as $value => $label)
                                    <option value="{{ $value }}" @selected($selectedAudience === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="broadcast-field-note">Choose the exact role group that should see this message on their dashboard.</div>
                        </div>

                        <div class="broadcast-course-field {{ $selectedAudience === 'course_students' ? 'is-active' : '' }}" data-course-target>
                            <div class="broadcast-field">
                                <label for="broadcastCourse">Course</label>
                                <select id="broadcastCourse" name="course_id" @disabled(! $notificationsReady)>
                                    <option value="">Optional unless sending to one course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                <div class="broadcast-field-note">This field becomes required when the audience is `Students In One Course`.</div>
                            </div>
                        </div>

                        <div class="broadcast-field broadcast-field--full">
                            <label for="broadcastTitle">Title</label>
                            <input id="broadcastTitle" type="text" name="title" value="{{ old('title') }}" maxlength="120" placeholder="Example: Submission window closes at 6 PM today" required @disabled(! $notificationsReady)>
                            <div class="broadcast-field-note">Aim for one short headline that users can scan quickly in the bell dropdown.</div>
                        </div>
                    </div>

                    <div class="broadcast-field">
                        <label for="broadcastMessage">Message</label>
                        <textarea id="broadcastMessage" name="message" rows="7" maxlength="1200" placeholder="Write the full message that should appear in dashboard notification cards." required @disabled(! $notificationsReady)>{{ old('message') }}</textarea>
                        <div class="broadcast-field-note">Best results come from simple messages: what changed, who it affects, and what the user should do next.</div>
                    </div>

                    <div class="broadcast-action-row">
                        <div class="broadcast-action-note">
                            Students, trainers, admins, IT, Manager / HR, and demo users will see broadcasts both in their dashboard notification cards and in the top-bar notification bell.
                        </div>
                        <button class="btn broadcast-send-btn" type="submit" @disabled(! $notificationsReady)>Send Broadcast</button>
                    </div>
                </form>
            </section>

            <aside class="broadcast-side">
                <section class="card broadcast-side-card">
                    <div>
                        <h2>Audience Guide</h2>
                        <p>The selected audience is highlighted below so you can sanity-check targeting before you send.</p>
                    </div>

                    <div class="audience-grid">
                        @foreach ($audienceOptions as $value => $label)
                            <article class="audience-card {{ $selectedAudience === $value ? 'is-selected' : '' }}">
                                <div class="audience-card-head">
                                    <strong>{{ $label }}</strong>
                                    <span class="audience-badge">{{ $selectedAudience === $value ? 'Selected' : 'Audience' }}</span>
                                </div>
                                <p>
                                    @if ($value === 'course_students')
                                        Best for class-specific reminders, assignment updates, schedule changes, and anything that should only reach one course.
                                    @elseif ($value === 'students')
                                        Useful for deadlines, certification reminders, course updates, and learning-related platform announcements.
                                    @elseif ($value === 'trainers')
                                        Good for review requests, assessment changes, and operational updates for teaching staff.
                                    @elseif ($value === 'manager_hr')
                                        Best for HR coordination, reporting notices, and internal workflow updates for Manager / HR accounts.
                                    @elseif ($value === 'it')
                                        Helpful for maintenance windows, platform support notices, and technical communication for IT users.
                                    @elseif ($value === 'admins')
                                        Use this for admin-only coordination across super admin and admin accounts.
                                    @elseif ($value === 'demo_users')
                                        Fits demo-access reminders, onboarding nudges, and follow-up communication for demo users.
                                    @else
                                        Sends a platform-wide message to every active account in the LMS.
                                    @endif
                                </p>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="card broadcast-side-card">
                    <div>
                        <h2>Writing Tips</h2>
                        <p>Small message quality changes make the dashboard feel much more professional.</p>
                    </div>

                    <div class="broadcast-tip-list">
                        <article class="broadcast-tip">
                            <strong>Lead with the action</strong>
                            <p>Open with the deadline, change, or request so the message is useful even when users only read the first line.</p>
                        </article>
                        <article class="broadcast-tip">
                            <strong>Target narrow when possible</strong>
                            <p>Use course-specific targeting for class reminders instead of sending the same message to every learner.</p>
                        </article>
                        <article class="broadcast-tip">
                            <strong>Keep titles scannable</strong>
                            <p>Short headlines work better in the top-bar bell, while the body can carry the extra detail.</p>
                        </article>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var audienceField = document.getElementById('broadcastAudience');
            var courseField = document.getElementById('broadcastCourse');
            var courseTarget = document.querySelector('[data-course-target]');

            if (!audienceField || !courseField || !courseTarget) {
                return;
            }

            function syncCourseField() {
                var isCourseAudience = audienceField.value === 'course_students';

                courseTarget.classList.toggle('is-active', isCourseAudience);
                courseField.required = isCourseAudience;
            }

            audienceField.addEventListener('change', syncCourseField);
            syncCourseField();
        });
    </script>
@endsection
