@extends('layouts.app')

@php
    $visibleRoleOptions = $isSuperAdmin
        ? $roleOptions
        : collect($roleOptions)->except([\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN])->all();
@endphp

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>User Management</h1>
                    <p>Manage users, roles, and account status. Super Admin has full control.</p>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Create User</h2>
            </div>
            <form method="POST" action="{{ route('users.store') }}" class="stack">
                @csrf
                <div class="form-grid">
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="field">
                        <label>Role</label>
                        <select name="role" required>
                            @foreach ($visibleRoleOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="actions-row">
                    <button class="btn" type="submit">Create User</button>
                </div>
            </form>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>All Users</h2>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($users as $u)
                        @php
                            $adminRestricted = in_array($u->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true);
                            $canManage = $isSuperAdmin || ! $adminRestricted;
                        @endphp
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>
                                <strong>{{ $u->name }}</strong><br>
                                <span class="muted">{{ $u->email }}</span>
                            </td>
                            <td>{{ $roleOptions[$u->role] ?? $u->role }}</td>
                            <td>
                                <span class="tag {{ $u->is_active ? 'ok' : 'no' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                @if ($canManage)
                                    <form method="POST" action="{{ route('users.update', $u) }}" class="stack mb-8">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-grid">
                                            <div>
                                                <label>Name</label>
                                                <input type="text" name="name" value="{{ $u->name }}" required>
                                            </div>
                                            <div>
                                                <label>Email</label>
                                                <input type="email" name="email" value="{{ $u->email }}" required>
                                            </div>
                                            <div>
                                                <label>Role</label>
                                                <select name="role">
                                                    @foreach ($visibleRoleOptions as $value => $label)
                                                        <option value="{{ $value }}" @selected($u->role === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label>Status</label>
                                                <select name="is_active">
                                                    <option value="1" @selected($u->is_active)>Active</option>
                                                    <option value="0" @selected(! $u->is_active)>Inactive</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label>New Password</label>
                                                <input type="password" name="password" placeholder="Optional">
                                            </div>
                                        </div>
                                        <div class="actions-row">
                                            <button class="btn btn-soft" type="submit">Update</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('users.destroy', $u) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit" onclick="return confirm('Delete this user?')">Delete</button>
                                    </form>
                                @else
                                    <span class="muted">Admin cannot manage admin/superadmin.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No users found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
