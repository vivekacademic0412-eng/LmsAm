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
