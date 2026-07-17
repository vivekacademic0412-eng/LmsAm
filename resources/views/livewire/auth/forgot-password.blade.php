{{-- resources/views/livewire/auth/forgot-password.blade.php --}}

<div>
    @if ($sent)
        {{-- ── Success state ── --}}
        <div class="error-box" style="background-color:#ECFDF5; border-color:#A7F3D0; color:#065F46;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                <path d="M20 6 9 17l-5-5" />
            </svg>
            <span>
                If an account exists for that email, we've sent a password reset link. Check your inbox
                (and spam folder) for the next steps.
            </span>
        </div>

        <a href="{{ route('login') }}" class="btn-login" style="text-decoration:none; margin-top:16px;">
            Back to Login
        </a>
    @else
        {{-- ── Request form ── --}}
        <form wire:submit.prevent="sendResetLink" novalidate>
            @if ($errors->has('email'))
                <div class="error-box">
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            <div class="field">
                <label for="fp-email">Email address</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                    </span>
                    <input type="email" id="fp-email" wire:model.live.debounce.400ms="email"
                        placeholder="you@academicmantra.com" autocomplete="email">
                </div>
            </div>

            <button type="submit" class="btn-login" wire:loading.attr="disabled" wire:target="sendResetLink">
                <span wire:loading.remove wire:target="sendResetLink">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        <polyline points="10 17 15 12 10 7" />
                        <line x1="15" y1="12" x2="3" y2="12" />
                    </svg>
                    Send Reset Link
                </span>
                <span wire:loading wire:target="sendResetLink" style="opacity:0.8">
                    Sending…
                </span>
            </button>

            <a href="{{ route('login') }}" class="forgot-link" style="display:block; text-align:center; margin-top:16px;">
                Back to Login
            </a>
        </form>
    @endif
</div>
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
        const input  = document.querySelector(btn.getAttribute('data-password-toggle'));
        const open   = btn.querySelector('.eye-open');
        const closed = btn.querySelector('.eye-closed');

        if (!input) {
            console.warn('[PasswordToggle] Target not found for', btn.getAttribute('data-password-toggle'));
            return;
        }

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';

            if (open)   open.style.display   = visible ? 'none'  : 'block';
            if (closed) closed.style.display = visible ? 'block' : 'none';

            btn.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            input.focus();
        });
    });
});
</script>
@endpush