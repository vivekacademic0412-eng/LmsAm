@extends('layouts.app')

@section('content')
    {{-- <style>
        .row-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .btn-mini {
            border: 1px solid #cfd7e4;
            border-radius: 10px;
            background: #f7f9fc;
            color: #1f2f48;
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 1;
            transition: 160ms ease;
        }
        .btn-mini:hover {
            border-color: #bcc8d9;
            background: #eef3f9;
            transform: translateY(-1px);
        }
        .btn-mini.danger:hover {
            border-color: #d8b8b8;
            background: #f9f1f1;
        }
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(8, 15, 28, 0.56);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 120;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            width: min(860px, 100%);
            max-height: calc(100vh - 36px);
            overflow: auto;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.25);
        }
        .modal.modal-sm {
            width: min(460px, 100%);
        }
        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line-soft);
        }
        .modal-head h3 { margin: 0; font-size: 22px; }
        .modal-close {
            border: 0;
            background: transparent;
            color: var(--muted);
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }
        .modal-body { padding: 14px 16px 16px; }
        .modal-body .form-premium {
            padding: 16px;
            border-radius: 14px;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid var(--line-soft);
            padding: 12px 16px;
            gap: 8px;
        }
    </style>
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>User Management</h1>
                    <p>Manage users, roles, and account status.</p>
                </div>
                <div class="actions-row">
                    <div class="filter-wrap">
                        <button type="button" class="filter-btn" data-filter-toggle="userFilterPanel" aria-expanded="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                            </svg>
                            <span>Filter</span>
                        </button>
                        <div class="filter-panel" id="userFilterPanel" aria-hidden="true">
                            <form method="GET" action="{{ route('users.index') }}" id="userFilterForm">
                                <div class="filter-field">
                                    <label>Role</label>
                                    <select name="role" id="userRoleFilter">
                                        <option value="">All Roles</option>
                                        @foreach ($visibleRoleOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($activeRole === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="filter-field">
                                    <label>Status</label>
                                    <select name="status" id="userStatusFilter">
                                        <option value="">All Statuses</option>
                                        <option value="active" @selected($activeStatus === 'active')>Active</option>
                                        <option value="inactive" @selected($activeStatus === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <div class="filter-actions">
                                    <a class="btn btn-soft" href="{{ route('users.index') }}">Clear</a>
                                    <button class="btn" type="submit">Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <button type="button" class="btn btn-soft" data-modal-open="modal-user-create">+ Add User</button>
                </div>
            </div>
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
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <div class="avatar small">
                                        @if ($u->avatar_url)
                                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}">
                                        @else
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $u->name }}</strong><br>
                                        <span class="muted">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $roleOptions[$u->role] ?? $u->role }}</td>
                            <td>
                                <span class="tag {{ $u->is_active ? 'ok' : 'no' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                @if ($canManage)
                                    <div class="row-actions">
                                        <form method="POST" action="{{ route('users.resend-email', $u) }}">
                                            @csrf
                                            <button type="submit" class="btn-mini">Resend Email</button>
                                        </form>
                                        <button type="button" class="btn-mini" data-modal-open="modal-user-edit-{{ $u->id }}">Edit</button>
                                        <button type="button" class="btn-mini danger" data-modal-open="modal-user-delete-{{ $u->id }}">Delete</button>
                                    </div>
                                @else
                                    <span class="muted">You do not have permission to manage this user.</span>
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
            <div class="mt-10">
                {{ $users->links('pagination.custom') }}
            </div>
        </section>
    </div>

    <div class="modal-overlay" id="modal-user-create" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-head">
                <h3>Create User</h3>
                <button type="button" class="modal-close" data-modal-close="modal-user-create" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('users.store') }}" class="stack form-premium" enctype="multipart/form-data">
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
                            <label>Profile Photo</label>
                            <input type="file" name="avatar" accept="image/*">
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft" data-modal-close="modal-user-create">Close</button>
            </div>
        </div>
    </div>

    @foreach ($users as $u)
        @php
            $adminRestricted = in_array($u->role, [\App\Models\User::ROLE_SUPERADMIN, \App\Models\User::ROLE_ADMIN], true);
            $canManage = $isSuperAdmin || ! $adminRestricted;
        @endphp
        @if ($canManage)
            <div class="modal-overlay" id="modal-user-edit-{{ $u->id }}" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Edit User</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-user-edit-{{ $u->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('users.update', $u) }}" class="stack form-premium" enctype="multipart/form-data">
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
                                <div>
                                    <label>Profile Photo</label>
                                    <input type="file" name="avatar" accept="image/*">
                                </div>
                            </div>
                            <div class="actions-row">
                                <button class="btn btn-soft" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-user-edit-{{ $u->id }}">Close</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="modal-user-delete-{{ $u->id }}" aria-hidden="true">
                <div class="modal modal-sm" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Delete User</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-user-delete-{{ $u->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <p class="muted">Delete <strong>{{ $u->name }}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-user-delete-{{ $u->id }}">Cancel</button>
                        <form method="POST" action="{{ route('users.destroy', $u) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach --}}
@livewire('user-management')
    {{-- <script src="{{ asset('js/course-modals.js') }}" defer></script>
    <script src="{{ asset('js/filters.js') }}" defer></script> --}}
@endsection
