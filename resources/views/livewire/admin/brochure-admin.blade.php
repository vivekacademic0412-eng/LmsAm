{{-- resources/views/livewire/admin/brochure-admin.blade.php --}}

<div class="bro-admin">
<style>
    /* ═══════════════════════════════════════════════════════════════
   Brochure Admin — bro-admin.css
   Uses only the design tokens already defined in :root /
   [data-theme="dark"] — no colors are hardcoded here.
═══════════════════════════════════════════════════════════════ */

.bro-admin {
    color: var(--text);
    padding: 1.5rem;
}

/* ── Header ───────────────────────────────────────────────────── */
.bro-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}

.bro-header__left {
    display: flex;
    align-items: center;
    gap: .875rem;
}

.bro-header__icon {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    border-radius: var(--radius-sm);
    background: var(--primary-glow);
    color: var(--brand-primary);
}

.bro-header__icon svg { width: 22px; height: 22px; }

.bro-header__title {
    font-size: 1.375rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.bro-header__sub {
    margin: .2rem 0 0;
    font-size: .875rem;
    color: var(--text-muted);
}

/* ── Layout ───────────────────────────────────────────────────── */
.bro-layout {
    display: grid;
    grid-template-columns: 380px 1fr;
    gap: 1.5rem;
    align-items: start;
}

@media (max-width: 900px) {
    .bro-layout { grid-template-columns: 1fr; }
}

.bro-card {
    background: var(--bg-card);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    padding: 1.5rem;
}

.bro-card__title {
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 1.25rem;
    color: var(--text);
}

/* ── Form ─────────────────────────────────────────────────────── */
.bro-field { margin-bottom: 1.1rem; }
.bro-field:last-of-type { margin-bottom: 0; }

.bro-label {
    display: block;
    font-size: .8125rem;
    font-weight: 600;
    margin-bottom: .4rem;
    color: var(--text);
}

.bro-required { color: var(--danger); }

.bro-input {
    width: 100%;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: var(--radius-xs);
    color: var(--text);
    padding: .65rem .85rem;
    font-size: .875rem;
    transition: border-color .15s ease, box-shadow .15s ease;
}

.bro-input::placeholder { color: var(--text-muted); }

.bro-input:focus {
    outline: none;
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px var(--primary-glow);
}

.bro-input--error { border-color: var(--danger); }

.bro-error-msg {
    display: block;
    margin-top: .35rem;
    font-size: .75rem;
    color: var(--danger);
}

.bro-hint {
    margin: .5rem 0 0;
    font-size: .75rem;
    color: var(--text-muted);
}

/* ── Dropzone ─────────────────────────────────────────────────── */
.bro-dropzone {
    position: relative;
    display: block;
    border: 1.5px dashed var(--input-border);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: border-color .15s ease, background .15s ease;
}

.bro-dropzone:hover {
    border-color: var(--brand-primary);
    background: var(--primary-glow);
}

.bro-dropzone__input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
}

.bro-dropzone__body {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    padding: 1.5rem .75rem;
    text-align: center;
    color: var(--text-muted);
    font-size: .8125rem;
}

.bro-dropzone__body svg {
    width: 24px;
    height: 24px;
    margin-bottom: .2rem;
    color: var(--brand-primary);
}

.bro-dropzone__body span {
    color: var(--text);
    font-weight: 600;
    word-break: break-word;
}

.bro-dropzone__body small {
    font-size: .6875rem;
    color: var(--text-muted);
}

.bro-uploading {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-top: .5rem;
    font-size: .8125rem;
    color: var(--text-muted);
}

.bro-spinner {
    width: 14px;
    height: 14px;
    border: 2px solid var(--line);
    border-top-color: var(--brand-primary);
    border-radius: 50%;
    animation: bro-spin .7s linear infinite;
}

@keyframes bro-spin { to { transform: rotate(360deg); } }

/* ── Toggle ───────────────────────────────────────────────────── */
.bro-toggle {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin: 1.25rem 0;
    cursor: pointer;
}

.bro-toggle__input { display: none; }

.bro-toggle__track {
    position: relative;
    width: 42px;
    height: 24px;
    flex-shrink: 0;
    border-radius: 999px;
    background: var(--line);
    transition: background .15s ease;
}

.bro-toggle__track::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #fff;
    box-shadow: var(--shadow-sm);
    transition: transform .15s ease;
}

.bro-toggle__input:checked + .bro-toggle__track {
    background: var(--brand-primary);
}

.bro-toggle__input:checked + .bro-toggle__track::after {
    transform: translateX(18px);
}

.bro-toggle__label {
    display: flex;
    flex-direction: column;
    font-size: .8125rem;
}

.bro-toggle__label strong { color: var(--text); }
.bro-toggle__label small { color: var(--text-muted); }

