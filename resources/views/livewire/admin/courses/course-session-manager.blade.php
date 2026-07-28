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
                                    <button wire:click="manageItems({{ $session->id }})" class="cc-btn-link">
                                        {{ $active_session_id === $session->id ? 'Hide Items' : 'Manage Items' }}
                                    </button>
                                    <button wire:click="deleteSession({{ $session->id }})"
                                            wire:confirm="Delete this session?"
                                            class="cc-btn-link-danger">Delete</button>
                                </div>
                            </td>
                        </tr>

                        {{-- Items panel for this session --}}
                        @if ($active_session_id === $session->id)
                            <tr>
                                <td colspan="5">
                                    <div class="cc-section" style="margin: 8px 0 16px;">
                                        <div class="cc-section-title">
                                            {{ $editing_item_id ? 'Edit Item' : 'Add Item' }}
                                            — Session {{ $session->session_number }}: {{ $session->title }}
                                        </div>

                                        <div class="cc-grid-2">
                                            <div class="cc-field">
                                                <label class="cc-label">Item Type</label>
                                                <select wire:model="item_type" class="cc-select">
                                                    <option value="">-- Select Type --</option>
                                                    <option value="intro">Intro</option>
                                                    <option value="main_video">Main Video</option>
                                                    <option value="task">Task</option>
                                                    <option value="quiz">Quiz</option>
                                                    <option value="notes">Notes</option>
                                                </select>
                                                @error('item_type') <span class="cc-error">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="cc-field">
                                                <label class="cc-label">Title</label>
                                                <input type="text" wire:model="item_title" class="cc-input" placeholder="e.g. Introduction video">
                                                @error('item_title') <span class="cc-error">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="cc-field">
                                                <label class="cc-label">Resource Type</label>
                                                <select wire:model.live="resource_type" class="cc-select">
                                                    <option value="">-- Select --</option>
                                                    <option value="video">Video (upload file)</option>
                                                    <option value="pdf">PDF</option>
                                                    <option value="link">Link</option>
                                                </select>
                                                @error('resource_type') <span class="cc-error">{{ $message }}</span> @enderror
                                            </div>

                                            @if ($resource_type === 'video')
                                                <div class="cc-field">
                                                    <label class="cc-label">
                                                        Video File
                                                        @if ($editing_item_id)
                                                            <span style="font-weight:400; color:var(--text-muted);">(leave empty to keep current video)</span>
                                                        @endif
                                                    </label>
                                                    <input type="file" wire:model="video_file" accept="video/mp4,video/quicktime,video/webm,video/x-msvideo" class="cc-input">
                                                    <div wire:loading wire:target="video_file" class="cc-toggle-desc">Uploading preview…</div>
                                                    @if ($video_uploading)
                                                        <div class="cc-toggle-desc">Uploading to Cloudinary, please wait…</div>
                                                    @endif
                                                    @error('video_file') <span class="cc-error">{{ $message }}</span> @enderror
                                                    <div class="cc-toggle-desc" style="margin-top:6px;">
                                                        Stored privately — students get a short-lived streaming link, never a shareable URL.
                                                    </div>
                                                </div>
                                            @else
                                                <div class="cc-field">
                                                    <label class="cc-label">Resource URL</label>
                                                    <input type="url" wire:model="resource_url" class="cc-input" placeholder="https://...">
                                                    @error('resource_url') <span class="cc-error">{{ $message }}</span> @enderror
                                                </div>
                                            @endif
                                        </div>

                                        <div class="cc-field" style="margin-top:16px;">
                                            <label class="cc-label">Content / Notes</label>
                                            <textarea wire:model="content" class="cc-input" rows="3" placeholder="Optional text content for this item"></textarea>
                                            @error('content') <span class="cc-error">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="cc-grid-2" style="margin-top:16px;">
                                            <div class="cc-toggle-row">
                                                <div>
                                                    <div class="cc-toggle-label">Live Item</div>
                                                    <div class="cc-toggle-desc">Marks this item as a live Google Meet session</div>
                                                </div>
                                                <label class="cc-switch">
                                                    <input type="checkbox" wire:model="is_live">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="cc-field">
                                                <label class="cc-label">Live At</label>
                                                <input type="datetime-local" wire:model="live_at" class="cc-input">
                                                @error('live_at') <span class="cc-error">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="cc-actions">
                                            <button wire:click="saveItem" class="cc-btn cc-btn-primary">
                                                {{ $editing_item_id ? 'Update Item' : 'Add Item' }}
                                            </button>
                                            @if ($editing_item_id)
                                                <button wire:click="resetItemForm" class="cc-btn cc-btn-outline">Cancel</button>
                                            @endif
                                            <button wire:click="closeItemsPanel" class="cc-btn cc-btn-outline">Close</button>
                                        </div>
                                    </div>

                                    <div class="cc-table-wrap">
                                        <table class="cc-table">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Title</th>
                                                    <th>Resource</th>
                                                    <th>Live At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($items as $item)
                                                    <tr>
                                                        <td data-label="Type"><span class="cc-badge cc-badge-muted">{{ $item->item_type }}</span></td>
                                                        <td data-label="Title">{{ $item->title }}</td>
                                                        <td data-label="Resource">
                                                            {{ $item->resource_type ?? '—' }}
                                                            @if ($item->resource_type === 'video' && $item->hasPrivateCloudinaryAsset())
                                                                <span class="cc-badge cc-badge-accent" style="margin-left:6px;">private</span>
                                                            @endif
                                                        </td>
                                                        <td data-label="Live At">
                                                            @if ($item->is_live && $item->live_at)
                                                                <span class="cc-badge cc-badge-accent">{{ $item->live_at->format('d M, h:i A') }}</span>
                                                            @else
                                                                <span style="color:var(--text-muted); font-size:12.5px;">—</span>
                                                            @endif
                                                        </td>
                                                        <td data-label="Actions">
                                                            <div class="cc-table-actions">
                                                                <button wire:click="editItem({{ $item->id }})" class="cc-btn-link">Edit</button>
                                                                @if ($item->resource_type === 'video')
                                                                    <button wire:click="viewLogs({{ $item->id }})" class="cc-btn-link">
                                                                        {{ $viewing_logs_for_item_id === $item->id ? 'Hide Activity' : 'View Activity' }}
                                                                    </button>
                                                                @endif
                                                                <button wire:click="deleteItem({{ $item->id }})"
                                                                        wire:confirm="Delete this item?"
                                                                        class="cc-btn-link-danger">Delete</button>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    @if ($viewing_logs_for_item_id === $item->id)
                                                        <tr>
                                                            <td colspan="5">
                                                                <div class="cc-table-wrap" style="margin: 6px 0 14px;">
                                                                    <table class="cc-table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Student</th>
                                                                                <th>Event</th>
                                                                                <th>Details</th>
                                                                                <th>When</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse ($item_logs as $log)
                                                                                <tr>
                                                                                    <td data-label="Student">{{ optional($log->user)->name ?? 'Unknown' }}</td>
                                                                                    <td data-label="Event">
                                                                                        @if (in_array($log->event, \App\Models\VideoAccessLog::SUSPICIOUS_EVENTS))
                                                                                            <span class="cc-badge cc-badge-danger">{{ $log->event }}</span>
                                                                                        @else
                                                                                            <span class="cc-badge cc-badge-muted">{{ $log->event }}</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td data-label="Details">{{ $log->meta ? json_encode($log->meta) : '—' }}</td>
                                                                                    <td data-label="When">{{ $log->created_at->diffForHumans() }}</td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr><td colspan="4"><div class="cc-empty">No activity logged yet.</div></td></tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                    <div class="cc-toggle-desc" style="padding:8px 12px;">
                                                                        Note: these are behavioral signals (tab switching, blocked right-click, devtools, etc.), not proof of screen recording — no website can detect that.
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr><td colspan="5"><div class="cc-empty">No items added yet for this session.</div></td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="5"><div class="cc-empty">No sessions added yet for this week.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>