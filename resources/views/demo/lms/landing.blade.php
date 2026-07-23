@extends('demo.layout')
@section('title', 'LIVE Skills Training Programs')
@section('content')
    <div class="lms-landing">


        @if ($hero)
            <section class="hero-section">

                <div class="hero-left">

                    <div class="logo-tag">
                        🔴 LIVE DEMO SESSION
                    </div>

                    <h1>
                        {{ $hero->heading_prefix }}
                        <span>{{ $hero->heading_highlight }}</span>
                        {{ $hero->heading_bold }} {{ $hero->heading_suffix }}
                    </h1>

                    <p>
                        {{ $hero->lede }}
                    </p>

                    <div class="hero-buttons">
                        <a href="{{ $hero->cta_primary_url }}" class="btn-primary-custom">
                            {{ $hero->cta_primary_label }}
                        </a>

                        <a href="{{ $hero->cta_secondary_url }}" class="btn-outline-custom">
                            {{ $hero->cta_secondary_label }}
                        </a>
                    </div>

                    <div class="hero-features">
                        @foreach ($hero->stats as $stat)
                            <div class="feature-pill">
                                {{ $stat->label }}
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="welcome-hero hero-right">

                    <div class="mentor-assistant">

                        <div class="shape shape-1"></div>
                        <div class="shape shape-2"></div>

                        {{-- Hand images --}}
                        @if (is_array($hero->hand_images))
                            @foreach ($hero->hand_images as $img)
                                <img src="{{ asset($img) }}" class="image-back-mentor1 shape" alt="mentor">
                            @endforeach
                        @endif

                        <div class="mentor-image">
                            <img src="{{ asset($hero->mascot_image) }}" alt="Mentor">
                            <span class="live-dot"></span>
                        </div>

                        <div class="mentor-content">

                            <span class="mentor-label">
                                {{ $hero->guide_tag }}
                            </span>

                            <h4>
                                {{ $hero->guide_name }} 👋
                            </h4>

                            <p>
                                {{ $hero->guide_text }}
                            </p>

                            @if (!session('demo_user_id'))
                                <button>
                                    <a href="{{ route('lms.step1') }}" style="text-decoration:none;color:white;">
                                        Start Demo →
                                    </a>
                                </button>
                            @endif

                        </div>

                    </div>

                </div>

            </section>
        @endif
        @if (count($feedbacks) > 0)
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


                        <a href="{{ route('course.show', ['slug' => $category->id]) }}">
                            Explore Courses →
                        </a>


                    </div>
                @endforeach


            </div>

        </section>


    </div>
    <script>
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
    </script>
@endsection
