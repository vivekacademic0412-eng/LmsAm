<div>
    <link rel="stylesheet" href="{{ asset('theme/css/admin/course-components.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/admin/course-index.css') }}">

    {{-- ================= HERO ================= --}}
    <div class="cc-hero">
        <div>
            <div class="cc-hero-title">All Courses ({{ $courses->total() }})</div>
            <div class="cc-hero-meta">Create and manage courses with category and subcategory.</div>
        </div>
    </div>

    @if (session('success'))
        <div class="cc-alert cc-alert-success">✓ {{ session('success') }}</div>
    @endif

    {{-- ================= FILTER BAR ================= --}}
    <div class="cc-filter-bar">
        <div style="position:relative;">
            <button type="button" wire:click="toggleFilterPanel" class="cc-btn cc-btn-outline">⚲ Filter</button>

            @if ($showFilterPanel)
                <div class="cc-filter-panel open">
                    <div class="cc-field">
                        <label class="cc-label">Main Category</label>
                        <select wire:model.live="category_id" class="cc-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cc-field">
                        <label class="cc-label">Subcategory</label>
                        <select wire:model.live="subcategory_id" class="cc-select">
                            <option value="">All Subcategories</option>
                            @foreach ($categories as $category)
                                @foreach ($category->children as $sub)
                                    <option value="{{ $sub->id }}">{{ $category->name }} / {{ $sub->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="cc-actions">
                        <button wire:click="clearFilters" class="cc-btn cc-btn-outline">Clear</button>
                        <button wire:click="toggleFilterPanel" class="cc-btn cc-btn-primary">Apply</button>
                    </div>
                </div>
            @endif
        </div>

        {{-- @if ($canManage) --}}
            <button type="button" wire:click="openCreateModal" class="cc-btn cc-btn-primary">+ Add Course</button>
        {{-- @endif --}}
    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="cc-wrap" style="max-width:none;">

        @if ($isTrainer)
            {{-- ================= TRAINER TAB-BROWSE VIEW ================= --}}
            <div style="text-align:center; margin-bottom:18px;">
                <h2 style="font-size:18px; color:var(--text); margin-bottom:4px;">Browse by Category</h2>
                <p style="font-size:13px; color:var(--text-muted);">Select a category and subcategory to filter courses</p>
            </div>

            <div class="cc-tabs" x-data="{ active: {{ $categories->first()?->id ?? 'null' }} }">
                @foreach ($categories as $index => $category)
                    <button type="button" class="cc-tab-btn" :class="active === {{ $category->id }} ? 'active' : ''"
                            @click="active = {{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach

                @foreach ($categories as $index => $category)
                    @php
                        $tabCourses = $category->courses
                            ->concat($category->children->flatMap->courses ?? collect())
                            ->unique('id')
                            ->values();
                    @endphp

                    <div x-show="active === {{ $category->id }}" x-data="{ subtab: 'all' }" style="width:100%; margin-top:16px;">

                        <div class="cc-subtab-label">Subcategories</div>
                        <div class="cc-subtabs">
                            <button type="button" class="cc-subtab-btn" :class="subtab === 'all' ? 'active' : ''" @click="subtab = 'all'">All</button>
                            @foreach ($category->children as $child)
                                <button type="button" class="cc-subtab-btn" :class="subtab === {{ $child->id }} ? 'active' : ''" @click="subtab = {{ $child->id }}">
                                    {{ $child->name }}
                                </button>
                            @endforeach
                        </div>

                        <div class="cc-divider"></div>

                        <div class="cc-course-grid">
                            @forelse ($tabCourses as $course)
                                @php
                                    $thumb = $course->thumbnail_url ?? null;
                                    $bg = $thumb ? "background-image:url('{$thumb}');" : '';
                                    $assigned = in_array($course->id, $this->assignedCourseIds, true);
                                    $courseCategory = $course->subcategory?->name ?? $course->category?->name ?? $category->name;
                                    $subCatId = $course->subcategory_id ?: 'none';
                                @endphp

                                <div x-show="subtab === 'all' || subtab === '{{ $subCatId }}'">
                                    @if ($assigned)
                                        <a href="{{ route('courses.show', $course) }}" class="cc-course-tile">
                                            <div class="cc-tile-top" style="{{ $bg }}"><h3>{{ $course->title }}</h3></div>
                                            <div class="cc-tile-body">
                                                <div class="cc-tile-meta">{{ $courseCategory }}</div>
                                                <span class="cc-btn cc-btn-outline" style="padding:6px 14px; font-size:12.5px;">Open Course</span>
                                            </div>
                                        </a>
                                    @else
                                        <div class="cc-course-tile disabled">
                                            <div class="cc-tile-top" style="{{ $bg }}"><h3>{{ $course->title }}</h3></div>
                                            <div class="cc-tile-body">
                                                <div class="cc-tile-meta">{{ $courseCategory }}</div>
                                                <span class="cc-badge cc-badge-muted">🔒 Locked</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="cc-empty">No courses found in this category</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            {{-- ================= ADMIN TABLE VIEW ================= --}}
            <div class="cc-table-wrap">
                <table class="cc-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Course</th>
                            <th>Language</th>
                            <th>Duration</th>
                            <th>Created By</th>
                            @if ($canManage) <th>Actions</th> @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            <tr>
                                <td data-label="Category">
                                    <strong>{{ $course->category?->name ?? '-' }}</strong>
                                    <div style="font-size:12px; color:var(--text-muted);">
                                        {{ $course->subcategory?->name ? 'Sub: '.$course->subcategory->name : 'No subcategory' }}
                                    </div>
                                </td>
                                <td data-label="Course">
                                    <div class="cc-course-title-cell">
                                        @if ($course->thumbnail_url)
                                            <img src="{{ $course->thumbnail_url }}" class="cc-course-thumb">
                                        @else
                                            <div class="cc-course-thumb-placeholder">NO IMAGE</div>
                                        @endif
                                        <div>
                                            <span class="name">{{ $course->title }}</span>
                                            <span class="meta">{{ $course->short_description }}</span>
                                        </div>
                                    </div>
                                    <a class="cc-btn cc-btn-outline" style="margin-top:8px; padding:6px 14px; font-size:12.5px;" href="{{ route('courses.show', $course) }}">
                                        Open Course
                                    </a>
                                </td>
                                <td data-label="Language">{{ $course->language ?: '-' }}</td>
                                <td data-label="Duration">{{ $course->duration_hours }}h</td>
                                <td data-label="Created By">{{ $course->creator?->name ?? 'N/A' }}</td>
                                @if ($canManage)
                                    <td data-label="Actions">
                                        <div class="cc-table-actions">
                                            <button wire:click="openEditModal({{ $course->id }})" class="cc-btn-link">Edit</button>
                                            <button wire:click="confirmDelete({{ $course->id }})" class="cc-btn-link-danger">Delete</button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="cc-empty">No courses found</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <div style="margin-top:18px;">{{ $courses->links('pagination.custom') }}</div>
    </div>

    {{-- ================= CREATE MODAL ================= --}}
    @if ($canManage && $showCreateModal)
        <div class="cc-modal-overlay">
            <div class="cc-modal">
                <div class="cc-modal-head">
                    <h3>Add Course</h3>
                    <button wire:click="closeCreateModal" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
                    @include('livewire.admin.courses.partials.course-form', ['submitMethod' => 'saveCourse'])
                </div>
                <div class="cc-modal-footer">
                    <button wire:click="closeCreateModal" class="cc-btn cc-btn-outline">Cancel</button>
                    <button wire:click="saveCourse" class="cc-btn cc-btn-primary">Create Course</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ================= EDIT MODAL ================= --}}
    @if ($canManage && $editingCourseId)
        <div class="cc-modal-overlay">
            <div class="cc-modal">
                <div class="cc-modal-head">
                    <h3>Edit Course</h3>
                    <button wire:click="closeEditModal" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
                    @include('livewire.admin.courses.partials.course-form', ['submitMethod' => 'updateCourse'])
                </div>
                <div class="cc-modal-footer">
                    <button wire:click="closeEditModal" class="cc-btn cc-btn-outline">Cancel</button>
                    <button wire:click="updateCourse" class="cc-btn cc-btn-primary">Update Course</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ================= DELETE CONFIRM ================= --}}
    @if ($canManage && $deletingCourseId)
        <div class="cc-modal-overlay">
            <div class="cc-modal" style="max-width:420px;">
                <div class="cc-modal-head">
                    <h3>Delete Course</h3>
                    <button wire:click="cancelDelete" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
                    <p style="color:var(--text-muted); font-size:13.5px;">Are you sure you want to delete this course? This cannot be undone.</p>
                </div>
                <div class="cc-modal-footer">
                    <button wire:click="cancelDelete" class="cc-btn cc-btn-outline">Cancel</button>
                    <button wire:click="deleteCourse" class="cc-btn" style="background:var(--danger); color:#fff;">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>