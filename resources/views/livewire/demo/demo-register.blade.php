{{-- resources/views/livewire/demo/demo-register.blade.php --}}

<div class="demo-register-wrap">

    {{-- ── Header ── --}}
    <div class="dreg-header">
        <div class="dreg-badge">
            <span class="dreg-badge-dot"></span>
             Demo Access
        </div>
        <h2 class="dreg-title">Start Your Journey</h2>
        <p class="dreg-subtitle">Create your free account and join a live session today.</p>
    </div>

    {{-- ── Form ── --}}
    <form wire:submit.prevent="register" class="dreg-form" novalidate>

        {{-- Full Name --}}
        <div class="dreg-field @error('name') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-name">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                Full Name
            </label>
            <input
                id="dreg-name"
                type="text"
                wire:model.live.debounce.400ms="name"
                class="dreg-input"
                placeholder="e.g. Priya Sharma"
                autocomplete="name"
            />
            @error('name')
                <span class="dreg-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="dreg-field @error('email') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-email">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/></svg>
                Email Address
            </label>
            <input
                id="dreg-email"
                type="email"
                wire:model.live.debounce.400ms="email"
                class="dreg-input"
                placeholder="you@example.com"
                autocomplete="email"
            />
            @error('email')
                <span class="dreg-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Gender --}}
        <div class="dreg-field @error('gender') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-gender">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2"/></svg>
                Gender
            </label>
            <div class="dreg-gender-group">
                <label class="dreg-gender-opt @if($gender === 'male') dreg-gender-opt--active @endif">
                    <input type="radio" wire:model.live="gender" value="male" hidden />
                    <span class="dreg-gender-icon">♂</span>
                    Male
                </label>
                <label class="dreg-gender-opt @if($gender === 'female') dreg-gender-opt--active @endif">
                    <input type="radio" wire:model.live="gender" value="female" hidden />
                    <span class="dreg-gender-icon">♀</span>
                    Female
                </label>
                <label class="dreg-gender-opt @if($gender === 'other') dreg-gender-opt--active @endif">
                    <input type="radio" wire:model.live="gender" value="other" hidden />
                    <span class="dreg-gender-icon">⚧</span>
                    Other
                </label>
            </div>
            @error('gender')
                <span class="dreg-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="dreg-field @error('password') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-password">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Password
            </label>
            <div class="dreg-input-wrap">
                <input
                    id="dreg-password"
                    type="password"
                    wire:model.live.debounce.400ms="password"
                    class="dreg-input"
                    placeholder="Min. 8 characters"
                    autocomplete="new-password"
                    x-data
                    x-ref="pwd"
                />
                <button type="button" class="dreg-eye" onclick="
                    const i=this.previousElementSibling;
                    i.type=i.type==='password'?'text':'password';
                    this.querySelector('.eye-open').classList.toggle('hidden');
                    this.querySelector('.eye-closed').classList.toggle('hidden');
                " aria-label="Toggle password">
                    <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-closed hidden" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            @error('password')
                <span class="dreg-error">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="dreg-field @error('password_confirmation') dreg-field--error @enderror">
            <label class="dreg-label" for="dreg-confirm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Confirm Password
            </label>
            <div class="dreg-input-wrap">
                <input
                    id="dreg-confirm"
                    type="password"
                    wire:model.live.debounce.400ms="password_confirmation"
                    class="dreg-input"
                    placeholder="Re-enter your password"
                    autocomplete="new-password"
                />
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="dreg-submit" wire:loading.attr="disabled">
            <span wire:loading.remove>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                Join Free Demo
            </span>
            <span wire:loading class="dreg-loading">
                <svg class="dreg-spinner" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Creating Account…
            </span>
        </button>

    </form>

    {{-- ── Trust Strip ── --}}
    <div class="dreg-trust">
        <span class="dreg-trust-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="var(--brand-green)" stroke="none"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            No credit card
        </span>
        <span class="dreg-trust-dot">·</span>
        <span class="dreg-trust-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="var(--brand-green)" stroke="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            SSL secured
        </span>
        <span class="dreg-trust-dot">·</span>
        <span class="dreg-trust-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="var(--brand-green)" stroke="none"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Instant access
        </span>
    </div>

    {{-- ── SweetAlert2 trigger ── --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('registration-success', (event) => {
                const name = event.name ?? 'there';
                Swal.fire({
                    icon: 'success',
                    title: `Welcome, ${name}! 🎉`,
                    html: `Your <strong>Student</strong> account has been created.<br>Check your email to get started.`,
                    confirmButtonText: 'Go to Payment',
                    confirmButtonColor: 'var(--brand-primary)',
                    background: 'var(--bg-card)',
                    color: 'var(--text)',
                    iconColor: 'var(--brand-green)',
                    showClass: { popup: 'animate__animated animate__fadeInDown' },
                    hideClass: { popup: 'animate__animated animate__fadeOutUp' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/choose-type';
                    }
                });
            });
        });
    </script>
    @endpush

</div>