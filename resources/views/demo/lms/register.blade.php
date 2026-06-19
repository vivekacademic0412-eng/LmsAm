{{-- resources/views/demo/lms/choose-type.blade.php --}}
@extends('demo.layout')
@section('title', 'Register')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/demo-register.css') }}">
    {{-- Animate.css for SweetAlert transitions --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush

@section('content')

    <section class="hero-section">
        <div class="hero-left">
            <div class="logo-tag">
                🔴 LIVE DEMO SESSION
            </div>
            <h1>
                Welcome to <span>LIVE Skills Training Programs</span>
            </h1>
            <p>
                We're excited to have you here! Explore your live demo session,
                interact with expert mentors, discover career-focused courses,
                and experience how practical learning can transform your future.
            </p>
            <div class="hero-buttons">
                <a href="#courses" class="btn-primary-custom">
                    Explore Demo Courses
                </a>
                <a href="#reviews" class="btn-outline-custom">
                    Student Success Stories
                </a>
            </div>
            <div class="hero-features">
                <div class="feature-pill">Live Demo Classes</div>
                <div class="feature-pill">Expert Trainers</div>
                <div class="feature-pill">Hands-on Learning</div>
                <div class="feature-pill">Career Growth</div>
            </div>
        </div>

        {{-- ── Right: Registration Form ── --}}
        <div class="welcome-hero hero-right">
            @livewire('demo.demo-register')
        </div>
    </section>

@endsection

@push('scripts')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush