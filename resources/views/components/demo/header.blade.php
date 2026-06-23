<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary SEO Meta -->
    <title>@yield('title', 'LMS AM - Learning Management System')</title>
    <meta name="description" content="@yield('meta_description', 'Join LMS AM - Learn trending skills, courses, certifications, and industry-ready training programs.')">
    <meta name="keywords" content="@yield('meta_keywords', 'LMS, online courses, learning management system, training, certifications, skill development, internships')">
    <meta name="author" content="LMS AM Team">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph (Facebook / WhatsApp / LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'LMS AM')">
    <meta property="og:description" content="@yield('meta_description', 'Learn skills with LMS AM platform')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('theme/images/am21.png') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'LMS AM')">
    <meta name="twitter:description" content="@yield('meta_description', 'Learn skills with LMS AM platform')">
    <meta name="twitter:image" content="{{ asset('theme/images/am35.png') }}">

    <!-- Favicon -->

    <!-- Favicon ICO (BEST COMPATIBILITY) -->
    <link rel="icon" href="{{ asset('theme/images/logo.png') }}" type="image/x-icon">

    <!-- PNG Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('theme/images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('theme/images/logo.png') }}">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="{{ asset('theme/images/logo.png') }}">
    <!-- Preconnect (Speed Optimization) -->

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('theme/css/lms-demo.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
