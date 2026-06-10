
<div>

{{-- =======================
    TOP STATS
======================== --}}
<div class="d-stats-grid mb-4">

    <div class="d-stat">
        <div class="d-stat-icon">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-value">{{ $totalUsers }}</div>
        <div class="stat-label">Total Users</div>
    </div>

    <div class="d-stat">
        <div class="d-stat-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="stat-value">{{ $completedUsers }}</div>
        <div class="stat-label">Completed Users</div>
    </div>

    <div class="d-stat">
        <div class="d-stat-icon">
            <i class="fa-solid fa-video"></i>
        </div>
        <div class="stat-value">{{ $totalDemos }}</div>
        <div class="stat-label">Demo Uploads</div>
    </div>

    <div class="d-stat">
        <div class="d-stat-icon">
            <i class="fa-solid fa-book-open"></i>
        </div>
        <div class="stat-value">{{ $totalCourses }}</div>
        <div class="stat-label">Courses</div>
    </div>

</div>

{{-- =======================
    FILTERS
======================== --}}
<div class="filter-card mb-4">

    <div class="filter-grid">

        <div>
            <input
                type="text"
                wire:model.live.debounce.500ms="search"
                class="theme-form-control form-control"
                placeholder="🔍 Search Name, Email, Phone">
        </div>

        <div>
            <select wire:model.live="educationLevel" class="theme-form-control form-control">
                <option value="">All Education Levels</option>

                @foreach($educationLevels as $level)
                    <option value="{{ $level->id }}">
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <select wire:model.live="courseFilter" class="theme-form-control form-control">
                <option value="">All Courses</option>

                @foreach($courses as $course)
                    <option value="{{ $course->id }}">
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <select wire:model.live="progressFilter" class="theme-form-control form-control">
                <option value="">All Progress</option>
                <option value="25">0 - 25%</option>
                <option value="50">25 - 50%</option>
                <option value="75">50 - 75%</option>
                <option value="100">Completed</option>
            </select>
        </div>

    </div>

</div>

{{-- =======================
    TABLE
======================== --}}
<div class="table-card">

    <div class="table-responsive">

        <table class="theme-table">

            <thead>
                <tr>

                    <th wire:click="sortBy('id')" class="sortable">
                        #
                    </th>

                    <th>
                        IP Address
                    </th>

                    <th wire:click="sortBy('full_name')" class="sortable">
                        Name
                        @if($sortField == 'full_name')
                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>

                    <th>
                        Email / Phone
                    </th>

                    <th>
                        Education
                    </th>

                    <th>
                        Course
                    </th>

                    <th wire:click="sortBy('progress_demo')" class="sortable">
                        Progress
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Demos
                    </th>

                    <th wire:click="sortBy('created_at')" class="sortable">
                        Created
                        @if($sortField == 'created_at')
                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                        @endif
                    </th>

                </tr>
            </thead>

            <tbody>

                @forelse($demoUsers as $demo)

                    <tr>

                        <td>
                            #{{ $demo->id }}
                        </td>

                        <td>
                            <small>{{ $demo->ip_address }}</small>
                        </td>

                        <td>
                            <div class="user-name">
                                {{ $demo->full_name }}
                            </div>
                        </td>

                        <td>
                            {{ $demo->email_phone }}
                        </td>

                        <td>
                            {{ $demo->educationLevel?->name ?? '-' }}
                        </td>

                        <td>
                            {{ $demo->course?->title ?? '-' }}
                        </td>

                        <td>

                            <div class="progress-wrap">
                                <div
                                    class="progress-fill"
                                    style="width:{{ $demo->progress_demo }}%">
                                </div>
                            </div>

                            <div class="progress-text">
                                {{ $demo->progress_demo }}%
                            </div>

                        </td>

                        <td>

                            @if($demo->progress_demo >= 100)

                                <span class="status success">
                                    Completed
                                </span>

                            @elseif($demo->progress_demo >= 50)

                                <span class="status warning">
                                    In Progress
                                </span>

                            @else

                                <span class="status danger">
                                    Started
                                </span>

                            @endif

                        </td>

                        <td>
                            <span class="demo-count">
                                {{ $demo->submittedDemos->count() }}
                            </span>
                        </td>

                        <td>
                            {{ $demo->created_at->format('d M Y') }}
                            <br>
                            <small>
                                {{ $demo->created_at->format('h:i A') }}
                            </small>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="10" class="text-center py-5">

                            <div class="empty-state">

                                <div class="empty-icon">
                                    📭
                                </div>

                                <h5>
                                    No Demo Users Found
                                </h5>

                                <p>
                                    No records match your filters.
                                </p>

                            </div>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- =======================
    PAGINATION
======================== --}}
<div class="mt-4">
    {{ $demoUsers->links('pagination.custom') }}
</div>


</div>