/* ── Form actions ─────────────────────────────────────────────── */
.bro-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
}

.bro-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    border: none;
    border-radius: var(--radius-xs);
    font-weight: 600;
    font-size: .875rem;
    padding: .65rem 1.2rem;
    cursor: pointer;
    transition: opacity .15s ease, transform .1s ease, background .15s ease;
}

.bro-btn:active { transform: translateY(1px); }
.bro-btn:disabled { opacity: .6; cursor: not-allowed; }

.bro-btn--primary {
    background: var(--brand-primary);
    color: #fff;
}

.bro-btn--primary:hover { opacity: .92; }

.bro-btn--ghost {
    background: transparent;
    color: var(--text-muted);
    border: 1px solid var(--line);
}

.bro-btn--ghost:hover {
    color: var(--text);
    border-color: var(--input-border);
}

/* ── Empty state ──────────────────────────────────────────────── */
.bro-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .6rem;
    padding: 3rem 1rem;
    color: var(--text-muted);
    text-align: center;
}

.bro-empty svg {
    width: 40px;
    height: 40px;
    opacity: .5;
}

/* ── List ─────────────────────────────────────────────────────── */
.bro-list {
    display: flex;
    flex-direction: column;
    gap: .75rem;
}

.bro-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .9rem 1rem;
    border: 1px solid var(--line);
    border-radius: var(--radius-sm);
    background: var(--bg-card2);
    transition: border-color .15s ease, box-shadow .15s ease;
}

.bro-item:hover {
    border-color: var(--input-border);
    box-shadow: var(--shadow-sm);
}

.bro-item__icon {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    border-radius: var(--radius-xs);
    background: color-mix(in srgb, var(--danger) 12%, transparent);
    color: var(--danger);
}

.bro-item__icon svg { width: 20px; height: 20px; }

.bro-item__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: .2rem;
}

.bro-item__title {
    font-size: .9375rem;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.bro-item__meta {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .75rem;
    color: var(--text-muted);
}

.bro-item__dot {
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: var(--text-muted);
}

.bro-item__link {
    color: var(--brand-primary);
    text-decoration: none;
    font-weight: 600;
}

.bro-item__link:hover { text-decoration: underline; }

/* ── Status badge (also a toggle button) ─────────────────────── */
.bro-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .65rem;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    flex-shrink: 0;
    transition: opacity .15s ease;
}

.bro-badge:hover { opacity: .85; }

.bro-badge--active {
    background: color-mix(in srgb, var(--success) 14%, transparent);
    color: var(--success);
}

.bro-badge--inactive {
    background: color-mix(in srgb, var(--muted) 16%, transparent);
    color: var(--muted);
}

.bro-badge__dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* ── Item actions ─────────────────────────────────────────────── */
.bro-item__actions {
    display: flex;
    gap: .4rem;
    flex-shrink: 0;
}

.bro-icon-btn {
    width: 32px;
    height: 32px;
    display: grid;
    place-items: center;
    border: 1px solid var(--line);
    border-radius: var(--radius-xs);
    background: var(--bg-card);
    color: var(--text-muted);
    cursor: pointer;
    transition: color .15s ease, border-color .15s ease, background .15s ease;
}

.bro-icon-btn svg { width: 16px; height: 16px; }

.bro-icon-btn:hover {
    color: var(--brand-primary);
    border-color: var(--brand-primary);
}

.bro-icon-btn--danger:hover {
    color: var(--danger);
    border-color: var(--danger);
    background: color-mix(in srgb, var(--danger) 8%, var(--bg-card));
}

