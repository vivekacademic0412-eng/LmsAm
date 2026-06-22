{{-- <!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Academic Mantra — LMS Dashboard</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('theme/css/index.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/aside.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/profile.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/demo.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/category.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/user.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/broadcast.css') }}">
 @vite(['resources/css/app.css', 'resources/js/app.js'])
 @livewireStyles
</head>
<body> --}}
<!DOCTYPE html>
<html lang="en" data-theme="link">

<head>
    <!-- ═══════════════════════════════════════════════
         SEO & META
    ═══════════════════════════════════════════════ -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Primary Meta -->
    <title>Dashboard — Academic Mantra LMS</title>
    <meta name="description"
        content="Academic Mantra LMS — Manage courses, students, trainers and certifications from one powerful dashboard.">
    <meta name="keywords" content="LMS, learning management system, courses, students, Academic Mantra, e-learning">
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
    <link rel="icon" type="image/svg+xml"
        href="{{ asset('theme/images/am35.png') }}">
    <link rel="apple-touch-icon"
        href="{{ asset('theme/images/am35.png') }}">

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
    {{-- <link rel="stylesheet" href="{{ asset('theme/css/index.css') }}"> --}}
    <!-- Structured Data -->
    {{-- <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Academic Mantra LMS",
        "url": "https://lms.academicmantra.com",
        "description": "Learning Management System for Academic Mantra",
        "applicationCategory": "EducationApplication",
        "operatingSystem": "Web"
    }
    </script> --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    <!-- Skip to main content (accessibility) -->
    <a href="#main-content" class="sr-only"
        style="position:absolute;left:-9999px;top:4px;padding:8px 16px;background:var(--brand-primary);color:#fff;border-radius:8px;z-index:9999;font-size:14px;font-weight:600;">
        Skip to main content
    </a>

    <!-- Mobile overlay -->
    <div class="sb-overlay" id="sbOverlay" aria-hidden="true" onclick="closeMobileSidebar()"></div>

    <!-- ═══════════════════════════════════════════════
         APP SHELL
    ═══════════════════════════════════════════════ -->
    <div class="app-shell" id="appShell">
