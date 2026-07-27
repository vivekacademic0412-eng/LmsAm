<div class="cc-wrap">
    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">🎓</div>
            <div>
                <div class="cc-title">Certificate Settings</div>
                <div class="cc-subtitle">Configure course-level & per-week certificate unlock rules</div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="cc-alert cc-alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="cc-breadcrumb">
        <span class="{{ !$category_id ? 'active' : '' }}">Select Subject</span>
        @if ($category_id)
            <span class="sep">›</span>
            <span class="{{ $category_id && !$course_id ? 'active' : '' }}">Select Course</span>
        @endif
        @if ($course_id)
            <span class="sep">›</span>
            <span class="active">Configure Rules</span>
        @endif
    </div>

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
            <div class="cc-section-title">Course-Level Rule</div>

            <div class="cc-grid-2">
                <div class="cc-field">
                    <label class="cc-label">Certificate Mode</label>
                    <select wire:model="certificate_mode" class="cc-select">
                        <option value="end_of_course">End of Course Only</option>
                        <option value="per_week">Per Week Only</option>
                        <option value="per_level">Per Level</option>
                        <option value="both">End of Course + Per Week</option>
                    </select>
                </div>

                <div class="cc-field">
                    <label class="cc-label">Minimum Completion % Required</label>
                    <input type="number" min="1" max="100" wire:model="min_completion_percent" class="cc-input">
                    <span class="cc-hint">Overall + all "required" sessions must be completed for the certificate to unlock.</span>
                </div>
            </div>
        </div>

        @if (count($weekRows))
            <div class="cc-section">
                <div class="cc-section-title">Per-Week Certificate Add-ons</div>

                <div class="cc-table-wrap">
                    <table class="cc-table">
                        <thead>
                            <tr>
                                <th>Week</th>
                                <th>Enable Certificate</th>
                                <th>Min % Override</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($weekRows as $i => $row)
                                <tr>
                                    <td data-label="Week">Week {{ $row['week_number'] }} — {{ $row['title'] }}</td>
                                    <td data-label="Enable">
                                        <label class="cc-switch">
                                            <input type="checkbox" wire:model="weekRows.{{ $i }}.certificate_enabled">
                                            <span class="slider"></span>
                                        </label>
                                    </td>
                                    <td data-label="Min %">
                                        <input type="number" min="1" max="100" placeholder="course default"
                                               wire:model="weekRows.{{ $i }}.min_completion_percent"
                                               class="cc-input" style="max-width:160px">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="cc-actions">
            <button wire:click="save" class="cc-btn cc-btn-primary">Save Certificate Settings</button>
        </div>
    @endif
</div>