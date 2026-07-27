<div class="cc-wrap">
    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">🎬</div>
            <div>
                <div class="cc-title">Session Manager</div>
                <div class="cc-subtitle">Manage sessions, required topics & live class links</div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="cc-alert cc-alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="cc-selector-row" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
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
        <div class="cc-field">
            <label class="cc-label">Week</label>
            <select wire:model.live="week_id" class="cc-select" @disabled(!$course_id)>
                <option value="">-- Select Week --</option>
                @foreach ($weeks as $week)
                    <option value="{{ $week->id }}">Week {{ $week->week_number }} — {{ $week->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($week_id)
        <div class="cc-section">
            <div class="cc-section-title">{{ $editing_session_id ? 'Edit Session' : 'Add New Session' }}</div>

            <div class="cc-grid-2">
                <div class="cc-field">
                    <label class="cc-label">Session Number</label>
                    <input type="number" min="1" wire:model="session_number" class="cc-input">
                    @error('session_number') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
                <div class="cc-field">
                    <label class="cc-label">Title</label>
                    <input type="text" wire:model="title" class="cc-input" placeholder="e.g. Introduction to SEO">
                    @error('title') <span class="cc-error">{{ $message }}</span> @enderror
                </div>

                <div class="cc-field">
                    <label class="cc-label">Google Meet Link</label>
                    <input type="url" placeholder="https://meet.google.com/xxx-xxxx-xxx" wire:model="meet_link" class="cc-input">
                    @error('meet_link') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
                <div class="cc-field">
                    <label class="cc-label">Class Date & Time</label>
                    <input type="datetime-local" wire:model="meet_datetime" class="cc-input">
                    @error('meet_datetime') <span class="cc-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="cc-grid-2" style="margin-top:16px;">
                <div class="cc-toggle-row">
                    <div>
                        <div class="cc-toggle-label">Required for Certificate</div>
                        <div class="cc-toggle-desc">Student must complete this to unlock the certificate</div>
                    </div>
                    <label class="cc-switch">
                        <input type="checkbox" wire:model="is_required_for_certificate">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="cc-toggle-row">
                    <div>
                        <div class="cc-toggle-label">Visible to Students</div>
                        <div class="cc-toggle-desc">Turn off to hide this session temporarily</div>
                    </div>
                    <label class="cc-switch">
                        <input type="checkbox" wire:model="is_visible">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <div class="cc-actions">
                <button wire:click="saveSession" class="cc-btn cc-btn-primary">
                    {{ $editing_session_id ? 'Update Session' : 'Add Session' }}
                </button>
                @if ($editing_session_id)
                    <button wire:click="resetForm" class="cc-btn cc-btn-outline">Cancel</button>
                @endif
            </div>
        </div>

        <div class="cc-table-wrap">
            <table class="cc-table">
                <thead>
                    <tr>
                        <th>Session</th>
                        <th>Title</th>
                        <th>Required</th>
                        <th>Live Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $session)
                        <tr>
                            <td data-label="Session">Session {{ $session->session_number }}</td>
                            <td data-label="Title">{{ $session->title }}</td>
                            <td data-label="Required">
                                @if (optional($session->settings)->is_required_for_certificate)
                                    <span class="cc-badge cc-badge-danger">Required</span>
                                @else
                                    <span class="cc-badge cc-badge-muted">Optional</span>
                                @endif
                            </td>
                            <td data-label="Live Class">
                                @if (optional($session->settings)->meet_datetime)
                                    <span class="cc-badge cc-badge-accent">
                                        {{ $session->settings->meet_datetime->format('d M, h:i A') }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted); font-size:12.5px;">Not scheduled</span>
                                @endif
                            </td>
                            <td data-label="Actions">
                                <div class="cc-table-actions">
                                    <button wire:click="editSession({{ $session->id }})" class="cc-btn-link">Edit</button>
                                    <button wire:click="deleteSession({{ $session->id }})"
                                            wire:confirm="Delete this session?"
                                            class="cc-btn-link-danger">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="cc-empty">No sessions added yet for this week.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>