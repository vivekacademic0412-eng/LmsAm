<div class="bn-wrap" wire:loading.class="bn-wrap--busy" wire:target="send">

    {{-- ============ HERO ============ --}}
    <section class="bn-hero">
        <p class="bn-eyebrow">Announcement Center</p>
        <h1 class="bn-title">Push the right update to the right LMS audience.</h1>
        <p class="bn-subtitle">Send dashboard notifications, emails with attachments, or both — to system roles, one course, or any manually entered address.</p>

        <div class="bn-stats">
            <span class="bn-stat">{{ count($this->audienceOptions) }} audience groups</span>
            <span class="bn-stat">{{ $this->courses->count() }} courses available</span>
            <span class="bn-stat {{ $this->notificationsReady ? 'bn-stat--ok' : 'bn-stat--danger' }}">
                {{ $this->notificationsReady ? 'Notifications ready' : 'Notifications table missing' }}
            </span>
        </div>
    </section>

    {{-- ============ ALERTS ============ --}}
    @if (session('broadcast-success'))
        <div class="bn-banner bn-banner--success">
            <strong>Sent.</strong>
            <p>{{ session('broadcast-success') }}</p>
        </div>
    @endif

    @unless ($this->notificationsReady)
        <div class="bn-banner bn-banner--danger">
            <strong>Broadcast sending is currently disabled.</strong>
            <p>The <code>notifications</code> table is missing. Run the notifications migration first, then reopen this page.</p>
        </div>
    @endunless

    @error('notifications')
        <div class="bn-banner bn-banner--danger"><p>{{ $message }}</p></div>
    @enderror

    @error('audience')
        <div class="bn-banner bn-banner--danger"><p>{{ $message }}</p></div>
    @enderror

    {{-- ============ LAYOUT ============ --}}
    <div class="bn-layout">

        {{-- ---------- COMPOSER ---------- --}}
        <section class="bn-card bn-composer">
            <header class="bn-card__head">
                <h2>Compose Broadcast</h2>
                <p>Pick who should receive this, how it should reach them, then write the message.</p>
            </header>

            <form wire:submit="send" class="bn-form">

                {{-- Delivery method --}}
                <div class="bn-block">
                    <span class="bn-label">Delivery Method</span>

                    <div class="bn-pill-group">
                        <button type="button"
                                wire:click="toggleChannel('notification')"
                                class="bn-pill {{ in_array('notification', $deliveryChannels) ? 'bn-pill--active' : '' }}">
                            <span class="bn-pill__dot"></span>
                            Dashboard Notification
                        </button>
                        <button type="button"
                                wire:click="toggleChannel('email')"
                                class="bn-pill {{ in_array('email', $deliveryChannels) ? 'bn-pill--active' : '' }}">
                            <span class="bn-pill__dot"></span>
                            Email
                        </button>
                    </div>

                    <p class="bn-hint">Notification posts an in-app card + bell alert. Email delivers to inboxes, optionally with attachments. Pick one or both.</p>
                    @error('deliveryChannels') <p class="bn-error">{{ $message }}</p> @enderror
                </div>

                {{-- Audience / course / title --}}
                <div class="bn-grid">
                    <div class="bn-field">
                        <label class="bn-label" for="broadcastAudience">Audience</label>
                        <select id="broadcastAudience" class="bn-select" wire:model.live="audience" @disabled(! $this->notificationsReady)>
                            @foreach ($this->audienceOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="bn-hint">System recipients this message will target.</p>
                        @error('audience') <p class="bn-error">{{ $message }}</p> @enderror
                    </div>

                    @if ($audience === 'course_students')
                        <div class="bn-field">
                            <label class="bn-label" for="broadcastCourse">Course</label>
                            <select id="broadcastCourse" class="bn-select" wire:model="courseId" @disabled(! $this->notificationsReady)>
                                <option value="">Select a course</option>
                                @foreach ($this->courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            <p class="bn-hint">Required when audience is Students In One Course.</p>
                            @error('courseId') <p class="bn-error">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="bn-field bn-field--wide">
                        <label class="bn-label" for="broadcastTitle">Title</label>
                        <input id="broadcastTitle" type="text" class="bn-input" wire:model="title" maxlength="120"
                               placeholder="Example: Submission window closes at 6 PM today" @disabled(! $this->notificationsReady)>
                        @error('title') <p class="bn-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Message --}}
                <div class="bn-field">
                    <label class="bn-label" for="broadcastMessage">Message</label>
                    <textarea id="broadcastMessage" class="bn-textarea" wire:model="message" rows="6"
                              placeholder="Write the full message." @disabled(! $this->notificationsReady)>{{ $message }}</textarea>
                    @error('message') <p class="bn-error">{{ $message }}</p> @enderror
                </div>

                {{-- Manual recipients --}}
                <div class="bn-field bn-field--wide">
                    <label class="bn-label" for="manualEmailInput">Add Extra Recipients Manually</label>

                    <div class="bn-inline-row">
                        <input id="manualEmailInput" type="text" class="bn-input" wire:model="manualEmailInput"
                               placeholder="name@example.com — press Add, or paste several separated by commas"
                               wire:keydown.enter.prevent="addManualEmail">
                        <button type="button" class="bn-btn bn-btn--ghost" wire:click="addManualEmail">Add</button>
                    </div>

                    <p class="bn-hint">These addresses receive email only (they may not have a system account). Useful for guardians, partners, or ad hoc contacts.</p>

                    @if (count($manualEmails))
                        <div class="bn-chip-row">
                            @foreach ($manualEmails as $index => $email)
                                <span class="bn-chip">
                                    {{ $email }}
                                    <button type="button" wire:click="removeManualEmail({{ $index }})" aria-label="Remove">&times;</button>
                                </span>
                            @endforeach
                        </div>
                    @endif
                    @error('manualEmails.*') <p class="bn-error">{{ $message }}</p> @enderror
                </div>

                {{-- Attachments (email only) --}}
                @if (in_array('email', $deliveryChannels))
                    <div class="bn-field bn-field--wide">
                        <label class="bn-label" for="broadcastAttachments">Attach Files (PDF, PPT, DOCX, images, any type)</label>

                        <label for="broadcastAttachments" class="bn-drop">
                            <input id="broadcastAttachments" type="file" wire:model="attachments" multiple class="bn-drop__input">
                            <span class="bn-drop__copy">
                                <strong>Click to upload</strong>
                                <span>or drag files here — up to 5 files, 10MB each</span>
                            </span>
                        </label>

                        <div wire:loading wire:target="attachments" class="bn-uploading">Uploading…</div>

                        @error('attachments') <p class="bn-error">{{ $message }}</p> @enderror
                        @error('attachments.*') <p class="bn-error">{{ $message }}</p> @enderror

                        @if (count($attachments))
                            <div class="bn-file-list">
                                @foreach ($attachments as $index => $file)
                                    <div class="bn-file">
                                        <span class="bn-file__name">{{ $file->getClientOriginalName() }}</span>
                                        <span class="bn-file__size">{{ number_format($file->getSize() / 1024, 0) }} KB</span>
                                        <button type="button" class="bn-file__remove" wire:click="removeAttachment({{ $index }})" aria-label="Remove file">&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Submit --}}
                <div class="bn-footer">
                    <p class="bn-footer__note">
                        Notifications appear in dashboard cards and the top-bar bell. Emails deliver directly to inboxes, with attachments if provided.
                    </p>
                    <button type="submit" class="bn-btn bn-btn--primary"
                            wire:loading.attr="disabled" wire:target="send"
                            @disabled(! $this->notificationsReady)>
                        <span wire:loading.remove wire:target="send">Send Broadcast</span>
                        <span wire:loading wire:target="send">Sending…</span>
                    </button>
                </div>
            </form>
        </section>

        {{-- ---------- GUIDE ---------- --}}
        <aside class="bn-card bn-guide">
            <header class="bn-card__head">
                <h2>Audience Guide</h2>
                <p>Selected audience is highlighted so you can sanity-check targeting before you send.</p>
            </header>

            <div class="bn-guide-list">
                @foreach ($this->audienceOptions as $value => $label)
                    <article class="bn-guide-item {{ $audience === $value ? 'bn-guide-item--active' : '' }}">
                        <div class="bn-guide-item__head">
                            <strong>{{ $label }}</strong>
                            <span class="bn-badge">{{ $audience === $value ? 'Selected' : 'Audience' }}</span>
                        </div>
                        <p>
                            @if ($value === 'course_students')
                                Best for class-specific reminders and anything scoped to one course.
                            @elseif ($value === 'students')
                                Deadlines, certification reminders, learning announcements.
                            @elseif ($value === 'trainers')
                                Review requests and operational updates for teaching staff.
                            @elseif ($value === 'manager_hr')
                                HR coordination and internal workflow updates.
                            @elseif ($value === 'it')
                                Maintenance windows and technical notices.
                            @elseif ($value === 'admins')
                                Admin-only coordination across super admin and admin accounts.
                            @elseif ($value === 'demo_users')
                                Demo-access reminders and onboarding nudges.
                            @else
                                Platform-wide message to every active account.
                            @endif
                        </p>
                    </article>
                @endforeach
            </div>
        </aside>
    </div>

    {{-- ============ STYLES ============ --}}
    <style>
        .bn-wrap {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
            /* max-width: 1280px; */
            margin: 0 auto;
            padding: 10px;
            transition: opacity .15s ease;
        }
        .bn-wrap--busy { opacity: .85; }

        /* ---------- Hero ---------- */
        .bn-hero {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
            border-radius: var(--radius, 20px);
            padding: clamp(20px, 4vw, 36px);
            color: #fff;
            box-shadow: var(--shadow);
        }
        .bn-eyebrow {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--brand-accent);
            margin-bottom: 8px;
        }
        .bn-title {
            font-size: clamp(22px, 4vw, 32px);
            line-height: 1.25;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .bn-subtitle {
            font-size: clamp(13.5px, 2vw, 15.5px);
            color: rgba(255, 255, 255, .88);
            max-width: 640px;
            margin-bottom: 18px;
        }
        .bn-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .bn-stat {
            font-size: 12.5px;
            font-weight: 600;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .25);
            white-space: nowrap;
        }
        .bn-stat--ok { background: rgba(22, 163, 74, .25); border-color: rgba(22, 163, 74, .4); }
        .bn-stat--danger { background: rgba(220, 38, 38, .25); border-color: rgba(220, 38, 38, .4); }

        /* ---------- Banners ---------- */
        .bn-banner {
            border-radius: var(--radius-sm, 12px);
            padding: 14px 18px;
            border-left: 4px solid transparent;
            background: var(--bg-card);
            box-shadow: var(--shadow-sm);
            font-size: 14px;
        }
        .bn-banner strong { display: block; margin-bottom: 2px; }
        .bn-banner p { color: var(--text-muted); font-size: 13.5px; }
        .bn-banner--success { border-color: var(--success); }
        .bn-banner--danger { border-color: var(--danger); }

        /* ---------- Layout ---------- */
        .bn-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .bn-card {
            background: var(--bg-card);
            border: 1px solid var(--line);
            border-radius: var(--radius, 20px);
            box-shadow: var(--shadow-card);
            padding: clamp(16px, 3vw, 28px);
        }
        .bn-card__head { margin-bottom: 18px; }
        .bn-card__head h2 { font-size: 19px; margin-bottom: 4px; }
        .bn-card__head p { font-size: 13.5px; color: var(--text-muted); }

        /* ---------- Form ---------- */
        .bn-form { display: flex; flex-direction: column; gap: 20px; }
        .bn-block { display: flex; flex-direction: column; gap: 8px; }

        .bn-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .bn-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .bn-field--wide { grid-column: 1 / -1; }

        .bn-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }
        .bn-hint { font-size: 12.5px; color: var(--text-muted); }
        .bn-error { font-size: 12.5px; color: var(--danger); font-weight: 500; }

        .bn-input, .bn-select, .bn-textarea {
            width: 100%;
            font-family: inherit;
            font-size: 14.5px;
            color: var(--text);
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: var(--radius-sm, 12px);
            padding: 11px 14px;
            min-height: 46px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .bn-textarea { min-height: 130px; resize: vertical; }
        .bn-input:focus, .bn-select:focus, .bn-textarea:focus {
            outline: none;
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .bn-input:disabled, .bn-select:disabled, .bn-textarea:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        /* ---------- Delivery pills ---------- */
        .bn-pill-group { display: flex; flex-wrap: wrap; gap: 10px; }
        .bn-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            border: 1px solid var(--input-border);
            background: var(--input-bg);
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 600;
            transition: all .15s ease;
        }
        .bn-pill__dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--muted);
            transition: background .15s ease;
        }
        .bn-pill--active {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            background: var(--primary-glow);
            box-shadow: var(--shadow-sm);
        }
        .bn-pill--active .bn-pill__dot { background: var(--brand-primary); }

        /* ---------- Inline row (manual email add) ---------- */
        .bn-inline-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .bn-inline-row .bn-input { flex: 1; }

        .bn-chip-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
        .bn-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--bg-card2);
            border: 1px solid var(--line);
            font-size: 12.5px;
            color: var(--text);
            max-width: 100%;
            overflow-wrap: anywhere;
        }
        .bn-chip button {
            border: none;
            background: none;
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1;
            cursor: pointer;
            flex-shrink: 0;
        }
        .bn-chip button:hover { color: var(--danger); }

        /* ---------- Dropzone ---------- */
        .bn-drop {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 4px;
            text-align: center;
            border: 1.5px dashed var(--input-border);
            border-radius: var(--radius-sm, 12px);
            padding: clamp(18px, 4vw, 28px);
            cursor: pointer;
            background: var(--input-bg);
            transition: border-color .15s ease, background .15s ease;
        }
        .bn-drop:hover { border-color: var(--brand-primary); background: var(--primary-glow); }
        .bn-drop__input { display: none; }
        .bn-drop__copy { display: flex; flex-direction: column; gap: 2px; }
        .bn-drop__copy strong { font-size: 14px; color: var(--text); }
        .bn-drop__copy span { font-size: 12.5px; color: var(--text-muted); }

        .bn-uploading { font-size: 12.5px; color: var(--brand-primary); }

        .bn-file-list { display: flex; flex-direction: column; gap: 8px; }
        .bn-file {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm, 12px);
            background: var(--bg-card2);
            border: 1px solid var(--line);
            font-size: 13px;
        }
        .bn-file__name {
            flex: 1;
            min-width: 0;
            color: var(--text);
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .bn-file__size { color: var(--text-muted); flex-shrink: 0; font-size: 12px; }
        .bn-file__remove {
            border: none;
            background: none;
            color: var(--text-muted);
            font-size: 17px;
            line-height: 1;
            cursor: pointer;
            flex-shrink: 0;
        }
        .bn-file__remove:hover { color: var(--danger); }

        /* ---------- Footer / submit ---------- */
        .bn-footer {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-top: 6px;
            border-top: 1px solid var(--line);
        }
        .bn-footer__note { font-size: 12.5px; color: var(--text-muted); }

        .bn-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 20px;
            border-radius: var(--radius-sm, 12px);
            font-size: 14px;
            font-weight: 600;
            border: 1px solid transparent;
            transition: all .15s ease;
            width: 100%;
        }
        .bn-btn--primary {
            background: var(--brand-primary);
            color: #fff;
            box-shadow: var(--shadow-sm);
        }
        .bn-btn--primary:hover { filter: brightness(1.06); }
        .bn-btn--primary:disabled { opacity: .55; cursor: not-allowed; }
        .bn-btn--ghost {
            background: var(--bg-card2);
            color: var(--text);
            border-color: var(--input-border);
        }
        .bn-btn--ghost:hover { border-color: var(--brand-primary); color: var(--brand-primary); }

        /* ---------- Guide ---------- */
        .bn-guide-list { display: flex; flex-direction: column; gap: 10px; }
        .bn-guide-item {
            border: 1px solid var(--line);
            border-radius: var(--radius-sm, 12px);
            padding: 12px 14px;
            background: var(--bg-card2);
            transition: border-color .15s ease, background .15s ease;
        }
        .bn-guide-item--active {
            border-color: var(--brand-primary);
            background: var(--primary-glow);
        }
        .bn-guide-item__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
        }
        .bn-guide-item__head strong { font-size: 13.5px; color: var(--text); }
        .bn-guide-item p { font-size: 12.5px; color: var(--text-muted); }
        .bn-badge {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 999px;
            background: var(--bg-card);
            color: var(--text-muted);
            border: 1px solid var(--line);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .bn-guide-item--active .bn-badge {
            background: var(--brand-primary);
            color: #fff;
            border-color: var(--brand-primary);
        }

        /* ========== RESPONSIVE BREAKPOINTS ========== */

        /* Small tablets and up: 2-column field grid, side-by-side email add row */
        @media (min-width: 640px) {
            .bn-wrap { padding: 20px; gap: 24px; }
            .bn-grid { grid-template-columns: 1fr 1fr; }
            .bn-inline-row { flex-direction: row; }
            .bn-footer { flex-direction: row; align-items: center; justify-content: space-between; }
            .bn-footer__note { max-width: 60%; }
            .bn-btn { width: auto; min-width: 180px; }
        }

        /* Tablet landscape / small desktop: composer + guide side by side */
        @media (min-width: 960px) {
            .bn-wrap { padding: 24px; }
            .bn-layout {
                grid-template-columns: minmax(0, 1fr) 340px;
                align-items: start;
            }
            .bn-guide { position: sticky; top: 16px; }
        }

        /* Large desktop: wider guide column */
        @media (min-width: 1200px) {
            .bn-layout { grid-template-columns: minmax(0, 1fr) 380px; gap: 28px; }
        }

        /* Fine-tune very small phones */
        @media (max-width: 380px) {
            .bn-title { font-size: 20px; }
            .bn-stat { font-size: 11.5px; padding: 6px 10px; }
        }
    </style>
</div>