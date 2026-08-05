<div class="cc-wrap" >
<style>
   /* ══════════════════════════════════════════════════════════
   COURSE CONTENT MODAL — full stylesheet
   Covers: overlay, modal shell, header, body, grid fields,
   inputs/selects/textarea, toggle switches, hints/errors,
   badges, footer buttons.
   ══════════════════════════════════════════════════════════ */

:root {
    --cc-primary: #4f46e5;
    --cc-primary-hover: #4338ca;
    --cc-border: #e2e8f0;
    --cc-bg: #ffffff;
    --cc-bg-soft: #f8fafc;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --cc-error: #dc2626;
    --cc-accent-bg: #eef2ff;
    --cc-accent-text: #4338ca;
    --radius-sm: 8px;
    --radius-xs: 6px;
}

/* ── Overlay ─────────────────────────────────────────── */
.cc-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 1000;
    animation: cc-fade-in 0.15s ease-out;
}

@keyframes cc-fade-in {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* ── Modal shell ─────────────────────────────────────── */
.cc-modal {
    background: var(--cc-bg);
    width: 100%;
    max-width: 640px;
    max-height: 90vh;
    border-radius: var(--radius-sm);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: cc-scale-in 0.18s ease-out;
}

@keyframes cc-scale-in {
    from { opacity: 0; transform: translateY(12px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ── Header ──────────────────────────────────────────── */
.cc-modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1px solid var(--cc-border);
    flex-shrink: 0;
}

.cc-modal-head h3 {
    margin: 0;
    font-size: 17px;
    font-weight: 600;
    color: var(--text-main);
}

.cc-modal-close {
    background: none;
    border: none;
    font-size: 16px;
    line-height: 1;
    color: var(--text-muted);
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s, color 0.15s;
}

.cc-modal-close:hover {
    background: var(--cc-bg-soft);
    color: var(--text-main);
}

/* ── Body ────────────────────────────────────────────── */
.cc-modal-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
}

/* ── Grid layout ─────────────────────────────────────── */
.cc-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

@media (max-width: 560px) {
    .cc-grid-2 {
        grid-template-columns: 1fr;
    }
}

/* ── Fields ──────────────────────────────────────────── */
.cc-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.cc-label {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.cc-input,
.cc-select,
textarea.cc-input {
    width: 100%;
    padding: 9px 12px;
    font-size: 14px;
    color: var(--text-main);
    background: var(--cc-bg);
    border: 1px solid var(--cc-border);
    border-radius: var(--radius-xs);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
    font-family: inherit;
}

.cc-input::placeholder {
    color: #94a3b8;
}

.cc-input:focus,
.cc-select:focus,
textarea.cc-input:focus {
    border-color: var(--cc-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

textarea.cc-input {
    resize: vertical;
    min-height: 60px;
}

input[type="file"].cc-input {
    padding: 7px 10px;
    cursor: pointer;
}

.cc-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 32px;
}

/* ── Error / hint text ───────────────────────────────── */
.cc-error {
    font-size: 12px;
    color: var(--cc-error);
    margin-top: 2px;
}

.cc-hint {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 6px;
}

/* ── Toggle row / switch ─────────────────────────────── */
.cc-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 14px;
    background: var(--cc-bg-soft);
    border: 1px solid var(--cc-border);
    border-radius: var(--radius-xs);
}

.cc-toggle-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-main);
}

.cc-toggle-desc {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 2px;
}

.cc-switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 22px;
    flex-shrink: 0;
}

.cc-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.cc-switch .slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #cbd5e1;
    border-radius: 999px;
    transition: background-color 0.15s;
}

.cc-switch .slider::before {
    content: "";
    position: absolute;
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: #fff;
    border-radius: 50%;
    transition: transform 0.15s;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.cc-switch input:checked + .slider {
    background-color: var(--cc-primary);
}

.cc-switch input:checked + .slider::before {
    transform: translateX(18px);
}

.cc-switch input:focus-visible + .slider {
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);
}

/* ── Badges ──────────────────────────────────────────── */
.cc-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 999px;
    background: var(--cc-bg-soft);
    color: var(--text-main);
}

.cc-badge-accent {
    background: var(--cc-accent-bg);
    color: var(--cc-accent-text);
}

