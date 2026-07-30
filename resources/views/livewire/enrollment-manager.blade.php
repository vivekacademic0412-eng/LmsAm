<div class="stack" wire:loading.class="opacity-60" wire:target="store,update,delete,resendEmail">

    {{-- ═══════════════ Header + Filters ═══════════════ --}}
    <section class="card">
        <div class="page-head">
            <div>
                <h1>Enrollment Management</h1>
                <p>Assign students to courses and optionally attach trainers.</p>
            </div>
            <div class="actions-row">
                <div class="filter-wrap" x-data @click.outside="$wire.showFilterPanel = false">
                    <button type="button" class="filter-btn" wire:click="toggleFilterPanel"
                            aria-expanded="{{ $showFilterPanel ? 'true' : 'false' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                        </svg>
                        <span>Filter</span>
                    </button>

                    <div class="filter-panel {{ $showFilterPanel ? 'open show' : '' }}"
                         x-show="$wire.showFilterPanel" x-transition.duration.200ms
                         aria-hidden="{{ $showFilterPanel ? 'false' : 'true' }}">
                        <div class="filter-field">
                            <label>Category</label>
                            <select wire:model.live="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-field">
                            <label>Subcategory</label>
                            <select wire:model.live="subcategoryFilter">
                                <option value="">All Subcategories</option>
                                @foreach ($this->subcategoryOptions as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-field">
                            <label>Course</label>
                            <select wire:model.live="courseFilter">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-field">
                            <label>Trainer</label>
                            <select wire:model.live="trainerFilter">
                                <option value="">All Trainers</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="button" class="btn btn-soft" wire:click="clearFilters">Clear</button>
                            <button type="button" class="btn" wire:click="toggleFilterPanel">Apply</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-soft" wire:click="openCreateModal">+ Assign Enrollment</button>
            </div>
        </div>
    </section>

    {{-- ═══════════════ Table ═══════════════ --}}
    <section class="card">
        <div class="page-head">
            <h2>Assigned Enrollments</h2>
        </div>
        <div class="table-wrap" wire:loading.class="opacity-50" wire:target="categoryFilter,subcategoryFilter,courseFilter,trainerFilter">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Student</th>
                    <th>Trainer</th>
                    <th>Assigned By</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($enrollments as $enrollment)
                    <tr wire:key="enrollment-{{ $enrollment->id }}">
                        <td><span class="id-pill">#{{ $enrollment->id }}</span></td>
                        <td>{{ $enrollment->course?->title }}</td>
                        <td>{{ $enrollment->student?->name }}</td>
                        <td>
                            @if($enrollment->trainer)
                                {{ $enrollment->trainer->name }}
                            @else
                                <span class="badge-empty">Not Assigned</span>
                            @endif
                        </td>
                        <td>{{ $enrollment->assignedBy?->name ?? 'System' }}</td>
                        <td>
                            <div class="row-actions">
                                <button type="button" class="btn-mini"
                                        onclick="confirmResendEmail({{ $enrollment->id }}, this)">
                                    Resend Email
                                </button>
                                <button type="button" class="btn-mini" wire:click="openEditModal({{ $enrollment->id }})">
                                    Edit
                                </button>
                                <button type="button" class="btn-mini danger"
                                        onclick="confirmDeleteEnrollment({{ $enrollment->id }}, '{{ addslashes($enrollment->student?->name) }}', '{{ addslashes($enrollment->course?->title) }}', this)">
                                    Remove
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No enrollments yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-10">
            {{ $enrollments->links('pagination.custom') }}
        </div>
    </section>

    {{-- ═══════════════ Create Modal ═══════════════ --}}
    <div class="modal-overlay {{ $showCreateModal ? 'open show' : '' }}" aria-hidden="{{ $showCreateModal ? 'false' : 'true' }}"
         x-show="$wire.showCreateModal" x-transition.duration.200ms
         @click.self="$wire.closeCreateModal()">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-head">
                <h3>Assign Enrollment</h3>
                <button type="button" class="modal-close" wire:click="closeCreateModal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="store" class="stack form-premium" novalidate>
                    <div class="form-grid">
                        <div class="field @error('course_id') is-invalid @enderror">
                            <label>Course <span class="req">*</span></label>
                            <select wire:model="course_id">
                                <option value="">Select course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="field @error('student_id') is-invalid @enderror">
                            <label>Student <span class="req">*</span></label>
                            <select wire:model="student_id">
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                            @error('student_id') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label>Trainer</label>
                            <select wire:model="trainer_id">
                                <option value="">No trainer</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="actions-row">
                        <button class="btn" type="submit" wire:loading.attr="disabled" wire:target="store">
                            <span wire:loading.remove wire:target="store">Assign Enrollment</span>
                            <span wire:loading wire:target="store">Assigning...</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft" wire:click="closeCreateModal">Close</button>
            </div>
        </div>
    </div>

    {{-- ═══════════════ Edit Modal (single, dynamic) ═══════════════ --}}
    <div class="modal-overlay {{ $showEditModal ? 'open show' : '' }}" aria-hidden="{{ $showEditModal ? 'false' : 'true' }}"
         x-show="$wire.showEditModal" x-transition.duration.200ms
         @click.self="$wire.closeEditModal()">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-head">
                <h3>Edit Enrollment</h3>
                <button type="button" class="modal-close" wire:click="closeEditModal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="update" class="stack form-premium" novalidate>
                    <div class="form-grid">
                        <div class="field @error('course_id') is-invalid @enderror">
                            <label>Course <span class="req">*</span></label>
                            <select wire:model="course_id">
                                <option value="">Select course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="field @error('student_id') is-invalid @enderror">
                            <label>Student <span class="req">*</span></label>
                            <select wire:model="student_id">
                                <option value="">Select student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                                @endforeach
                            </select>
                            @error('student_id') <span class="error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label>Trainer</label>
                            <select wire:model="trainer_id">
                                <option value="">No trainer</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="actions-row">
                        <button class="btn btn-soft" type="submit" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">Update</span>
                            <span wire:loading wire:target="update">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-soft" wire:click="closeEditModal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════ SweetAlert2 + interop script (load once in layout, safe to keep here too) ═══════════════ --}}
@once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteEnrollment(id, student, course, btn) {
            Swal.fire({
                icon: 'warning',
                title: 'Remove enrollment?',
                html: 'Remove <b>' + student + '</b> from <b>' + course + '</b>? This cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    var wireEl = btn.closest('[wire\\:id]');
                    Livewire.find(wireEl.getAttribute('wire:id')).dispatch('delete-enrollment', { id: id });
                }
            });
        }

        function confirmResendEmail(id, btn) {
            Swal.fire({
                icon: 'question',
                title: 'Resend enrollment email?',
                showCancelButton: true,
                confirmButtonText: 'Send',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    var wireEl = btn.closest('[wire\\:id]');
                    Livewire.find(wireEl.getAttribute('wire:id')).dispatch('resend-enrollment-email', { id: id });
                }
            });
        }

        document.addEventListener('livewire:init', function () {
            Livewire.on('toast', function (event) {
                var data = Array.isArray(event) ? event[0] : event;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: data.type || 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2800,
                    timerProgressBar: true
                });
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            document.querySelectorAll('.modal-overlay.open').forEach(function (el) {
                el.dispatchEvent(new Event('click'));
            });
        });
    </script>
@endonce