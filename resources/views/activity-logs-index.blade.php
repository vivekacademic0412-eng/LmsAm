@extends('layouts.app')

@section('content')
    <style>
        .activity-page {
            display: grid;
            gap: 14px;
        }
        .activity-head-card {
            display: grid;
            gap: 14px;
        }
        .activity-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
        }
        .activity-title {
            display: grid;
            gap: 6px;
        }
        .activity-title h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.12;
        }
        .activity-title p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
            max-width: 70ch;
        }
        .activity-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
        }
        .activity-page-size-form {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            background: var(--card);
        }
        .activity-page-size-form label {
            margin: 0;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: var(--muted);
            text-transform: uppercase;
        }
        .activity-page-size-form select {
            min-width: 90px;
            padding: 7px 30px 7px 10px;
            margin: 0;
        }
        .activity-filter-wrap {
            position: relative;
        }
        .activity-filter-wrap .filter-panel {
            width: min(380px, calc(100vw - 32px));
        }
        .activity-note {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .activity-note-chip,
        .activity-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 11px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.03em;
        }
        .activity-note-chip {
            background: color-mix(in srgb, var(--field-bg) 86%, #ffffff 14%);
            color: var(--muted);
        }
        .activity-summary-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
        }
        .activity-summary-card {
            border: 1px solid var(--line-soft);
            border-radius: 16px;
            padding: 16px;
            background: var(--card);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        }
        .activity-summary-card strong {
            display: block;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .activity-summary-card span {
            display: block;
            margin-top: 9px;
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
        }
        .activity-summary-card small {
            display: block;
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }
        .activity-feed {
            display: grid;
            gap: 12px;
        }
        .activity-entry {
            border: 1px solid var(--line-soft);
            border-radius: 18px;
            padding: 16px;
            background: var(--card);
            box-shadow: 0 12px 24px rgba(10, 28, 56, 0.06);
        }
        .activity-entry-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }
        .activity-time {
            display: grid;
            gap: 4px;
            min-width: 120px;
        }
        .activity-time strong {
            color: var(--text);
            font-size: 14px;
        }
        .activity-time span {
            color: var(--muted);
            font-size: 12px;
        }
        .activity-head-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }
        .activity-badge {
            background: var(--primary-soft);
            color: var(--primary);
        }
        .activity-badge--success {
            background: rgba(21, 131, 76, 0.14);
            color: var(--ok);
        }
        .activity-badge--warning {
            background: rgba(247, 148, 29, 0.16);
            color: #a25b08;
        }
        .activity-badge--danger {
            background: rgba(197, 58, 58, 0.14);
            color: var(--danger);
        }
        .activity-badge--accent {
            background: rgba(20, 95, 209, 0.13);
            color: var(--primary);
        }
        .activity-badge--default,
        .activity-badge--muted {
            background: color-mix(in srgb, var(--field-bg) 88%, #ffffff 12%);
            color: var(--muted);
        }
        .activity-entry-grid {
            display: grid;
            grid-template-columns: minmax(220px, 0.95fr) minmax(300px, 1.3fr) minmax(220px, 0.95fr);
            gap: 16px;
            margin-top: 14px;
        }
        .activity-block {
            display: grid;
            gap: 8px;
            min-width: 0;
        }
        .activity-block strong {
            color: var(--text);
            line-height: 1.5;
        }
        .activity-user {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .activity-avatar {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, rgba(20, 95, 209, 0.14), rgba(20, 95, 209, 0.06));
            color: var(--primary);
            border: 1px solid rgba(20, 95, 209, 0.12);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.04em;
        }
        .activity-user-copy {
            display: grid;
            gap: 4px;
            min-width: 0;
        }
        .activity-subject {
            font-size: 14px;
            line-height: 1.55;
        }
        .activity-muted,
        .activity-route,
        .activity-url {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.6;
            word-break: break-word;
        }
        .activity-payload {
            margin-top: 14px;
            border-top: 1px solid var(--line-soft);
            padding-top: 14px;
        }
        .activity-payload details {
            border: 1px solid var(--line-soft);
            border-radius: 14px;
            background: color-mix(in srgb, var(--field-bg) 88%, #ffffff 12%);
            padding: 10px 12px;
        }
        .activity-payload details[open] {
            border-color: #cbd9ee;
            background: color-mix(in srgb, var(--primary-soft) 22%, #ffffff 78%);
        }
        .activity-payload summary {
            list-style: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
            color: var(--text);
        }
        .activity-payload summary::-webkit-details-marker {
            display: none;
        }
        .activity-payload pre {
            margin: 10px 0 0;
            white-space: pre-wrap;
            word-break: break-word;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.65;
        }
        .activity-empty {
            display: grid;
            gap: 12px;
            padding: 20px;
            border: 1px dashed var(--line);
            border-radius: 18px;
            background: color-mix(in srgb, var(--field-bg) 84%, #ffffff 16%);
        }
        .activity-empty h3 {
            margin: 0;
            font-size: 18px;
        }
        .activity-empty p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }
        .activity-empty-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .activity-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 16px;
            padding-top: 4px;
        }
        .activity-pagination-copy {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }
        @media (max-width: 1200px) {
            .activity-summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .activity-entry-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 840px) {
            .activity-head {
                flex-direction: column;
            }
            .activity-actions {
                width: 100%;
                justify-content: flex-start;
            }
            .activity-summary-grid,
            .activity-entry-grid {
                grid-template-columns: 1fr;
            }
            .activity-entry-head {
                flex-direction: column;
            }
            .activity-head-meta {
                justify-content: flex-start;
            }
        }
        @media (max-width: 560px) {
            .activity-summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="activity-page">
        <section class="card activity-head-card">
            <div class="activity-head">
                <div class="activity-title">
                    <h1>Activity Logs</h1>
                    <p>See user login history, logout history, submissions, reviews, enrollments, and other important changes in one simple activity feed.</p>
                </div>
                <div class="activity-actions">
                    <form method="GET" action="{{ route('activity-logs.index') }}" class="activity-page-size-form">
                        <input type="hidden" name="user_id" value="{{ $activeUserId }}">
                        <input type="hidden" name="module" value="{{ $activeModule }}">
                        <input type="hidden" name="action" value="{{ $activeAction }}">
                        <input type="hidden" name="search" value="{{ $activeSearch }}">
                        <input type="hidden" name="from_date" value="{{ $activeFromDate }}">
                        <input type="hidden" name="to_date" value="{{ $activeToDate }}">
                        <label for="activityPerPage">Per Page</label>
                        <select name="per_page" id="activityPerPage" onchange="this.form.submit()">
                            @foreach ([8, 20, 50, 100] as $perPageOption)
                                <option value="{{ $perPageOption }}" @selected((int) $activePerPage === $perPageOption)>{{ $perPageOption }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div class="filter-wrap activity-filter-wrap">
                        <button type="button" class="filter-btn" data-filter-toggle="activityLogFilterPanel" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter Logs</span>
                        </button>
                        <div class="filter-panel" id="activityLogFilterPanel" aria-hidden="true">
                            <form method="GET" action="{{ route('activity-logs.index') }}">
                                <div class="filter-field">
                                    <label>User</label>
                                    <select name="user_id">
                                        <option value="">All Users</option>
                                        @foreach ($users as $filterUser)
                                            <option value="{{ $filterUser->id }}" @selected((int) $activeUserId === (int) $filterUser->id)>{{ $filterUser->name }} ({{ $filterUser->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Module</label>
                                    <select name="module">
                                        <option value="">All Modules</option>
                                        @foreach ($modules as $module)
                                            <option value="{{ $module }}" @selected($activeModule === $module)>{{ $module }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Action</label>
                                    <select name="action">
                                        <option value="">All Actions</option>
                                        @foreach ($actions as $action)
                                            <option value="{{ $action }}" @selected($activeAction === $action)>{{ \Illuminate\Support\Str::headline($action) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Search</label>
                                    <input type="text" name="search" value="{{ $activeSearch }}" placeholder="Subject, route, or description">
                                </div>
                                <div class="filter-field">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" value="{{ $activeFromDate }}">
                                </div>
                                <div class="filter-field">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" value="{{ $activeToDate }}">
                                </div>
                                <div class="filter-field">
                                    <label>Per Page</label>
                                    <select name="per_page">
                                        @foreach ([8, 20, 50, 100] as $perPageOption)
                                            <option value="{{ $perPageOption }}" @selected((int) $activePerPage === $perPageOption)>{{ $perPageOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-actions">
                                    <a class="btn btn-soft" href="{{ route('activity-logs.index') }}">Clear</a>
                                    <button class="btn" type="submit">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="activity-note">
                <span class="activity-note-chip">Login activity included</span>
                <span class="activity-note-chip">Logout activity included</span>
                <span class="activity-note-chip">Submissions and reviews included</span>
                <span class="activity-note-chip">Admin changes included</span>
                @if ($activeUserId || $activeModule !== '' || $activeAction !== '' || $activeSearch !== '' || $activeFromDate !== '' || $activeToDate !== '')
                    <span class="activity-note-chip">Filtered view</span>
                @endif
            </div>
        </section>

        <section class="activity-summary-grid">
            <article class="activity-summary-card">
                <strong>Total Events</strong>
                <span>{{ number_format($summary['total']) }}</span>
                <small>All activity rows for the current filter.</small>
            </article>
            <article class="activity-summary-card">
                <strong>Today</strong>
                <span>{{ number_format($summary['today']) }}</span>
                <small>Activity captured today.</small>
            </article>
            <article class="activity-summary-card">
                <strong>Logins</strong>
                <span>{{ number_format($summary['loginCount']) }}</span>
                <small>Successful sign in records.</small>
            </article>
            <article class="activity-summary-card">
                <strong>Logouts</strong>
                <span>{{ number_format($summary['logoutCount']) }}</span>
                <small>Successful sign out records.</small>
            </article>
            <article class="activity-summary-card">
                <strong>Submissions</strong>
                <span>{{ number_format($summary['submissionCount']) }}</span>
                <small>Student and demo submissions.</small>
            </article>
            <article class="activity-summary-card">
                <strong>Other Changes</strong>
                <span>{{ number_format($summary['changeCount']) }}</span>
                <small>Updates outside login/logout flow.</small>
            </article>
        </section>

        <section class="card">
            @if (! $loggingReady)
                <div class="activity-empty">
                    <h3>Activity logging is not ready yet.</h3>
                    <p>The <code>activity_logs</code> table is missing, so this page cannot show records until the latest migration is available in the database.</p>
                </div>
            @elseif ($logs->count() === 0)
                <div class="activity-empty">
                    <h3>No activity found yet.</h3>
                    <p>This page is ready, but there are no stored rows right now. Sign out and sign back in once, or make any update like assigning a course, reviewing a submission, or editing a user.</p>
                    <div class="activity-empty-list">
                        <span class="activity-note-chip">Login</span>
                        <span class="activity-note-chip">Logout</span>
                        <span class="activity-note-chip">User Update</span>
                        <span class="activity-note-chip">Enrollment</span>
                        <span class="activity-note-chip">Submission Review</span>
                    </div>
                </div>
            @else
                <div class="activity-feed">
                    @foreach ($logs as $log)
                        @php
                            $payload = $log->payloadForDisplay();
                            $actorName = $log->actorName();
                            $actorInitials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(\Illuminate\Support\Str::replace(' ', '', $actorName), 0, 2)) ?: 'SY';
                        @endphp
                        <article class="activity-entry">
                            <div class="activity-entry-head">
                                <div class="activity-time">
                                    <strong>{{ $log->created_at?->format('d M Y') }}</strong>
                                    <span>{{ $log->created_at?->format('h:i A') }}</span>
                                    <span>{{ $log->created_at?->diffForHumans() }}</span>
                                </div>
                                <div class="activity-head-meta">
                                    <span class="activity-badge activity-badge--{{ $log->actionTone() }}">{{ $log->actionLabel() }}</span>
                                    <span class="activity-badge activity-badge--default">{{ $log->module }}</span>
                                    <span class="activity-badge activity-badge--muted">{{ $log->method }}</span>
                                </div>
                            </div>

                            <div class="activity-entry-grid">
                                <div class="activity-block">
                                    <div class="activity-user">
                                        <div class="activity-avatar">{{ $actorInitials }}</div>
                                        <div class="activity-user-copy">
                                            <strong>{{ $actorName }}</strong>
                                            <span class="activity-muted">{{ $log->actorEmail() ?? 'No email snapshot stored' }}</span>
                                            @if ($log->actorRoleLabel())
                                                <span class="activity-muted">{{ $log->actorRoleLabel() }}</span>
                                            @endif
                                            @if ($log->ip_address)
                                                <span class="activity-muted">IP: {{ $log->ip_address }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="activity-block">
                                    <strong>{{ $log->description }}</strong>
                                    @if ($log->subject_label)
                                        <div class="activity-subject">{{ $log->subject_label }}</div>
                                    @else
                                        <div class="activity-subject">General activity</div>
                                    @endif
                                    @if ($log->route_name)
                                        <span class="activity-route">{{ $log->route_name }}</span>
                                    @endif
                                    @if ($log->url)
                                        <span class="activity-url">{{ $log->url }}</span>
                                    @endif
                                </div>

                                <div class="activity-block">
                                    <strong>Target Details</strong>
                                    @if ($log->subject_type)
                                        <span class="activity-muted">{{ class_basename($log->subject_type) }}{{ $log->subject_id ? ' #'.$log->subject_id : '' }}</span>
                                    @elseif ($log->subject_id)
                                        <span class="activity-muted">ID: {{ $log->subject_id }}</span>
                                    @else
                                        <span class="activity-muted">No direct model target stored.</span>
                                    @endif
                                    @if (! empty($payload))
                                        <span class="activity-muted">Extra request details available below.</span>
                                    @else
                                        <span class="activity-muted">No extra request payload stored.</span>
                                    @endif
                                </div>
                            </div>

                            @if (! empty($payload))
                                <div class="activity-payload">
                                    <details>
                                        <summary>View request details</summary>
                                        <pre>{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div class="activity-pagination">
                    <div class="activity-pagination-copy">
                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} activity log{{ $logs->total() === 1 ? '' : 's' }}.
                    </div>
                    <div>
                        {{ $logs->links('pagination.custom') }}
                    </div>
                </div>
            @endif
        </section>
    </div>

    <script src="{{ asset('js/filters.js') }}" defer></script>
@endsection
