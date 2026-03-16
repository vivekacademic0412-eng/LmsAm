<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManager($request);

        return view('users.index', [
            'users' => User::orderBy('id')->get(),
            'roleOptions' => User::roleOptions(),
            'isSuperAdmin' => $request->user()->role === User::ROLE_SUPERADMIN,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManager($request);

        $allowedRoles = $this->allowedRolesForActor($request->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $managedUser): RedirectResponse
    {
        $this->authorizeManager($request);

        if (! $this->canManageUser($request->user(), $managedUser)) {
            return back()->withErrors(['user' => 'You do not have permission to update this user.']);
        }

        $allowedRoles = $this->allowedRolesForActor($request->user());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($managedUser->id)],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => (bool) ($data['is_active'] ?? false),
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $managedUser->update($updateData);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $managedUser): RedirectResponse
    {
        $this->authorizeManager($request);

        if ($request->user()->id === $managedUser->id) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if (! $this->canManageUser($request->user(), $managedUser)) {
            return back()->withErrors(['user' => 'You do not have permission to delete this user.']);
        }

        $managedUser->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    private function authorizeManager(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }

    /**
     * @return list<string>
     */
    private function allowedRolesForActor(User $actor): array
    {
        if ($actor->role === User::ROLE_SUPERADMIN) {
            return User::ROLES;
        }

        return [
            User::ROLE_MANAGER_HR,
            User::ROLE_IT,
            User::ROLE_TRAINER,
            User::ROLE_STUDENT,
        ];
    }

    private function canManageUser(User $actor, User $target): bool
    {
        if ($actor->role === User::ROLE_SUPERADMIN) {
            return true;
        }

        return ! in_array($target->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);
    }
}
