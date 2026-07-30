<div class="cc-wrap" >

    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">🎬</div>
            <div>
                <div class="cc-title">Session Manager</div>
                <div class="cc-subtitle">Manage sessions and their content, one week at a time</div>
            </div>
        </div>
    </div>

    {{-- ═══════════ HOW THIS WORKS — always visible at the top ═══════════ --}}
    <div class="cc-section" style="background:var(--primary-glow); border-color:var(--brand-primary);">
        <div style="display:flex; gap:12px; align-items:flex-start;">
            <div style="font-size:20px;">💡</div>
            <div>
                <div style="font-weight:700; color:var(--text); margin-bottom:6px; font-size:13.5px;">How this page works</div>
                <ol style="margin:0; padding-left:18px; font-size:12.5px; color:var(--text-muted); line-height:1.9;">
                    <li>Pick a <strong>Subject → Course → Week</strong> below.</li>
                    <li>Each <strong>Session</strong> shows as a card — click <strong>"+ Content"</strong> to expand it and add items (videos, tasks, quizzes, notes).</li>
                    <li>Mark a session <strong>"Required"</strong> if students must complete it to earn the certificate.</li>
                    <li>Add a <strong>Google Meet link + time</strong> to a session to turn it into a scheduled live class.</li>
                    <li>Videos upload privately to Cloudinary — students never get a shareable link, only secure streaming.</li>
                </ol>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="cc-alert cc-alert-success">✓ {{ session('success') }}</div>
    @endif

    {{-- ═══════════ STEP SELECTOR ═══════════ --}}
    <div class="cc-selector-row" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="cc-field">
            <label class="cc-label">1. Subject</label>
            <select wire:model.live="category_id" class="cc-select">
                <option value="">-- Select Subject --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="cc-field">
            <label class="cc-label">2. Course</label>
            <select wire:model.live="course_id" class="cc-select" @disabled(!$category_id)>
                <option value="">-- Select Course --</option>
                @foreach ($this->courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="cc-field">
            <label class="cc-label">3. Week</label>
            <select wire:model.live="week_id" class="cc-select" @disabled(!$course_id)>
                <option value="">-- Select Week --</option>
                @foreach ($weeks as $week)
                    <option value="{{ $week->id }}">Week {{ $week->week_number }} — {{ $week->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($week_id)

        <div style="display:flex; justify-content:space-between; align-items:center; margin:22px 0 14px;">
            <div style="font-size:15px; font-weight:700; color:var(--text);">Sessions in this week</div>
            @if (!$showSessionForm)
                <button wire:click="openSessionForm" class="cc-btn cc-btn-primary">+ Add Session</button>
            @endif
        </div>

        {{-- ═══════════ ADD/EDIT SESSION FORM (toggle) ═══════════ --}}
        @if ($showSessionForm)
            <div class="cc-section">
                <div class="cc-section-title">{{ $editing_session_id ? 'Edit Session' : 'New Session' }}</div>

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
                        <label class="cc-label">Google Meet Link (optional)</label>
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
                        {{ $editing_session_id ? 'Update Session' : 'Save Session' }}
                    </button>
                    <button wire:click="resetForm" class="cc-btn cc-btn-outline">Cancel</button>
                </div>
            </div>
        @endif

        {{-- ═══════════ SESSION CARDS (accordion) ═══════════ --}}
        <div style="display:flex; flex-direction:column; gap:14px;">
            @forelse ($sessions as $session)
                @php $isOpen = $active_session_id === $session->id; @endphp

                <div class="cc-section" style="margin-bottom:0; {{ $isOpen ? 'border-color:var(--brand-primary);' : '' }}">

                    {{-- Session summary row --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:var(--primary-glow); color:var(--brand-primary); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; flex-shrink:0;">
                                S{{ $session->session_number }}
                            </div>
                            <div>
                                <div style="font-weight:700; color:var(--text); font-size:14.5px;">{{ $session->title }}</div>
                                <div style="display:flex; gap:8px; margin-top:4px; flex-wrap:wrap;">
                                    @if (optional($session->settings)->is_required_for_certificate)
                                        <span class="cc-badge cc-badge-danger">Required</span>
                                    @else
                                        <span class="cc-badge cc-badge-muted">Optional</span>
                                    @endif
                                    @if (optional($session->settings)->meet_datetime)
                                        <span class="cc-badge cc-badge-accent">🎥 {{ $session->settings->meet_datetime->format('d M, h:i A') }}</span>
                                    @endif
                                    <span class="cc-badge cc-badge-muted">{{ $session->items->count() }} item{{ $session->items->count() === 1 ? '' : 's' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="cc-table-actions">
                            <button wire:click="editSession({{ $session->id }})" class="cc-btn-link">Edit</button>
                            <button wire:click="manageItems({{ $session->id }})" class="cc-btn cc-btn-outline" style="padding:6px 14px; font-size:12.5px;">
                                {{ $isOpen ? '▲ Hide Content' : '▼ + Content' }}
                            </button>
                            <button wire:click="deleteSession({{ $session->id }})" wire:confirm="Delete this session and all its content?" class="cc-btn-link-danger">Delete</button>
                        </div>
                    </div>

                    {{-- Items panel — expands below the session summary --}}
                    @if ($isOpen)
                        <div class="cc-divider"></div>

                        {{-- Add/Edit item form --}}
                        <div style="background:var(--bg-card); border:1px solid var(--line); border-radius:var(--radius-sm); padding:16px; margin-bottom:16px;">
                            <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:12px;">
                                {{ $editing_item_id ? '✏ Edit Item' : '+ Add New Item' }}
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
                                                <span style="font-weight:400; color:var(--text-muted); text-transform:none;">(leave empty to keep current video)</span>
                                            @endif
                                        </label>
                                        <input type="file" wire:model="video_file" accept="video/mp4,video/quicktime,video/webm,video/x-msvideo" class="cc-input">
                                        <div wire:loading wire:target="video_file" class="cc-hint">Uploading preview…</div>
                                        @if ($video_uploading)
                                            <div class="cc-hint">Uploading to Cloudinary, please wait…</div>
                                        @endif
                                        @error('video_file') <span class="cc-error">{{ $message }}</span> @enderror
                                        <div class="cc-hint">Stored privately — students get a secure streaming link, never a shareable URL.</div>
                                    </div>
                                @else
                                    <div class="cc-field">
                                        <label class="cc-label">Resource URL</label>
                                        <input type="url" wire:model="resource_url" class="cc-input" placeholder="https://...">
                                        @error('resource_url') <span class="cc-error">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="cc-field" style="margin-top:14px;">
                                <label class="cc-label">Content / Notes</label>
                                <textarea wire:model="content" class="cc-input" rows="2" placeholder="Optional text content for this item"></textarea>
                            </div>

                            <div class="cc-grid-2" style="margin-top:14px;">
                                <div class="cc-toggle-row">
                                    <div>
                                        <div class="cc-toggle-label">Live Item</div>
                                        <div class="cc-toggle-desc">Marks this as a live Meet session item</div>
                                    </div>
                                    <label class="cc-switch">
                                        <input type="checkbox" wire:model="is_live">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <div class="cc-field">
                                    <label class="cc-label">Live At</label>
                                    <input type="datetime-local" wire:model="live_at" class="cc-input">
                                </div>
                            </div>

                            <div class="cc-actions">
                                <button wire:click="saveItem" class="cc-btn cc-btn-primary">
                                    {{ $editing_item_id ? 'Update Item' : 'Add Item' }}
                                </button>
                                @if ($editing_item_id)
                                    <button wire:click="resetItemForm" class="cc-btn cc-btn-outline">Cancel Edit</button>
                                @endif
                            </div>
                        </div>

                        {{-- Item cards --}}
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @forelse ($items as $item)
                                @php
                                    $typeIcon = match($item->item_type) {
                                        'intro' => '🎬', 'main_video' => '▶️', 'task' => '📝', 'quiz' => '❓', 'notes' => '📄', default => '📦',
                                    };
                                @endphp
                                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; background:var(--bg-card2); border:1px solid var(--line); border-radius:var(--radius-xs); padding:12px 14px;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <span style="font-size:18px;">{{ $typeIcon }}</span>
                                        <div>
                                            <div style="font-weight:600; color:var(--text); font-size:13.5px;">{{ $item->title ?: ucfirst($item->item_type) }}</div>
                                            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                                                <span class="cc-badge cc-badge-muted">{{ $item->item_type }}</span>
                                                @if ($item->resource_type)
                                                    <span class="cc-badge cc-badge-muted">{{ $item->resource_type }}</span>
                                                @endif
                                                @if ($item->resource_type === 'video' && $item->hasPrivateCloudinaryAsset())
                                                    <span class="cc-badge cc-badge-accent">🔒 private video</span>
                                                @endif
                                                @if ($item->is_live && $item->live_at)
                                                    <span class="cc-badge cc-badge-accent">🔴 {{ $item->live_at->format('d M, h:i A') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cc-table-actions">
                                        <button wire:click="editItem({{ $item->id }})" class="cc-btn-link">Edit</button>
                                        <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this item?" class="cc-btn-link-danger">Delete</button>
                                    </div>
                                </div>
                            @empty
                                <div class="cc-empty">No content added to this session yet — use the form above to add your first item.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            @empty
                <div class="cc-empty">No sessions in this week yet — click "+ Add Session" above to create one.</div>
            @endforelse
        </div>
    @endif
</div>