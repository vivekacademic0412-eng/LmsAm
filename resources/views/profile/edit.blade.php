@extends('layouts.app')

@php
    $roleLabels = \App\Models\User::roleOptions();
@endphp

@section('content')
<livewire:user.profile-studio />
    {{-- <div class="stack">
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
    </div> --}}
{{-- <div class="profile-page">

   
    <div class="profile-hero">

        <div class="profile-cover"></div>

        <div class="profile-user">

            <div class="profile-avatar">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name,0,1)) }}
                @endif
            </div>

            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <p>{{ $roleLabels[$user->role] ?? $user->role }}</p>
                <span>{{ $user->email }}</span>
            </div>

        </div>

    </div>

   
    <div class="profile-stats">

        <div class="stat-card">
            <i class="fa-solid fa-user-shield"></i>
            <span>Role</span>
            <h4>{{ $roleLabels[$user->role] ?? $user->role }}</h4>
        </div>

        <div class="stat-card">
            <i class="fa-solid fa-circle-check"></i>
            <span>Status</span>
            <h4>{{ $user->is_active ? 'Active' : 'Inactive' }}</h4>
        </div>

        <div class="stat-card">
            <i class="fa-solid fa-envelope"></i>
            <span>Email</span>
            <h4>{{ $user->email }}</h4>
        </div>

    </div>


    <div class="profile-card">

        <div class="card-head">
            <h3>Profile Settings</h3>
            <p>Manage account details and security.</p>
        </div>

        <form method="POST"
              action="{{ route('profile.update') }}"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="field">
                    <label>Full Name</label>
                    <input type="text"
                           name="name"
                           value="{{ $user->name }}">
                </div>

                <div class="field">
                    <label>Email Address</label>
                    <input type="email"
                           name="email"
                           value="{{ $user->email }}">
                </div>

                <div class="field">
                    <label>Profile Photo</label>
                    <input type="file"
                           name="avatar">
                </div>

                <div class="field">
                    <label>New Password</label>
                    <input type="password"
                           name="password">
                </div>

                <div class="field field-full">
                    <label>Current Password</label>
                    <input type="password"
                           name="current_password">
                </div>

            </div>

            <div class="profile-actions">
                <button class="btn-primary">
                    Save Changes
                </button>

                <a href="{{ route('dashboard') }}"
                   class="btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>

</div> --}}
@endsection
