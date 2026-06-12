<?php
// FILE: app/Livewire/UserManagement.php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class UserManagement extends Component
{
    use WithFileUploads, WithPagination;

    // ── Auth state ──────────────────────────────────────────────
    public bool $isSuperAdmin         = false;
    public array $visibleRoleOptions  = [];

    // ── Filters (synced to URL) ──────────────────────────────────
    #[Url(as: 'role')]
    public string $filterRole   = '';

    #[Url(as: 'status')]
    public string $filterStatus = '';

    #[Url(as: 'q')]
    public string $search       = '';

    // ── Modal flags ─────────────────────────────────────────────
    public bool $showCreate = false;
    public bool $showEdit   = false;

    // ── CREATE form fields ──────────────────────────────────────
    public string  $name       = '';
    public string  $email      = '';
    public string  $role       = '';
    public string  $password   = '';
    public int     $is_active  = 1;
    public         $avatar     = null;   // Livewire temp upload

    // ── EDIT form fields ─────────────────────────────────────────
    public ?int    $editingId        = null;
    public string  $editName         = '';
    public string  $editEmail        = '';
    public string  $editRole         = '';
    public string  $editPassword     = '';
    public int     $editIsActive     = 1;
    public         $editAvatar       = null;
    public ?string $editCurrentAvatar= null;

    // ── Mount ────────────────────────────────────────────────────
    public function mount(): void
    {
        abort_unless(
            in_array(auth()->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );

        $this->isSuperAdmin       = auth()->user()?->role === User::ROLE_SUPERADMIN;
        $this->visibleRoleOptions = $this->resolveVisibleRoles();
    }

    // ── COMPUTED: paginated users ────────────────────────────────
    #[Computed]
    public function users()
    {
        return User::query()
            ->when(
                !$this->isSuperAdmin,
                fn($q) => $q->whereNotIn('role', [User::ROLE_SUPERADMIN, User::ROLE_ADMIN])
            )
            ->when($this->filterStatus === 'active',   fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->filterRole,                  fn($q) => $q->where('role', $this->filterRole))
            ->when($this->search,                      fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('name',  'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                )
            )
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }

    // ── COMPUTED: role options ───────────────────────────────────
    #[Computed]
    public function roleOptions(): array
    {
        return User::roleOptions();
    }

    // ── Filter / search reset page ───────────────────────────────
    public function updatedSearch():      void { $this->resetPage(); }
    public function updatedFilterRole():  void { $this->resetPage(); }
    public function updatedFilterStatus():void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->reset(['filterRole', 'filterStatus', 'search']);
        $this->resetPage();
    }

    // ════════════════════════════════════════════════════════════
    //  CREATE
    // ════════════════════════════════════════════════════════════
    public function openCreate(): void
    {
        $this->resetCreateForm();
        $this->showCreate = true;
    }

    public function saveUser(): void
    {
        // $this->authorize();

        $this->validate([
            'name'      => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', 'max:150', 'unique:users,email'],
            'role'      => ['required', Rule::in(array_keys($this->visibleRoleOptions))],
            'password'  => ['required', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
            'avatar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarPath = $this->avatar?->store('avatars', 'public');

        $plainPassword = $this->password;

        $user = User::create([
            'name'      => $this->name,
            'email'     => $this->email,
            'role'      => $this->role,
            'password'  => Hash::make($plainPassword),
            'is_active' => (bool) $this->is_active,
            'avatar'    => $avatarPath,
        ]);

        $emailSent = $this->sendWelcomeEmail($user, $plainPassword);

        $this->showCreate = false;
        $this->resetCreateForm();

        $this->dispatch(
            'swal',
            type: 'success',
            title: 'User Created!',
            message: $emailSent
                ? "{$user->name} created. Welcome email sent to {$user->email}."
                : "{$user->name} created. (Email could not be sent — check mail config.)"
        );
    }

    private function resetCreateForm(): void
    {
        $this->reset(['name', 'email', 'role', 'password', 'is_active', 'avatar']);
        $this->is_active = 1;
        $this->resetValidation();
    }

    // ════════════════════════════════════════════════════════════
    //  EDIT
    // ════════════════════════════════════════════════════════════
    public function openEdit(int $id): void
    {
        $u = User::findOrFail($id);

        if (!$this->canManageUser($u)) {
            $this->dispatch('swal', type: 'error', title: 'Permission Denied', message: 'You cannot edit this user.');
            return;
        }

        $this->editingId          = $id;
        $this->editName           = $u->name;
        $this->editEmail          = $u->email;
        $this->editRole           = $u->role;
        $this->editIsActive       = (int) $u->is_active;
        $this->editPassword       = '';
        $this->editAvatar         = null;
        $this->editCurrentAvatar  = $u->avatar;
        $this->resetValidation();
        $this->showEdit = true;
    }

    public function updateUser(): void
    {
        // $this->authorize();

        $u = User::findOrFail($this->editingId);

        if (!$this->canManageUser($u)) {
            $this->dispatch('swal', type: 'error', title: 'Permission Denied', message: 'You cannot update this user.');
            return;
        }

        $this->validate([
            'editName'     => ['required', 'string', 'max:120'],
            'editEmail'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($u->id)],
            'editRole'     => ['required', Rule::in(array_keys($this->visibleRoleOptions))],
            'editPassword' => ['nullable', 'string', 'min:8'],
            'editIsActive' => ['required', 'boolean'],
            'editAvatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $updateData = [
            'name'      => $this->editName,
            'email'     => $this->editEmail,
            'role'      => $this->editRole,
            'is_active' => (bool) $this->editIsActive,
        ];

        if (!empty($this->editPassword)) {
            $updateData['password'] = Hash::make($this->editPassword);
        }

        if ($this->editAvatar) {
            if ($this->editCurrentAvatar
                && !Str::startsWith($this->editCurrentAvatar, ['http://', 'https://'])
                && Storage::disk('public')->exists($this->editCurrentAvatar)
            ) {
                Storage::disk('public')->delete($this->editCurrentAvatar);
            }
            $updateData['avatar'] = $this->editAvatar->store('avatars', 'public');
        }

        $u->update($updateData);

        $this->showEdit = false;
        $this->dispatch('swal', type: 'success', title: 'Updated!', message: "{$u->name} updated successfully.");
    }

    // ════════════════════════════════════════════════════════════
    //  DELETE — triggered after SweetAlert JS confirm
    // ════════════════════════════════════════════════════════════
    #[On('confirmed-delete-user')]
    public function deleteUser(int $id): void
    {
        // $this->authorize();

        if (auth()->id() === $id) {
            $this->dispatch('swal', type: 'error', title: 'Not Allowed', message: 'You cannot delete your own account.');
            return;
        }

        $u = User::findOrFail($id);

        if (!$this->canManageUser($u)) {
            $this->dispatch('swal', type: 'error', title: 'Permission Denied', message: 'You cannot delete this user.');
            return;
        }

        $name = $u->name;

        if ($u->avatar
            && !Str::startsWith($u->avatar, ['http://', 'https://'])
            && Storage::disk('public')->exists($u->avatar)
        ) {
            Storage::disk('public')->delete($u->avatar);
        }

        $u->delete();

        $this->dispatch('swal', type: 'success', title: 'Deleted', message: "\"{$name}\" has been removed.");
    }

    // ════════════════════════════════════════════════════════════
    //  RESEND EMAIL — triggered after SweetAlert JS confirm
    // ════════════════════════════════════════════════════════════
    #[On('confirmed-resend-email')]
    public function resendEmail(int $id): void
    {
        // $this->authorize();

        $u = User::findOrFail($id);

        if (!$this->canManageUser($u)) {
            $this->dispatch('swal', type: 'error', title: 'Permission Denied', message: 'You cannot resend email for this user.');
            return;
        }

        $sent = $this->sendWelcomeEmail($u, null, true);

        $this->dispatch(
            'swal',
            type: $sent ? 'success' : 'error',
            title: $sent ? 'Email Sent!' : 'Failed',
            message: $sent
                ? "Access email resent to {$u->email}."
                : 'Email could not be sent. Check your mail configuration.'
        );
    }

    // ════════════════════════════════════════════════════════════
    //  HELPERS
    // ════════════════════════════════════════════════════════════
    // public function authorize()
    // {
    //     abort_unless(
    //         in_array(auth()->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
    //         403
    //     );
    // }

    private function canManageUser(User $target): bool
    {
        if ($this->isSuperAdmin) return true;
        return !in_array($target->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);
    }

    private function resolveVisibleRoles(): array
    {
        $all = User::roleOptions();
        if ($this->isSuperAdmin) return $all;
        return collect($all)->except([User::ROLE_SUPERADMIN, User::ROLE_ADMIN])->all();
    }

    private function sendWelcomeEmail(User $user, ?string $plainPassword, bool $isResend = false): bool
    {
        // Reuse the HTML builder from UserManagementController
        $controller = new \App\Http\Controllers\UserManagementController;
        $reflection  = new \ReflectionClass($controller);

        $method = $reflection->getMethod('sendWelcomeCredentialsEmail');
        $method->setAccessible(true);

        try {
            return $method->invoke($controller, $user, $plainPassword, auth()->user(), $isResend);
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }

    // ── Render ──────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.user-management');
    }
}