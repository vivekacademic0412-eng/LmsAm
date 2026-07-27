<div class="cc-wrap">
    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">💺</div>
            <div>
                <div class="cc-title">Seat Booking & Availability</div>
                <div class="cc-subtitle">Control seat display and access countdown per course</div>
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
            <div class="cc-section-title">Seat Display</div>

            <div class="cc-toggle-row" style="margin-bottom:16px;">
                <div>
                    <div class="cc-toggle-label">Always show "Seats Full"</div>
                    <div class="cc-toggle-desc">Use for Advanced level — displayed but never actually bookable</div>
                </div>
                <label class="cc-switch">
                    <input type="checkbox" wire:model="show_seats_as_full">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="cc-grid-2" @if($show_seats_as_full) style="opacity:.45; pointer-events:none;" @endif>
                <div class="cc-field">
                    <label class="cc-label">Total Seats</label>
                    <input type="number" min="0" wire:model="total_seats" class="cc-input" @disabled($show_seats_as_full)>
                </div>
                <div class="cc-field">
                    <label class="cc-label">Booked Seats</label>
                    <input type="number" min="0" wire:model="booked_seats" class="cc-input" @disabled($show_seats_as_full)>
                </div>
            </div>
        </div>

        <div class="cc-section">
            <div class="cc-section-title">Zero-Day Countdown</div>

            <div class="cc-toggle-row" style="margin-bottom:16px;">
                <div>
                    <div class="cc-toggle-label">Show countdown after purchase</div>
                    <div class="cc-toggle-desc">Displayed to student before course access begins</div>
                </div>
                <label class="cc-switch">
                    <input type="checkbox" wire:model="zero_day_countdown_enabled">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="cc-field" style="max-width:220px;">
                <label class="cc-label">Countdown Days</label>
                <input type="number" min="0" wire:model="countdown_days" class="cc-input">
                <span class="cc-hint">0 = access starts immediately after purchase</span>
            </div>
        </div>

        <div class="cc-actions">
            <button wire:click="save" class="cc-btn cc-btn-primary">Save Settings</button>
        </div>
    @endif
</div>