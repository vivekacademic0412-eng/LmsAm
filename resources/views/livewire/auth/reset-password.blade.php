{{-- resources/views/livewire/auth/reset-password.blade.php --}}

<div>
    @if ($done)
        {{-- ── Success state ── --}}
        <div class="error-box" style="background-color:#ECFDF5; border-color:#A7F3D0; color:#065F46;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                <path d="M20 6 9 17l-5-5" />
            </svg>
            <span>Your password has been reset. You can now sign in with your new password.</span>
        </div>

        <a href="{{ route('login') }}" class="btn-login" style="text-decoration:none; margin-top:16px;">
            Back to Login
        </a>
    @else
        <form wire:submit.prevent="resetPassword" novalidate>

            @if ($errors->has('email'))
                <div class="error-box">
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            {{-- Email (read-only — comes from the reset link) --}}
            <div class="field">
                <label for="rp-email">Email address</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                    </span>
                    <input type="email" id="rp-email" wire:model="email" readonly
                        style="background-color:#F1F5F9; cursor:not-allowed;">
                </div>
            </div>

            {{-- New password --}}
            <div class="field @error('password') dreg-field--error @enderror">
                <label for="rp-password">New password</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </span>
                    <input type="password" id="rp-password" wire:model.live.debounce.400ms="password"
                        placeholder="Min. 8 characters" autocomplete="new-password">
                    <button type="button" class="eye-btn" data-password-toggle="#rp-password" aria-label="Show password">
                        <svg class="eye-open" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="eye-closed" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="dreg-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Confirm password --}}
            <div class="field">
                <label for="rp-password-confirm">Confirm new password</label>
                <div class="input-wrap">
                    <span class="input-icon" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </span>
                    <input type="password" id="rp-password-confirm" wire:model.live.debounce.400ms="password_confirmation"
                        placeholder="Re-enter your new password" autocomplete="new-password">
                    <button type="button" class="eye-btn" data-password-toggle="#rp-password-confirm" aria-label="Show password">
                        <svg class="eye-open" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="eye-closed" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" wire:loading.attr="disabled" wire:target="resetPassword">
                <span wire:loading.remove wire:target="resetPassword">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Reset Password
                </span>
                <span wire:loading wire:target="resetPassword" style="opacity:0.8">
                    Resetting…
                </span>
            </button>

            <a href="{{ route('login') }}" class="forgot-link" style="display:block; text-align:center; margin-top:16px;">
                Back to Login
            </a>
        </form>
    @endif
</div>
