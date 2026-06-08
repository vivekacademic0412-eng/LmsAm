<div class="container-fluid demo-video-page">
    <style>
        /* =====================================================
   DEMO VIDEO MANAGEMENT PAGE
===================================================== */

        .demo-video-page {
            position: relative;
            z-index: 1;
            color: var(--text);
        }

        /* Hero Section */
        /* .d-admin-hero{
    background:linear-gradient(
        135deg,
        rgba(99,102,241,.15),
        rgba(129,140,248,.08)
    );
    border:1px solid var(--border);
    border-radius:var(--radius-xl);
    padding:32px;
    backdrop-filter:blur(12px);
    box-shadow:var(--shadow-card);
} */

        .d-hero-title {
            color: var(--text);
            font-size: 2rem;
            font-weight: 700;
        }

        .d-hero-meta {
            color: var(--text3);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            background: rgba(99, 102, 241, .15);
            color: var(--accent);
            font-size: .85rem;
            font-weight: 600;
            border: 1px solid rgba(99, 102, 241, .25);
        }

        /* Stats */
        .d-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .d-stat {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: .3s ease;
            box-shadow: var(--shadow-card);
        }

        .d-stat:hover {
            transform: translateY(-4px);
            background: var(--card-hover);
        }

        .d-stat h3 {
            color: var(--text);
            font-size: 1.8rem;
            margin-bottom: 4px;
        }

        .d-stat span {
            color: var(--text3);
        }

        .d-stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
        }

        .d-stat-icon i {
            color: #fff;
        }

        .d-stat-icon.g {
            background: linear-gradient(135deg,
                    var(--accent),
                    var(--accent-light));
        }

        .d-stat-icon.success {
            background: linear-gradient(135deg,
                    var(--green),
                    var(--green-dark));
        }

        .d-stat-icon.warning {
            background: linear-gradient(135deg,
                    var(--gold),
                    var(--gold-dark));
        }

        /* Cards */
        .form-card,
        .filter-card,
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-card);
        }

        .card-title {
            margin-bottom: 20px;
        }

        .card-title h4 {
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Form Elements */
        .form-label {
            color: var(--text2);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            background: var(--bg2);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .form-control:focus {
            background: var(--bg2);
            color: var(--text);
            border-color: var(--accent);
            box-shadow: 0 0 0 .2rem rgba(99, 102, 241, .15);
        }

        .form-control::placeholder {
            color: var(--text4);
        }

        .field-note {
            color: var(--text4);
            font-size: .8rem;
            margin-top: 6px;
        }

        /* Buttons */
        .btn-primary {
            background: var(--accent);
            border: none;
        }

        .btn-primary:hover {
            background: var(--accent-dark);
        }

        .btn-success {
            background: var(--green);
            border: none;
        }

        .btn-success:hover {
            background: var(--green-dark);
        }

        .btn-warning {
            background: var(--gold);
            border: none;
            color: #fff;
        }

        .btn-danger {
            background: var(--coral);
            border: none;
        }

        .btn-secondary {
            border: none;
        }

        /* Table */
        .theme-table {
            margin-bottom: 0;
        }

        .theme-table thead th {
            background: var(--bg2);
            color: var(--text);
            border-bottom: 1px solid var(--border);
            padding: 16px;
            white-space: nowrap;
        }

        .theme-table tbody td {
            color: var(--text2);
            border-color: var(--border);
            vertical-align: middle;
            padding: 16px;
        }

        .theme-table tbody tr {
            transition: .25s ease;
        }

        .theme-table tbody tr:hover {
            background: var(--card-hover);
        }

        .theme-table strong {
            color: var(--text);
        }

        /* Video Preview */
        .video-table video {
            width: 180px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        /* Badge */
        .badge.bg-primary {
            background: var(--accent) !important;
            padding: 8px 12px;
            border-radius: 999px;
        }

        /* Progress */
        .progress {
            height: 12px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--bg3);
        }

        .progress-bar {
            background: linear-gradient(90deg,
                    var(--accent),
                    var(--accent-light));
        }

        /* Empty State */
        .theme-table td.text-center {
            color: var(--text3);
        }

        /* Scrollbar */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 999px;
        }

        /* Mobile */
        @media(max-width:768px) {

            .d-admin-hero {
                padding: 24px;
            }

            .d-hero-title {
                font-size: 1.5rem;
            }

            .d-stat {
                padding: 18px;
            }

            .video-table video {
                width: 140px;
                height: 80px;
            }

            .form-card,
            .filter-card,
            .table-card {
                padding: 18px;
            }
        }
    </style>
    <div class="video-manager ">

        {{-- Hero Section --}}
        <div class="d-admin-hero mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 d-hero-inner">

                <div>

                    <span class="hero-badge">
                        <i class="fa-solid fa-video"></i>
                        Demo Feature Videos
                    </span>

                    <h2 class="mt-3 mb-2 d-hero-title">
                        Feature Video Management
                    </h2>

                    <p class=" mb-0 d-hero-meta">
                        Upload, manage and organize dashboard videos shown to demo users.
                    </p>

                </div>

            </div>

        </div>

        {{-- Stats --}}
        <div class="  d-stats-grid g-4 mb-4">

            <div class=" d-stat">



                <div class="stat-icon d-stat-icon g">
                    <i class="fa-solid fa-video"></i>
                </div>

                <div>

                    <h3>{{ $videos->count() }}</h3>

                    <span>Total Videos</span>

                </div>



            </div>

            <div class=" d-stat">



                <div class="stat-icon success d-stat-icon g">
                    <i class="fa-solid fa-play"></i>
                </div>

                <div>

                    <h3>{{ $featured?->position ?? '-' }}</h3>

                    <span>Featured Position</span>

                </div>


            </div>

            <div class=" d-stat">



                <div class="stat-icon warning d-stat-icon g">
                    <i class="fa-solid fa-arrow-up"></i>
                </div>

                <div>

                    <h3>{{ $nextPosition }}</h3>

                    <span>Next Position</span>

                </div>



            </div>

        </div>

        {{-- Form Card --}}
        <div class="form-card mb-4">

            <div class="card-title">

                <h4>

                    @if ($isEdit)
                        <i class="fa-solid fa-pen"></i> Edit Video
                    @else
                        <i class="fa-solid fa-upload"></i> Upload Video
                    @endif

                </h4>

            </div>

            <form wire:submit="{{ $isEdit ? 'update' : 'save' }}">

                <div class="row">

                    <div class="col-md-3 mb-3">

                        <label class="form-label">
                            Position
                        </label>

                        <input type="number" wire:model.live="position" class="form-control">
                        <div class="field-note">Use unique positions. Video 1 shows first, video 2 shows second.</div>
                        @error('position')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    <div class="upload-wrapper col-md-9 mb-3">

                        <label class="upload-box">

                            <input type="file" wire:model="video_file" accept="video/*" class="upload-input">

                            <div class="upload-content">

                                <div class="upload-icon">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>

                                <h5>Upload Video</h5>

                                <p>
                                    Drag & Drop your video here or click to browse
                                </p>

                                <span class="upload-note">
                                    MP4, MOV, AVI, MKV, WEBM
                                </span>

                                @if ($video_file)
                                    <div class="selected-file">

                                        <i class="fa-solid fa-circle-check"></i>

                                        <div>
                                            <strong>
                                                {{ $video_file->getClientOriginalName() }}
                                            </strong>

                                            <small>
                                                {{ round($video_file->getSize() / 1024 / 1024, 2) }}
                                                MB
                                            </small>
                                        </div>

                                    </div>
                                @endif

                            </div>

                        </label>

                        @error('video_file')
                            <small class="text-danger d-block mt-2">
                                {{ $message }}
                            </small>
                        @enderror

                        <div wire:loading wire:target="video_file" class="mt-3">

                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:100%">
                                    Uploading...
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Title
                        </label>

                        <input type="text" wire:model.live="title" class="form-control">

                        @error('title')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea rows="4" wire:model.live="description" class="form-control"></textarea>

                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

                {{-- Upload Loader --}}
                <div wire:loading wire:target="video_file">

                    <div class="progress mb-3">

                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:100%">
                            Uploading...
                        </div>

                    </div>

                </div>

                <div class="d-flex gap-2">

                    @if ($isEdit)
                        <button type="submit" class="btn btn-success">

                            <i class="fa-solid fa-save"></i>
                            Update Video

                        </button>

                        <button type="button" wire:click="resetForm" class="btn btn-secondary">

                            Cancel

                        </button>
                    @else
                        <button type="submit" class="theme-submit-btn">

                            <i class="fa-solid fa-cloud-upload"></i>
                            Save Video

                        </button>
                    @endif

                </div>

            </form>

        </div>

        {{-- Filters --}}
        <div class="filter-card mb-4">

            <div class="row">

                <div class="col-md-8">

                    <input type="text" wire:model.live.debounce.500ms="search"
                        placeholder="Search title or description..." class="form-control">

                </div>

                <div class="col-md-4">

                    <input type="number" wire:model.live="filterPosition" placeholder="Filter Position"
                        class="form-control">

                </div>

            </div>

        </div>

        {{-- Video Table --}}
        <div class="table-card">

            <div class="table-header">

                <div>
                    <h4>
                        <i class="fa-solid fa-film"></i>
                        Uploaded Videos
                    </h4>

                    <p>
                        Manage all uploaded demo videos and featured positions.
                    </p>
                </div>

                <div class="video-count">
                    {{ $videos->count() }} Videos
                </div>

            </div>

            <div class="table-responsive">
                <div class="table-responsive ">

                    <table class=" theme-table video-table align-middle">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Position</th>
                                <th>Size</th>
                                <th>Created</th>
                                <th width="180">Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($videos as $video)
                                <tr>

                                    <td>
                                        #{{ $video->id }}
                                    </td>

                                    <td>

                                        <video width="180" height="100" controls>

                                            <source src="{{ Storage::url($video->file_path) }}"
                                                type="{{ $video->file_mime }}">

                                        </video>

                                    </td>

                                    <td>

                                        <strong>
                                            {{ $video->title }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">

                                            {{ \Illuminate\Support\Str::limit($video->description, 80) }}

                                        </small>

                                    </td>

                                    <td>

                                        <span class="badge bg-primary">

                                            {{ $video->position }}

                                        </span>

                                    </td>

                                    <td>

                                        @if ($video->file_size)
                                            {{ round($video->file_size / 1024 / 1024, 2) }} MB
                                        @endif

                                    </td>

                                    <td>

                                        {{ $video->created_at?->format('d M Y') }}

                                    </td>

                                    <td>

                                        <div class="d-flex gap-2">

                                            <button wire:click="edit({{ $video->id }})"
                                                class="btn btn-warning btn-sm">

                                                <i class="fa-solid fa-pen"></i>

                                            </button>

                                            <button onclick="confirmDelete({{ $video->id }})"
                                                class="btn btn-danger btn-sm">

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-5">

                                        No videos found.

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

        </div>


        {{-- Sweet Alert --}}
        @script
            <script>
                window.confirmDelete = function(id) {

                    Swal.fire({
                        title: 'Delete Video?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Delete'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            $wire.delete(id);
                        }

                    });

                }


                Livewire.on('video-created', () => {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Video uploaded successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });

                });

                Livewire.on('video-updated', () => {

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: 'Video updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });

                });

                Livewire.on('video-deleted', () => {

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Video deleted successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });

                });
            </script>
        @endscript

    </div>

</div>