/* ── Footer / buttons ────────────────────────────────── */
.cc-modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 24px;
    border-top: 1px solid var(--cc-border);
    flex-shrink: 0;
    background: var(--cc-bg);
}

.cc-btn {
    padding: 9px 18px;
    font-size: 14px;
    font-weight: 600;
    border-radius: var(--radius-xs);
    border: 1px solid transparent;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, opacity 0.15s;
}

.cc-btn-primary {
    background: var(--cc-primary);
    color: #fff;
}

.cc-btn-primary:hover {
    background: var(--cc-primary-hover);
}

.cc-btn-primary[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

.cc-btn-outline {
    background: transparent;
    color: var(--text-main);
    border-color: var(--cc-border);
}

.cc-btn-outline:hover {
    background: var(--cc-bg-soft);
}

/* ── Livewire loading state on file inputs ──────────────── */
[wire\:loading] {
    font-style: italic;
}
</style>
    <div class="cc-header">
        <div class="cc-header-left">
            <div class="cc-icon">🎬</div>
            <div>
                <div class="cc-title">Session Manager</div>
                <div class="cc-subtitle">Manage sessions and their content, one week at a time</div>
            </div>
        </div>
    </div>

    <div class="cc-section" style="background:var(--primary-glow); border-color:var(--brand-primary);">
        <div style="display:flex; gap:12px; align-items:flex-start;">
            <div style="font-size:20px;">💡</div>
            <div>
                <div style="font-weight:700; color:var(--text); margin-bottom:6px; font-size:13.5px;">How this page works</div>
                <ol style="margin:0; padding-left:18px; font-size:12.5px; color:var(--text-muted); line-height:1.9;">
                    <li>Pick a <strong>Subject → Course → Week</strong> below. Crash Courses don't appear here — they auto-sync from their parent course.</li>
                    <li>Each <strong>Session</strong> shows as a card — click <strong>"+ Content"</strong> to open its items.</li>
                    <li><strong>Intro</strong> and <strong>Main Video</strong> items always need a video, PPT, or PDF attached — the form won't save without one.</li>
                    <li>The item form stays open after saving so you can add several items in a row without reopening it.</li>
                    <li>Files are stored securely in your storage folder — students stream/view them, never a raw download link.</li>
                </ol>
            </div>
        </div>
    </div>

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
            <span class="cc-hint">Crash Courses are hidden — they auto-sync from Basic / Professional-Beginner courses.</span>
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
            <button type="button" wire:click="openSessionModal" class="cc-btn cc-btn-primary">+ Add Session</button>
        </div>

        {{-- ═══════════ SESSION CARDS ═══════════ --}}
        <div style="display:flex; flex-direction:column; gap:14px;">
            @forelse ($sessions as $session)
                @php $isOpen = $active_session_id === $session->id; @endphp

                <div class="cc-section" style="margin-bottom:0; {{ $isOpen ? 'border-color:var(--brand-primary);' : '' }}">
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
                            <button type="button" wire:click="editSession({{ $session->id }})" class="cc-btn-link">Edit</button>
                            <button type="button" wire:click="manageItems({{ $session->id }})" class="cc-btn cc-btn-outline" style="padding:6px 14px; font-size:12.5px;">
                                {{ $isOpen ? '▲ Hide Content' : '▼ + Content' }}
                            </button>
                            <button type="button"
                                x-on:click="
                                    Swal.fire({
                                        title: 'Delete this session?',
                                        text: 'All its items will be deleted too. This cannot be undone.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, delete',
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#5a718a',
                                    }).then((r) => { if (r.isConfirmed) $wire.deleteSession({{ $session->id }}) })
                                "
                                class="cc-btn-link-danger">Delete</button>
                        </div>
                    </div>

                    @if ($isOpen)
                        <div class="cc-divider"></div>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <div style="font-size:13px; font-weight:700; color:var(--text);">Session content</div>
                            <button type="button" wire:click="openItemModal" class="cc-btn cc-btn-primary" style="padding:7px 16px; font-size:12.5px;">+ Add Item</button>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @forelse ($items as $item)
                                @php
                                    $typeIcon = match($item->item_type) {
                                        'intro' => '🎬', 'main_video' => '▶️', 'task' => '📝', 'quiz' => '❓', 'notes' => '📄', default => '📦',
                                    };
                                @endphp
                                <div style="background:var(--bg-card2); border:1px solid var(--line); border-radius:var(--radius-xs); padding:12px 14px;">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <span style="font-size:18px;">{{ $typeIcon }}</span>
                                            <div>
                                                <div style="font-weight:600; color:var(--text); font-size:13.5px;">{{ $item->title ?: ucfirst($item->item_type) }}</div>
                                                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px; display:flex; gap:6px; flex-wrap:wrap;">
                                                    <span class="cc-badge cc-badge-muted">{{ $item->item_type }}</span>
                                                    @if ($item->resource_type)
                                                        <span class="cc-badge cc-badge-muted">{{ $item->resource_type }}</span>
                                                    @endif
                                                    @if ($item->is_live && $item->live_at)
                                                        <span class="cc-badge cc-badge-accent">🔴 {{ $item->live_at->format('d M, h:i A') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cc-table-actions">
                                            <button type="button" wire:click="editItem({{ $item->id }})" class="cc-btn-link">Edit</button>
                                            <button type="button"
                                                x-on:click="
                                                    Swal.fire({
                                                        title: 'Delete this item?',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonText: 'Yes, delete',
                                                        confirmButtonColor: '#dc2626',
                                                    }).then((r) => { if (r.isConfirmed) $wire.deleteItem({{ $item->id }}) })
                                                "
                                                class="cc-btn-link-danger">Delete</button>
                                        </div>
                                    </div>

                                    {{-- Preview --}}
                                    @if ($item->resource_type === 'video' && $item->resource_url)
                                        <video controls style="width:100%; max-width:420px; border-radius:var(--radius-xs); margin-top:10px;">
                                            <source src="{{ $item->resource_url }}">
                                        </video>
                                    @elseif ($item->resource_type === 'document' && $item->resource_url)
                                        <a href="{{ $item->resource_url }}" target="_blank" class="cc-btn cc-btn-outline" style="margin-top:10px; padding:6px 14px; font-size:12.5px;">
                                            📄 View Document
                                        </a>
                                    @elseif ($item->resource_type === 'link' && $item->resource_url)
                                        <a href="{{ $item->resource_url }}" target="_blank" class="cc-btn cc-btn-outline" style="margin-top:10px; padding:6px 14px; font-size:12.5px;">
                                            🔗 Open Link
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <div class="cc-empty">No content added to this session yet — click "+ Add Item" above.</div>
                            @endforelse
                        </div>
                    @endif
                </div>
            @empty
                <div class="cc-empty">No sessions in this week yet — click "+ Add Session" above to create one.</div>
            @endforelse
        </div>
    @endif

    {{-- ═══════════ SESSION MODAL ═══════════ --}}
    @if ($showSessionModal)
        <div class="cc-modal-overlay">
            <div class="cc-modal">
                <div class="cc-modal-head">
                    <h3>{{ $editing_session_id ? 'Edit Session' : 'New Session' }}</h3>
                    <button type="button" wire:click="closeSessionModal" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
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
                </div>
                <div class="cc-modal-footer">
                    <button type="button" wire:click="closeSessionModal" class="cc-btn cc-btn-outline">Cancel</button>
                    <button type="button" wire:click="saveSession" class="cc-btn cc-btn-primary">
                        {{ $editing_session_id ? 'Update Session' : 'Save Session' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════ ITEM MODAL — stays open after save ═══════════ --}}
    @if ($showItemModal)
        <div class="cc-modal-overlay">
            <div class="cc-modal">
                <div class="cc-modal-head">
                    <h3>{{ $editing_item_id ? 'Edit Item' : 'Add Item' }}</h3>
                    <button type="button" wire:click="closeItemModal" class="cc-modal-close">✕</button>
                </div>
                <div class="cc-modal-body">
                    <div class="cc-grid-2">
                        <div class="cc-field">
                            <label class="cc-label">Item Type</label>
                            <select wire:model="item_type" class="cc-select">
                                <option value="">-- Select Type --</option>
                                <option value="intro">Intro (needs video/PPT)</option>
                                <option value="main_video">Main Video (needs video/PPT)</option>
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
                        <div class="cc-field" style="grid-column: span 2;">
                            <label class="cc-label">Resource Type</label>
                            <select wire:model.live="resource_type" class="cc-select">
                                <option value="">-- None --</option>
                                <option value="video">Video (upload file)</option>
                                <option value="document">PPT / PDF (upload file)</option>
                                <option value="link">External Link</option>
                            </select>
                            @error('resource_type') <span class="cc-error">{{ $message }}</span> @enderror
                        </div>

                        @if ($resource_type === 'video')
                            <div class="cc-field" style="grid-column: span 2;">
                                <label class="cc-label">
                                    Video File
                                    @if ($editing_item_id)<span style="font-weight:400; color:var(--text-muted); text-transform:none;">(leave empty to keep current video)</span>@endif
                                </label>
                                <input type="file" wire:model="video_file" accept="video/mp4,video/quicktime,video/webm,video/x-msvideo" class="cc-input">
                                <div wire:loading wire:target="video_file" class="cc-hint">Uploading preview…</div>
                                @if ($video_file)
                                    <video controls style="width:100%; max-width:320px; border-radius:var(--radius-xs); margin-top:8px;">
                                        <source src="{{ $video_file->temporaryUrl() }}">
                                    </video>
                                @endif
                                @error('video_file') <span class="cc-error">{{ $message }}</span> @enderror
                                <div class="cc-hint">Stored in your storage folder — students get a secure streaming link.</div>
                            </div>
                        @elseif ($resource_type === 'document')
                            <div class="cc-field" style="grid-column: span 2;">
                                <label class="cc-label">
                                    PPT / PDF File
                                    @if ($editing_item_id)<span style="font-weight:400; color:var(--text-muted); text-transform:none;">(leave empty to keep current file)</span>@endif
                                </label>
                                <input type="file" wire:model="doc_file" accept=".pdf,.ppt,.pptx" class="cc-input">
                                <div wire:loading wire:target="doc_file" class="cc-hint">Uploading…</div>
                                @if ($doc_file)
                                    <div class="cc-badge cc-badge-accent" style="margin-top:8px;">📄 {{ $doc_file->getClientOriginalName() }}</div>
                                @endif
                                @error('doc_file') <span class="cc-error">{{ $message }}</span> @enderror
                            </div>
                        @elseif ($resource_type === 'link')
                            <div class="cc-field" style="grid-column: span 2;">
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
                </div>
                <div class="cc-modal-footer">
                    <button type="button" wire:click="closeItemModal" class="cc-btn cc-btn-outline">Close</button>
                    <button type="button" wire:click="saveItem" class="cc-btn cc-btn-primary" wire:loading.attr="disabled" wire:target="saveItem,video_file,doc_file">
                        {{ $editing_item_id ? 'Update Item' : 'Add Item (stays open)' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endassets

@script
<script>
    $wire.on('session-saved', (p) => {
        const wasEditing = (Array.isArray(p) ? p[0] : p)?.wasEditing;
        Swal.fire({  icon: 'success', title: wasEditing ? 'Session updated' : 'Session created', showConfirmButton: false, timer: 2000 });
    });
    $wire.on('session-deleted', () => {
        Swal.fire({  icon: 'success', title: 'Session deleted', showConfirmButton: false, timer: 2000 });
    });
    $wire.on('item-saved', (p) => {
        const wasEditing = (Array.isArray(p) ? p[0] : p)?.wasEditing;
        Swal.fire({ icon: 'success', title: wasEditing ? 'Item updated' : 'Item added — add another or close', showConfirmButton: false, timer: 2200 });
    });
    $wire.on('item-deleted', () => {
        Swal.fire({  icon: 'success', title: 'Item deleted', showConfirmButton: false, timer: 2000 });
    });

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Please fix the following',
            html: `<ul style="text-align:left; margin:0; padding-left:18px; font-size:13px;">
                {!! collect($errors->all())->map(fn($e) => "<li>" . e($e) . "</li>")->implode('') !!}
            </ul>`,
            confirmButtonColor: '#0947a8',
        });
    @endif
</script>
@endscript