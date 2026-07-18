{{-- resources/views/livewire/demo/demo-login.blade.php --}}

<div class="demo-register-wrap">

    <div class="dreg-header">
        <h2 class="dreg-title">Welcome Back</h2>
        <p class="dreg-subtitle">Log in to continue your learning journey.</p>
    </div>

    <form wire:submit.prevent="login" class="dreg-form" novalidate>

        @if (session()->has('login_error'))
            <div class="dreg-error-box">
                {{ session('login_error') }}
            </div>
        @endif

        <div class="dreg-field @error('login') dreg-field--error @enderror">
            <label class="dreg-label" for="dlogin-id">Email or Mobile Number</label>
            <input id="dlogin-id" type="text" wire:model.live.debounce.400ms="login" class="dreg-input"
                placeholder="you@example.com or 9876543210" autocomplete="username" />
            @error('login')
                <span class="dreg-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="dreg-field @error('password') dreg-field--error @enderror">
            <label class="dreg-label" for="dlogin-password">Password</label>
            <div class="dreg-input-wrap">
                <input id="dlogin-password" type="password" wire:model.live.debounce.400ms="password" class="dreg-input"
                    placeholder="Your password" autocomplete="current-password" />
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
                <span class="dreg-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="dreg-login-row">
            <label class="dreg-remember">
                <input type="checkbox" wire:model="remember" />
                Remember me
            </label>
            <a href="{{ \Illuminate\Support\Facades\Route::has('password.request') ? route('password.request') : '#' }}"
                class="dreg-forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="dreg-submit" wire:target="login">

            <span wire:loading.remove wire:target="login">
                Log In
            </span>

            <span wire:loading wire:target="login">
                Logging In...
            </span>
        </button>

    </form>

</div>
