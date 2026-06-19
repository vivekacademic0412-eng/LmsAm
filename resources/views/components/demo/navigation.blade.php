    <header class="lms-header">

        <!-- Logo -->
        <div class="brand">
            <a href="{{ route('lms.landing') }}">
                <img src="{{ asset('theme/images/am35.png') }}" alt="LIVE Skills" class="brand-logo"
                    title="LIVE Skills Training Programs" loading="lazy"></a>

            <img src="{{ asset('theme/images/am21.png') }}" alt="Academic Mantra Services" class="brand-logo am-logo"
                title="Academic Mantra Services" loading="lazy">

        </div>


        <!-- Progress -->

        {{-- <nav class="steps-track">

            @php
                $currentStep = $currentStep ?? 1;

                $steps = [
                    // 1 => 'Welcome',
                    1 => 'Form fill up',
                    2 => 'Demo Task',
                    3 => 'Submit Assessment',
                    4 => 'Rate Us!',
                    5 => 'Choose your Next Course',
                    6 => 'Certification',
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

        </nav> --}}



        <!-- Actions -->

        <div class="header-actions">


            <button class="theme-btn" id="themeBtn">

                🌙

            </button>


          @if(auth()->check())
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="login-btn btn-primary-custom">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
            @endif


        </div>


    </header>
