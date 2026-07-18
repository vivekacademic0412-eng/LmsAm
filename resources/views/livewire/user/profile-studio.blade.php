
<div class="profile-page">

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <div class="profile-hero" role="banner">

        {{-- Avatar --}}
        <div class="profile-hero-avatar">
            <div class="profile-hero-avatar-ring" aria-label="Profile photo for {{ $name }}">
                @if ($selectedAvatar)
                    <img src="{{ $selectedAvatar }}" alt="{{ $name }}">
                @else
                    {{ strtoupper(substr($name, 0, 1)) }}
                @endif
            </div>
            <span class="profile-hero-avatar-online" title="Online now" aria-label="Online"></span>
        </div>

        {{-- Info --}}
        <div class="profile-hero-info">
            <div class="profile-hero-name">{{ $name }}</div>
            <div class="profile-hero-email">{{ $email }}</div>
            <div class="profile-hero-badges">
                <span class="profile-hero-badge">
                    <i class="ti ti-user-circle" aria-hidden="true"></i>
                    {{ \App\Models\User::roleOptions()[$user->role] ?? $user->role }}
                </span>
                <span class="profile-hero-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                    <i class="ti ti-{{ $user->is_active ? 'circle-check' : 'circle-x' }}" aria-hidden="true"></i>
                    {{ $user->is_active ? 'Active Account' : 'Inactive Account' }}
                </span>
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="profile-hero-stats" aria-label="Quick stats">
            <div class="hero-quick-stat">
                <div class="hero-quick-stat-val">{{ $user->created_at->format('Y') }}</div>
                <div class="hero-quick-stat-label">Member Since</div>
            </div>
            <div class="hero-quick-stat">
                <div class="hero-quick-stat-val">{{ $user->courses_count ?? '—' }}</div>
                <div class="hero-quick-stat-label">Courses</div>
            </div>
            <div class="hero-quick-stat">
                <div class="hero-quick-stat-val">{{ $user->certificates_count ?? '—' }}</div>
                <div class="hero-quick-stat-label">Certificates</div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════
         STAT CARDS
    ═══════════════════════════════════════════ --}}
    <div class="profile-stats-grid" role="region" aria-label="Account details">

        <div class="profile-stat-card">
            <div class="profile-stat-icon blue" aria-hidden="true"><i class="ti ti-user"></i></div>
            <div>
                <div class="profile-stat-label">Role</div>
                <div class="profile-stat-value">{{ \App\Models\User::roleOptions()[$user->role] ?? $user->role }}</div>
            </div>
        </div>

        <div class="profile-stat-card">
            <div class="profile-stat-icon purple" aria-hidden="true"><i class="ti ti-mail"></i></div>
            <div>
                <div class="profile-stat-label">Email</div>
                <div class="profile-stat-value" style="font-size:13px">{{ $email }}</div>
            </div>
        </div>

        <div class="profile-stat-card">
            <div class="profile-stat-icon {{ $user->is_active ? 'green' : 'amber' }}" aria-hidden="true">
                <i class="ti ti-shield-{{ $user->is_active ? 'check' : 'off' }}"></i>
            </div>
            <div>
                <div class="profile-stat-label">Status</div>
                <div class="profile-stat-value" style="color: {{ $user->is_active ? 'var(--success)' : 'var(--danger)' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>

        <div class="profile-stat-card">
            <div class="profile-stat-icon amber" aria-hidden="true"><i class="ti ti-calendar"></i></div>
            <div>
                <div class="profile-stat-label">Joined</div>
                <div class="profile-stat-value" style="font-size:13px">{{ $user->created_at->format('d M Y') }}</div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════
         VALIDATION ERRORS
    ═══════════════════════════════════════════ --}}
    @if ($errors->any())
        <div class="error-alert" role="alert" aria-live="assertive">
            <i class="ti ti-alert-circle" aria-hidden="true"></i>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         PROFILE INFORMATION FORM
    ═══════════════════════════════════════════ --}}
    <div class="p-card">
        <div class="p-card-head">
            <div class="p-card-head-left">
                <div class="p-card-head-icon" aria-hidden="true"><i class="ti ti-user-edit"></i></div>
                <div>
                    <div class="p-card-title">Profile Information</div>
                    <div class="p-card-sub">Update your name, email and photo.</div>
                </div>
            </div>
        </div>
        <div class="p-card-body">
            <div class="form-grid">

                {{-- Name --}}
                <div class="form-group">
                    <label class="form-label" for="profile-name">
                        Full Name <span class="req" aria-hidden="true">*</span>
                    </label>
                    <div class="input-wrap">
                        <i class="ti ti-user input-icon" aria-hidden="true"></i>
                        <input type="text"
                               id="profile-name"
                               wire:model="name"
                               class="form-input @error('name') is-invalid @enderror"
                               placeholder="Your full name"
                               autocomplete="name"
                               required
                               aria-required="true"
                               aria-describedby="name-error">
                    </div>
                    @error('name')
                        <div class="field-error" id="name-error" role="alert">
                            <i class="ti ti-alert-circle" aria-hidden="true"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="profile-email">
                        Email Address <span class="req" aria-hidden="true">*</span>
                    </label>
                    <div class="input-wrap">
                        <i class="ti ti-mail input-icon" aria-hidden="true"></i>
                        <input type="email"
                               id="profile-email"
                               wire:model="email"
                               class="form-input @error('email') is-invalid @enderror"
                               placeholder="you@example.com"
                               autocomplete="email"
                               inputmode="email"
                               required
                               aria-required="true"
                               aria-describedby="email-error">
                    </div>
                    @error('email')
                        <div class="field-error" id="email-error" role="alert">
                            <i class="ti ti-alert-circle" aria-hidden="true"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Photo upload --}}
                <div class="form-group form-col-full">
                    <label class="form-label" for="profile-avatar-upload">
                        Upload Profile Photo
                    </label>
                    <div class="upload-zone" aria-label="Upload profile photo">
                        <input type="file"
                               id="profile-avatar-upload"
                               wire:model="avatarUpload"
                               accept="image/*"
                               aria-label="Choose profile photo">
                        <div class="upload-zone-icon" aria-hidden="true">
                            <i class="ti ti-cloud-upload"></i>
                        </div>
                        <div class="upload-zone-title">Click or drag a photo here</div>
                        <div class="upload-zone-sub">PNG, JPG or WEBP · Max 2 MB</div>
                    </div>
                    @error('avatarUpload')
                        <div class="field-error" role="alert">
                            <i class="ti ti-alert-circle" aria-hidden="true"></i> {{ $message }}
                        </div>
                    @enderror
                    <div class="field-hint">
                        <i class="ti ti-info-circle" style="font-size:12px" aria-hidden="true"></i>
                        Uploading a photo will replace your selected avatar.
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         AVATAR PICKER
    ═══════════════════════════════════════════ --}}
    <div class="p-card">
        <div class="p-card-head">
            <div class="p-card-head-left">
                <div class="p-card-head-icon" aria-hidden="true"><i class="ti ti-mood-smile"></i></div>
                <div>
                    <div class="p-card-title">Choose Avatar</div>
                    <div class="p-card-sub">Pick a pre-generated avatar as your profile picture.</div>
                </div>
            </div>
            @if ($selectedAvatar)
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--success);font-weight:600;">
                    <i class="ti ti-circle-check" aria-hidden="true"></i>
                    Avatar selected
                </div>
            @endif
        </div>
        <div class="p-card-body">
            <div class="avatar-grid" role="listbox" aria-label="Choose an avatar">
                @foreach ($avatars as $avatar)
                    <button type="button"
                            wire:click="selectAvatar(@js($avatar))"
                            class="avatar-item {{ $selectedAvatar === $avatar ? 'selected' : '' }}"
                            role="option"
                            aria-selected="{{ $selectedAvatar === $avatar ? 'true' : 'false' }}"
                            aria-label="Select avatar {{ $loop->iteration }}">
                        <img src="{{ $avatar }}" alt="Avatar option {{ $loop->iteration }}" loading="lazy">
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Save row inside avatar card --}}
        <div class="save-row">
            <button type="button" class="btn btn-ghost" wire:click="resetForm" aria-label="Discard changes">
                <i class="ti ti-refresh" aria-hidden="true"></i>
                Reset
            </button>
            <button type="button"
                    class="btn btn-primary"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    aria-label="Save profile changes">
                <span wire:loading.remove wire:target="save">
                    <i class="ti ti-device-floppy" aria-hidden="true"></i>
                    Save Changes
                </span>
                <span wire:loading wire:target="save"
                      style="display:flex;align-items:center;gap:8px">
                    <span class="btn-spinner" aria-hidden="true"></span>
                    Saving…
                </span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         CHANGE PASSWORD
    ═══════════════════════════════════════════ --}}
    <div class="p-card">
        <div class="p-card-head">
            <div class="p-card-head-left">
                <div class="p-card-head-icon" aria-hidden="true"><i class="ti ti-lock"></i></div>
                <div>
                    <div class="p-card-title">Change Password</div>
                    <div class="p-card-sub">Use a strong password you don't use elsewhere.</div>
                </div>
            </div>
        </div>
        <div class="p-card-body">
            <div class="form-grid">

                <div class="form-group form-col-full">
                    <label class="form-label" for="current-password">Current Password</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock input-icon" aria-hidden="true"></i>
                        <input type="password"
                               id="current-password"
                               wire:model="currentPassword"
                               class="form-input @error('currentPassword') is-invalid @enderror"
                               placeholder="Enter current password"
                               autocomplete="current-password">
                    </div>
                    @error('currentPassword')
                        <div class="field-error" role="alert"><i class="ti ti-alert-circle" aria-hidden="true"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="new-password">New Password</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock-open input-icon" aria-hidden="true"></i>
                        <input type="password"
                               id="new-password"
                               wire:model="newPassword"
                               class="form-input @error('newPassword') is-invalid @enderror"
                               placeholder="Min. 8 characters"
                               autocomplete="new-password">
                    </div>
                    @error('newPassword')
                        <div class="field-error" role="alert"><i class="ti ti-alert-circle" aria-hidden="true"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm-password">Confirm New Password</label>
                    <div class="input-wrap">
                        <i class="ti ti-lock-check input-icon" aria-hidden="true"></i>
                        <input type="password"
                               id="confirm-password"
                               wire:model="confirmPassword"
                               class="form-input @error('confirmPassword') is-invalid @enderror"
                               placeholder="Repeat new password"
                               autocomplete="new-password">
                    </div>
                    @error('confirmPassword')
                        <div class="field-error" role="alert"><i class="ti ti-alert-circle" aria-hidden="true"></i> {{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>
        <div class="save-row">
            <button type="button"
                    class="btn btn-primary"
                    wire:click="updatePassword"
                    wire:loading.attr="disabled"
                    aria-label="Update password">
                <span wire:loading.remove wire:target="updatePassword">
                    <i class="ti ti-key" aria-hidden="true"></i>
                    Update Password
                </span>
                <span wire:loading wire:target="updatePassword"
                      style="display:flex;align-items:center;gap:8px">
                    <span class="btn-spinner" aria-hidden="true"></span>
                    Updating…
                </span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         DANGER ZONE
    ═══════════════════════════════════════════ --}}
    {{-- <div class="p-card danger-zone" role="region" aria-label="Danger zone">
        <div class="p-card-head">
            <div class="p-card-head-left">
                <div class="p-card-head-icon" aria-hidden="true"><i class="ti ti-alert-triangle"></i></div>
                <div>
                    <div class="p-card-title">Danger Zone</div>
                    <div class="p-card-sub">These actions are permanent and cannot be undone.</div>
                </div>
            </div>
        </div>
        <div class="p-card-body">
            <div class="danger-row">
                <div class="danger-row-text">
                    <strong>Sign out everywhere</strong>
                    <span>Log out from all devices and active sessions.</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" aria-label="Sign out of all sessions">
                        <i class="ti ti-logout" aria-hidden="true"></i>
                        Sign Out All
                    </button>
                </form>
            </div>
            <div class="danger-row">
                <div class="danger-row-text">
                    <strong>Delete account</strong>
                    <span>Permanently delete your account and all associated data.</span>
                </div>
                <button type="button"
                        class="btn btn-danger btn-sm"
                        onclick="confirmDeleteAccount()"
                        aria-label="Delete my account permanently">
                    <i class="ti ti-trash" aria-hidden="true"></i>
                    Delete Account
                </button>
            </div>
        </div>
    </div> --}}



