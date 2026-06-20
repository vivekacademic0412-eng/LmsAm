   <nav class="steps-track">

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

        </nav> 