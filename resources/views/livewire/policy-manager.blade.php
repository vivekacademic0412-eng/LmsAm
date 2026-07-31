<div>
    <style>
        /* ═══════════════════════════════════════════════
           POLICIES — SCOPED THEME-BASED STYLES
           Prefixed `policy-` so classes never collide with
           other modules. Uses the shared theme CSS vars.
        ═══════════════════════════════════════════════ */
        .policy-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius, 16px);
            box-shadow: var(--shadow-card);
        }

        .policy-table-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            background: var(--bg-card2, var(--bg2));
        }
        .policy-table-head-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
        }
        .policy-table-head-top h2 { font-size: 17px; font-weight: 700; color: var(--text); }
        .policy-count-pill {
            font-size: 12.5px; font-weight: 700; color: var(--muted);
            background: var(--card); border: 1px solid var(--line);
            border-radius: 999px; padding: 4px 12px;
        }

        .policy-filter-bar {
            display: flex;
            flex-wrap: nowrap;
            align-items: flex-end;
            gap: 10px;
            width: 100%;
            margin-top: 14px;
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .policy-filter-field {
            display: flex; flex-direction: column; gap: 6px;
            flex: 1 1 170px; min-width: 150px;
        }
        .policy-filter-field label {
            font-size: 11px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .5px;
        }
        .policy-filter-field input,
        .policy-filter-field select {
            border: 1px solid var(--input-border, var(--line));
            background: var(--input-bg, var(--card));
            color: var(--text);
            border-radius: var(--radius-xs, 8px);
            padding: 9px 10px;
            font-size: 14px;
            width: 100%;
        }
        .policy-filter-field input:focus,
        .policy-filter-field select:focus {
            outline: none;
            border-color: var(--input-focus, var(--primary));
            box-shadow: 0 0 0 3px var(--primary-glow, rgba(13, 93, 209, .12));
        }
        .policy-filter-actions { display: flex; gap: 8px; flex: 0 0 auto; }
        .policy-filter-actions .btn,
        .policy-filter-actions .btn-soft { white-space: nowrap; }

        @media (max-width: 720px) {
            .policy-table-head-top { flex-direction: column; align-items: stretch; }
            .policy-filter-bar { flex-wrap: wrap; overflow-x: visible; }
            .policy-filter-actions { justify-content: flex-end; width: 100%; }
        }

        /* ── Row actions ───────────────────────────────────────── */
        .policy-row-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .policy-btn-mini {
            border: 1px solid var(--line);
            border-radius: var(--radius-xs, 10px);
            background: var(--bg-card2, var(--card));
            color: var(--text);
            padding: 7px 12px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px; line-height: 1;
            transition: 160ms ease;
        }
        .policy-btn-mini:hover { border-color: var(--primary); background: var(--primary-glow); transform: translateY(-1px); }
        .policy-btn-mini.danger { color: var(--danger); }
        .policy-btn-mini.danger:hover { border-color: var(--danger); background: rgba(220, 38, 38, .08); }

        /* ── Table ─────────────────────────────────────────────── */
        .policy-table-wrap { overflow-x: auto; }
        .policy-table-wrap table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .policy-table-wrap thead th {
            text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .6px;
            color: var(--muted); background: var(--bg-card2, var(--bg2)); border-bottom: 1px solid var(--line);
            padding: 12px 14px; position: sticky; top: 0; z-index: 1;
        }
        .policy-table-wrap tbody td { padding: 14px; border-bottom: 1px solid var(--line); color: var(--text); }
        .policy-table-wrap tbody tr:nth-child(even) td { background: var(--bg-card2, var(--bg2)); }
        .policy-table-wrap tbody tr:hover td { background: var(--primary-glow, rgba(13, 93, 209, .06)); }
        .policy-id-pill {
            display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 28px;
            padding: 0 8px; border-radius: 999px; background: var(--bg-card2, var(--bg2)); color: var(--text);
            font-weight: 700; font-size: 12px; border: 1px solid var(--line);
        }
        .policy-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 3px 10px; border-radius: 999px; font-size: 12.5px; font-weight: 600;
            cursor: pointer; border: none;
        }
        .policy-badge.active { background: rgba(22, 163, 74, .12); color: var(--success, #16a34a); }
        .policy-badge.inactive { background: rgba(220, 38, 38, .1); color: var(--danger, #dc2626); }
        .policy-code { font-weight: 700; color: var(--text); }
        .policy-version { color: var(--muted); font-size: 13px; }

        /* ── Modal ─────────────────────────────────────────────── */
        [x-cloak] { display: none !important; }
        .policy-modal-overlay {
            position: fixed; inset: 0; background: rgba(8, 15, 28, .56);
            backdrop-filter: blur(3px); display: flex; align-items: center;
            justify-content: center; padding: 18px; z-index: 120;
        }
        .policy-modal {
            width: min(620px, 100%); max-height: calc(100vh - 36px); overflow: auto;
            border-radius: var(--radius, 16px); border: 1px solid var(--line);
            background: var(--card); box-shadow: var(--shadow);
        }
        .policy-modal-head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 16px; border-bottom: 1px solid var(--line);
        }
        .policy-modal-head h3 { margin: 0; font-size: 20px; color: var(--text); }
        .policy-modal-close { border: 0; background: transparent; color: var(--muted); font-size: 26px; line-height: 1; cursor: pointer; }
        .policy-modal-close:hover { color: var(--danger); }
        .policy-modal-body { padding: 16px; }
        .policy-modal-footer { display: flex; justify-content: flex-end; border-top: 1px solid var(--line); padding: 12px 16px; gap: 8px; }

        .policy-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .policy-field label { font-size: 13px; font-weight: 600; color: var(--muted); }
        .policy-field label .req { color: var(--danger); }
        .policy-field input, .policy-field select {
            border: 1px solid var(--input-border, var(--line)); background: var(--input-bg, var(--card));
            color: var(--text); border-radius: var(--radius-xs, 8px); padding: 10px 12px; font-size: 14px;
            transition: border-color 140ms ease, box-shadow 140ms ease;
        }
        .policy-field input:focus, .policy-field select:focus {
            outline: none; border-color: var(--input-focus, var(--primary));
            box-shadow: 0 0 0 3px var(--primary-glow, rgba(13, 93, 209, .12));
        }
        .policy-field.is-invalid input, .policy-field.is-invalid select { border-color: var(--danger); }
        .policy-error-text { font-size: 12px; color: var(--danger); }
        .policy-toggle-row { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .policy-toggle-row label { font-size: 13px; font-weight: 600; color: var(--text); }
        .policy-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 520px) { .policy-grid-2 { grid-template-columns: 1fr; } }
    </style>

    <section class="policy-card">
        {{-- Header + single-row filters --}}
        <div class="policy-table-head">
            <div class="policy-table-head-top">
                <h2>Policy Management</h2>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span class="policy-count-pill">{{ $policies->total() }} total</span>
                    <button type="button" class="btn btn-soft" wire:click="openCreateModal">+ New Policy</button>
                </div>
            </div>

            <div class="policy-filter-bar">
                <div class="policy-filter-field">
                    <label>Search</label>
                    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search by title or code…">
                </div>
                <div class="policy-filter-field">
                    <label>Status</label>
                    <select wire:model.live="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="policy-filter-field">
                    <label>Per Page</label>
                    <select wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="policy-filter-actions">
                    <button type="button" class="btn btn-soft" wire:click="clearFilters">Clear</button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="policy-table-wrap" wire:loading.class="opacity-60">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Version</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($policies as $policy)
                    <tr wire:key="policy-{{ $policy->id }}">
                        <td><span class="policy-id-pill">#{{ $policy->id }}</span></td>
                        <td><span class="policy-code">{{ $policy->code }}</span></td>
                        <td>{{ $policy->title }}</td>
                        <td><span class="policy-version">v{{ $policy->version }}</span></td>
                        <td>
                            <button type="button"
                                    class="policy-badge {{ $policy->is_active ? 'active' : 'inactive' }}"
                                    wire:click="toggleActive({{ $policy->id }})"
                                    title="Click to toggle status">
                                {{ $policy->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td>{{ $policy->published_at?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <div class="policy-row-actions">
                                <button type="button" class="policy-btn-mini" wire:click="openEditModal({{ $policy->id }})">Edit</button>
                                <button type="button" class="policy-btn-mini danger" wire:click="confirmDelete({{ $policy->id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No policies found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-10" style="padding: 14px 20px;">
            {{ $policies->links('pagination.custom') }}
        </div>
    </section>

    {{-- Create / Edit Modal --}}
    @if ($showModal)
        <div class="policy-modal-overlay" x-data @keydown.escape.window="$wire.closeModal()" @click.self="$wire.closeModal()">
            <div class="policy-modal" role="dialog" aria-modal="true">
                <div class="policy-modal-head">
                    <h3>{{ $isEditing ? 'Edit Policy' : 'Create Policy' }}</h3>
                    <button type="button" class="policy-modal-close" wire:click="closeModal" aria-label="Close">x</button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="policy-modal-body">
                        <div class="policy-grid-2">
                            <div class="policy-field @error('code') is-invalid @enderror">
                                <label>Code <span class="req">*</span></label>
                                <input type="text" wire:model="code" placeholder="e.g. POL-001">
                                @error('code') <span class="policy-error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="policy-field @error('version') is-invalid @enderror">
                                <label>Version <span class="req">*</span></label>
                                <input type="text" wire:model="version" placeholder="e.g. 1.0">
                                @error('version') <span class="policy-error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="policy-field @error('title') is-invalid @enderror">
                            <label>Title <span class="req">*</span></label>
                            <input type="text" wire:model="title" placeholder="Policy title">
                            @error('title') <span class="policy-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="policy-field @error('published_at') is-invalid @enderror">
                            <label>Published At</label>
                            <input type="datetime-local" wire:model="published_at">
                            @error('published_at') <span class="policy-error-text">{{ $message }}</span> @enderror
                        </div>

                        <div class="policy-toggle-row">
                            <input type="checkbox" wire:model="is_active" id="policy_is_active">
                            <label for="policy_is_active">Active</label>
                        </div>
                    </div>
                    <div class="policy-modal-footer">
                        <button type="button" class="btn btn-soft" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Update Policy' : 'Create Policy' }}</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- SweetAlert2 hooks: delete confirmation + toast notifications --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-policy-delete', (event) => {
                const { id, title } = event;
                Swal.fire({
                    icon: 'warning',
                    title: 'Delete this policy?',
                    html: 'Remove <b>' + title + '</b>? This cannot be undone.',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deletePolicyConfirmed', { id: id });
                    }
                });
            });

            Livewire.on('toast', (event) => {
                const { type, message } = event;
                Swal.fire({
                    // toast: true,
                    // position: 'top-end',
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