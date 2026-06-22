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
                <input type="text" wire:model.live.debounce.500ms="search" class="theme-form-control form-control"
                    placeholder="🔍 Search Name, Email, Phone">
            </div>

            <div>
                <select wire:model.live="educationLevel" class="theme-form-control form-control">
                    <option value="">All Education Levels</option>

                    @foreach ($educationLevels as $level)
                        <option value="{{ $level->id }}">
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select wire:model.live="courseFilter" class="theme-form-control form-control">
                    <option value="">All Courses</option>

                    @foreach ($courses as $course)
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
    <section class="card">

        <div class="table-responsive">
            <table class="table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>IP Address</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Source</th>
                        <th>Landing Page</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="220">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($demoUsers as $index => $demo)
                        @php
                            $paymentType = $demo->paymentType?->demo_type;
                            $paymentStatus = $demo->paymentType?->status;

                            $source = $demo->paymentType?->trafficSource?->source;
                            $landingPage = $demo->paymentType?->trafficSource?->landing_page;
                        @endphp

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <small>
                                    {{ $demo->paymentType?->trafficSource?->user_ip ?? '-' }}
                                </small>
                            </td>

                            <td>
                                <strong>
                                    {{ $demo->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $demo->email }}
                            </td>


                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($source ?? 'Direct') }}
                                </span>
                            </td>

                            <td style="max-width:220px;">
                                <small>
                                    {{ Str::limit($landingPage, 40) }}
                                </small>
                            </td>

                            <td>

                                @if ($paymentType == 'free')
                                    <span class="badge bg-success">
                                        Free Demo
                                    </span>
                                @elseif($paymentType == 'paid_qr')
                                    <span class="badge bg-warning text-dark">
                                        QR Payment
                                    </span>
                                @elseif($paymentType == 'online')
                                    <span class="badge bg-primary">
                                        Online Payment
                                    </span>
                                @elseif($paymentType == 'invoice')
                                    <span class="badge bg-info">
                                        Invoice
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        N/A
                                    </span>
                                @endif

                            </td>

                            <td>

                                @if ($paymentStatus == 'completed')
                                    <span class="badge bg-success">
                                        Completed
                                    </span>
                                @elseif($paymentStatus == 'pending')
                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        N/A
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

                                    {{-- Activate Button only for FREE and QR --}}
                                    @if (in_array($paymentType, ['free', 'paid_qr']))
                                              @if($demo->paymentType?->is_confirm==1)
                                        <button wire:click="activateUser({{ $demo->id }})"
                                            wire:confirm="Activate this user?" class="btn btn-success btn-sm">

                                            Activate

                                        </button>
                                        @endif
                                        {{-- Send Login Credentials --}}
                                        <button wire:click="sendLoginMail({{$demo->id}})"
                                            class="btn btn-primary btn-sm">

                                            Send Mail

                                        </button>
                                    @endif



                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="15" class="text-center py-5">

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

    </section>

    {{-- =======================
    PAGINATION
======================== --}}
    <div class="mt-4">
        {{ $demoUsers->links('pagination.custom') }}
    </div>


</div>
<script>
document.addEventListener('livewire:init', () => {

    Livewire.on('success', (event) => {

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: event.message,
            confirmButtonColor: '#0947a8',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });

    });

});
</script>