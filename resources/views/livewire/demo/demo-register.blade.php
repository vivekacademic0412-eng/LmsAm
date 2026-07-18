{{-- resources/views/livewire/demo/demo-register.blade.php --}}

<div class="demo-register-wrap">

    {{-- ── Header ── --}}
    <div class="dreg-header">
        <h2 class="dreg-title">Start Your Journey</h2>
        <p class="dreg-subtitle">Create your free account and join a live session today.</p>
    </div>

    {{-- ── Form ── --}}
    <form wire:submit.prevent="register" class="dreg-form" novalidate>
        @if (session()->has('success'))
    <div class="dreg-success-box">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2">
            <path d="M20 6L9 17l-5-5"/>
        </svg>

        <div>
            <strong>Registration Successful!</strong><br>
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session()->has('error'))
    <div class="dreg-error-box">
        {{ session('error') }}
    </div>
@endif
        <div class="dreg-row">

            <div class="dreg-field @error('first_name') dreg-field--error @enderror">
                <label class="dreg-label">First Name</label>
                <input type="text" wire:model.live.debounce.400ms="first_name" class="dreg-input"
                    placeholder="First Name" />
                @error('first_name')
                    <span class="dreg-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="dreg-field @error('last_name') dreg-field--error @enderror">
                <label class="dreg-label">Last Name</label>
                <input type="text" wire:model.live.debounce.400ms="last_name" class="dreg-input"
                    placeholder="Last Name" />
                @error('last_name')
                    <span class="dreg-error">{{ $message }}</span>
                @enderror
            </div>

        </div>
        {{-- Full Name --}}


        {{-- Email --}}
        <div class="dreg-row">

            {{-- Contact Number --}}
            <div class="dreg-field @error('contact') dreg-field--error @enderror">
                <label class="dreg-label" for="dreg-contact">
                    Contact Number
                </label>
                <input id="dreg-contact" type="tel" wire:model.live.debounce.400ms="contact" class="dreg-input"
                    placeholder="9876543210" />
                @error('contact')
                    <span class="dreg-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="dreg-field @error('email') dreg-field--error @enderror">
                <label class="dreg-label" for="dreg-email">
                    Email Address
                </label>
                <input id="dreg-email" type="email" wire:model.live.debounce.400ms="email" class="dreg-input"
                    placeholder="you@example.com" autocomplete="email" />
                @error('email')
                    <span class="dreg-error">{{ $message }}</span>
                @enderror
            </div>

        </div>

        {{-- Gender --}}
        <div class="dreg-field @error('gender') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-gender">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <circle cx="12" cy="12" r="4" />
                    <path d="M12 2v2m0 16v2M2 12h2m16 0h2" />
                </svg>
                Gender
            </label>
            <div class="dreg-gender-group">
                <label class="dreg-gender-opt @if ($gender === 'male') dreg-gender-opt--active @endif">
                    <input type="radio" wire:model.live="gender" value="male" hidden />
                    <span class="dreg-gender-icon">♂</span>
                    Male
                </label>
                <label class="dreg-gender-opt @if ($gender === 'female') dreg-gender-opt--active @endif">
                    <input type="radio" wire:model.live="gender" value="female" hidden />
                    <span class="dreg-gender-icon">♀</span>
                    Female
                </label>
                <label class="dreg-gender-opt @if ($gender === 'other') dreg-gender-opt--active @endif">
                    <input type="radio" wire:model.live="gender" value="other" hidden />
                    <span class="dreg-gender-icon">⚧</span>
                    Other
                </label>
            </div>
            @error('gender')
                <span class="dreg-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="dreg-field @error('password') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-password">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                Password
            </label>
            <div class="dreg-input-wrap">
                <input id="dreg-password" type="password" wire:model.live.debounce.400ms="password" class="dreg-input"
                    placeholder="Min. 8 characters" autocomplete="new-password" x-data x-ref="pwd" />
                <button type="button" class="dreg-eye"
                    onclick="
                    const i=this.previousElementSibling;
                    i.type=i.type==='password'?'text':'password';
                    this.querySelector('.eye-open').classList.toggle('hidden');
                    this.querySelector('.eye-closed').classList.toggle('hidden');
                "
                    aria-label="Toggle password">
                    <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg class="eye-closed hidden" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path
                            d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                        <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                </button>
            </div>
            @error('password')
                <span class="dreg-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                    </svg>
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="dreg-field @error('password_confirmation') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-confirm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Confirm Password
            </label>
            <div class="dreg-input-wrap">
                <input id="dreg-confirm" type="password" wire:model.live.debounce.400ms="password_confirmation"
                    class="dreg-input" placeholder="Re-enter your password" autocomplete="new-password" />
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="dreg-submit" wire:loading.attr="disabled">
            <span wire:loading.remove>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
               Register
            </span>
            <span wire:loading class="dreg-loading">
                <svg class="dreg-spinner" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56" />
                </svg>
                Creating Account…
            </span>
        </button>

    </form>


    {{-- ── SweetAlert2 trigger ── --}}
    @push('scripts')
        <script>
         
document.addEventListener('livewire:init', () => {

    // Generic SweetAlert
    Livewire.on('swal', (event) => {

        const data = Array.isArray(event) ? event[0] : event;

        Swal.fire({
            icon: data.icon,
            title: data.title,
            text: data.text,
            confirmButtonColor: '#0947a8'
        });

    });

    // Registration Success
    Livewire.on('registration-success', (event) => {

        const data = Array.isArray(event) ? event[0] : event;

        Swal.fire({
            icon: 'success',
            title: `Welcome, ${data.name}!`,
            html: `
                <strong>Your account has been created successfully.</strong><br><br>
                Please check your email and verify your account before logging in.
            `,
            confirmButtonText: 'OK',
            confirmButtonColor: '#0947a8',
            allowOutsideClick: false
        });

    });

});

        </script>
    @endpush

</div>
