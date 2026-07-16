{{-- resources/views/components/demo/navigation.blade.php --}}
<nav class="nav-root" role="navigation" aria-label="Main navigation">
    <div class="nav-inner">

        {{-- Logo --}}
        <a href="https://www.academicmantraservices.com" class="nav-logo">
            {{-- <img src="{{ asset('theme/images/logo.png') }}" alt="Academic Mantra" loading="lazy"> --}}
            <img src="{{ asset('theme/images/am35.png') }}" alt="LIVE Skills" class="brand-logo"
                title="LIVE Skills Training Programs" loading="lazy"></a>

        <img src="{{ asset('theme/images/am21.png') }}" alt="Academic Mantra Services" class="brand-logo am-logo"
            title="Academic Mantra Services" loading="lazy">
        </a>

        {{-- Desktop nav links --}}
        {{-- <ul class="nav-links" role="list">
            <li>
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <a href="{{ url('/courses') }}" class="{{ request()->is('courses*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                    Courses
                </a>
            </li>
            <li>
                <a href="{{ url('/live-training') }}" class="{{ request()->is('live-training*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />
                        <polygon points="10 8 16 12 10 16 10 8" />
                    </svg>
                    Live Training
                </a>
            </li>
            <li>
                <a href="{{ url('/about') }}" class="{{ request()->is('about*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4M12 8h.01" />
                    </svg>
                    About
                </a>
            </li>
        </ul> --}}

        {{-- Desktop right actions --}}
        <div class="nav-actions">

            {{-- Theme toggle --}}
            <button class="nav-theme-btn" id="navThemeBtn" aria-label="Toggle dark mode" title="Toggle dark mode">
                <svg class="sun" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="5" />
                    <line x1="12" y1="1" x2="12" y2="3" />
                    <line x1="12" y1="21" x2="12" y2="23" />
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                    <line x1="1" y1="12" x2="3" y2="12" />
                    <line x1="21" y1="12" x2="23" y2="12" />
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                </svg>
                <svg class="moon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round" style="display:none"
                    aria-hidden="true">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z" />
                </svg>
            </button>

            @auth
                {{-- Logged in: user pill + logout --}}
                <div class="nav-user" aria-label="Logged in as {{ auth()->user()->name }}">
                    <div class="nav-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="nav-user-info">
                        <span class="nav-user-name">{{ auth()->user()->name }}</span>
                        <span class="nav-user-role">{{ ucfirst(auth()->user()->role ?? 'Learner') }}</span>
                    </div>
                </div>

                <a href="{{ url('/dashboard') }}" class="btn-nav btn-outline">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                    </svg>
                    Dashboard
                </a>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="btn-nav btn-danger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                        Logout
                    </button>
                </form>
            @else
                {{-- Guest: Register + Login --}}
                @if (request()->routeIs('lms.demo'))
                    {{-- Register page par Login button dikhe --}}
                    <a href="{{ route('login') }}" class="btn-nav btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" aria-hidden="true">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                        Login
                    </a>
                @elseif (request()->routeIs('login'))
                    {{-- Login page par Register button dikhe --}}
                    <a href="{{ route('lms.demo') }}" class="btn-nav btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                            <line x1="19" y1="8" x2="19" y2="14" />
                            <line x1="22" y1="11" x2="16" y2="11" />
                        </svg>
                        Register
                    </a>
                @else
                    {{-- Baaki sab pages par dono buttons dikhenge --}}
                    <a href="{{ route('lms.demo') }}" class="btn-nav btn-outline">
                        Register
                    </a>

                    <a href="{{ route('login') }}" class="btn-nav btn-primary">
                        Login
                    </a>
                @endif
            @endauth

            {{-- Hamburger (mobile) --}}
            <button class="nav-burger" id="navBurger" aria-label="Open navigation menu" aria-expanded="false"
                aria-controls="navDrawer">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>
    </div>
</nav>

{{-- Overlay --}}
<div class="nav-overlay" id="navOverlay" aria-hidden="true"></div>

{{-- Mobile Drawer --}}
<div class="nav-drawer" id="navDrawer" role="dialog" aria-label="Mobile navigation" aria-modal="true">

    @auth
        {{-- User card --}}
        <div class="drawer-user-card">
            <div class="drawer-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="drawer-user-name">{{ auth()->user()->name }}</div>
                <div class="drawer-user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>
    @endauth

    {{-- Nav links --}}
    <a href="{{ url('/') }}" class="drawer-link {{ request()->is('/') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            aria-hidden="true">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <polyline points="9 22 9 12 15 12 15 22" />
        </svg>
        Home
    </a>
    <a href="{{ url('/courses') }}" class="drawer-link {{ request()->is('courses*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            aria-hidden="true">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
        </svg>
        Courses
    </a>
    <a href="{{ url('/live-training') }}" class="drawer-link {{ request()->is('live-training*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <polygon points="10 8 16 12 10 16 10 8" />
        </svg>
        Live Training
    </a>
    <a href="{{ url('/about') }}" class="drawer-link {{ request()->is('about*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 16v-4M12 8h.01" />
        </svg>
        About
    </a>

    @auth
        <a href="{{ url('/dashboard') }}" class="drawer-link {{ request()->is('dashboard*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                aria-hidden="true">
                <rect x="3" y="3" width="7" height="7" />
                <rect x="14" y="3" width="7" height="7" />
                <rect x="14" y="14" width="7" height="7" />
                <rect x="3" y="14" width="7" height="7" />
            </svg>
            Dashboard
        </a>
    @endauth

    <hr class="drawer-divider">

    @auth
        {{-- Logged in: logout button --}}
        <form method="POST" action="{{ route('logout') }}" style="display:contents">
            @csrf
            <button type="submit" class="drawer-btn drawer-btn-danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Logout
            </button>
        </form>
    @else
        {{-- Guest: register + login --}}
        <a href="{{ route('lms.demo') }}" class="drawer-btn drawer-btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
                <line x1="19" y1="8" x2="19" y2="14" />
                <line x1="22" y1="11" x2="16" y2="11" />
            </svg>
            Create Account
        </a>
        <a href="{{ route('login') }}" class="drawer-btn drawer-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                aria-hidden="true">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                <polyline points="10 17 15 12 10 7" />
                <line x1="15" y1="12" x2="3" y2="12" />
            </svg>
            Sign In
        </a>
    @endauth

</div>

<script>
    (function() {
        /* ── Theme ── */
        const themeBtn = document.getElementById('navThemeBtn');
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        themeBtn?.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        });

        /* ── Hamburger / drawer ── */
        const burger = document.getElementById('navBurger');
        const drawer = document.getElementById('navDrawer');
        const overlay = document.getElementById('navOverlay');

        function openDrawer() {
            burger.classList.add('open');
            drawer.classList.add('open');
            overlay.classList.add('open');
            burger.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            burger.classList.remove('open');
            drawer.classList.remove('open');
            overlay.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }

        burger?.addEventListener('click', () => {
            drawer.classList.contains('open') ? closeDrawer() : openDrawer();
        });

        overlay?.addEventListener('click', closeDrawer);

        /* close on drawer link click */
        drawer?.querySelectorAll('.drawer-link').forEach(link => {
            link.addEventListener('click', closeDrawer);
        });

        /* close on Escape */
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && drawer?.classList.contains('open')) closeDrawer();
        });

        /* close drawer if viewport grows past mobile breakpoint */
        const mq = window.matchMedia('(min-width: 769px)');
        mq.addEventListener('change', e => {
            if (e.matches) closeDrawer();
        });
    })();
</script>
