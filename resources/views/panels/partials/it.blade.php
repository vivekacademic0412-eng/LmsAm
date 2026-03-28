<section class="summary-grid">
    @foreach ($itMetrics as $metric)
        <article class="summary-card">
            <span>{{ $metric['label'] }}</span>
            <strong>{{ number_format((int) $metric['value']) }}</strong>
            <p>{{ $metric['hint'] }}</p>
        </article>
    @endforeach
</section>

<section class="panel-actions">
    <h2>Service &amp; Integration Status</h2>
    <p class="feed-note">Readiness checks for auditing, notifications, secure media, and the active course-builder structure.</p>
    <div class="status-grid" style="margin-top: 12px;">
        @foreach ($itServiceStatuses as $status)
            <article class="status-card">
                <div class="status-card-head">
                    <strong>{{ $status['label'] }}</strong>
                    <span class="pill-tag {{ $status['tone'] }}">{{ $status['state'] }}</span>
                </div>
                <p>{{ $status['detail'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<div class="panel-wide-grid">
    <section class="panel-actions">
        <h2>Recent Security Events</h2>
        <p class="feed-note">Latest authentication events captured by the activity logger.</p>
        <div class="feed-list" style="margin-top: 12px;">
            @forelse ($itSecurityEvents as $event)
                <article class="feed-item">
                    <div class="feed-item-top">
                        <div>
                            <strong>{{ $event['actor'] }}</strong>
                            <div class="feed-meta">
                                <span>{{ $event['role'] }}</span>
                                <span>{{ $event['when'] }}</span>
                            </div>
                        </div>
                        <span class="pill-tag {{ $event['tone'] }}">{{ $event['action'] }}</span>
                    </div>
                    <div class="feed-meta">
                        <span>{{ $event['description'] }}</span>
                    </div>
                    <div class="feed-meta">
                        <span>{{ $event['ip_address'] }}</span>
                    </div>
                </article>
            @empty
                <div class="empty-note">No authentication activity is available yet.</div>
            @endforelse
        </div>
    </section>

    <section class="panel-actions">
        <h2>Recent Platform Changes</h2>
        <p class="feed-note">Latest non-authentication updates that can affect content, workflow, or data visibility.</p>
        <div class="feed-list" style="margin-top: 12px;">
            @forelse ($itChangeEvents as $event)
                <article class="feed-item">
                    <div class="feed-item-top">
                        <div>
                            <strong>{{ $event['module'] }}</strong>
                            <div class="feed-meta">
                                <span>{{ $event['actor'] }}</span>
                                <span>{{ $event['when'] }}</span>
                            </div>
                        </div>
                        <span class="pill-tag {{ $event['tone'] }}">{{ $event['action'] }}</span>
                    </div>
                    <div class="feed-meta">
                        <span>{{ $event['subject'] }}</span>
                    </div>
                    <div class="feed-meta">
                        <span>{{ $event['description'] }}</span>
                    </div>
                </article>
            @empty
                <div class="empty-note">No platform change events are available yet.</div>
            @endforelse
        </div>
    </section>
</div>

<section class="panel-actions">
    <h2>Content Delivery Footprint</h2>
    <p class="feed-note">Storage and delivery signals that help IT understand what the platform is currently serving.</p>
    <div class="signal-grid" style="margin-top: 12px;">
        @foreach ($itContentFootprint as $signal)
            <article class="signal-card">
                <span>{{ $signal['label'] }}</span>
                <strong>{{ number_format((int) $signal['value']) }}</strong>
                <p>{{ $signal['hint'] }}</p>
            </article>
        @endforeach
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
