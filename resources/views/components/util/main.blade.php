
{{-- ── JS ───────────────────────────────────────────────── --}}
<!-- ══════════════ TOPBAR ══════════════ -->
<header class="topbar" role="banner">

    <!-- Left -->
    <div class="topbar-left">

        <!-- Mobile hamburger -->
        <button class="tb-btn mobile-menu-btn" id="mobileMenuBtn" onclick="openMobileSidebar()" aria-label="Open navigation"
            aria-expanded="false" aria-controls="sidebar">
            <i class="ti ti-menu-2" aria-hidden="true"></i>
        </button>

        <div>
            <div class="topbar-title"> {{ $roleLabels[$user->role] ?? $user->role }} Panel</div>
            <div class="topbar-sub"> {{ $user->name }} — Academic Mantra LMS</div>
        </div>

        <div class="topbar-divider" aria-hidden="true"></div>

        <div class="topbar-tag" role="status" aria-live="polite">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            All systems live
        </div>
    </div>

    <!-- Right -->
    <div class="topbar-right">

        <!-- Search -->
        <div class="search-bar" role="search">
            <i class="fa-solid fa-search" aria-hidden="true"></i>
            <label for="globalSearch" class="sr-only" style="position:absolute;left:-9999px">Search students
                and courses</label>
            <input type="search" id="globalSearch" placeholder="Search students, courses..." autocomplete="off"
                aria-label="Search students and courses">
        </div>

        <!-- Notifications -->
        <div class="notif-shell">
            <button type="button" class="tb-btn" id="notifBtn"
                aria-label="Notifications — {{ $topbarUnreadCount ?? 0 }} unread" aria-expanded="false"
                aria-haspopup="true" onclick="toggleNotif()">
                <i class="fa-solid fa-bell" aria-hidden="true"></i>
                <span class="notif-badge"
                    aria-label="{{ $topbarUnreadCount ?? 0 }} unread notifications">{{ $topbarUnreadCount ?? 0 }}</span>
            </button>

            <div class="notif-popup" id="notifPopup" role="dialog" aria-label="Notifications">
                <div class="notif-popup-head">
                    <div>
                        <strong>Notifications</strong>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ $topbarUnreadCount ?? 0 }}
                            unread</div>
                    </div>
                    <button class="notif-clear" onclick="clearNotifs()">Mark all read</button>
                </div>
                <div class="notif-list" role="list">
                    @if ($topbarNotifications->count())
                        @foreach ($topbarNotifications as $notification)
                            @php
                                $title = $notification->data['title'] ?? 'Notification';
                                $message = $notification->data['message'] ?? '';
                                $sender = $notification->data['sender_name'] ?? 'System';

                                $icon = match ($notification->data['type'] ?? '') {
                                    'student' => 'ti-user-plus',
                                    'assignment' => 'ti-git-pull-request',
                                    'certificate' => 'ti-certificate',
                                    'broadcast' => 'ti-speakerphone',
                                    default => 'ti-bell',
                                };
                            @endphp

                            <div class="notif-item {{ is_null($notification->read_at) ? 'unread' : '' }}"
                                role="listitem">

                                <div class="notif-icon-wrap">
                                    <i class="ti {{ $icon }}" aria-hidden="true"></i>
                                </div>

                                <div>
                                    <div class="notif-content-title">
                                        {{ $title }}
                                    </div>

                                    <div class="notif-content-msg">
                                        {{ $message }}
                                    </div>

                                    <div class="notif-content-time">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            No notifications found.
                        </div>
                    @endif
                </div>
                <div class="notif-popup-footer" role="button" tabindex="0">
                    View all notifications <i class="ti ti-arrow-right" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <!-- Theme toggle -->
        <button type="button" class="tb-btn" id="themeBtn" aria-label="Toggle light/dark theme" title="Toggle theme"
            onclick="toggleTheme()">
            <i class="fa-solid fa-sun" id="themeBtnIcon" aria-hidden="true"></i>
        </button>

        <!-- Profile -->
        <div class="profile-shell">
            <button type="button" class="profile-trigger" id="profileBtn" aria-label="Profile menu"
                aria-expanded="false" aria-haspopup="true" onclick="toggleProfile()">
                <div class="tb-avatar" aria-hidden="true">  @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}"
                             alt="{{ $user->name }}">
                    @else
                        {{ $initials }}
                    @endif</div>
                <span class="profile-name">{{ auth()->user()->role ?? '' }}</span>
                <i class="ti ti-chevron-down" style="font-size:13px;color:var(--text-muted)" aria-hidden="true"></i>
            </button>

            <div class="profile-popup" id="profilePopup" role="dialog" aria-label="Profile menu">
                <div class="profile-popup-head">
                    <div class="profile-popup-avatar" aria-hidden="true">  @if ($avatarUrl)
                        <img src="{{ $avatarUrl }}"
                             alt="{{ $user->name }}">
                    @else
                        {{ $initials }}
                    @endif</div>
                    <div>
                        <div class="pp-name">{{ $user->name }}</div>
                        <div class="pp-role"><i class="fa-solid fa-shield-check" style="font-size:9px"
                                aria-hidden="true"></i>  {{ $roleLabels[$user->role] ?? $user->role }}</div>
                        <div class="pp-email">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="profile-popup-list">
                    <a href="/profile" class="popup-item">
                        <i class="fa-solid fa-user-circle" aria-hidden="true"></i>My Profile
                    </a>
                    <a href="/dashboard" class="popup-item">
                        <i class="fa-solid fa-layout-dashboard" aria-hidden="true"></i>Dashboard
                    </a>
                    <a href="/settings" class="popup-item">
                        <i class="fa-solid fa-gear" aria-hidden="true"></i>Settings
                    </a>
                    <div class="popup-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0">
                        @csrf
                        <button type="submit" class="popup-item danger" style="width:100%;text-align:left">
                            <i class="fa-solid fa-sign-out-alt" aria-hidden="true"></i>Sign out
                        </button>
                    </form>
                </div>
                <div class="profile-popup-footer">
                    <span class="online-dot"></span>
                    Online now · Academic Mantra LMS
                </div>
            </div>
        </div>

    </div>
</header>