{{-- ═══════════════════════════════════════════════
     SWEETALERT2 — SUCCESS / ERROR EVENTS
═══════════════════════════════════════════════ --}}
<script>
    function swalTheme() {
        const dark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            background: dark ? '#111d2e' : '#ffffff',
            color:      dark ? '#f5f9ff' : '#0e1f36',
        };
    }

    /* Livewire dispatches 'profile-saved' from the component */
    window.addEventListener('profile-saved', () => {
        const t = swalTheme();
        Swal.fire({
            icon:              'success',
            title:             'Profile updated!',
            text:              'Your changes have been saved successfully.',
            showConfirmButton: false,
            timer:             2000,
            timerProgressBar:  true,
            background:        t.background,
            color:             t.color,
            iconColor:         '#16a34a',
            customClass:       { popup: 'swal-rounded' },
        });
    });

    window.addEventListener('password-update', () => {
        const t = swalTheme();
        Swal.fire({
            icon:              'success',
            title:             'Password changed',
            text:              'Your new password is active.',
            showConfirmButton: false,
            timer:             2000,
            timerProgressBar:  true,
            background:        t.background,
            color:             t.color,
            iconColor:         '#16a34a',
            customClass:       { popup: 'swal-rounded' },
        });
    });

    window.addEventListener('profile-error', event => {
        const t = swalTheme();
        Swal.fire({
            icon:               'error',
            title:              'Something went wrong',
            text:               event.detail?.[0]?.message ?? 'Please check the form and try again.',
            confirmButtonColor: '#dc2626',
            background:         t.background,
            color:              t.color,
            customClass:        { popup: 'swal-rounded' },
        });
    });

    /* Delete account confirmation */
    function confirmDeleteAccount() {
        const t = swalTheme();
        Swal.fire({
            icon:                 'warning',
            title:                'Delete your account?',
            text:                 'This will permanently remove all your data. This action cannot be undone.',
            showCancelButton:     true,
            confirmButtonText:    'Yes, delete my account',
            cancelButtonText:     'Cancel',
            confirmButtonColor:   '#dc2626',
            cancelButtonColor:    'transparent',
            reverseButtons:       true,
            background:           t.background,
            color:                t.color,
            customClass:          { popup: 'swal-rounded', confirmButton: 'swal-btn-danger', cancelButton: 'swal-btn-cancel' },
        }).then(result => {
            if (result.isConfirmed) {
                @this.deleteAccount();
            }
        });
    }
</script>

<script>
    document.addEventListener('livewire:init', () => {

        Livewire.on('profile-updated', () => {
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: 'Your profile has been saved successfully!',
                confirmButtonColor: '#6366f1'
            });
        });

    });
</script>
</div>{{-- /.profile-page --}}