<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <!-- ═══════════════════════════════════════════════
         SEO & META
    ═══════════════════════════════════════════════ -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Primary Meta -->
    <title>Payment Confirmation & Receipt | Academic Mantra Services</title>

    <meta name="description"
        content="Your payment has been confirmed successfully. Download your receipt and view your enrollment details with Academic Mantra Services.">

    <meta name="keywords"
        content="payment confirmation, payment receipt, Academic Mantra, invoice, enrollment confirmation, student payment">
    ">
    <meta name="author" content="Academic Mantra">
    <meta name="robots" content="noindex, nofollow"><!-- private panel -->
    <meta name="theme-color" content="#0947a8">

    <!-- Canonical -->
    <link rel="canonical" href="https://lms.academicmantra.com/dashboard">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Dashboard — Academic Mantra LMS">
    <meta property="og:description"
        content="Manage courses, students, trainers and certifications from one powerful dashboard.">
    <meta property="og:url" content="https://lms.academicmantra.com/dashboard">
    <meta property="og:site_name" content="Academic Mantra LMS">
    <meta property="og:image" content="https://lms.academicmantra.com/assets/og-cover.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Dashboard — Academic Mantra LMS">
    <meta name="twitter:description" content="Manage courses, students, trainers and certifications.">
    <meta name="twitter:image" content="https://lms.academicmantra.com/assets/og-cover.png">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('theme/images/am35.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('theme/images/am35.png') }}">

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons: Tabler + Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('theme/css/admin.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/category.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/broadcast.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/course-component.css') }}">
    <!-- Structured Data -->


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('style')
</head>

<body>

    <livewire:student.payment-success :token="$token" />
</body>

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

@stack('script')
@livewireScripts

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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
