<div>
    <style>
        /* ═══════════════════════════════════════════════
           COURSE MANAGER — SCOPED THEME-BASED STYLES
           Prefixed `course-` so classes never collide.
        ═══════════════════════════════════════════════ */
        .course-card {
            background: var(--card); border: 1px solid var(--line);
            border-radius: var(--radius, 16px); box-shadow: var(--shadow-card);
        }
        .course-head {
            display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between;
            gap: 14px; padding: 18px 20px; border-bottom: 1px solid var(--line);
        }
        .course-head h1 { font-size: 21px; color: var(--text); margin-bottom: 4px; }
        .course-head .course-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
        .course-pill {
            font-size: 12px; font-weight: 700; color: var(--muted); background: var(--bg-card2, var(--bg2));
            border: 1px solid var(--line); border-radius: 999px; padding: 4px 12px;
        }
        .course-price { font-size: 15px; font-weight: 800; color: var(--text); }
        .course-price .strike { color: var(--muted); font-weight: 500; text-decoration: line-through; margin-right: 6px; font-size: 13px; }

        .course-section-title {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 20px; border-bottom: 1px solid var(--line); background: var(--bg-card2, var(--bg2));
        }
        .course-section-title h2 { font-size: 16px; font-weight: 700; color: var(--text); }

        .course-settings-grid {
            display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; padding: 18px 20px;
        }
        @media (max-width: 800px) { .course-settings-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 520px) { .course-settings-grid { grid-template-columns: 1fr; } }
        .course-settings-stat { display: flex; flex-direction: column; gap: 4px; }
        .course-settings-stat .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); }
        .course-settings-stat .value { font-size: 14px; font-weight: 700; color: var(--text); }

        /* ── Accordion: weeks / sessions / items ──────────────── */
        .course-week { border-bottom: 1px solid var(--line); }
        .course-week:last-child { border-bottom: none; }
        .course-week-row {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 14px 20px; cursor: pointer;
        }
        .course-week-row:hover { background: var(--primary-glow, rgba(13,93,209,.05)); }
        .course-week-title { display: flex; align-items: center; gap: 10px; }
        .course-week-title .caret { transition: transform 160ms ease; color: var(--muted); font-size: 12px; }
        .course-week-title .caret.open { transform: rotate(90deg); }
        .course-week-title b { color: var(--text); font-size: 14.5px; }
        .course-week-badge {
            font-size: 11.5px; font-weight: 700; color: var(--primary); background: var(--primary-glow);
            border-radius: 999px; padding: 3px 10px;
        }
        .course-week-body { padding: 0 20px 16px 40px; display: none; }
        .course-week-body.open { display: block; }

        .course-session {
            border: 1px solid var(--line); border-radius: var(--radius-sm, 12px); margin-top: 10px; overflow: hidden;
        }
        .course-session-row {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 10px 14px; background: var(--bg-card2, var(--bg2)); cursor: pointer;
        }
        .course-session-title { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: var(--text); }
        .course-session-body { padding: 12px 14px; display: none; }
        .course-session-body.open { display: block; }
        .course-badge-req { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; background: rgba(217,119,6,.12); color: var(--warning); }
        .course-badge-hidden { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; background: rgba(220,38,38,.1); color: var(--danger); }

        .course-item-row {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            padding: 9px 10px; border-radius: var(--radius-xs, 8px);
        }
        .course-item-row:hover { background: var(--primary-glow, rgba(13,93,209,.05)); }
        .course-item-type {
            font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .4px;
            padding: 2px 8px; border-radius: 6px; background: var(--bg-card2, var(--bg2)); color: var(--muted);
        }
        .course-item-info { display: flex; align-items: center; gap: 10px; min-width: 0; }
        .course-item-info span.title { font-size: 13.5px; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .course-row-actions { display: flex; flex-wrap: wrap; gap: 6px; }
        .course-btn-mini {
            border: 1px solid var(--line); border-radius: var(--radius-xs, 8px); background: var(--card);
            color: var(--text); padding: 5px 10px; font-size: 12.5px; font-weight: 600; cursor: pointer;
            display: inline-flex; align-items: center; gap: 5px; transition: 150ms ease;
        }
        .course-btn-mini:hover { border-color: var(--primary); background: var(--primary-glow); }
        .course-btn-mini.danger { color: var(--danger); }
        .course-btn-mini.danger:hover { border-color: var(--danger); background: rgba(220,38,38,.08); }
        .course-btn-mini.preview { color: var(--success); }
        .course-btn-mini.preview:hover { border-color: var(--success); background: rgba(22,163,74,.08); }

        .course-empty { padding: 14px; color: var(--muted); font-size: 13px; text-align: center; }

        /* ── Modal ─────────────────────────────────────────────── */
        [x-cloak] { display: none !important; }
        .course-modal-overlay {
            position: fixed; inset: 0; background: rgba(8,15,28,.56); backdrop-filter: blur(3px);
            display: flex; align-items: center; justify-content: center; padding: 18px; z-index: 130;
        }
        .course-modal {
            width: min(640px, 100%); max-height: calc(100vh - 36px); overflow: auto;
            border-radius: var(--radius, 16px); border: 1px solid var(--line); background: var(--card); box-shadow: var(--shadow);
        }
        .course-modal.wide { width: min(820px, 100%); }
        .course-modal-head { display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border-bottom: 1px solid var(--line); }
        .course-modal-head h3 { font-size: 19px; color: var(--text); }
        .course-modal-close { border: 0; background: transparent; color: var(--muted); font-size: 26px; line-height: 1; cursor: pointer; }
        .course-modal-close:hover { color: var(--danger); }
        .course-modal-body { padding: 16px; }
        .course-modal-footer { display: flex; justify-content: flex-end; border-top: 1px solid var(--line); padding: 12px 16px; gap: 8px; }

        .course-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .course-field label { font-size: 13px; font-weight: 600; color: var(--muted); }
        .course-field label .req { color: var(--danger); }
        .course-field input, .course-field select, .course-field textarea {
            border: 1px solid var(--input-border, var(--line)); background: var(--input-bg, var(--card));
            color: var(--text); border-radius: var(--radius-xs, 8px); padding: 10px 12px; font-size: 14px;
        }
        .course-field textarea { resize: vertical; min-height: 80px; }
        .course-field.is-invalid input, .course-field.is-invalid select, .course-field.is-invalid textarea { border-color: var(--danger); }
        .course-error-text { font-size: 12px; color: var(--danger); }
        .course-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .course-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
        @media (max-width: 520px) { .course-grid-2, .course-grid-3 { grid-template-columns: 1fr; } }
        .course-toggle-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .course-toggle-row label { font-size: 13px; font-weight: 600; color: var(--text); }

        .course-video-wrap { width: 100%; border-radius: var(--radius-sm, 12px); overflow: hidden; background: #000; }
        .course-video-wrap video { width: 100%; max-height: 70vh; display: block; }

        /* ── Upload vs URL tabs ─────────────────────────────────── */
        .course-source-tabs { display: flex; gap: 8px; }
        .course-tab-btn {
            flex: 1; border: 1px solid var(--line); background: var(--input-bg, var(--card));
            color: var(--muted); padding: 8px 10px; border-radius: var(--radius-xs, 8px);
            font-size: 13px; font-weight: 700; cursor: pointer; transition: 150ms ease;
        }
        .course-tab-btn.active { border-color: var(--primary); color: var(--primary); background: var(--primary-glow); }
        .course-tab-btn:hover { border-color: var(--primary); }

        .course-progress-track { width: 100%; height: 6px; border-radius: 999px; background: var(--bg-card2, var(--bg2)); overflow: hidden; margin-top: 8px; }
        .course-progress-fill { height: 100%; background: var(--primary); border-radius: 999px; transition: width 120ms linear; }

        .course-file-chip {
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
            margin-top: 8px; padding: 8px 12px; border: 1px solid var(--line); border-radius: var(--radius-xs, 8px);
            background: var(--bg-card2, var(--bg2)); font-size: 13px; color: var(--text);
        }
    </style>

    {{-- Course header --}}
    <section class="course-card" style="margin-bottom:14px;">
        <div class="course-head">
            <div>
                <h1>{{ $course->title }}</h1>
                <div class="course-meta">
                    <span class="course-pill">{{ $course->category?->name }}</span>
                    <span class="course-pill">{{ $course->courseType?->name }}</span>
                    <span class="course-pill">{{ $course->courseLevel?->name }}</span>
                    <span class="course-pill">{{ $course->language }}</span>
                </div>
                <div style="margin-top:10px;" class="course-price">
                    @if($course->original_price > $course->price)
                        <span class="strike">₹{{ number_format($course->original_price) }}</span>
                    @endif
                    ₹{{ number_format($course->price) }}
                </div>
            </div>
            <button type="button" class="btn btn-soft" wire:click="openCourseInfoModal">Edit Course Info</button>
        </div>
    </section>

    {{-- Course settings summary --}}
    <section class="course-card" style="margin-bottom:14px;">
        <div class="course-section-title">
            <h2>Enrollment Settings</h2>
            <button type="button" class="btn btn-soft" wire:click="openCourseSettingsModal">Edit Settings</button>
        </div>
        <div class="course-settings-grid">
            <div class="course-settings-stat">
                <span class="label">Min. Completion</span>
                <span class="value">{{ $course->settings?->min_completion_percent ?? '—' }}%</span>
            </div>
            <div class="course-settings-stat">
                <span class="label">Weekly Unlock Mode</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $course->settings?->weekly_unlock_mode ?? '—')) }}</span>
            </div>
            <div class="course-settings-stat">
                <span class="label">Certificate Mode</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $course->settings?->certificate_mode ?? '—')) }}</span>
            </div>
            <div class="course-settings-stat">
                <span class="label">Seats Shown Full</span>
                <span class="value">{{ $course->settings?->show_seats_as_full ? 'Yes' : 'No' }}</span>
            </div>
            <div class="course-settings-stat">
                <span class="label">Zero-Day Countdown</span>
                <span class="value">{{ $course->settings?->zero_day_countdown_enabled ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="course-settings-stat">
                <span class="label">Countdown Days</span>
                <span class="value">{{ $course->settings?->countdown_days ?? 0 }}</span>
            </div>
        </div>
    </section>

    {{-- Weeks / Sessions / Items --}}
    <section class="course-card">
        <div class="course-section-title">
            <h2>Weeks, Sessions &amp; Content</h2>
            <button type="button" class="btn btn-soft" wire:click="openWeekModal">+ Add Week</button>
        </div>

        @forelse ($course->weeks as $week)
            <div class="course-week" wire:key="week-{{ $week->id }}">
                <div class="course-week-row" wire:click="toggleWeek({{ $week->id }})">
                    <div class="course-week-title">
                        <span class="caret {{ in_array($week->id, $expandedWeeks) ? 'open' : '' }}">▶</span>
                        <span class="course-week-badge">Week {{ $week->week_number }}</span>
                        <b>{{ $week->title }}</b>
                        <span style="color:var(--muted); font-size:12.5px;">({{ $week->sessions->count() }} sessions)</span>
                    </div>
                    <div class="course-row-actions" onclick="event.stopPropagation()">
                        <button type="button" class="course-btn-mini" wire:click="openSessionModal({{ $week->id }})">+ Session</button>
                        <button type="button" class="course-btn-mini" wire:click="openWeekModal({{ $week->id }})">Edit</button>
                        <button type="button" class="course-btn-mini danger" wire:click="confirmDeleteWeek({{ $week->id }})">Delete</button>
                    </div>
                </div>

                <div class="course-week-body {{ in_array($week->id, $expandedWeeks) ? 'open' : '' }}">
                    @forelse ($week->sessions as $session)
                        <div class="course-session" wire:key="session-{{ $session->id }}">
                            <div class="course-session-row" wire:click="toggleSession({{ $session->id }})">
                                <div class="course-session-title">
                                    <span class="caret {{ in_array($session->id, $expandedSessions) ? 'open' : '' }}">▶</span>
                                    Session {{ $session->session_number }} — {{ $session->title }}
                                    @if($session->setting?->is_required_for_certificate)
                                        <span class="course-badge-req">Required</span>
                                    @endif
                                    @if($session->setting && ! $session->setting->is_visible)
                                        <span class="course-badge-hidden">Hidden</span>
                                    @endif
                                </div>
                                <div class="course-row-actions" onclick="event.stopPropagation()">
                                    <button type="button" class="course-btn-mini" wire:click="openItemModal({{ $session->id }})">+ Item</button>
                                    <button type="button" class="course-btn-mini" wire:click="openSessionSettingsModal({{ $session->id }})">Settings</button>
                                    <button type="button" class="course-btn-mini" wire:click="openSessionModal({{ $week->id }}, {{ $session->id }})">Edit</button>
                                    <button type="button" class="course-btn-mini danger" wire:click="confirmDeleteSession({{ $session->id }})">Delete</button>
                                </div>
                            </div>

                            <div class="course-session-body {{ in_array($session->id, $expandedSessions) ? 'open' : '' }}">
                                @forelse ($session->items as $item)
                                    <div class="course-item-row" wire:key="item-{{ $item->id }}">
                                        <div class="course-item-info">
                                            <span class="course-item-type">{{ $itemTypes[$item->item_type] ?? $item->item_type }}</span>
                                            <span class="title">{{ $item->title }}</span>
                                        </div>
                                        <div class="course-row-actions">
                                            @if(in_array($item->resource_type, ['video', 'video_or_ppt']) && $item->resource_url)
                                                <button type="button" class="course-btn-mini preview"
                                                        wire:click="openVideoPreview('{{ $item->resource_url }}', '{{ addslashes($item->title) }}')">▶ Preview</button>
                                            @endif
                                            <button type="button" class="course-btn-mini" wire:click="openItemModal({{ $session->id }}, {{ $item->id }})">Edit</button>
                                            <button type="button" class="course-btn-mini danger" wire:click="confirmDeleteItem({{ $item->id }})">Delete</button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="course-empty">No items in this session yet.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="course-empty">No sessions in this week yet.</div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="course-empty">No weeks added yet. Click "Add Week" to get started.</div>
        @endforelse
    </section>

    {{-- ═══════════ MODALS ═══════════ --}}

    {{-- Course Info --}}
    @if ($activeModal === 'course-info')
        <div class="course-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="course-modal wide">
                <div class="course-modal-head">
                    <h3>Edit Course Info</h3>
                    <button type="button" class="course-modal-close" wire:click="closeModal">x</button>
                </div>
                <form wire:submit.prevent="saveCourseInfo">
                    <div class="course-modal-body">
                        <div class="course-grid-3">
                            <div class="course-field @error('category_id') is-invalid @enderror">
                                <label>Category <span class="req">*</span></label>
                                <select wire:model="category_id">
                                    <option value="">Select</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('course_type_id') is-invalid @enderror">
                                <label>Type <span class="req">*</span></label>
                                <select wire:model="course_type_id">
                                    <option value="">Select</option>
                                    @foreach($courseTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_type_id') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('course_level_id') is-invalid @enderror">
                                <label>Level <span class="req">*</span></label>
                                <select wire:model="course_level_id">
                                    <option value="">Select</option>
                                    @foreach($courseLevels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_level_id') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="course-field @error('title') is-invalid @enderror">
                            <label>Title <span class="req">*</span></label>
                            <input type="text" wire:model="title">
                            @error('title') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="course-field @error('short_description') is-invalid @enderror">
                            <label>Short Description <span class="req">*</span></label>
                            <textarea wire:model="short_description" rows="2"></textarea>
                            @error('short_description') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="course-field @error('description') is-invalid @enderror">
                            <label>Full Description <span class="req">*</span></label>
                            <textarea wire:model="description" rows="4"></textarea>
                            @error('description') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="course-grid-3">
                            <div class="course-field @error('original_price') is-invalid @enderror">
                                <label>Original Price <span class="req">*</span></label>
                                <input type="number" step="0.01" wire:model="original_price">
                                @error('original_price') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('price') is-invalid @enderror">
                                <label>Selling Price <span class="req">*</span></label>
                                <input type="number" step="0.01" wire:model="price">
                                @error('price') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('gst') is-invalid @enderror">
                                <label>GST %</label>
                                <input type="text" wire:model="gst">
                                @error('gst') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="course-grid-3">
                            <div class="course-field @error('language') is-invalid @enderror">
                                <label>Language <span class="req">*</span></label>
                                <input type="text" wire:model="language">
                                @error('language') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('duration_hours') is-invalid @enderror">
                                <label>Duration (hours)</label>
                                <input type="number" wire:model="duration_hours">
                                @error('duration_hours') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('thumbnail') is-invalid @enderror">
                                <label>Thumbnail Path</label>
                                <input type="text" wire:model="thumbnail" placeholder="images/course-1.png">
                                @error('thumbnail') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="course-modal-footer">
                        <button type="button" class="btn btn-soft" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn" wire:loading.attr="disabled" wire:target="saveCourseInfo">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Course Settings --}}
    @if ($activeModal === 'course-settings')
        <div class="course-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="course-modal">
                <div class="course-modal-head">
                    <h3>Edit Enrollment Settings</h3>
                    <button type="button" class="course-modal-close" wire:click="closeModal">x</button>
                </div>
                <form wire:submit.prevent="saveCourseSettings">
                    <div class="course-modal-body">
                        <div class="course-grid-2">
                            <div class="course-field @error('min_completion_percent') is-invalid @enderror">
                                <label>Min. Completion % <span class="req">*</span></label>
                                <input type="number" min="0" max="100" wire:model="min_completion_percent">
                                @error('min_completion_percent') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('countdown_days') is-invalid @enderror">
                                <label>Countdown Days</label>
                                <input type="number" min="0" wire:model="countdown_days">
                                @error('countdown_days') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="course-field @error('weekly_unlock_mode') is-invalid @enderror">
                            <label>Weekly Unlock Mode <span class="req">*</span></label>
                            <select wire:model="weekly_unlock_mode">
                                <option value="sequential_all_weeks">Sequential — all weeks</option>
                                <option value="week1_gate_only">Week 1 gate only</option>
                                <option value="free_no_lock">Free — no lock</option>
                            </select>
                            @error('weekly_unlock_mode') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="course-field @error('certificate_mode') is-invalid @enderror">
                            <label>Certificate Mode <span class="req">*</span></label>
                            <select wire:model="certificate_mode">
                                <option value="both">Both</option>
                                <option value="per_level">Per Level</option>
                                <option value="end_of_course">End of Course</option>
                            </select>
                            @error('certificate_mode') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="course-toggle-row">
                            <input type="checkbox" wire:model="show_seats_as_full" id="show_seats_as_full">
                            <label for="show_seats_as_full">Always show seats as full</label>
                        </div>
                        <div class="course-toggle-row">
                            <input type="checkbox" wire:model="zero_day_countdown_enabled" id="zero_day_countdown_enabled">
                            <label for="zero_day_countdown_enabled">Enable zero-day countdown</label>
                        </div>
                    </div>
                    <div class="course-modal-footer">
                        <button type="button" class="btn btn-soft" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn" wire:loading.attr="disabled" wire:target="saveCourseSettings">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Week --}}
    @if ($activeModal === 'week')
        <div class="course-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="course-modal" style="width:min(420px,100%)">
                <div class="course-modal-head">
                    <h3>{{ $editWeekId ? 'Edit Week' : 'Add Week' }}</h3>
                    <button type="button" class="course-modal-close" wire:click="closeModal">x</button>
                </div>
                <form wire:submit.prevent="saveWeek">
                    <div class="course-modal-body">
                        <div class="course-field @error('week_number') is-invalid @enderror">
                            <label>Week Number <span class="req">*</span></label>
                            <input type="number" min="1" wire:model="week_number">
                            @error('week_number') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="course-field @error('week_title') is-invalid @enderror">
                            <label>Title <span class="req">*</span></label>
                            <input type="text" wire:model="week_title" placeholder="Week 1 - Learning Module">
                            @error('week_title') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="course-modal-footer">
                        <button type="button" class="btn btn-soft" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn">Save Week</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Session --}}
    @if ($activeModal === 'session')
        <div class="course-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="course-modal" style="width:min(420px,100%)">
                <div class="course-modal-head">
                    <h3>{{ $editSessionId ? 'Edit Session' : 'Add Session' }}</h3>
                    <button type="button" class="course-modal-close" wire:click="closeModal">x</button>
                </div>
                <form wire:submit.prevent="saveSession">
                    <div class="course-modal-body">
                        <div class="course-field @error('session_number') is-invalid @enderror">
                            <label>Session Number <span class="req">*</span></label>
                            <input type="number" min="1" wire:model="session_number">
                            @error('session_number') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="course-field @error('session_title') is-invalid @enderror">
                            <label>Title <span class="req">*</span></label>
                            <input type="text" wire:model="session_title" placeholder="Session 1">
                            @error('session_title') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="course-modal-footer">
                        <button type="button" class="btn btn-soft" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn">Save Session</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Session Settings --}}
    @if ($activeModal === 'session-settings')
        <div class="course-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="course-modal">
                <div class="course-modal-head">
                    <h3>Session Settings</h3>
                    <button type="button" class="course-modal-close" wire:click="closeModal">x</button>
                </div>
                <form wire:submit.prevent="saveSessionSettings">
                    <div class="course-modal-body">
                        <div class="course-field @error('meet_link') is-invalid @enderror">
                            <label>Meet Link</label>
                            <input type="url" wire:model="meet_link" placeholder="https://meet.google.com/...">
                            @error('meet_link') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="course-field @error('meet_datetime') is-invalid @enderror">
                            <label>Meet Date &amp; Time</label>
                            <input type="datetime-local" wire:model="meet_datetime">
                            @error('meet_datetime') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>
                        <div class="course-toggle-row">
                            <input type="checkbox" wire:model="is_required_for_certificate" id="is_required_for_certificate">
                            <label for="is_required_for_certificate">Required for certificate</label>
                        </div>
                        <div class="course-toggle-row">
                            <input type="checkbox" wire:model="is_visible" id="is_visible">
                            <label for="is_visible">Visible to students</label>
                        </div>
                    </div>
                    <div class="course-modal-footer">
                        <button type="button" class="btn btn-soft" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Session Item --}}
    @if ($activeModal === 'session-item')
        <div class="course-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="course-modal">
                <div class="course-modal-head">
                    <h3>{{ $editItemId ? 'Edit Session Item' : 'Add Session Item' }}</h3>
                    <button type="button" class="course-modal-close" wire:click="closeModal">x</button>
                </div>
                <form wire:submit.prevent="saveItem">
                    <div class="course-modal-body">
                        <div class="course-grid-2">
                            <div class="course-field @error('item_type') is-invalid @enderror">
                                <label>Item Type <span class="req">*</span></label>
                                <select wire:model="item_type">
                                    <option value="">Select</option>
                                    @foreach($itemTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('item_type') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="course-field @error('resource_type') is-invalid @enderror">
                                <label>Resource Type</label>
                                <select wire:model="resource_type">
                                    <option value="">None / Text</option>
                                    <option value="video">Video</option>
                                    <option value="ppt">PPT</option>
                                    <option value="video_or_ppt">Video or PPT</option>
                                </select>
                                @error('resource_type') <span class="course-error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="course-field @error('item_title') is-invalid @enderror">
                            <label>Title <span class="req">*</span></label>
                            <input type="text" wire:model="item_title">
                            @error('item_title') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="course-field @error('item_content') is-invalid @enderror">
                            <label>Content / Notes</label>
                            <textarea wire:model="item_content" rows="3"></textarea>
                            @error('item_content') <span class="course-error-text">{{ $message }}</span> @enderror
                        </div>

                        @if($resource_type)
                            <div class="course-field">
                                <label>{{ $resource_type === 'ppt' ? 'PPT Source' : ($resource_type === 'video' ? 'Video Source' : 'Video / PPT Source') }}</label>
                                <div class="course-source-tabs">
                                    <button type="button"
                                            class="course-tab-btn {{ $uploadMode === 'url' ? 'active' : '' }}"
                                            wire:click="setUploadMode('url')">Paste URL</button>
                                    <button type="button"
                                            class="course-tab-btn {{ $uploadMode === 'upload' ? 'active' : '' }}"
                                            wire:click="setUploadMode('upload')">Upload File</button>
                                </div>
                            </div>

                            @if($uploadMode === 'url')
                                <div class="course-field @error('resource_url') is-invalid @enderror">
                                    <label>Resource URL {{ in_array($resource_type, ['video','video_or_ppt']) ? '(link for student video preview)' : '(PPT / PDF link)' }}</label>
                                    <input type="url" wire:model="resource_url" placeholder="https://...">
                                    @error('resource_url') <span class="course-error-text">{{ $message }}</span> @enderror
                                </div>

                                @if($resource_url && in_array($resource_type, ['video', 'video_or_ppt']))
                                    <div class="course-video-wrap">
                                        <video controls src="{{ $resource_url }}"></video>
                                    </div>
                                @endif
                            @else
                                <div class="course-field @error('itemFile') is-invalid @enderror"
                                     x-data="{ progress: 0, uploading: false }"
                                     @livewire-upload-start="uploading = true; progress = 0"
                                     @livewire-upload-progress="progress = $event.detail.progress"
                                     @livewire-upload-finish="uploading = false; progress = 100"
                                     @livewire-upload-error="uploading = false; progress = 0">
                                    <label>
                                        {{ $resource_type === 'ppt' ? 'Upload PPT / PDF' : ($resource_type === 'video' ? 'Upload Video' : 'Upload Video or PPT/PDF') }}
                                    </label>
                                    <input type="file" wire:model="itemFile"
                                           accept="{{ $resource_type === 'video' ? 'video/*' : ($resource_type === 'ppt' ? '.ppt,.pptx,.pdf' : 'video/*,.ppt,.pptx,.pdf') }}">
                                    @error('itemFile') <span class="course-error-text">{{ $message }}</span> @enderror

                                    <p style="font-size:12px; color:var(--muted); margin-top:2px;">
                                        {{ $resource_type === 'ppt' ? 'Accepted: .ppt, .pptx, .pdf — max 20MB.' : ($resource_type === 'video' ? 'Accepted: mp4, mov, webm, mkv — max 100MB.' : 'Video (mp4/mov/webm) max 100MB, or PPT/PDF max 20MB.') }}
                                    </p>

                                    <div x-show="uploading" class="course-progress-track">
                                        <div class="course-progress-fill" :style="`width: ${progress}%`"></div>
                                    </div>

                                    @if($itemFile)
                                        <div class="course-file-chip">
                                            <span>📎 {{ $itemFile->getClientOriginalName() }}</span>
                                            <button type="button" class="course-btn-mini danger" wire:click="removeSelectedFile">Remove</button>
                                        </div>
                                        @if(in_array($resource_type, ['video', 'video_or_ppt']) && str_starts_with($itemFile->getMimeType() ?? '', 'video/'))
                                            <div class="course-video-wrap" style="margin-top:8px;">
                                                <video controls src="{{ $itemFile->temporaryUrl() }}"></video>
                                            </div>
                                        @endif
                                    @elseif($existingFilePath)
                                        <div class="course-file-chip">
                                            <span>📎 Currently using an uploaded file</span>
                                        </div>
                                        @if(in_array($resource_type, ['video', 'video_or_ppt']))
                                            <div class="course-video-wrap" style="margin-top:8px;">
                                                <video controls src="{{ $resource_url }}"></video>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="course-modal-footer">
                        <button type="button" class="btn btn-soft" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Video Preview (student-view purpose) --}}
    @if ($activeModal === 'video-preview')
        <div class="course-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="course-modal wide">
                <div class="course-modal-head">
                    <h3>{{ $previewTitle }}</h3>
                    <button type="button" class="course-modal-close" wire:click="closeModal">x</button>
                </div>
                <div class="course-modal-body">
                    <div class="course-video-wrap">
                        <video controls autoplay src="{{ $previewUrl }}"></video>
                    </div>
                    <p style="color:var(--muted); font-size:12.5px; margin-top:10px;">
                        This is exactly how the video will appear to enrolled students.
                    </p>
                </div>
            </div>
        </div>
    @endif
<livewire:admin.course-batch-assignment :courseId="$course->id" />
    {{-- SweetAlert2 hooks --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-delete', (event) => {
                const { kind, id, label } = event;
                const kindLabel = kind === 'week' ? 'week' : (kind === 'session' ? 'session' : 'item');
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete this ' + kindLabel + '?',
                    html: 'Remove <b>' + label + '</b>? Everything nested inside it will also be removed. This cannot be undone.',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteConfirmed', { kind: kind, id: id });
                    }
                });
            });

            Livewire.on('toast', (event) => {
                const { type, message } = event;
                Swal.fire({
                   
                    icon: type || 'success',
                    title: message,
                    showConfirmButton: false,
                    timer: 2800,
                    timerProgressBar: true
                });
            });
        });
    </script>
</div>