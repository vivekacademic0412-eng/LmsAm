<div>

    {{-- ==========================
        STATS CARDS
    ========================== --}}
    <div class="d-stats-grid mb-4">

        <div class="d-stat">
            <div class="d-stat-icon">
                <i class="fa-solid fa-video"></i>
            </div>
            <div class="stat-value">{{ $totalDemos }}</div>
            <div class="stat-label">Total Demos</div>
        </div>

        <div class="d-stat">
            <div class="d-stat-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-value">{{ $approvedDemos }}</div>
            <div class="stat-label">Approved</div>
        </div>

        <div class="d-stat">
            <div class="d-stat-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-value">{{ $pendingDemos }}</div>
            <div class="stat-label">Pending</div>
        </div>

        <div class="d-stat">
            <div class="d-stat-icon">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div class="stat-value">{{ $totalCourses }}</div>
            <div class="stat-label">Courses</div>
        </div>

    </div>

    {{-- ==========================
        FILTERS
    ========================== --}}
    <div class="filter-card mb-4">

        <div class="filter-grid">

            <input wire:model.live.debounce.500ms="search" class="theme-form-control form-control"
                placeholder="Search Topic or Student">

            <select wire:model.live="courseFilter" class="theme-form-control form-control">

                <option value="">
                    All Courses
                </option>

                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">
                        {{ $course->title }}
                    </option>
                @endforeach

            </select>

            <select wire:model.live="statusFilter" class="theme-form-control form-control">

                <option value="">
                    All Status
                </option>

                <option value="pending">
                    Pending
                </option>

                <option value="approved">
                    Approved
                </option>

                <option value="rejected">
                    Rejected
                </option>

            </select>

        </div>

    </div>

    {{-- ==========================
        TABLE
    ========================== --}}
    <div class="table-card">

        <div class="table-responsive">

            <table class="theme-table">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>
                            Student
                        </th>

                        <th>
                            Course
                        </th>

                        <th>
                            Topic
                        </th>
                        <th>
                            Desc
                        </th>


                        <th>
                            Status
                        </th>

                        <th>
                            Video
                        </th>

                        <th>
                            Submitted
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($demos as $index=> $demo)
                        <tr>

                            <td>
                                #{{ $index + 1 }}
                            </td>

                            <td>

                                <div class="fw-bold">
                                    {{ $demo->demoUser?->full_name }}
                                </div>

                                <small>
                                    {{ $demo->demoUser?->email_phone }}
                                </small>

                            </td>

                            <td>
                                {{ $demo->course?->title ?? '-' }}
                            </td>

                            <td>
                                {{ $demo->demo_topic }}
                            </td>
                 <td style="max-width:500px">
    @php
        $desc = $demo->demo_description;
    @endphp

    @if(strlen($desc) > 100)

        <span id="short{{ $demo->id }}">
            {{ \Illuminate\Support\Str::limit($desc, 100) }}
        </span>

        <span id="full{{ $demo->id }}" style="display:none;">
            {{ $desc }}
        </span>

        <a href="javascript:void(0)"
           class="text-primary fw-bold"
           onclick="toggleDescription({{ $demo->id }})"
           id="btn{{ $demo->id }}">
            Read More
        </a>

    @else
        {{ $desc }}
    @endif
</td>

                            <td>

                                @if ($demo->status == 'approved')
                                    <span class="status success">
                                        Approved
                                    </span>
                                @elseif($demo->status == 'rejected')
                                    <span class="status danger">
                                        Rejected
                                    </span>
                                @else
                                    <span class="status warning">
                                        Pending
                                    </span>
                                @endif

                            </td>

                            <td>

                                @if ($demo->demo_video)
                                    <a href="{{ asset('storage/' . $demo->demo_video) }}" target="_blank"
                                        class="btn btn-sm btn-primary">

                                        Preview

                                    </a>
                                @else
                                    <span class="text-muted">
                                        No Video
                                    </span>
                                @endif

                            </td>

                            <td>

                                {{ $demo->created_at->format('d M Y') }}

                                <br>

                                <small>
                                    {{ $demo->created_at->format('h:i A') }}
                                </small>

                            </td>

                            <td>

                                <div class="d-flex gap-2 flex-wrap">
                                    @if ($demo->status == 'pending')
                                        @if ($demo->status != 'approved')
                                            <button wire:click="approve({{ $demo->id }})"
                                                class="btn btn-success btn-sm">

                                                <i class="fa-solid fa-check"></i>

                                            </button>
                                        @endif

                                        @if ($demo->status != 'rejected')
                                            <button wire:click="reject({{ $demo->id }})"
                                                class="btn btn-danger btn-sm">

                                                <i class="fa-solid fa-xmark"></i>

                                            </button>
                                        @endif
                                    @endif
                                    @if ($demo->status == 'approved')
                                        @if ($demo->demo_video)
                                            <a href="{{ asset('storage/' . $demo->demo_video) }}" download
                                                class="btn btn-info btn-sm">

                                                <i class="fa-solid fa-download"></i>

                                            </a>
                                        @endif
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9" class="text-center py-5">

                                <div class="empty-state">

                                    <div style="font-size:2rem">
                                        🎥
                                    </div>

                                    <h5 class="mt-2">
                                        No Demo Submissions Found
                                    </h5>

                                    <p>
                                        No records available for selected filters.
                                    </p>

                                </div>

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- ==========================
        PAGINATION
    ========================== --}}
    <div class="mt-4">
        {{ $demos->links('pagination.custom') }}
    </div>
    <script>
        document.addEventListener('livewire:init', () => {

            Livewire.on('swal', (event) => {

                Swal.fire({
                    icon: event.icon,
                    title: event.title,
                    confirmButtonColor: '#6366f1',
                    timer: 2500,
                    showConfirmButton: false
                });

            });

        });
    </script>
</div>
