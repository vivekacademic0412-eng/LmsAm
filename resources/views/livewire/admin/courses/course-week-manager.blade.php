<div class="cc-wrap">
    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">🗓️</div>
            <div>
                <div class="cc-title">Week Manager</div>
                <div class="cc-subtitle">Add, edit or hide weeks for a course</div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="cc-alert cc-alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="cc-selector-row">
        <div class="cc-field">
            <label class="cc-label">Subject</label>
            <select wire:model.live="category_id" class="cc-select">
                <option value="">-- Select Subject --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="cc-field">
            <label class="cc-label">Course</label>
            <select wire:model.live="course_id" class="cc-select" @disabled(!$category_id)>
                <option value="">-- Select Course --</option>
                @foreach ($this->courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($course_id)
        <div class="cc-section">
            <div class="cc-section-title">{{ $editing_week_id ? 'Edit Week' : 'Add New Week' }}</div>

            <div class="cc-grid-3">
                <div class="cc-field">
                    <label class="cc-label">Week Number</label>
                    <input type="number" min="1" wire:model="week_number" class="cc-input">
                    @error('week_number') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
                <div class="cc-field">
                    <label class="cc-label">Title</label>
                    <input type="text" wire:model="title" class="cc-input" placeholder="e.g. Week 1 - Foundations">
                    @error('title') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
                <div class="cc-field">
                    <label class="cc-label">&nbsp;</label>
                    <div class="cc-toggle-row">
                        <span class="cc-toggle-label">Visible to Students</span>
                        <label class="cc-switch">
                            <input type="checkbox" wire:model="is_visible">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="cc-actions">
                <button wire:click="saveWeek" class="cc-btn cc-btn-primary">
                    {{ $editing_week_id ? 'Update Week' : 'Add Week' }}
                </button>
                @if ($editing_week_id)
                    <button wire:click="resetForm" class="cc-btn cc-btn-outline">Cancel</button>
                @endif
            </div>
        </div>

        <div class="cc-table-wrap">
            <table class="cc-table">
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Title</th>
                        <th>Visible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($weeks as $week)
                        <tr>
                            <td data-label="Week">Week {{ $week->week_number }}</td>
                            <td data-label="Title">{{ $week->title }}</td>
                            <td data-label="Visible">
                                @if (optional($week->settings)->is_visible ?? true)
                                    <span class="cc-badge cc-badge-success">Visible</span>
                                @else
                                    <span class="cc-badge cc-badge-muted">Hidden</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <div class="cc-table-actions">
                                    <button wire:click="editWeek({{ $week->id }})" class="cc-btn-link">Edit</button>
                                    <button wire:click="deleteWeek({{ $week->id }})"
                                            wire:confirm="Delete this week and all its sessions?"
                                            class="cc-btn-link-danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="cc-empty">No weeks added yet — create the first one above.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>