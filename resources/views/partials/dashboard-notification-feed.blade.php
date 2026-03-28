<div class="notification-list">
    @foreach ($notifications as $notification)
        @php
            $notificationTitle = $notification->data['title'] ?? 'Update';
            $notificationMessage = $notification->data['message'] ?? 'A new update is available.';
            $notificationSender = $notification->data['sender_name'] ?? 'System';
            $notificationAudience = $notification->data['audience'] ?? null;
        @endphp
        <article class="notification-card {{ is_null($notification->read_at) ? 'notification-card--unread' : '' }}">
            <div class="notification-card-top">
                <div class="notification-card-copy">
                    <span class="notification-kicker">{{ $notificationSender }}</span>
                    <strong>{{ $notificationTitle }}</strong>
                </div>
                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
            </div>
            <p class="muted">{{ $notificationMessage }}</p>
            <div class="notification-card-meta">
                @if ($notificationAudience)
                    <span class="notification-tag">{{ \Illuminate\Support\Str::headline((string) $notificationAudience) }}</span>
                @endif
                <span class="notification-tag notification-tag--soft">{{ is_null($notification->read_at) ? 'Unread' : 'Seen' }}</span>
            </div>
        </article>
    @endforeach
</div>
