<!-- ══════════════ FOOTER ══════════════ -->
<footer class="footer" role="contentinfo">
    <div class="footer-left">
        <span>© 2026 <strong>Academic Mantra LMS</strong>. All rights reserved.</span>
        <span class="footer-version">v2.4.1</span>
    </div>
    <div class="footer-right">
        <a href="/privacy" class="footer-link">Privacy Policy</a>
        <a href="/terms" class="footer-link">Terms of Service</a>
        <a href="/support" class="footer-link">Support</a>
        <a href="/docs" class="footer-link">Documentation</a>
    </div>
</footer>

</div><!-- /.app-shell -->

<!-- Sidebar collapse toggle (desktop) -->
<button class="sb-toggle" id="sbToggleBtn" aria-label="Collapse sidebar" aria-expanded="true" aria-controls="sidebar"
    onclick="toggleSidebar()">
    <i class="ti ti-chevron-left"></i>
</button>
 @stack('script')
@livewireScripts
<!-- ═══════════════════════════════════════════════
         JAVASCRIPT
    ═══════════════════════════════════════════════ -->
<script>
    /* ── Sidebar collapse ───────────────────── */
    const shell = document.getElementById('appShell');

    function toggleSidebar() {
        const collapsed = shell.classList.toggle('sb-collapsed');
        const btn = document.getElementById('sbToggleBtn');
        btn.setAttribute('aria-expanded', String(!collapsed));
        btn.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        try {
            localStorage.setItem('sb_collapsed', collapsed ? '1' : '0');
        } catch (e) {}
    }

    /* ── Mobile sidebar ─────────────────────── */
    function openMobileSidebar() {
        shell.classList.add('mobile-open');
        document.getElementById('mobileMenuBtn').setAttribute('aria-expanded', 'true');
        document.getElementById('sbOverlay').removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        shell.classList.remove('mobile-open');
        document.getElementById('mobileMenuBtn').setAttribute('aria-expanded', 'false');
        document.getElementById('sbOverlay').setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    /* ── Accordion groups ───────────────────── */
    function toggleGroup(id) {
        const grp = document.getElementById(id);
        const open = grp.classList.toggle('open');
        const trigger = grp.querySelector('.sb-group-trigger');
        trigger.setAttribute('aria-expanded', String(open));
    }

    /* ── Notifications ──────────────────────── */
    function toggleNotif() {
        const popup = document.getElementById('notifPopup');
        const btn = document.getElementById('notifBtn');
        const open = popup.classList.toggle('open');
        btn.setAttribute('aria-expanded', String(open));
        if (open) closeProfile();
    }

    function clearNotifs() {
        document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
        const badge = document.querySelector('.notif-badge');
        if (badge) badge.remove();
        document.getElementById('notifBtn').setAttribute('aria-label', 'Notifications — no unread');
    }

    /* ── Profile popup ──────────────────────── */
    function toggleProfile() {
        const popup = document.getElementById('profilePopup');
        const btn = document.getElementById('profileBtn');
        const open = popup.classList.toggle('open');
        btn.setAttribute('aria-expanded', String(open));
        if (open) closeNotif();
    }

    function closeProfile() {
        document.getElementById('profilePopup').classList.remove('open');
        document.getElementById('profileBtn').setAttribute('aria-expanded', 'false');
    }

    function closeNotif() {
        document.getElementById('notifPopup').classList.remove('open');
        document.getElementById('notifBtn').setAttribute('aria-expanded', 'false');
    }

    /* ── Theme toggle ───────────────────────── */
    function toggleTheme() {
        const html = document.documentElement;
        const dark = html.getAttribute('data-theme') === 'dark';
        html.setAttribute('data-theme', dark ? 'light' : 'dark');
        document.getElementById('themeBtnIcon').className = dark ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
        document.getElementById('themeBtn').setAttribute('aria-label', dark ? 'Switch to dark theme' :
            'Switch to light theme');
        try {
            localStorage.setItem('theme', dark ? 'light' : 'dark');
        } catch (e) {}
    }

    /* ── Close popups on outside click ─────── */
    document.addEventListener('click', function(e) {
        if (!document.getElementById('notifBtn').contains(e.target) &&
            !document.getElementById('notifPopup').contains(e.target)) {
            closeNotif();
        }
        if (!document.getElementById('profileBtn').contains(e.target) &&
            !document.getElementById('profilePopup').contains(e.target)) {
            closeProfile();
        }
    });

    /* ── Close popups on Escape ─────────────── */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotif();
            closeProfile();
            closeMobileSidebar();
        }
    });

    /* ── Persist preferences on load ────────── */
    (function init() {
        try {
            const theme = localStorage.getItem('theme');
            if (theme) {
                document.documentElement.setAttribute('data-theme', theme);
                document.getElementById('themeBtnIcon').className =
                    theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
            if (localStorage.getItem('sb_collapsed') === '1') {
                shell.classList.add('sb-collapsed');
                document.getElementById('sbToggleBtn').setAttribute('aria-expanded', 'false');
            }
        } catch (e) {}
    })();
</script>
{{-- 
<script src="{{ asset('theme/js/index.js') }}" defer></script>
<script src="{{ asset('theme/js/header.js') }}" defer></script> --}}
@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#ef4444'
        });
    </script>
@endif

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            confirmButtonColor: '#22c55e'
        });
    </script>
@endif
</body>

</html>
