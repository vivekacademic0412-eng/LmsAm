<div class="rpm" x-data="{ dirty: @entangle('dirty') }">

    {{-- ══════════════════════════════════════════════
         HEADER — role tabs + save bar
    ══════════════════════════════════════════════ --}}
    <div class="rpm-head">
        <div class="rpm-head-top">
            <div>
                <h1 class="rpm-title">Role Permissions</h1>
                <p class="rpm-subtitle">Control what each role can view, create, edit, and delete across every module.</p>
            </div>

            <button
                type="button"
                class="rpm-save-btn"
                :class="{ 'is-active': dirty }"
                :disabled="!dirty"
                @click="
                    const res = await $wire.save();
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'Permissions updated for &quot;{{ ucfirst(str_replace('_',' ', $activeRole)) }}&quot;.',
                        confirmButtonColor: '#4338ca',
                        timer: 1800,
                        showConfirmButton: false,
                    });
                "
            >
                <svg viewBox="0 0 20 20" fill="none" class="rpm-save-icon"><path d="M4 10.5l3.5 3.5L16 5.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-text="dirty ? 'Save changes' : 'Saved'"></span>
            </button>
        </div>

        <div class="rpm-tabs" role="tablist" aria-label="Select role">
            @foreach ($roles as $role)
                <button
                    type="button"
                    role="tab"
                    aria-selected="{{ $activeRole === $role ? 'true' : 'false' }}"
                    class="rpm-tab {{ $activeRole === $role ? 'is-active' : '' }}"
                    @click.prevent="
                        if (dirty) {
                            const result = await Swal.fire({
                                icon: 'warning',
                                title: 'Discard unsaved changes?',
                                text: 'Switching roles now will lose what you changed for &quot;{{ ucfirst(str_replace('_',' ', $activeRole)) }}&quot;.',
                                showCancelButton: true,
                                confirmButtonText: 'Discard and switch',
                                cancelButtonText: 'Keep editing',
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#4338ca',
                                reverseButtons: true,
                            });
                            if (!result.isConfirmed) return;
                        }
                        $wire.switchRole('{{ $role }}');
                    "
                >
                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                </button>
            @endforeach
        </div>
    </div>

    @if (session('success'))
        <div class="rpm-flash" x-data x-init="Swal.fire({ icon: 'success', title: '{{ session('success') }}', timer: 1600, showConfirmButton: false })"></div>
    @endif

    @error('activeRole')
        <div class="rpm-error">{{ $message }}</div>
    @enderror

    {{-- ══════════════════════════════════════════════
         PERMISSION TREE
    ══════════════════════════════════════════════ --}}
    <div class="rpm-body">
        @forelse ($tree as $category)
            <section class="rpm-category">
                <h2 class="rpm-category-title">
                    <i class="{{ $category->icon }}" aria-hidden="true"></i>
                    {{ $category->name }}
                </h2>

                <div class="rpm-table">
                    <div class="rpm-row rpm-row--head">
                        <span class="rpm-col-label">Module</span>
                        <span class="rpm-col-flag">View</span>
                        <span class="rpm-col-flag">Create</span>
                        <span class="rpm-col-flag">Edit</span>
                        <span class="rpm-col-flag">Delete</span>
                        <span class="rpm-col-bulk">All / None</span>
                    </div>

                    @foreach ($category->modules as $module)
                        @php $p = $this->permissions[$module->id] ?? ['view'=>false,'create'=>false,'edit'=>false,'delete'=>false]; @endphp

                        <div class="rpm-row">
                            <span class="rpm-col-label">
                                @if ($module->icon)
                                    <i class="{{ $module->icon }}" aria-hidden="true"></i>
                                @endif
                                {{ $module->label }}
                            </span>

                            @foreach (['view', 'create', 'edit', 'delete'] as $action)
                                <span class="rpm-col-flag">
                                    <button
                                        type="button"
                                        class="rpm-pill rpm-pill--{{ $action }} {{ $p[$action] ? 'is-on' : '' }}"
                                        wire:click="toggle({{ $module->id }}, '{{ $action }}')"
                                        aria-pressed="{{ $p[$action] ? 'true' : 'false' }}"
                                        aria-label="{{ ucfirst($action) }} — {{ $module->label }}"
                                    >{{ strtoupper(substr($action, 0, 1)) }}</button>
                                </span>
                            @endforeach

                            <span class="rpm-col-bulk">
                                <button type="button" class="rpm-bulk-btn" wire:click="toggleAllForModule({{ $module->id }}, true)">All</button>
                                <button type="button" class="rpm-bulk-btn rpm-bulk-btn--muted" wire:click="toggleAllForModule({{ $module->id }}, false)">None</button>
                            </span>
                        </div>

                        @foreach ($module->children as $child)
                            @php $cp = $this->permissions[$child->id] ?? ['view'=>false,'create'=>false,'edit'=>false,'delete'=>false]; @endphp

                            <div class="rpm-row rpm-row--child">
                                <span class="rpm-col-label rpm-col-label--child">
                                    <span class="rpm-branch" aria-hidden="true"></span>
                                    @if ($child->icon)
                                        <i class="{{ $child->icon }}" aria-hidden="true"></i>
                                    @endif
                                    {{ $child->label }}
                                </span>

                                @foreach (['view', 'create', 'edit', 'delete'] as $action)
                                    <span class="rpm-col-flag">
                                        <button
                                            type="button"
                                            class="rpm-pill rpm-pill--{{ $action }} rpm-pill--sm {{ $cp[$action] ? 'is-on' : '' }}"
                                            wire:click="toggle({{ $child->id }}, '{{ $action }}')"
                                            aria-pressed="{{ $cp[$action] ? 'true' : 'false' }}"
                                            aria-label="{{ ucfirst($action) }} — {{ $child->label }}"
                                        >{{ strtoupper(substr($action, 0, 1)) }}</button>
                                    </span>
                                @endforeach

                                <span class="rpm-col-bulk">
                                    <button type="button" class="rpm-bulk-btn" wire:click="toggleAllForModule({{ $child->id }}, true)">All</button>
                                    <button type="button" class="rpm-bulk-btn rpm-bulk-btn--muted" wire:click="toggleAllForModule({{ $child->id }}, false)">None</button>
                                </span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </section>
        @empty
            <div class="rpm-empty">
                <p>No modules yet. Run the module seeder to populate the navigation tree.</p>
            </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════
         MOBILE STICKY SAVE BAR
    ══════════════════════════════════════════════ --}}
    <div class="rpm-mobile-bar" x-show="dirty" x-transition x-cloak>
        <span>Unsaved changes</span>
        <button
            type="button"
            @click="
                await $wire.save();
                Swal.fire({ icon: 'success', title: 'Saved', timer: 1400, showConfirmButton: false });
            "
        >Save changes</button>
    </div>
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
@endonce

