@php
    $unreadCount = $notifications->whereNull('read_at')->count();
    $latestNotification = $notifications->first();
@endphp

<div class="notification-feed">
    <div class="notification-summary">
        <div class="notification-summary-copy">
            <span class="notification-summary-kicker">Inbox</span>
            <strong>
                {{ $unreadCount > 0 ? $unreadCount.' new update'.($unreadCount === 1 ? '' : 's') : 'All caught up' }}
            </strong>
            <p>
                @if ($latestNotification)
                    Latest update {{ $latestNotification->created_at->diffForHumans() }}.
                @else
                    New alerts will appear here automatically.
                @endif
            </p>
        </div>
        <div class="notification-summary-actions">
            <span class="notification-summary-pill">{{ $notifications->count() }} total</span>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="notification-summary-btn">Mark All Read</button>
                </form>
            @endif
        </div>
    </div>

    <div class="notification-list">
        @foreach ($notifications as $notification)
            @php
                $notificationTitle = $notification->data['title'] ?? 'Update';
                $notificationMessage = $notification->data['message'] ?? 'A new update is available.';
                $notificationSender = $notification->data['sender_name'] ?? 'System';
                $notificationAudience = $notification->data['audience'] ?? null;
                $notificationLowerTitle = \Illuminate\Support\Str::lower($notificationTitle.' '.$notificationMessage);
                $notificationTone = match (true) {
                    \Illuminate\Support\Str::contains($notificationLowerTitle, 'quiz') => 'quiz',
                    \Illuminate\Support\Str::contains($notificationLowerTitle, 'submission') => 'submission',
                    !empty($notificationAudience) => 'broadcast',
                    default => 'system',
                };
                $notificationMonogram = \Illuminate\Support\Str::upper(
                    \Illuminate\Support\Str::substr(\Illuminate\Support\Str::replace(' ', '', $notificationTitle), 0, 2)
                ) ?: 'UP';
            @endphp
            <article class="notification-card notification-card--{{ $notificationTone }} {{ is_null($notification->read_at) ? 'notification-card--unread' : '' }}">
                <div class="notification-card-top">
                    <div class="notification-card-lead">
                        <div class="notification-avatar notification-avatar--{{ $notificationTone }}">{{ $notificationMonogram }}</div>
                        <div class="notification-card-copy">
                            <span class="notification-kicker">{{ $notificationSender }}</span>
                            <strong>{{ $notificationTitle }}</strong>
                        </div>
                    </div>
                    <div class="notification-card-side">
                        <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                        @if (is_null($notification->read_at))
                            <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                @csrf
                                <button type="submit" class="notification-inline-btn">Mark Read</button>
                            </form>
                        @endif
                    </div>
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
</div>
