@once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
@endonce

<div class="pol-wrap">

    {{-- ============ HEADER ============ --}}
    <section class="pol-header">
        <div>
            <p class="pol-eyebrow">Policy Center</p>
            <h1 class="pol-title">Manage policies & their sections</h1>
            <p class="pol-subtitle">Each policy is versioned and made up of ordered sections — recording rules, refund terms, academic conduct, and anything else you publish to students and trainers.</p>
        </div>
        <button type="button" class="pol-btn pol-btn-primary" wire:click="create">+ New Policy</button>
    </section>

    {{-- ============ FILTERS ============ --}}
    <section class="pol-card pol-filters">
        <div class="pol-field">
            <label class="pol-label" for="polSearch">Search</label>
            <input id="polSearch" type="text" class="pol-input" wire:model.live.debounce.400ms="search"
                   placeholder="Search by title or code…">
        </div>

        <div class="pol-field">
            <label class="pol-label" for="polStatusFilter">Status</label>
            <select id="polStatusFilter" class="pol-select" wire:model.live="statusFilter">
                <option value="">All statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="pol-field">
            <label class="pol-label" for="polPublishedFilter">Published</label>
            <select id="polPublishedFilter" class="pol-select" wire:model.live="publishedFilter">
                <option value="">All</option>
                <option value="published">Published</option>
                <option value="draft">Draft (unpublished)</option>
            </select>
        </div>

        <div class="pol-field">
            <label class="pol-label" for="polJumpTo">Jump to policy</label>
            <select id="polJumpTo" class="pol-select" wire:model.live="jumpToPolicyId">
                <option value="">Select a policy to edit…</option>
                @foreach ($this->policyOptions as $option)
                    <option value="{{ $option->id }}">#{{ $option->id }} — {{ $option->title }} ({{ $option->is_active ? 'Active' : 'Inactive' }})</option>
                @endforeach
            </select>
            <p class="pol-hint">Populated live from the policies table.</p>
        </div>
    </section>

    {{-- ============ CREATE / EDIT FORM ============ --}}
    @if ($showForm)
        <section class="pol-card pol-form-card" wire:key="policy-form">
            <header class="pol-card__head">
                <h2>{{ $editingId ? 'Edit Policy #'.$editingId : 'New Policy' }}</h2>
                <p>{{ $editingId ? 'Update the policy and its sections, then save.' : 'Fill in the policy details, then add its sections below.' }}</p>
            </header>

            <form wire:submit="save" class="pol-form">
                {{-- Policy fields --}}
                <div class="pol-grid">
                    <div class="pol-field">
                        <label class="pol-label" for="polCode">Code</label>
                        <div class="pol-inline-row">
                            <input id="polCode" type="text" class="pol-input" wire:model="code"
                                   @disabled(! $codeLocked) placeholder="auto-generated-from-title">
                            @unless ($codeLocked)
                                <button type="button" class="pol-btn pol-btn-ghost" wire:click="unlockCode">Edit manually</button>
                            @endunless
                        </div>
                        <p class="pol-hint">Auto-follows the title unless unlocked. Must be unique.</p>
                        @error('code') <p class="pol-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pol-field">
                        <label class="pol-label" for="polVersion">Version</label>
                        <input id="polVersion" type="text" class="pol-input" wire:model="version" placeholder="e.g. v1.2">
                        @error('version') <p class="pol-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pol-field pol-field--wide">
                        <label class="pol-label" for="polTitle">Title</label>
                        <input id="polTitle" type="text" class="pol-input" wire:model.live.debounce.300ms="title"
                               placeholder="Example: Course Recording & Data Protection Policy">
                        @error('title') <p class="pol-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pol-field">
                        <label class="pol-label" for="polPublishedAt">Published At</label>
                        <input id="polPublishedAt" type="datetime-local" class="pol-input" wire:model="publishedAt">
                        <p class="pol-hint">Leave blank to keep this policy as a draft.</p>
                        @error('publishedAt') <p class="pol-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="pol-field">
                        <span class="pol-label">Active</span>
                        <label class="pol-switch">
                            <input type="checkbox" wire:model="isActive">
                            <span class="pol-switch-track"><span class="pol-switch-thumb"></span></span>
                            <span class="pol-switch-label">{{ $isActive ? 'Active' : 'Inactive' }}</span>
                        </label>
                        @error('isActive') <p class="pol-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Sections repeater --}}
                <div class="pol-sections">
                    <div class="pol-sections__head">
                        <div>
                            <h3>Sections</h3>
                            <p>Ordered blocks of policy content — drag order with the arrows, each needs a unique key.</p>
                        </div>
                        <button type="button" class="pol-btn pol-btn-soft pol-btn-sm" wire:click="addSection">+ Add Section</button>
                    </div>

                    @error('sections') <p class="pol-error">{{ $message }}</p> @enderror

                    <div class="pol-section-list">
                        @forelse ($sections as $index => $section)
                            <div class="pol-section-row" wire:key="section-{{ $index }}-{{ $section['id'] ?? 'new' }}">
                                <div class="pol-section-row__order">
                                    <button type="button" class="pol-order-btn" wire:click="moveSectionUp({{ $index }})" @disabled($index === 0) aria-label="Move up">↑</button>
                                    <span class="pol-order-index">{{ $index + 1 }}</span>
                                    <button type="button" class="pol-order-btn" wire:click="moveSectionDown({{ $index }})" @disabled($index === count($sections) - 1) aria-label="Move down">↓</button>
                                </div>

                                <div class="pol-section-row__fields">
                                    <div class="pol-field">
                                        <label class="pol-label">Section Key</label>
                                        <input type="text" class="pol-input" wire:model="sections.{{ $index }}.section_key" placeholder="e.g. recording_policy">
                                        @error("sections.{$index}.section_key") <p class="pol-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="pol-field">
                                        <label class="pol-label">Section Title</label>
                                        <input type="text" class="pol-input" wire:model="sections.{{ $index }}.title" placeholder="e.g. Video Recording Rules">
                                        @error("sections.{$index}.title") <p class="pol-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="pol-field pol-field--wide">
                                        <label class="pol-label">Body</label>
                                        <textarea class="pol-textarea" rows="4" wire:model="sections.{{ $index }}.body" placeholder="Section content shown to students/trainers."></textarea>
                                        @error("sections.{$index}.body") <p class="pol-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <button type="button" class="pol-btn pol-btn-danger pol-btn-sm pol-section-remove" wire:click="removeSection({{ $index }})">Remove</button>
                            </div>
                        @empty
                            <div class="pol-empty">No sections yet — click "Add Section" above.</div>
                        @endforelse
                    </div>
                </div>

                <div class="pol-footer">
                    <button type="button" class="pol-btn pol-btn-ghost" wire:click="cancel">Cancel</button>
                    <button type="submit" class="pol-btn pol-btn-primary" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Save Changes' : 'Create Policy' }}</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                </div>
            </form>
        </section>
    @endif

    {{-- ============ LIST ============ --}}
    <section class="pol-card">
        <header class="pol-card__head">
            <h2>All Policies</h2>
            <p>{{ $this->policies->total() }} total</p>
        </header>

        <div class="pol-table-wrap">
            <table class="pol-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Code</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th>Sections</th>
                        <th class="pol-th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->policies as $policy)
                        <tr wire:key="policy-row-{{ $policy->id }}">
                            <td data-label="ID">#{{ $policy->id }}</td>
                            <td data-label="Title"><strong>{{ $policy->title }}</strong></td>
                            <td data-label="Code">{{ $policy->code }}</td>
                            <td data-label="Version">{{ $policy->version ?: '—' }}</td>
                            <td data-label="Status">
                                <span class="pol-badge {{ $policy->is_active ? 'pol-badge--published' : 'pol-badge--draft' }}">
                                    {{ $policy->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td data-label="Published">
                                @if ($policy->published_at)
                                    <span class="pol-badge pol-badge--published">{{ $policy->published_at->format('d M Y, H:i') }}</span>
                                @else
                                    <span class="pol-badge pol-badge--archived">Draft</span>
                                @endif
                            </td>
                            <td data-label="Sections">{{ $policy->sections_count }}</td>
                            <td data-label="Actions" class="pol-td-actions">
                                <button type="button" class="pol-btn pol-btn-soft pol-btn-sm" wire:click="edit({{ $policy->id }})">Edit</button>
                                <button type="button" class="pol-btn pol-btn-danger pol-btn-sm"
                                        x-data
                                        @click="
                                            Swal.fire({
                                                title: 'Delete this policy?',
                                                text: '\'{{ addslashes($policy->title) }}\' and all of its sections will be permanently removed.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Delete',
                                                confirmButtonColor: '#dc2626',
                                                cancelButtonText: 'Cancel',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete({{ $policy->id }});
                                                }
                                            })
                                        ">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="pol-empty">No policies match your filters yet.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pol-pagination">
            {{ $this->policies->links() }}
        </div>
    </section>

    {{-- ============ STYLES ============ --}}
    <style>
        .pol-wrap {
            display: flex; flex-direction: column; gap: 20px;
            width: 100%; max-width: 1200px; margin: 0 auto; padding: 16px;
        }

        .pol-header { display: flex; flex-direction: column; gap: 14px; justify-content: space-between; align-items: flex-start; }
        .pol-eyebrow { font-size: 12px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--brand-secondary); margin-bottom: 6px; }
        .pol-title { font-size: clamp(20px, 3.5vw, 28px); font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .pol-subtitle { font-size: 13.5px; color: var(--text-muted); max-width: 64ch; }

        .pol-card {
            background: var(--bg-card); border: 1px solid var(--line); border-radius: var(--radius, 20px);
            box-shadow: var(--shadow-card); padding: clamp(16px, 3vw, 24px);
        }
        .pol-card__head { margin-bottom: 16px; display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 6px; }
        .pol-card__head h2 { font-size: 17px; color: var(--text); }
        .pol-card__head p { font-size: 12.5px; color: var(--text-muted); }

        .pol-filters { display: grid; grid-template-columns: 1fr; gap: 14px; }

        .pol-form { display: flex; flex-direction: column; gap: 22px; }
        .pol-grid { display: grid; grid-template-columns: 1fr; gap: 14px; }
        .pol-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .pol-field--wide { grid-column: 1 / -1; }

        .pol-label { font-size: 12.5px; font-weight: 600; color: var(--text); }
        .pol-hint { font-size: 12px; color: var(--text-muted); }
        .pol-error { font-size: 12px; color: var(--danger); font-weight: 500; }

        .pol-input, .pol-select, .pol-textarea {
            width: 100%; font-family: inherit; font-size: 14px; color: var(--text);
            background: var(--input-bg); border: 1px solid var(--input-border);
            border-radius: var(--radius-sm, 12px); padding: 10px 13px; min-height: 44px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .pol-textarea { min-height: 100px; resize: vertical; }
        .pol-input:focus, .pol-select:focus, .pol-textarea:focus {
            outline: none; border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .pol-input:disabled { opacity: .6; cursor: not-allowed; }

        .pol-inline-row { display: flex; flex-direction: column; gap: 8px; }
        .pol-inline-row .pol-input { flex: 1; }

        /* Toggle switch */
        .pol-switch { display: inline-flex; align-items: center; gap: 10px; cursor: pointer; min-height: 44px; }
        .pol-switch input { position: absolute; opacity: 0; width: 0; height: 0; }
        .pol-switch-track {
            width: 42px; height: 24px; border-radius: 999px; background: var(--input-border);
            position: relative; transition: background .15s ease; flex-shrink: 0;
        }
        .pol-switch-thumb {
            position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%;
            background: #fff; transition: transform .15s ease; box-shadow: 0 1px 3px rgba(0,0,0,.25);
        }
        .pol-switch input:checked + .pol-switch-track { background: var(--brand-primary); }
        .pol-switch input:checked + .pol-switch-track .pol-switch-thumb { transform: translateX(18px); }
        .pol-switch-label { font-size: 13px; font-weight: 600; color: var(--text); }

        /* Sections repeater */
        .pol-sections { border-top: 1px solid var(--line); padding-top: 18px; display: flex; flex-direction: column; gap: 14px; }
        .pol-sections__head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; }
        .pol-sections__head h3 { font-size: 15px; color: var(--text); margin-bottom: 2px; }
        .pol-sections__head p { font-size: 12px; color: var(--text-muted); }

        .pol-section-list { display: flex; flex-direction: column; gap: 12px; }
        .pol-section-row {
            display: grid; grid-template-columns: auto 1fr auto; gap: 12px; align-items: start;
            border: 1px solid var(--line); border-radius: var(--radius-sm, 12px); background: var(--bg-card2);
            padding: 14px;
        }
        .pol-section-row__order { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .pol-order-btn {
            width: 26px; height: 26px; border-radius: 6px; border: 1px solid var(--input-border);
            background: var(--bg-card); color: var(--text-muted); font-size: 12px; display: flex; align-items: center; justify-content: center;
        }
        .pol-order-btn:hover:not(:disabled) { border-color: var(--brand-primary); color: var(--brand-primary); }
        .pol-order-btn:disabled { opacity: .35; cursor: not-allowed; }
        .pol-order-index { font-size: 11px; font-weight: 700; color: var(--text-muted); }

        .pol-section-row__fields { display: grid; grid-template-columns: 1fr; gap: 10px; }
        .pol-section-remove { align-self: start; }

        .pol-footer { display: flex; flex-direction: column; gap: 10px; padding-top: 14px; border-top: 1px solid var(--line); }

        .pol-btn {
            display: inline-flex; align-items: center; justify-content: center;
            min-height: 44px; padding: 0 18px; border-radius: var(--radius-sm, 12px);
            font-size: 13.5px; font-weight: 600; border: 1px solid transparent; cursor: pointer; transition: all .15s ease;
        }
        .pol-btn-sm { min-height: 34px; padding: 0 12px; font-size: 12.5px; }
        .pol-btn-primary { background: var(--brand-primary); color: #fff; box-shadow: var(--shadow-sm); }
        .pol-btn-primary:hover { filter: brightness(1.06); }
        .pol-btn-primary:disabled { opacity: .55; cursor: not-allowed; }
        .pol-btn-ghost { background: var(--bg-card2); color: var(--text); border-color: var(--input-border); }
        .pol-btn-ghost:hover { border-color: var(--brand-primary); color: var(--brand-primary); }
        .pol-btn-soft { background: var(--bg2); color: var(--brand-primary); border-color: var(--line); }
        .pol-btn-soft:hover { background: var(--bg-card2); }
        .pol-btn-danger { background: color-mix(in srgb, var(--danger) 10%, var(--bg-card)); color: var(--danger); border-color: color-mix(in srgb, var(--danger) 35%, var(--line)); }
        .pol-btn-danger:hover { background: var(--danger); color: #fff; }

        .pol-table-wrap { overflow-x: auto; }
        .pol-table { width: 100%; border-collapse: collapse; min-width: 820px; }
        .pol-table thead th {
            text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em;
            color: var(--text-muted); font-weight: 700; padding: 10px 12px; border-bottom: 1px solid var(--line);
        }
        .pol-table tbody td { padding: 12px; border-bottom: 1px solid var(--line); font-size: 13.5px; color: var(--text); vertical-align: middle; }
        .pol-table tbody tr:hover { background: var(--bg-card2); }
        .pol-th-actions, .pol-td-actions { text-align: right; white-space: nowrap; }
        .pol-td-actions { display: flex; gap: 8px; justify-content: flex-end; }

        .pol-badge { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--line); }
        .pol-badge--draft { background: var(--bg2); color: var(--text-muted); }
        .pol-badge--published { background: color-mix(in srgb, var(--success) 14%, var(--bg2)); color: var(--success); border-color: color-mix(in srgb, var(--success) 40%, var(--line)); }
        .pol-badge--archived { background: color-mix(in srgb, var(--warning) 14%, var(--bg2)); color: var(--warning); border-color: color-mix(in srgb, var(--warning) 40%, var(--line)); }

        .pol-empty { text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px; }
        .pol-pagination { margin-top: 14px; }

        @media (min-width: 640px) {
            .pol-header { flex-direction: row; align-items: center; }
            .pol-filters { grid-template-columns: 1fr 1fr; }
            .pol-grid { grid-template-columns: 1fr 1fr; }
            .pol-inline-row { flex-direction: row; }
            .pol-footer { flex-direction: row; justify-content: flex-end; }
            .pol-section-row__fields { grid-template-columns: 1fr 1fr; }
        }

        @media (min-width: 960px) {
            .pol-filters { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

    {{-- ============ SWEETALERT TOASTS ============ --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('policy-saved', (event) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: event.message ?? 'Policy saved.',
                    timer: 1800,
                    showConfirmButton: false,
                });
            });

            Livewire.on('policy-deleted', (event) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: event.message ?? 'Policy deleted.',
                    timer: 1800,
                    showConfirmButton: false,
                });
            });
        });
    </script>
</div>