<style>
    :root {
        --rpm-ink: #1e1b2e;
        --rpm-ink-soft: #5b5670;
        --rpm-line: #e6e3f0;
        --rpm-bg: #faf9fd;
        --rpm-surface: #ffffff;
        --rpm-indigo: #4338ca;
        --rpm-indigo-soft: #eef0fd;
        --rpm-green: #15803d;
        --rpm-green-soft: #e9f7ef;
        --rpm-amber: #b45309;
        --rpm-amber-soft: #fdf1e3;
        --rpm-rose: #be123c;
        --rpm-rose-soft: #fdeaf0;
        --rpm-radius: 10px;
    }

    .rpm { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; color: var(--rpm-ink); background: var(--rpm-bg); padding: 28px; border-radius: 16px; }
    [x-cloak] { display: none !important; }

    /* ── Header ───────────────────────────────── */
    .rpm-head-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .rpm-title { font-size: 22px; font-weight: 700; letter-spacing: -0.01em; margin: 0; }
    .rpm-subtitle { font-size: 13.5px; color: var(--rpm-ink-soft); margin: 4px 0 0; max-width: 46ch; }

    .rpm-save-btn {
        display: inline-flex; align-items: center; gap: 8px;
        background: #d8d5e6; color: #8b87a0;
        border: none; border-radius: var(--rpm-radius);
        padding: 10px 18px; font-size: 14px; font-weight: 600;
        cursor: not-allowed; transition: background .15s ease, color .15s ease, transform .1s ease;
    }
    .rpm-save-btn.is-active { background: var(--rpm-indigo); color: #fff; cursor: pointer; }
    .rpm-save-btn.is-active:hover { background: #372f9e; transform: translateY(-1px); }
    .rpm-save-icon { width: 16px; height: 16px; }

    /* ── Tabs ─────────────────────────────────── */
    .rpm-tabs { display: flex; gap: 6px; margin-top: 18px; flex-wrap: wrap; border-bottom: 1px solid var(--rpm-line); padding-bottom: 0; }
    .rpm-tab {
        background: transparent; border: none; padding: 9px 14px 11px;
        font-size: 13.5px; font-weight: 600; color: var(--rpm-ink-soft);
        cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px;
        transition: color .15s ease, border-color .15s ease;
    }
    .rpm-tab:hover { color: var(--rpm-ink); }
    .rpm-tab.is-active { color: var(--rpm-indigo); border-bottom-color: var(--rpm-indigo); }

    .rpm-error { margin-top: 12px; background: var(--rpm-rose-soft); color: var(--rpm-rose); padding: 10px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; }

    /* ── Body / Category ──────────────────────── */
    .rpm-body { margin-top: 24px; display: flex; flex-direction: column; gap: 22px; }
    .rpm-category-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
        color: var(--rpm-ink-soft); margin: 0 0 8px 2px;
    }

    .rpm-table { background: var(--rpm-surface); border: 1px solid var(--rpm-line); border-radius: 12px; overflow: hidden; }

    .rpm-row {
        display: grid;
        grid-template-columns: 1fr repeat(4, 64px) 96px;
        align-items: center;
        padding: 10px 16px;
        border-top: 1px solid var(--rpm-line);
        gap: 4px;
    }
    .rpm-row:first-child { border-top: none; }
    .rpm-row--head {
        background: #f5f4fb; font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .05em; color: var(--rpm-ink-soft); padding: 9px 16px;
    }
    .rpm-row--child { background: #fcfcfe; }

    .rpm-col-label { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; }
    .rpm-col-label i { color: var(--rpm-indigo); font-size: 14px; width: 16px; text-align: center; }
    .rpm-col-label--child { font-weight: 500; padding-left: 22px; position: relative; }
    .rpm-branch { position: absolute; left: 6px; top: -14px; bottom: 50%; width: 10px; border-left: 1.5px solid var(--rpm-line); border-bottom: 1.5px solid var(--rpm-line); border-radius: 0 0 0 6px; }

    .rpm-col-flag { display: flex; justify-content: center; }
    .rpm-col-bulk { display: flex; gap: 6px; justify-content: flex-end; }

    /* ── Permission pills ─────────────────────── */
    .rpm-pill {
        width: 30px; height: 30px; border-radius: 50%;
        border: 1.5px solid var(--rpm-line); background: var(--rpm-surface);
        font-size: 11.5px; font-weight: 700; color: #b7b3c9;
        cursor: pointer; transition: all .12s ease;
    }
    .rpm-pill--sm { width: 26px; height: 26px; font-size: 10.5px; }
    .rpm-pill:hover { border-color: var(--rpm-indigo); color: var(--rpm-indigo); }

    .rpm-pill--view.is-on   { background: var(--rpm-indigo-soft); border-color: var(--rpm-indigo); color: var(--rpm-indigo); }
    .rpm-pill--create.is-on { background: var(--rpm-green-soft);  border-color: var(--rpm-green);  color: var(--rpm-green); }
    .rpm-pill--edit.is-on   { background: var(--rpm-amber-soft);  border-color: var(--rpm-amber);  color: var(--rpm-amber); }
    .rpm-pill--delete.is-on { background: var(--rpm-rose-soft);   border-color: var(--rpm-rose);   color: var(--rpm-rose); }

    .rpm-bulk-btn {
        background: transparent; border: 1px solid var(--rpm-line); border-radius: 6px;
        font-size: 11px; font-weight: 600; color: var(--rpm-ink-soft);
        padding: 4px 8px; cursor: pointer; transition: all .12s ease;
    }
    .rpm-bulk-btn:hover { border-color: var(--rpm-indigo); color: var(--rpm-indigo); }
    .rpm-bulk-btn--muted:hover { border-color: var(--rpm-rose); color: var(--rpm-rose); }

    .rpm-empty { background: var(--rpm-surface); border: 1px dashed var(--rpm-line); border-radius: 12px; padding: 40px; text-align: center; color: var(--rpm-ink-soft); font-size: 14px; }

    /* ── Mobile sticky save bar ────────────────── */
    .rpm-mobile-bar {
        display: none; position: fixed; left: 16px; right: 16px; bottom: 16px;
        background: var(--rpm-ink); color: #fff; border-radius: 12px;
        padding: 12px 16px; align-items: center; justify-content: space-between;
        box-shadow: 0 10px 30px rgba(30, 27, 46, .25); z-index: 40;
    }
    .rpm-mobile-bar button { background: var(--rpm-indigo); color: #fff; border: none; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 600; }

    @media (max-width: 720px) {
        .rpm { padding: 18px; }
        .rpm-row { grid-template-columns: 1fr repeat(4, 44px); }
        .rpm-col-bulk { display: none; }
        .rpm-save-btn { display: none; }
        .rpm-mobile-bar { display: flex; }
    }
</style>