@extends('demo.layout')

@section('title', 'LIVE Skills Training Programs')

@section('content')

    <div class="lms-landing">






        <section class="hero-section">

            <div class="hero-left">

                <div class="logo-tag">
                    🔴 LIVE DEMO SESSION
                </div>

                <h1>
                    Welcome to <span>LIVE Skills Training Programs</span>
                </h1>

                <p>
                    We’re excited to have you here! Explore your live demo session,
                    interact with expert mentors, discover career-focused courses,
                    and experience how practical learning can transform your future.
                </p>


                <div class="hero-buttons">

                    <a href="#courses" class="btn-primary-custom">
                        🚀 Explore Demo Courses
                    </a>

                    <a href="#reviews" class="btn-outline-custom">
                        ⭐ Student Success Stories
                    </a>

                </div>


                <div class="hero-features">

                    <div class="feature-pill">
                        🎥 Live Demo Classes
                    </div>

                    <div class="feature-pill">
                        🧑‍🏫 Expert Trainers
                    </div>

                    <div class="feature-pill">
                        💻 Hands-on Learning
                    </div>

                    <div class="feature-pill">
                        🏆 Career Growth
                    </div>

                </div>

            </div>
            <div class="welcome-hero hero-right">




                <!-- AI Guide Bitmoji -->

                <div class="mentor-assistant">
                    <div class="shape shape-1"></div>

                    <div class="shape shape-2"></div>
                    <div class="mentor-image">
                        <img src="{{ asset('theme/images/hii-bitmoji.png') }}" alt="Mentor">
                        <span class="live-dot"></span>
                    </div>

                    <div class="mentor-content">

                        <span class="mentor-label">
                            Your Personal Guide
                        </span>

                        <h4>
                            Hi, I'm Academic Mantra 👋
                        </h4>

                        <p>
                            I'll guide you through this LIVE demo journey,
                            show courses, videos and help you explore your future skills.
                        </p>

                        <button>
                           <a href="{{ route('lms.step1') }}" style="text-decoration:none;color:white;">Start Demo →</a> 
                        </button>

                    </div>

                </div>





            </div>


        </section>

        @if (count($feedbacks)>0)
            <section class="section review-section" id="reviews">

                <div class="section-title">



                    <h2>
                        What Our Learners Say
                    </h2>

                    <p>
                        Hear from students who transformed their skills through
                        LIVE expert training, practical projects and real-world learning.
                    </p>

                </div>

                <div class="review-carousel" id="reviewCarousel">

                    @forelse($feedbacks as $feedback)
                        <div class="review-card">

                            <div class="quote">
                                “
                            </div>

                            <p>

                                {{ $feedback->message }}

                            </p>


                            <div class="stars">

                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $feedback->rating)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor

                            </div>


                            <div class="user">

                                <div class="avatar">

                                    {{ strtoupper(substr($feedback->user->name ?? 'U', 0, 1)) }}

                                </div>


                                <div>

                                    <strong>

                                        {{ $feedback->user->name ?? 'Learner' }}

                                    </strong>

                                    <span>

                                        {{ $feedback->course->title ?? 'Student' }}

                                    </span>

                                </div>

                            </div>


                            @if ($feedback->liked_tags)
                                <div class="tags">

                                    @foreach ($feedback->liked_tags as $tag)
                                        <span>

                                            {{ $tag }}

                                        </span>
                                    @endforeach

                                </div>
                            @endif


                            @if ($feedback->would_recommend)
                                <div class="recommend">

                                    ✅ Recommended

                                </div>
                            @endif

                        </div>

                    @empty

                        <div class="review-card">

                            <p>

                                No reviews available yet.

                            </p>

                        </div>
                    @endforelse

                </div>

            </section>

        @endif

        <!-- WHY CHOOSE US -->

        <section class="section">

            <div class="section-title">

                <h2>
                    Why Choose LIVE Skills Training?
                </h2>

                <p>
                    Learn from professionals and gain practical experience.
                </p>

            </div>


            <div class="why-grid">


                <div class="why-card">
                    <span>🎥</span>
                    <h3>Live Interactive Classes</h3>
                    <p>
                        Connect directly with mentors and participate
                        in engaging live sessions.
                    </p>
                </div>


                <div class="why-card">
                    <span>🚀</span>
                    <h3>Real Projects</h3>
                    <p>
                        Work on practical industry projects and build
                        your portfolio.
                    </p>
                </div>


                <div class="why-card">
                    <span>🏆</span>
                    <h3>Certificates</h3>

                    <p>
                        Get recognized certificates after completing
                        your learning journey.
                    </p>

                </div>


                <div class="why-card">

                    <span>💼</span>

                    <h3>Career Growth</h3>

                    <p>
                        Build job-ready skills and advance your career.
                    </p>

                </div>


            </div>


        </section>


        <!-- ======================================
                                         CHOOSE YOUR LEARNING PATH
                                    ====================================== -->

        <section class="section learning-path">


            <div class="section-title">

                <h2>
                    Choose Your Learning Path
                </h2>

                <p>
                    Start with fundamentals or become an industry professional through advanced training programs.
                </p>

            </div>


            <div class="path-grid">


                <!-- Basic -->
                <div class="path-card basic">

                    <div class="path-icon">
                        🌱
                    </div>

                    <h3>
                        Basic Training Program
                    </h3>

                    <span class="duration">
                        5 - 45 Hours
                    </span>


                    <ul>

                        <li>
                            ✓ Understand fundamental concepts
                        </li>

                        <li>
                            ✓ Learn industry basics step-by-step
                        </li>


                        <li>
                            ✓ Hands-on practical exercises
                        </li>


                        <li>
                            ✓ Build confidence before professional level
                        </li>

                    </ul>

                </div>


                <!-- Beginner -->

                <div class="path-card beginner">


                    <div class="path-icon">
                        🚀
                    </div>


                    <h3>
                        Professional Beginner
                    </h3>


                    <span class="duration">
                        90+ Hours
                    </span>


                    <ul>

                        <li>
                            ✓ Build strong foundations
                        </li>


                        <li>
                            ✓ Learn industry tools
                        </li>


                        <li>
                            ✓ Complete mini projects
                        </li>


                        <li>
                            ✓ Prepare for advanced learning
                        </li>


                    </ul>


                </div>


                <!-- Intermediate -->


                <div class="path-card intermediate">


                    <div class="path-icon">
                        💡
                    </div>


                    <h3>
                        Professional Intermediate
                    </h3>


                    <span class="duration">
                        100+ Hours
                    </span>


                    <ul>

                        <li>
                            ✓ Solve real-world case studies
                        </li>


                        <li>
                            ✓ Work on practical projects
                        </li>


                        <li>
                            ✓ Improve problem-solving skills
                        </li>


                        <li>
                            ✓ Gain professional confidence
                        </li>


                    </ul>

                </div>



                <!-- Advanced -->


                <div class="path-card advanced">


                    <div class="path-icon">
                        👑
                    </div>


                    <h3>
                        Professional Advanced
                    </h3>


                    <span class="duration">
                        120+ Hours
                    </span>


                    <ul>

                        <li>
                            ✓ Master advanced concepts
                        </li>


                        <li>
                            ✓ Build enterprise-level projects
                        </li>


                        <li>
                            ✓ Become industry-ready
                        </li>


                        <li>
                            ✓ Prepare for leadership roles
                        </li>


                    </ul>


                </div>


            </div>

        </section>

        <!-- ======================================
                                         COURSE CATEGORIES (DYNAMIC)
                                    ====================================== -->

        <section class="section">


            <div class="section-title">

                <h2>
                    Explore Trending Categories
                </h2>


                <p>
                    Discover the most in-demand skills designed for modern careers.
                </p>


            </div>


            <div class="category-grid">


                @foreach ($categories as $category)
                    <div class="category-card">


                        <img src="{{ asset($category->thumbnail) }}" alt="{{ $category->name }}">


                        <h3>
                            {{ $category->name }}
                        </h3>


                        <p>
                            Learn practical skills and real-world knowledge in
                            {{ $category->name }}.
                        </p>


                        <a href="#courses">
                            Explore Courses →
                        </a>


                    </div>
                @endforeach


            </div>

        </section>

        <!-- ======================================
                                         FEATURED COURSES
                                    ====================================== -->

        <section class="section" id="courses">

            <div class="section-title">


                <h2>
                    Popular Live Courses
                </h2>


                <p>
                    Learn from expert mentors with projects, assignments and certification.
                </p>


            </div>


            <div class="course-grid">


                @foreach ($courses->take(8) as $course)
                    <div class="course-card">


                        <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">


                        <div class="course-content">


                            <span class="course-tag">

                                {{ $course->courseType->name ?? 'Course' }}

                            </span>


                            <h3>

                                {{ $course->title }}

                            </h3>


                            <p>

                                {{ Str::limit($course->short_description, 90) }}

                            </p>


                            <div class="course-meta">


                                <span>
                                    ⏰ {{ $course->duration_hours }} Hours
                                </span>


                                <span>
                                    🌎 {{ $course->language }}
                                </span>


                            </div>


                            <button class="btn-primary-custom">
                                Start Learning
                            </button>


                        </div>


                    </div>
                @endforeach


            </div>


        </section>
    </div>
{{-- <script>
     const slider =
            document.getElementById(
                'reviewCarousel'
            );

        let scroll = 0;

        setInterval(() => {

            scroll += 340;

            if (
                scroll >=
                slider.scrollWidth -
                slider.clientWidth
            ) {
                scroll = 0;
            }

            slider.scrollTo({
                left: scroll,
                behavior: 'smooth'
            });

        }, 3000);
</script> --}}
@endsection
