<section class="summary-grid">
    @foreach ($managerHrMetrics as $metric)
        <article class="summary-card">
            <span>{{ $metric['label'] }}</span>
            <strong>
                {{ number_format((int) $metric['value']) }}@if (!empty($metric['suffix'])){{ $metric['suffix'] }}@endif
            </strong>
            <p>{{ $metric['hint'] }}</p>
        </article>
    @endforeach
</section>

<section class="panel-actions">
    <h2>Export Reports</h2>
    <p class="feed-note">Download the current HR reporting snapshot in Excel or PDF format.</p>
    <div class="export-grid" style="margin-top: 12px;">
        @foreach ($managerHrReportExports as $report)
            <article class="export-card">
                <div>
                    <strong>{{ $report['label'] }}</strong>
                    <p>{{ $report['description'] }}</p>
                </div>
                <div class="export-actions">
                    <a class="export-btn" href="{{ $report['excel_route'] }}">Excel</a>
                    <a class="export-btn" href="{{ $report['pdf_route'] }}">PDF</a>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="panel-actions">
    <h2>Training Pipeline</h2>
    <p class="feed-note">Use this snapshot to spot completion momentum, stalled enrollments, and learners who may need HR follow-up.</p>
    <div class="pipeline-grid" style="margin-top: 12px;">
        @foreach ($managerHrPipeline as $metric)
            <article class="pipeline-card">
                <span>{{ $metric['label'] }}</span>
                <strong>{{ number_format((int) $metric['value']) }}</strong>
                <p>{{ $metric['hint'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="panel-actions">
    <h2>Attendance & Progress Focus</h2>
    <p class="feed-note">Weekly attendance trend on the left; per-student attendance and course progress on the right.</p>

    <div class="hr-focus-grid" style="margin-top: 12px;">
        {{-- LEFT: weekly attendance trend --}}
        <div class="pipeline-card" style="padding: 16px 18px;">
            <span>Weekly Attendance Trend</span>
            <div class="mini-bars">
                @php
                    // Dummy data — replace with a real weekly attendance query
                    $weeklyAttendanceTrend = [
                        ['label' => 'Mon', 'percent' => 92],
                        ['label' => 'Tue', 'percent' => 88],
                        ['label' => 'Wed', 'percent' => 95],
                        ['label' => 'Thu', 'percent' => 81],
                        ['label' => 'Fri', 'percent' => 74],
                    ];
                @endphp
                @foreach ($weeklyAttendanceTrend as $day)
                    <div class="mini-bar-col">
                        <div class="mini-bar" style="height: {{ $day['percent'] }}%;"></div>
                        <small>{{ $day['percent'] }}%</small>
                        <span>{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <p style="margin-top: 10px;">Average weekly attendance: <strong style="color: var(--text);">86%</strong> — Friday shows the steepest drop-off.</p>
        </div>

        {{-- RIGHT: stacked attendance + progress --}}
        <div class="hr-side-stack">
            <div class="panel-actions" style="padding: 16px 18px;">
                <h2 style="font-size: 16px; margin-bottom: 10px;">Per-Student Attendance</h2>
                @php
                    // Dummy data — replace with a real attendance query
                    $studentAttendance = [
                        ['name' => 'Ravi Malhotra', 'course' => 'Frontend Basics', 'days' => ['present', 'present', 'present', 'late', 'present'], 'rate' => '96%'],
                        ['name' => 'Simran Kaur', 'course' => 'Data Analytics', 'days' => ['present', 'absent', 'present', 'present', 'present'], 'rate' => '84%'],
                        ['name' => 'Arjun Nair', 'course' => 'Cloud Fundamentals', 'days' => ['present', 'present', 'late', 'present', 'absent'], 'rate' => '78%'],
                        ['name' => 'Priya Sharma', 'course' => 'UI/UX Design', 'days' => ['present', 'present', 'present', 'present', 'present'], 'rate' => '100%'],
                    ];
                @endphp
                <div class="attendance-list">
                    @foreach ($studentAttendance as $s)
                        <div class="attendance-row">
                            <div class="attendance-who">
                                <strong>{{ $s['name'] }}</strong>
                                <span>{{ $s['course'] }}</span>
                            </div>
                            <div class="attendance-days">
                                @foreach ($s['days'] as $d)
                                    <span class="attendance-dot {{ $d }}" title="{{ ucfirst($d) }}"></span>
                                @endforeach
                            </div>
                            <span class="attendance-rate">{{ $s['rate'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel-actions" style="padding: 16px 18px;">
                <h2 style="font-size: 16px; margin-bottom: 10px;">Student Progress</h2>
                @php
                    // Dummy data — replace with your existing progressItems relation
                    $studentProgress = [
                        ['name' => 'Ravi Malhotra', 'course' => 'Frontend Basics', 'percent' => 82],
                        ['name' => 'Simran Kaur', 'course' => 'Data Analytics', 'percent' => 55],
                        ['name' => 'Arjun Nair', 'course' => 'Cloud Fundamentals', 'percent' => 38],
                        ['name' => 'Priya Sharma', 'course' => 'UI/UX Design', 'percent' => 97],
                    ];
                @endphp
                <div class="progress-list">
                    @foreach ($studentProgress as $p)
                        <div>
                            <div class="progress-row-top">
                                <strong>{{ $p['name'] }}</strong>
                                <span class="progress-pct">{{ $p['percent'] }}%</span>
                            </div>
                            <span style="font-size: 11px; color: var(--text-muted);">{{ $p['course'] }}</span>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ $p['percent'] }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<div class="panel-wide-grid">
    <section class="panel-actions">
        <h2>Recent Assignments</h2>
        <p class="feed-note">Latest learner-course assignments with trainer coverage and progress context.</p>
        <div class="feed-list" style="margin-top: 12px;">
            @forelse ($managerHrRecentAssignments as $assignment)
                <article class="feed-item">
                    <div class="feed-item-top">
                        <div>
                            <strong>{{ $assignment['learner_name'] }}</strong>
                            <div class="feed-meta">
                                <span>{{ $assignment['learner_email'] }}</span>
                                <span>{{ $assignment['assigned_at'] }}</span>
                            </div>
                        </div>
                        <span class="pill-tag">{{ $assignment['progress_percent'] }}%</span>
                    </div>
                    <div class="feed-tags">
                        <span class="pill-tag">{{ $assignment['course_title'] }}</span>
                        <span class="pill-tag muted">{{ $assignment['category_name'] }}</span>
                    </div>
                    <div class="feed-meta">
                        <span>Trainer: {{ $assignment['trainer_name'] }}</span>
                        <span>Assigned by: {{ $assignment['assigned_by'] }}</span>
                        <span>{{ $assignment['progress_label'] }}</span>
                    </div>
                </article>
            @empty
                <div class="empty-note">No course assignments have been created yet.</div>
            @endforelse
        </div>
    </section>

    <section class="panel-actions">
        <h2>Learners Needing Follow-up</h2>
        <p class="feed-note">Priority list for inactive accounts, missing trainer ownership, and low activity enrollments.</p>
        <div class="feed-list" style="margin-top: 12px;">
            @forelse ($managerHrAttentionRows as $row)
                <article class="feed-item">
                    <div class="feed-item-top">
                        <div>
                            <strong>{{ $row['learner_name'] }}</strong>
                            <div class="feed-meta">
                                <span>{{ $row['course_title'] }}</span>
                                <span>{{ $row['assigned_at'] }}</span>
                            </div>
                        </div>
                        <span class="pill-tag {{ $row['tone'] ?? 'muted' }}">{{ $row['progress_percent'] }}%</span>
                    </div>
                    <div class="feed-tags">
                        <span class="pill-tag {{ $row['tone'] ?? 'muted' }}">{{ $row['reason'] ?? 'Needs follow-up' }}</span>
                    </div>
                    <div class="feed-meta">
                        <span>Trainer: {{ $row['trainer_name'] }}</span>
                    </div>
                </article>
            @empty
                <div class="empty-note">No high-priority follow-up items right now.</div>
            @endforelse
        </div>
    </section>
</div>

<section class="panel-actions">
    <h2>Top Training Categories</h2>
    <p class="feed-note">Category demand based on assigned enrollments across the catalog.</p>
    <div class="feed-list" style="margin-top: 12px;">
        @forelse ($managerHrCategoryDemand as $category)
            <article class="feed-item">
                <div class="feed-item-top">
                    <strong>{{ $category['name'] }}</strong>
                    <span class="pill-tag">{{ number_format($category['enrollments_count']) }} enrollments</span>
                </div>
                <div class="feed-meta">
                    <span>{{ number_format($category['course_count']) }} course{{ $category['course_count'] === 1 ? '' : 's' }}</span>
                </div>
            </article>
        @empty
            <div class="empty-note">No category demand data is available yet.</div>
        @endforelse
    </div>
</section>

<section class="panel-actions">
    <h2>Available Actions</h2>
    <div class="action-grid">
        @foreach ($quickActions as $action)
            <a class="action-link" href="{{ $action['route'] }}">{{ $action['label'] }}</a>
        @endforeach
    </div>
</section>