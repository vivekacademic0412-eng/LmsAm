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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>

<body>

    {{-- ═══ PROGRESS HEADER ═══ --}}
    <header class="progress-header">
        <div class="logo">
            <i class="fas fa-graduation-cap" style="color:var(--brand-primary)"></i>
            Learning Management System<span> AM</span>
        </div>
        <nav class="steps-track">
            @php
                $currentStep = $currentStep ?? 1;
                $steps = [
                    1 => 'Welcome',
                    2 => 'Demo Stage',
                    3 => 'Submission Stage',
                    4 => 'Feedback Stage',
                    5 => 'Explore Recommended Couses & Reviews',
                ];
            @endphp
            @foreach ($steps as $num => $label)
                @if (!$loop->first)
                    <div class="step-line {{ $num <= $currentStep ? 'done' : '' }}"></div>
                @endif
                <div class="step-dot {{ $num == $currentStep ? 'active' : ($num < $currentStep ? 'done' : '') }}">
                    <div class="dot">
                        @if ($num < $currentStep)
                            <i class="fas fa-check" style="font-size:0.6rem"></i>
                        @else
                            {{ $num }}
                        @endif
                    </div>
                    <span class="step-label">{{ $label }}</span>
                </div>
            @endforeach
        </nav>
        <div class="logout-area">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
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
    const sp  = document.getElementById('bmSpeech');
    if (!arm || !sp) return;
    arm.style.animation = 'none'; void arm.offsetWidth;
    arm.style.animation = 'waveArm .5s ease-in-out 3';
    bmIdx = (bmIdx + 1) % bmMsgs.length;
    sp.textContent = bmMsgs[bmIdx];
    sp.style.animation = 'none'; void sp.offsetWidth;
    sp.style.animation = 'bmBubble .4s ease both';
}
setInterval(() => {
    const sp = document.getElementById('bmSpeech');
    if (!sp) return;
    bmIdx = (bmIdx + 1) % bmMsgs.length;
    sp.textContent = bmMsgs[bmIdx];
    sp.style.animation = 'none'; void sp.offsetWidth;
    sp.style.animation = 'bmBubble .4s ease both';
}, 4500);
    </script>

    @yield('scripts')
</body>

</html>
