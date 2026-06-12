@extends('layouts.app')

@php
    $selectedAudience = old('audience', 'all_active_users');
@endphp

@section('content')


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
            <section class=" broadcast-panel">
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
                <section class=" broadcast-side-card">
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

                <section class=" broadcast-side-card">
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
