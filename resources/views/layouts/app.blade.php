<x-util.header :title="'Admin Dashboard — Academic Mantra LMS'">
</x-util.header>
<x-util.aside />

<main class="main">
    <x-util.main/>
    <!-- Page Body -->
    <div class="page-body">
        @yield('content')
    </div>

    {{-- <!-- Footer -->
    <footer class="footer">
        <div class="footer-brand">
            <div class="sb-logo-mark" style="width:24px;height:24px;font-size:12px;border-radius:6px">A</div>
            Academic Mantra LMS
        </div>
        <div class="footer-links">
            <a href="#" class="footer-link">Docs</a>
            <a href="#" class="footer-link">Support</a>
            <a href="#" class="footer-link">Privacy</a>
            <a href="#" class="footer-link">v2.4.1</a>
        </div>
        <span>© 2026 Academic Mantra · All rights reserved</span>
    </footer> --}}
  
</main>
<x-util.footer />