/* ── Small screens ────────────────────────────────────────────── */
@media (max-width: 560px) {
    .bro-item {
        flex-wrap: wrap;
    }

    .bro-item__body { flex-basis: 100%; order: 1; }
    .bro-badge { order: 2; }
    .bro-item__actions { order: 3; margin-left: auto; }
}
</style>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════════════════════ --}}
    <div class="bro-header">
        <div class="bro-header__left">
            <div class="bro-header__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <path d="M14 2v6h6"/>
                    <path d="M9 15h6M9 11h2"/>
                </svg>
            </div>
            <div>
                <h1 class="bro-header__title">Brochures</h1>
                <p class="bro-header__sub">Upload and manage downloadable PDF brochures</p>
            </div>
        </div>
    </div>

    <div class="bro-layout">

        {{-- ═══════════════════════════════════════════════════════
             FORM
        ═══════════════════════════════════════════════════════ --}}
        <div class="bro-card bro-form-card">
            <h2 class="bro-card__title">
                {{ $editingId ? 'Edit Brochure' : 'Add New Brochure' }}
            </h2>

            <form wire:submit.prevent="save" class="bro-form">
                <div class="bro-field">
                    <label class="bro-label">Title <span class="bro-required">*</span></label>
                    <input type="text" wire:model="title" class="bro-input @error('title') bro-input--error @enderror"
                           placeholder="e.g. AI Training Program Brochure">
                    @error('title') <span class="bro-error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="bro-field">
                    <label class="bro-label">
                        PDF File
                        @if (!$editingId) <span class="bro-required">*</span> @endif
                    </label>

                    <label class="bro-dropzone">
                        <input type="file" wire:model="file" accept="application/pdf" class="bro-dropzone__input">
                        <div class="bro-dropzone__body">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M8 12l4-4 4 4M12 8v8"/>
                            </svg>
                            <span>
                                @if ($file)
                                    {{ $file->getClientOriginalName() }}
                                @else
                                    Click or drag a PDF here
                                @endif
                            </span>
                            <small>Max 10 MB · PDF only</small>
                        </div>
                    </label>

                    <div wire:loading wire:target="file" class="bro-uploading">
                        <span class="bro-spinner"></span> Uploading…
                    </div>

                    @error('file') <span class="bro-error-msg">{{ $message }}</span> @enderror

                    @if ($editingId && !$file)
                        @php($current = $brochures->firstWhere('id', $editingId))
                        @if ($current && $current->file_path)
                            <p class="bro-hint">
                                Current file: <strong>{{ $current->original_name ?? basename($current->file_path) }}</strong>
                                — leave empty to keep it.
                            </p>
                        @endif
                    @endif
                </div>

                <label class="bro-toggle">
                    <input type="checkbox" wire:model="is_active" class="bro-toggle__input">
                    <span class="bro-toggle__track"></span>
                    <span class="bro-toggle__label">
                        <strong>{{ $is_active ? 'Active' : 'Inactive' }}</strong>
                        <small>{{ $is_active ? 'Visible for download on the site.' : 'Hidden from visitors.' }}</small>
                    </span>
                </label>

                <div class="bro-form-actions">
                    @if ($editingId)
                        <button type="button" wire:click="cancelEdit" class="bro-btn bro-btn--ghost">
                            Cancel
                        </button>
                    @endif
                    <button type="submit" wire:loading.attr="disabled" wire:target="save,file" class="bro-btn bro-btn--primary">
                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Update Brochure' : 'Add Brochure' }}
                        </span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             LIST
        ═══════════════════════════════════════════════════════ --}}
        <div class="bro-card bro-list-card">
            <h2 class="bro-card__title">All Brochures ({{ $brochures->count() }})</h2>

            @if ($brochures->isEmpty())
                <div class="bro-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <path d="M14 2v6h6"/>
                    </svg>
                    <p>No brochures uploaded yet.</p>
                </div>
            @else
                <div class="bro-list">
                    @foreach ($brochures as $brochure)
                        <div class="bro-item" wire:key="brochure-{{ $brochure->id }}">
                            <div class="bro-item__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                    <path d="M14 2v6h6"/>
                                </svg>
                            </div>

                            <div class="bro-item__body">
                                <strong class="bro-item__title">{{ $brochure->title }}</strong>
                                <div class="bro-item__meta">
                                    <span>{{ $brochure->file_size_human }}</span>
                                    <span class="bro-item__dot"></span>
                                    <span>{{ $brochure->created_at->format('d M Y') }}</span>
                                    @if ($brochure->file_url)
                                        <span class="bro-item__dot"></span>
                                        <a href="{{ $brochure->file_url }}" target="_blank" rel="noopener" class="bro-item__link">View PDF</a>
                                    @endif
                                </div>
                            </div>

                            <button wire:click="toggleActive({{ $brochure->id }})" type="button"
                                    class="bro-badge {{ $brochure->is_active ? 'bro-badge--active' : 'bro-badge--inactive' }}"
                                    title="Click to toggle">
                                <span class="bro-badge__dot"></span>
                                {{ $brochure->is_active ? 'Active' : 'Inactive' }}
                            </button>

                            <div class="bro-item__actions">
                                <button wire:click="edit({{ $brochure->id }})" type="button" class="bro-icon-btn" title="Edit">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.207 2.207L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                </button>
                                <button wire:click="delete({{ $brochure->id }})" type="button"
                                        wire:confirm="Delete '{{ $brochure->title }}'? This can't be undone."
                                        class="bro-icon-btn bro-icon-btn--danger" title="Delete">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    const toast = (title, icon) => {
        Swal.fire({
            title, icon, toast: true, position: 'top-end',
            showConfirmButton: false, timer: 2200, timerProgressBar: true,
            background: 'var(--bg-card)', color: 'var(--text)',
        });
    };

    Livewire.on('brochure-saved', (e) => toast(`"${e.title}" saved`, 'success'));
    Livewire.on('brochure-deleted', () => toast('Brochure deleted', 'info'));
});
</script>
@endpush