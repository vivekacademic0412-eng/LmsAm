<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LMS AM')</title>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> --}}
    <link rel="stylesheet" href="{{ asset('theme/css/lms-demo.css') }}">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> --}}
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body>


    <header class="lms-header">

        <!-- Logo -->
        <div class="brand">

            <img src="{{ asset('theme/images/am35.png') }}" alt="LIVE Skills" class="brand-logo"
                title="LIVE Skills Training Programs" loading="lazy">

            <img src="{{ asset('theme/images/am21.png') }}" alt="Academic Mantra Services" class="brand-logo am-logo"
                title="Academic Mantra Services" loading="lazy">

        </div>


        <!-- Progress -->

        <nav class="steps-track">

            @php
                $currentStep = $currentStep ?? 1;

                $steps = [
                    1 => 'Welcome',
                    2 => 'Basic Details',
                    3 => 'Live Demo',
                    4 => 'Assessment',
                    5 => 'Feedback',
                    6 => 'Courses',
                ];
            @endphp


            @foreach ($steps as $num => $label)
                <div class="step-item">

                    @if (!$loop->first)
                        <div class="step-line {{ $num <= $currentStep ? 'done' : '' }}">
                        </div>
                    @endif


                    <div
                        class="step-dot
                    {{ $num == $currentStep ? 'active' : ($num < $currentStep ? 'done' : '') }}">

                        @if ($num < $currentStep)
                            ✓
                        @else
                            {{ $num }}
                        @endif

                    </div>


                    <span>
                        {{ $label }}
                    </span>

                </div>
            @endforeach

        </nav>



        <!-- Actions -->

        <div class="header-actions">


            <button class="theme-btn" id="themeBtn">

                🌙

            </button>


            <form method="POST" action="{{ route('logout') }}">

                @csrf


                <button type="submit" class="logout-btn">

                    <i class="fas fa-sign-out-alt"></i>

                    Logout

                </button>


            </form>


        </div>


    </header>

    {{-- ═══ PAGE CONTENT ═══ --}}
    <div class="page-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    {{-- ═══ BITMOJI GUIDE ═══ --}}
    <div class="bitmoji-guide" id="bitmojiGuide">
        <div class="bitmoji-bubble" id="bitmojiMsg">
            @yield('bitmoji-message', '👋 Hi! I\'m your learning guide. I\'ll be with you every step of the way!')
        </div>
        <div class="bitmoji-avatar" onclick="toggleBitmoji()" title="Toggle Guide">
            @yield('bitmoji-emoji', '🧑‍🏫')
        </div>
    </div>
    <footer class="lms-footer">

        <div class="footer-content">

            <div class="footer-brand">

                <h3>
                    🔴 LIVE Skills Training Programs
                </h3>

                <p>
                    Learn • Practice • Build • Grow
                </p>

            </div>


            <div class="footer-links">

                <a href="#courses">
                    Courses
                </a>

                <a href="#reviews">
                    Reviews
                </a>

                <a href="#">
                    Support
                </a>

            </div>
            <div class="footer-copy">
                © {{ date('Y') }} Academic Mantra Services.
                All Rights Reserved.

            </div>

        </div>
    </footer>
    <script>
        function toggleBitmoji() {
            const msg = document.getElementById('bitmojiMsg');
            msg.style.display = msg.style.display === 'none' ? 'block' : 'none';
        }
        // Auto-hide bubble after 6s, re-show on hover
        setTimeout(() => {
            const msg = document.getElementById('bitmojiMsg');
            if (msg) msg.style.display = 'none';
        }, 6000);
        document.querySelector('.bitmoji-avatar').addEventListener('mouseenter', () => {
            document.getElementById('bitmojiMsg').style.display = 'block';
        });
        // Add inside layout.blade.php before </body>

        function toggleBitmoji() {
            const msg = document.getElementById('bitmojiMsg');
            msg.style.display = msg.style.display === 'none' ? 'block' : 'none';
        }

        // Auto-hide after 7s, re-show on hover
        setTimeout(() => {
            const msg = document.getElementById('bitmojiMsg');
            if (msg) msg.style.display = 'none';
        }, 7000);

        const avatar = document.querySelector('.bitmoji-avatar');
        if (avatar) {
            avatar.addEventListener('mouseenter', () => {
                document.getElementById('bitmojiMsg').style.display = 'block';
            });
        }
        const bmMsgs = [
            "Hi! I'm Acedmic Mantra👋 Let's start!",
            "You're going to love this! 😊",
            "I'll guide every step!",
            "Fill in your details below 📝",
            "Let's gooo! 🚀"
        ];
        let bmIdx = 0;

        function waveHello() {
            const arm = document.getElementById('waveArmG');
            const sp = document.getElementById('bmSpeech');
            if (!arm || !sp) return;
            arm.style.animation = 'none';
            void arm.offsetWidth;
            arm.style.animation = 'waveArm .5s ease-in-out 3';
            bmIdx = (bmIdx + 1) % bmMsgs.length;
            sp.textContent = bmMsgs[bmIdx];
            sp.style.animation = 'none';
            void sp.offsetWidth;
            sp.style.animation = 'bmBubble .4s ease both';
        }
        setInterval(() => {
            const sp = document.getElementById('bmSpeech');
            if (!sp) return;
            bmIdx = (bmIdx + 1) % bmMsgs.length;
            sp.textContent = bmMsgs[bmIdx];
            sp.style.animation = 'none';
            void sp.offsetWidth;
            sp.style.animation = 'bmBubble .4s ease both';
        }, 4500);

        const themeBtn =
            document.getElementById("themeBtn");

        const saved =
            localStorage.getItem("theme");

        if (saved) {

            document.documentElement
                .setAttribute(
                    "data-theme",
                    saved
                );

            themeBtn.innerHTML =
                saved === "dark" ?
                "☀️" :
                "🌙";

        }


        themeBtn.onclick = () => {

            const current =
                document.documentElement
                .getAttribute("data-theme");

            const next =
                current === "dark" ?
                "light" :
                "dark";

            document.documentElement
                .setAttribute(
                    "data-theme",
                    next
                );

            localStorage
                .setItem(
                    "theme",
                    next
                );

            themeBtn.innerHTML =
                next === "dark" ?
                "☀️" :
                "🌙";

        };
    </script>
    @yield('scripts')
</body>

</html>
