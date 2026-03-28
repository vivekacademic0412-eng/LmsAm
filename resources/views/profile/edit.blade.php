@extends('layouts.app')

@php
    $roleLabels = \App\Models\User::roleOptions();
@endphp

@section('content')
    <div class="stack">
        <section class="card">
            <div class="page-head">
                <div>
                    <h1>My Profile</h1>
                    <p>Manage your account information and security settings.</p>
                </div>
            </div>
            <div class="grid-3">
                <div class="card panel-soft">
                    <p class="muted m-0">Role</p>
                    <p class="kpi kpi-sm">{{ $roleLabels[$user->role] ?? $user->role }}</p>
                </div>
                <div class="card panel-soft">
                    <p class="muted m-0">Status</p>
                    <p class="kpi kpi-sm {{ $user->is_active ? 'text-ok' : 'text-danger' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </p>
                </div>
                <div class="card panel-soft">
                    <p class="muted m-0">Email</p>
                    <p class="kpi kpi-xs">{{ $user->email }}</p>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="page-head">
                <h2>Update Profile</h2>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" class="stack" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="field">
                        <label>Current Photo</label>
                        <div class="avatar" style="width:64px;height:64px;">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>
                    <div class="field">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="field">
                        <label>Profile Photo</label>
                        <input type="file" name="avatar" accept="image/*">
                    </div>
                    <div class="field">
                        <label>New Password (optional)</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current password">
                    </div>
                    <div class="field">
                        <label>Current Password (required to change password)</label>
                        <input type="password" name="current_password" placeholder="Enter current password only if changing">
                    </div>
                </div>
                <div class="actions-row">
                    <button class="btn" type="submit">Save Profile</button>
                    <a class="btn btn-soft" href="{{ route('dashboard') }}">Back to Dashboard</a>
                </div>
            </form>
        </section>
    </div>
@endsection
