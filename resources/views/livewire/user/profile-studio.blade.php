<div class="container-fluid py-4">

    <!-- HERO SECTION -->
    <div class="card shadow-sm border-0 text-white mb-4"
        style="background: linear-gradient(135deg,#1e3a8a,#111827); border-radius:16px;">

        <div class="card-body d-flex flex-column flex-md-row align-items-center gap-3 p-4">

            <!-- AVATAR -->
            <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center"
                style="width:90px;height:90px;background:#374151;">

                @if ($selectedAvatar)
                    <img src="{{ $selectedAvatar }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span class="fw-bold fs-3 text-white">
                        {{ strtoupper(substr($name, 0, 1)) }}
                    </span>
                @endif

            </div>

            <!-- USER INFO -->
            <div class="text-center text-md-start flex-grow-1">

                <h3 class="mb-1 fw-bold">{{ $name }}</h3>
                <p class="mb-2 text-light opacity-75">{{ $email }}</p>

                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">

                    <span class="badge rounded-pill bg-primary px-3 py-2">
                        <i class="fa-solid fa-user-graduate me-1"></i>
                        {{ \App\Models\User::roleOptions()[$user->role] }}
                    </span>

                    <span class="badge rounded-pill {{ $user->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>

                </div>

            </div>

        </div>
    </div>

    <!-- STATS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:14px;">
                <i class="fa-solid fa-user fs-4 text-primary mb-2"></i>
                <span class="text-muted">Role</span>
                <h5 class="mb-0">{{ \App\Models\User::roleOptions()[$user->role] }}</h5>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:14px;">
                <i class="fa-solid fa-envelope fs-4 text-info mb-2"></i>
                <span class="text-muted">Email</span>
                <h6 class="mb-0 text-truncate">{{ $email }}</h6>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3" style="border-radius:14px;">
                <i class="fa-solid fa-shield-halved fs-4 text-success mb-2"></i>
                <span class="text-muted">Status</span>
                <h5 class="mb-0">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </h5>
            </div>
        </div>

    </div>

    <!-- VALIDATION ERRORS -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- PROFILE FORM -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
        <div class="card-body p-4">

            <h5 class="mb-1">Profile Information</h5>
            <p class="text-muted mb-4">Update your account information.</p>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" wire:model="name" class="form-control" style="border-radius:10px;">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="form-control" style="border-radius:10px;">
                </div>

                <div class="col-12">
                    <label class="form-label">Upload Profile Photo</label>
                    <input type="file" wire:model="avatarUpload" class="form-control" style="border-radius:10px;">
                </div>

            </div>

        </div>
    </div>

    <!-- AVATAR SELECT -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
        <div class="card-body p-4">

            <h5 class="mb-1">Choose Avatar</h5>
            <p class="text-muted mb-3">Select a generated avatar.</p>

            <div class="d-flex flex-wrap gap-3">

                @foreach ($avatars as $avatar)
                    <button type="button" wire:click="selectAvatar(@js($avatar))" class="border-0 p-1"
                        style="
            width:70px;
            height:70px;
            border-radius:50%;
            overflow:hidden;
            background: {{ $selectedAvatar === $avatar ? '#6366f1' : '#e5e7eb' }};
            transform: {{ $selectedAvatar === $avatar ? 'scale(1.1)' : 'scale(1)' }};
            transition:0.2s;
        ">

                        <img src="{{ $avatar }}"
                            style="width:100%;height:100%;object-fit:cover;border-radius:50%;">

                    </button>
                @endforeach
            </div>

        </div>
    </div>

    <!-- SAVE BUTTON -->
    <div class="text-end">
        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary px-4 py-2"
            style="border-radius:10px;">

            <span wire:loading.remove>
                <i class="fa-solid fa-floppy-disk me-1"></i>
                Save Profile
            </span>

            <span wire:loading>
                Saving...
            </span>

        </button>
    </div>

</div>



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
