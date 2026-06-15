

<header class="topbar">

    {{-- ── LEFT ─────────────────────────────────────────── --}}
    <div class="topbar-left">
        <div class="topbar-crumb">
            <div class="topbar-title">
                {{ $roleLabels[$user->role] ?? $user->role }} Panel
            </div>
            <div class="topbar-sub">
                {{ $user->name }} — Academic Mantra LMS
            </div>
        </div>

        <div class="topbar-divider"></div>

        <div class="topbar-tag">
            <i class="fa-solid  fa-circle-check" aria-hidden="true"></i>
            All systems live
        </div>
    </div>

    {{-- ── RIGHT ────────────────────────────────────────── --}}
    <div class="topbar-right">

        {{-- Search --}}
        <div class="search-bar">
            <i class="fa-solid  fa-search" aria-hidden="true"></i>
            <input
                type="text"
                placeholder="Search students, courses..."
                autocomplete="off">
        </div>

        {{-- ── Notifications ────────────────────────────── --}}
        <div class="notification-shell">

            <button
                type="button"
                class="tb-btn notification-trigger {{ $topbarUnreadCount > 0 ? 'has-unread' : '' }}"
                id="notificationToggle"
                aria-label="Notifications ({{ $topbarUnreadCount }} unread)"
                aria-expanded="false"
                aria-haspopup="true">

                <i class="fa-solid  fa-bell" aria-hidden="true"></i>

                @if($topbarUnreadCount > 0)
                    <span class="notification-badge" aria-hidden="true">
                        {{ min($topbarUnreadCount, 99) }}
                    </span>
                @endif

            </button>

            {{-- Notification Popup --}}
            <div
                class="notification-popup"
                id="notificationPopup"
                role="region"
                aria-label="Notifications">

                {{-- Popup Head --}}
                <div class="notification-popup-head">
                    <div>
                        <strong>Notifications</strong>
                        <div class="notification-popup-summary">
                            <span class="notif-count-pill {{ $topbarUnreadCount === 0 ? 'is-zero' : '' }}">
                                <i class="fa-solid  fa-circle-dot" aria-hidden="true" style="font-size:10px"></i>
                                {{ $topbarUnreadCount > 0 ? $topbarUnreadCount.' unread' : 'All read' }}
                            </span>
                            <span class="notif-count-pill is-zero">
                                {{ $topbarNotifications->count() }} shown
                            </span>
                        </div>
                    </div>

                    @if($topbarUnreadCount > 0)
                        <form method="POST"
                              action="{{ route('notifications.read-all') }}"
                              style="margin:0">
                            @csrf
                            <button type="submit"
                                    class="notif-head-action">
                                Mark all read
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Notification List --}}
                @if($topbarNotifications->count())

                    <div class="notification-popup-list">

                        {{-- Unread Group --}}
                        @if($topbarUnreadNotifications->isNotEmpty())
                            <div class="notif-group-label">New</div>

                            @foreach($topbarUnreadNotifications as $notification)
                                @php
                                    $title    = $notification->data['title']   ?? 'Notification';
                                    $message  = $notification->data['message'] ?? '';
                                    $sender   = $notification->data['sender_name'] ?? 'System';
                                    $initials = strtoupper(
                                        substr(str_replace(' ', '', $title), 0, 2)
                                    ) ?: 'NT';
                                @endphp

                                <div class="notification-popup-item is-unread">
                                    <div class="notif-item-icon">{{ $initials }}</div>

                                    <div class="notification-popup-copy">
                                        <strong>{{ $title }}</strong>
                                        @if($message)
                                            <p>{{ $message }}</p>
                                        @endif
                                        <small>
                                            {{ $sender }} &middot; {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>

                                    <form method="POST"
                                          action="{{ route('notifications.read', $notification) }}"
                                          style="margin:0;flex-shrink:0">
                                        @csrf
                                        <button type="submit"
                                                class="notif-mark-read"
                                                title="Mark as read"
                                                aria-label="Mark as read">
                                            <i class="fa-solid  fa-check" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @endif

                        {{-- Seen Group --}}
                        @if($topbarSeenNotifications->isNotEmpty())
                            <div class="notif-group-label"
                                 style="margin-top: {{ $topbarUnreadNotifications->isNotEmpty() ? '6px' : '0' }}">
                                Earlier
                            </div>

                            @foreach($topbarSeenNotifications as $notification)
                                @php
                                    $title    = $notification->data['title']   ?? 'Notification';
                                    $message  = $notification->data['message'] ?? '';
                                    $sender   = $notification->data['sender_name'] ?? 'System';
                                    $initials = strtoupper(
                                        substr(str_replace(' ', '', $title), 0, 2)
                                    ) ?: 'NT';
                                @endphp

                                <div class="notification-popup-item">
                                    <div class="notif-item-icon"
                                         style="opacity:.6">{{ $initials }}</div>

                                    <div class="notification-popup-copy">
                                        <strong style="color:#b8aee8">{{ $title }}</strong>
                                        @if($message)
                                            <p>{{ $message }}</p>
                                        @endif
                                        <small>
                                            {{ $sender }} &middot; {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    </div>

                @else

                    <div class="notification-popup-empty">
                        <i class="fa-solid  fa-bell-off" aria-hidden="true"></i>
                        No notifications yet
                    </div>

                @endif

                {{-- Popup Footer --}}
                <div class="notification-popup-footer">
                    <a href="{{ route('dashboard') }}"
                       class="notif-footer-link">
                        View all
                        <i class="fa-solid  fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <span style="font-size:10px;color:#4e4480">
                        {{ $topbarNotifications->count() }} of {{ $topbarUnreadCount + $topbarSeenNotifications->count() }} notifications
                    </span>
                </div>

            </div>
        </div>

        {{-- ── Theme Toggle ──────────────────────────────── --}}
        <button
            type="button"
            class="tb-btn"
            id="themeBtn"
            aria-label="Toggle theme"
            title="Toggle theme">
            <i class="fa-solid  fa-sun" id="themeBtnIcon" aria-hidden="true"></i>
        </button>

        {{-- ── Profile ───────────────────────────────────── --}}
        <div class="profile-shell">

            <button
                type="button"
                class="profile-trigger"
                id="profileToggle"
                aria-label="Profile menu"
                aria-expanded="false"
                aria-haspopup="true">

                <div class="tb-avatar" title="{{ $user->name }}">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}"
                             alt="{{ $user->name }}">
                    @else
                        {{ $initials }}
                    @endif
                </div>

            </button>

            {{-- Profile Popup --}}
            <div
                class="profile-popup"
                id="profilePopup"
                role="dialog"
                aria-label="Profile">

                {{-- Profile Head --}}
                <div class="profile-popup-head">
                    <a href="{{ route('profile.edit') }}"
                       class="profile-popup-head-link">

                        <div class="avatar">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}"
                                     alt="{{ $user->name }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>

                        <div class="profile-meta">
                            <strong>{{ $user->name }}</strong>
                            <span class="profile-role-badge">
                                <i class="fa-solid  fa-shield-check"
                                   aria-hidden="true"
                                   style="font-size:10px"></i>
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                            <span>{{ $user->email }}</span>
                        </div>

                    </a>
                </div>

                {{-- Profile Nav --}}
                <div class="profile-popup-list">

                    <a href="{{ route('profile.edit') }}"
                       class="popup-item">
                        <i class="fa-solid  fa-user-circle" aria-hidden="true"></i>
                        <span>My Profile</span>
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="popup-item">
                        <i class="fa-solid  fa-layout-dashboard" aria-hidden="true"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="#"
                       class="popup-item">
                        <i class="fa-solid  fa-settings" aria-hidden="true"></i>
                        <span>Settings</span>
                    </a>

                    <div class="popup-divider"></div>

                    <form method="POST"
                          action="{{ route('logout') }}"
                          style="margin:0">
                        @csrf
                        <button
                            type="submit"
                            class="popup-item danger">
                            <i class="fa-solid  fa-sign-out-alt" aria-hidden="true"></i>
                            <span>Log out</span>
                        </button>
                    </form>

                </div>

                {{-- Online Status --}}
                <div class="profile-popup-footer">
                    <div class="profile-popup-footer-meta">
                        <span class="online-dot"></span>
                        Online now · Academic Mantra LMS
                    </div>
                </div>

            </div>

        </div>
    </div>
</header>

{{-- ── JS ───────────────────────────────────────────────── --}}
