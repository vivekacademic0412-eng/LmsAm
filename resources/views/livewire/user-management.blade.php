{{-- FILE: resources/views/livewire/user-management.blade.php --}}
<div class="um-wrap">



{{-- ═══════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════ --}}
<div class="um-header">
    <div class="um-header-text">
        <h1>User Management</h1>
        <p>Manage users, assign roles, and control account access.</p>
    </div>
    <div class="um-header-right">
        {{-- live search --}}
        <div class="um-search-wrap">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.350ms="search" placeholder="Search name or email…" aria-label="Search users">
        </div>
        <button type="button" wire:click="openCreate" class="btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add User
        </button>
    </div>
</div>


{{-- ═══════════════════════════════════════
     FILTER BAR
════════════════════════════════════════ --}}
<div class="um-filter-bar">
    <div class="filter-group">
        <label>Role</label>
        <select wire:model.live="filterRole" aria-label="Filter by role">
            <option value="">All Roles</option>
            @foreach($this->visibleRoleOptions as $val => $lbl)
                <option value="{{ $val }}">{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label>Status</label>
        <select wire:model.live="filterStatus" aria-label="Filter by status">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    @if($filterRole || $filterStatus || $search)
    <button type="button" wire:click="clearFilters" class="um-clear-btn">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Clear Filters
    </button>
    @endif
    <span style="margin-left:auto; font-size:12px; color:var(--text4)">
        {{ $this->users->total() }} {{ Str::plural('user', $this->users->total()) }} found
    </span>
</div>


{{-- ═══════════════════════════════════════
     TABLE CARD
════════════════════════════════════════ --}}
<div class="um-table-card">
    <div class="um-table-head">
        <h2>All Users <span class="um-count-badge">{{ $this->users->total() }}</span></h2>
    </div>

    <div class="um-table-wrap" wire:loading.class="opacity-60">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->users as $u)
                @php
                    $adminRestricted = in_array($u->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true);
                    $canManage = $isSuperAdmin || !$adminRestricted;
                    $roleClass = match($u->role) {
                        \App\Models\User::ROLE_SUPERADMIN => 'role-superadmin',
                        \App\Models\User::ROLE_ADMIN      => 'role-admin',
                        \App\Models\User::ROLE_TRAINER    => 'role-trainer',
                        \App\Models\User::ROLE_STUDENT    => 'role-student',
                        \App\Models\User::ROLE_MANAGER_HR => 'role-hr',
                        \App\Models\User::ROLE_IT         => 'role-it',
                        \App\Models\User::ROLE_DEMO       => 'role-demo',
                        default                           => 'role-default',
                    };
                @endphp
                <tr wire:key="user-{{ $u->id }}">
                    <td style="color:var(--text4); font-size:12px">{{ $u->id }}</td>

                    <td>
                        <div class="um-user-cell">
                            <div class="um-avatar">
                                @if($u->avatar_url ?? false)
                                    <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}">
                                @else
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <strong>{{ $u->name }}</strong>
                                <span>{{ $u->email }}</span>
                            </div>
                        </div>
                    </td>

                    <td>
                        <span class="role-tag {{ $roleClass }}">
                            {{ $this->roleOptions[$u->role] ?? $u->role }}
                        </span>
                    </td>

                    <td>
                        <span class="status-badge {{ $u->is_active ? 'status-active' : 'status-inactive' }}">
                            <span class="status-dot {{ $u->is_active ? 'on' : 'off' }}"></span>
                            {{ $u->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>

                    <td>
                        @if($canManage)
                        <div class="row-actions">
                            {{-- Edit --}}
                            <button type="button"
                                    wire:click="openEdit({{ $u->id }})"
                                    class="btn-icon edit"
                                    title="Edit {{ $u->name }}">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                            </button>

                            {{-- Resend Email --}}
                            <button type="button"
                                    class="btn-icon "
                                    title="Resend access email to {{ $u->name }}"
                                    onclick="askResend({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/></svg>
                            </button>

                            {{-- Delete --}}
                            <button type="button"
                                    class="btn-icon danger"
                                    title="Delete {{ $u->name }}"
                                    onclick="askDeleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                        </div>
                        @else
                        <span class="um-no-perm">No permission</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="um-empty">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                            <p>@if($search) No users match "{{ $search }}" @else No users found. @endif</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->users->hasPages())
    <div class="um-pagination">
        <button wire:click="previousPage" class="page-btn {{ $this->users->onFirstPage() ? 'disabled' : '' }}" aria-label="Previous page">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        @foreach($this->users->getUrlRange(1, $this->users->lastPage()) as $page => $url)
            <button wire:click="gotoPage({{ $page }})"
                    class="page-btn {{ $page === $this->users->currentPage() ? 'active' : '' }}">
                {{ $page }}
            </button>
        @endforeach
        <button wire:click="nextPage" class="page-btn {{ $this->users->hasMorePages() ? '' : 'disabled' }}" aria-label="Next page">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
    @endif
</div>


{{-- ═══════════════════════════════════════
     MODAL — CREATE USER
════════════════════════════════════════ --}}
@if($showCreate)
<div class="lw-modal-bg" wire:click.self="$set('showCreate',false)">
    <div class="lw-modal" role="dialog" aria-modal="true" aria-labelledby="modal-create-title">
        <div class="lw-modal-head">
            <h3 id="modal-create-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.5" stroke-linecap="round" style="margin-right:6px;vertical-align:-2px" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/><line x1="18" y1="11" x2="18" y2="17"/><line x1="15" y1="14" x2="21" y2="14"/></svg>
                Create New User
            </h3>
            <button type="button" wire:click="$set('showCreate',false)" class="lw-modal-close" aria-label="Close">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="lw-modal-body">
            <div class="form-stack">

                {{-- Basic info --}}
                <div class="form-section-label">Basic Information</div>
                <div class="form-grid-2">
                    <div class="lw-field">
                        <label>Full Name *</label>
                        <input type="text" wire:model.live.debounce.300ms="name" placeholder="e.g. Priya Sharma" autofocus>
                        @error('name')<p class="lw-err"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</p>@enderror
                    </div>
                    <div class="lw-field">
                        <label>Email Address *</label>
                        <input type="email" wire:model.live.debounce.400ms="email" placeholder="user@academicmantra.com">
                        @error('email')<p class="lw-err"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Role & Status --}}
                <div class="form-section-label">Role & Access</div>
                <div class="form-grid-2">
                    <div class="lw-field">
                        <label>Role *</label>
                        <select wire:model.live="role">
                            <option value="">— Select Role —</option>
                            @foreach($this->visibleRoleOptions as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('role')<p class="lw-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="lw-field">
                        <label>Account Status *</label>
                        <select wire:model="is_active">
                            <option value="1">✅ Active</option>
                            <option value="0">⛔ Inactive</option>
                        </select>
                    </div>
                </div>

                {{-- Password --}}
                <div class="form-section-label">Security</div>
                <div class="lw-field">
                    <label>Password *</label>
                    <input type="password" wire:model="password" placeholder="Min 8 characters">
                    <p class="pw-hint">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        Password will be emailed to the user after creation.
                    </p>
                    @error('password')<p class="lw-err">{{ $message }}</p>@enderror
                </div>

                {{-- Avatar --}}
                <div class="form-section-label">Profile Photo</div>
                <div class="lw-field">
                    <label>Avatar <span style="color:var(--text4);font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
                    @if($avatar)
                    <div class="avatar-prev-wrap">
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Preview" class="avatar-prev">
                        <span style="font-size:12px;color:var(--text3)">Preview</span>
                    </div>
                    @endif
                    <input type="file" wire:model="avatar" accept="image/jpg,image/jpeg,image/png,image/webp">
                    <div wire:loading wire:target="avatar" class="lw-upload-bar"><div class="lw-upload-fill"></div></div>
                    @error('avatar')<p class="lw-err">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>
        <div class="lw-modal-footer">
            <button type="button" wire:click="$set('showCreate',false)" class="btn-ghost">Cancel</button>
            <button type="button" wire:click="saveUser"
                    wire:loading.attr="disabled" wire:target="saveUser"
                    class="btn-primary">
                <span wire:loading.remove wire:target="saveUser">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Create &amp; Send Email
                </span>
                <span wire:loading wire:target="saveUser">Creating…</span>
            </button>
        </div>
    </div>
</div>
@endif


{{-- ═══════════════════════════════════════
     MODAL — EDIT USER
════════════════════════════════════════ --}}
@if($showEdit)
<div class="lw-modal-bg" wire:click.self="$set('showEdit',false)">
    <div class="lw-modal" role="dialog" aria-modal="true" aria-labelledby="modal-edit-title">
        <div class="lw-modal-head">
            <h3 id="modal-edit-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:-2px" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                Edit User
            </h3>
            <button type="button" wire:click="$set('showEdit',false)" class="lw-modal-close" aria-label="Close">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="lw-modal-body">
            <div class="form-stack">

                <div class="form-section-label">Basic Information</div>
                <div class="form-grid-2">
                    <div class="lw-field">
                        <label>Full Name *</label>
                        <input type="text" wire:model.live.debounce.300ms="editName">
                        @error('editName')<p class="lw-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="lw-field">
                        <label>Email Address *</label>
                        <input type="email" wire:model.live.debounce.400ms="editEmail">
                        @error('editEmail')<p class="lw-err">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-section-label">Role & Access</div>
                <div class="form-grid-2">
                    <div class="lw-field">
                        <label>Role *</label>
                        <select wire:model.live="editRole">
                            @foreach($this->visibleRoleOptions as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('editRole')<p class="lw-err">{{ $message }}</p>@enderror
                    </div>
                    <div class="lw-field">
                        <label>Account Status *</label>
                        <select wire:model="editIsActive">
                            <option value="1">✅ Active</option>
                            <option value="0">⛔ Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-section-label">Security</div>
                <div class="lw-field">
                    <label>New Password <span style="color:var(--text4);font-weight:400;text-transform:none;letter-spacing:0">(leave blank to keep current)</span></label>
                    <input type="password" wire:model="editPassword" placeholder="Optional — min 8 characters">
                    @error('editPassword')<p class="lw-err">{{ $message }}</p>@enderror
                </div>

                <div class="form-section-label">Profile Photo</div>
                <div class="lw-field">
                    <label>Avatar <span style="color:var(--text4);font-weight:400;text-transform:none;letter-spacing:0">(leave empty to keep current)</span></label>
                    <div class="avatar-prev-wrap">
                        @if($editAvatar)
                            <img src="{{ $editAvatar->temporaryUrl() }}" alt="New preview" class="avatar-prev">
                            <span style="font-size:12px;color:var(--teal)">New photo selected</span>
                        @elseif($editCurrentAvatar)
                            <img src="{{ Storage::disk('public')->url($editCurrentAvatar) }}" alt="Current avatar" class="avatar-prev">
                            <span style="font-size:12px;color:var(--text4)">Current photo</span>
                        @else
                            <div class="avatar-prev-placeholder">{{ strtoupper(substr($editName, 0, 1)) }}</div>
                            <span style="font-size:12px;color:var(--text4)">No photo set</span>
                        @endif
                    </div>
                    <input type="file" wire:model="editAvatar" accept="image/jpg,image/jpeg,image/png,image/webp">
                    <div wire:loading wire:target="editAvatar" class="lw-upload-bar"><div class="lw-upload-fill"></div></div>
                    @error('editAvatar')<p class="lw-err">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>
        <div class="lw-modal-footer">
            <button type="button" wire:click="$set('showEdit',false)" class="btn-ghost">Cancel</button>
            <button type="button" wire:click="updateUser"
                    wire:loading.attr="disabled" wire:target="updateUser"
                    class="btn-primary">
                <span wire:loading.remove wire:target="updateUser">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    Save Changes
                </span>
                <span wire:loading wire:target="updateUser">Saving…</span>
            </button>
        </div>
    </div>
</div>
@endif


{{-- ═══════════════════════════════════════
     SCRIPTS
════════════════════════════════════════ --}}
<script>
const _swalBase = {
    background: '#111827',
    color: '#fff',
    confirmButtonColor: '#6366f1',
    cancelButtonColor: '#374151',
};

// ── Delete confirmation ─────────────────────────────────────
function askDeleteUser(id, name) {
    Swal.fire({
        ..._swalBase,
        icon: 'warning',
        title: 'Delete User?',
        html: `Remove <strong>${name}</strong>? This cannot be undone.`,
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#ef4444',
        reverseButtons: true,
        focusCancel: true,
    }).then(r => {
        if (r.isConfirmed) {
            @this.dispatch('confirmed-delete-user', { id });
        }
    });
}

// ── Resend email confirmation ───────────────────────────────
function askResend(id, name, email) {
    Swal.fire({
        ..._swalBase,
        icon: 'question',
        title: 'Resend Access Email?',
        html: `Send account access details to <strong>${name}</strong><br>
        <small style="color:#94a3b8">${email}</small>`,
        showCancelButton: true,
        confirmButtonText: 'Yes, resend',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#06b6d4',
        reverseButtons: true,
        focusCancel: true,
    }).then((r) => {

        if (r.isConfirmed) {

            Livewire.dispatch('confirmed-resend-email', {
                id: id
            });

        }

    });
}

// ── Listen for Livewire swal event ─────────────────────────
document.addEventListener('livewire:init', () => {
    Livewire.on('swal', (payload) => {
        const e = Array.isArray(payload) ? payload[0] : payload;
        const isSuccess = e?.type === 'success';
        const isToast   = isSuccess;

        Swal.fire({
            ..._swalBase,
            icon: e?.type ?? 'info',
            title: e?.title ?? 'Done',
            text:  e?.message ?? '',
            iconColor: isSuccess ? '#22c55e' : (e?.type === 'error' ? '#ef4444' : '#6366f1'),
            ...(isToast ? {
                // toast: true,
                // position: 'top-end',
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
            } : {
                confirmButtonText: 'OK',
            })
        });
    });
});

// ── Close modal on Escape ───────────────────────────────────
document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    @this.set('showCreate', false);
    @this.set('showEdit',   false);
});
</script>

</div>