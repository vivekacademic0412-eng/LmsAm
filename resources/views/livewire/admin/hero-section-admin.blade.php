{{-- resources/views/livewire/admin/hero-section-admin.blade.php --}}

<div x-data="{ activeTab: 'content' }" class="hs-admin">

    {{-- ═══════════════════════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════════════════════ --}}
    <div class="hs-page-header">
        <div class="hs-page-header__left">
            <div class="hs-page-header__icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="2" y="3" width="20" height="14" rx="3"/>
                    <path d="M8 21h8M12 17v4"/>
                </svg>
            </div>
            <div>
                <h1 class="hs-page-header__title">Hero Section</h1>
                <p class="hs-page-header__sub">Manage your homepage hero content, stats & ratings</p>
            </div>
        </div>
        <div class="hs-page-header__right">
            <span class="hs-badge {{ $is_active ? 'hs-badge--active' : 'hs-badge--draft' }}">
                <span class="hs-badge__dot"></span>
                {{ $is_active ? 'Active' : 'Draft' }}
            </span>
            <button wire:click="save" wire:loading.attr="disabled" class="hs-btn hs-btn--primary">
                <span wire:loading.remove wire:target="save">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="hs-btn__icon">
                        <path d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z"/>
                    </svg>
                    Save Changes
                </span>
                <span wire:loading wire:target="save" class="hs-btn__loading">
                    <svg class="hs-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10" stroke-opacity=".25"/>
                        <path d="M12 2a10 10 0 0110 10"/>
                    </svg>
                    Saving…
                </span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         VALIDATION ERRORS BANNER
    ═══════════════════════════════════════════════════════════ --}}
    @if ($errors->any())
    <div class="hs-error-banner">
        <svg viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            <strong>Fix {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} before saving</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         TAB NAV
    ═══════════════════════════════════════════════════════════ --}}
    <div class="hs-tabs">
        <button class="hs-tab" :class="{ 'hs-tab--active': activeTab === 'content' }"
                @click="activeTab = 'content'">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4zm2 2h4v1H8V6zm0 3h4v1H8V9zm0 3h2v1H8v-1z" clip-rule="evenodd"/></svg>
            Content
        </button>
        <button class="hs-tab" :class="{ 'hs-tab--active': activeTab === 'media' }"
                @click="activeTab = 'media'">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
            Media
        </button>
        <button class="hs-tab" :class="{ 'hs-tab--active': activeTab === 'stats' }"
                @click="activeTab = 'stats'">
            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
            Stats & Ratings
        </button>
        <button class="hs-tab" :class="{ 'hs-tab--active': activeTab === 'guide' }"
                @click="activeTab = 'guide'">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            Guide Card
        </button>
        <button class="hs-tab" :class="{ 'hs-tab--active': activeTab === 'settings' }"
                @click="activeTab = 'settings'">
            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
            Settings
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         TAB: CONTENT
    ═══════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'content'" x-transition:enter="hs-fade-in">
        <div class="hs-grid-2">

            {{-- Heading Builder --}}
            <div class="hs-card hs-card--full">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-primary)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.207 2.207L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Heading Builder</h2>
                        <p class="hs-card__subtitle">Compose the hero headline with styled segments</p>
                    </div>
                </div>

                <div class="hs-heading-preview">
                    <span class="hs-hp-prefix">{{ $heading_prefix ?: 'Prefix' }}</span>
                    @if($heading_highlight)
                        <span class="hs-hp-highlight">{{ $heading_highlight }}</span>
                    @endif
                    @if($heading_bold)
                        <span class="hs-hp-bold">{{ $heading_bold }}</span>
                    @endif
                    @if($heading_suffix)
                        <span class="hs-hp-suffix">{{ $heading_suffix }}</span>
                    @endif
                </div>

                <div class="hs-grid-2 hs-grid--gap-sm">
                    <div class="hs-field">
                        <label class="hs-label">Prefix <span class="hs-required">*</span></label>
                        <input wire:model.live.debounce.300ms="heading_prefix" type="text"
                               class="hs-input @error('heading_prefix') hs-input--error @enderror"
                               placeholder="e.g. The fastest way to">
                        @error('heading_prefix') <span class="hs-error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="hs-field">
                        <label class="hs-label">Highlight <span class="hs-label-hint">(accent color)</span></label>
                        <input wire:model.live.debounce.300ms="heading_highlight" type="text"
                               class="hs-input"
                               placeholder="e.g. grow">
                    </div>
                    <div class="hs-field">
                        <label class="hs-label">Bold segment</label>
                        <input wire:model.live.debounce.300ms="heading_bold" type="text"
                               class="hs-input"
                               placeholder="e.g. your business">
                    </div>
                    <div class="hs-field">
                        <label class="hs-label">Suffix</label>
                        <input wire:model.live.debounce.300ms="heading_suffix" type="text"
                               class="hs-input"
                               placeholder="e.g. online.">
                    </div>
                </div>
            </div>

            {{-- Lede --}}
            <div class="hs-card hs-card--full">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-secondary)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Sub-heading / Lede</h2>
                        <p class="hs-card__subtitle">Supporting text beneath the headline</p>
                    </div>
                </div>
                <div class="hs-field">
                    <label class="hs-label">Lede text <span class="hs-required">*</span></label>
                    <textarea wire:model.live.debounce.300ms="lede" rows="4"
                              class="hs-input hs-textarea @error('lede') hs-input--error @enderror"
                              placeholder="Describe your value proposition in 1–2 sentences…"></textarea>
                    <div class="hs-field-meta">
                        <span @error('lede') class="hs-error-msg" @else class="hs-hint" @enderror>
                            @error('lede') {{ $message }} @else {{ strlen($lede) }}/1000 @enderror
                        </span>
                    </div>
                </div>
            </div>

            {{-- CTA Buttons --}}
            <div class="hs-card hs-card--full">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-green)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"/><path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Call-to-Action Buttons</h2>
                        <p class="hs-card__subtitle">Primary and secondary CTAs in the hero</p>
                    </div>
                </div>
                <div class="hs-cta-grid">
                    <div class="hs-cta-block hs-cta-block--primary">
                        <div class="hs-cta-block__label">
                            <span class="hs-cta-pill hs-cta-pill--primary">Primary</span>
                        </div>
                        <div class="hs-field">
                            <label class="hs-label">Button label <span class="hs-required">*</span></label>
                            <input wire:model="cta_primary_label" type="text"
                                   class="hs-input @error('cta_primary_label') hs-input--error @enderror"
                                   placeholder="e.g. Get started free">
                            @error('cta_primary_label') <span class="hs-error-msg">{{ $message }}</span> @enderror
                        </div>
                        <div class="hs-field">
                            <label class="hs-label">URL <span class="hs-required">*</span></label>
                            <input wire:model="cta_primary_url" type="url"
                                   class="hs-input @error('cta_primary_url') hs-input--error @enderror"
                                   placeholder="https://…">
                            @error('cta_primary_url') <span class="hs-error-msg">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="hs-cta-block hs-cta-block--secondary">
                        <div class="hs-cta-block__label">
                            <span class="hs-cta-pill hs-cta-pill--secondary">Secondary</span>
                        </div>
                        <div class="hs-field">
                            <label class="hs-label">Button label</label>
                            <input wire:model="cta_secondary_label" type="text"
                                   class="hs-input"
                                   placeholder="e.g. See how it works">
                        </div>
                        <div class="hs-field">
                            <label class="hs-label">URL</label>
                            <input wire:model="cta_secondary_url" type="url"
                                   class="hs-input @error('cta_secondary_url') hs-input--error @enderror"
                                   placeholder="https://…">
                            @error('cta_secondary_url') <span class="hs-error-msg">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         TAB: MEDIA
    ═══════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'media'" x-transition:enter="hs-fade-in" style="display:none">
        <div class="hs-grid-3">

            {{-- Logo --}}
            <div class="hs-card">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-primary)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Logo</h2>
                        <p class="hs-card__subtitle">PNG / SVG recommended</p>
                    </div>
                </div>
                @if($logo_path)
                    <div class="hs-media-preview">
                        <img src="{{ Storage::url($logo_path) }}" alt="Logo" class="hs-media-preview__img hs-media-preview__img--contain">
                        <span class="hs-media-preview__tag">Current</span>
                    </div>
                @endif
                @if($logo)
                    <div class="hs-media-preview">
                        <img src="{{ $logo->temporaryUrl() }}" alt="Logo preview" class="hs-media-preview__img hs-media-preview__img--contain">
                        <span class="hs-media-preview__tag hs-media-preview__tag--new">New</span>
                    </div>
                @endif
                <label class="hs-upload-zone">
                    <input wire:model="logo" type="file" accept="image/*" class="hs-upload-zone__input">
                    <div class="hs-upload-zone__body">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M8 12l4-4 4 4M12 8v8"/>
                        </svg>
                        <span>Click or drag to upload</span>
                        <small>Max 2 MB · PNG, JPG, WebP, SVG</small>
                    </div>
                </label>
                @error('logo') <span class="hs-error-msg">{{ $message }}</span> @enderror
            </div>

            {{-- Mascot --}}
            <div class="hs-card">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-secondary)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Mascot Image</h2>
                        <p class="hs-card__subtitle">Hero illustration or character</p>
                    </div>
                </div>
                @if($mascot_image)
                    <div class="hs-media-preview hs-media-preview--tall">
                        <img src="{{ Storage::url($mascot_image) }}" alt="Mascot" class="hs-media-preview__img">
                        <span class="hs-media-preview__tag">Current</span>
                    </div>
                @endif
                @if($mascot)
                    <div class="hs-media-preview hs-media-preview--tall">
                        <img src="{{ $mascot->temporaryUrl() }}" alt="Mascot preview" class="hs-media-preview__img">
                        <span class="hs-media-preview__tag hs-media-preview__tag--new">New</span>
                    </div>
                @endif
                <label class="hs-upload-zone">
                    <input wire:model="mascot" type="file" accept="image/*" class="hs-upload-zone__input">
                    <div class="hs-upload-zone__body">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M8 12l4-4 4 4M12 8v8"/>
                        </svg>
                        <span>Click or drag to upload</span>
                        <small>Max 4 MB · PNG, JPG, WebP, SVG</small>
                    </div>
                </label>
                @error('mascot') <span class="hs-error-msg">{{ $message }}</span> @enderror
            </div>

            {{-- Hand Images --}}
            <div class="hs-card">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-accent)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Hand Images</h2>
                        <p class="hs-card__subtitle">Gallery of hand / device images</p>
                    </div>
                </div>

                @if(!empty($hand_images))
                    <div class="hs-hand-grid">
                        @foreach($hand_images as $i => $path)
                            <div class="hs-hand-item">
                                <img src="{{ Storage::url($path) }}" alt="Hand {{ $i+1 }}" class="hs-hand-item__img">
                                <button wire:click="removeHandImage({{ $i }})" type="button" class="hs-hand-item__remove" title="Remove">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <label class="hs-upload-zone">
                    <input wire:model="new_hand_images" type="file" accept="image/*" multiple class="hs-upload-zone__input">
                    <div class="hs-upload-zone__body">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M8 12l4-4 4 4M12 8v8"/>
                        </svg>
                        <span>Upload multiple images</span>
                        <small>Max 2 MB each · PNG, JPG, WebP</small>
                    </div>
                </label>
                @error('new_hand_images.*') <span class="hs-error-msg">{{ $message }}</span> @enderror
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         TAB: STATS & RATINGS
    ═══════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'stats'" x-transition:enter="hs-fade-in" style="display:none">
        <div class="hs-grid-2">

            {{-- Stats --}}
            <div class="hs-card">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-primary)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Hero Stats</h2>
                        <p class="hs-card__subtitle">Key numbers displayed in the hero</p>
                    </div>
                </div>

                <div class="hs-repeater">
                    @foreach($stats as $i => $stat)
                        <div class="hs-repeater__row" wire:key="stat-{{ $i }}">
                            <div class="hs-repeater__index">{{ $i + 1 }}</div>
                            <div class="hs-repeater__fields">
                                <div class="hs-field">
                                    <label class="hs-label">Value <span class="hs-required">*</span></label>
                                    <input wire:model="stats.{{ $i }}.number" type="text"
                                           class="hs-input hs-input--mono @error('stats.'.$i.'.number') hs-input--error @enderror"
                                           placeholder="e.g. 10K+">
                                    @error('stats.'.$i.'.number') <span class="hs-error-msg">{{ $message }}</span> @enderror
                                </div>
                                <div class="hs-field">
                                    <label class="hs-label">Label <span class="hs-required">*</span></label>
                                    <input wire:model="stats.{{ $i }}.label" type="text"
                                           class="hs-input @error('stats.'.$i.'.label') hs-input--error @enderror"
                                           placeholder="e.g. Happy customers">
                                    @error('stats.'.$i.'.label') <span class="hs-error-msg">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if(count($stats) > 1)
                                <button wire:click="removeStat({{ $i }})" type="button" class="hs-repeater__remove" title="Remove row">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button wire:click="addStat" type="button" class="hs-btn hs-btn--ghost hs-btn--sm">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="hs-btn__icon"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                    Add Stat
                </button>
            </div>

            {{-- Ratings --}}
            <div class="hs-card">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-accent)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Ratings</h2>
                        <p class="hs-card__subtitle">Platform ratings shown alongside the hero</p>
                    </div>
                </div>

                <div class="hs-repeater">
                    @foreach($ratings as $i => $rating)
                        <div class="hs-repeater__row" wire:key="rating-{{ $i }}">
                            <div class="hs-repeater__index">{{ $i + 1 }}</div>
                            <div class="hs-repeater__fields">
                                <div class="hs-field">
                                    <label class="hs-label">Score <span class="hs-required">*</span></label>
                                    <input wire:model="ratings.{{ $i }}.score" type="number" step="0.1" min="0" max="10"
                                           class="hs-input hs-input--mono @error('ratings.'.$i.'.score') hs-input--error @enderror"
                                           placeholder="e.g. 4.9">
                                    @error('ratings.'.$i.'.score') <span class="hs-error-msg">{{ $message }}</span> @enderror
                                </div>
                                <div class="hs-field">
                                    <label class="hs-label">Platform / Label <span class="hs-required">*</span></label>
                                    <input wire:model="ratings.{{ $i }}.label" type="text"
                                           class="hs-input @error('ratings.'.$i.'.label') hs-input--error @enderror"
                                           placeholder="e.g. Trustpilot">
                                    @error('ratings.'.$i.'.label') <span class="hs-error-msg">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @if(count($ratings) > 1)
                                <button wire:click="removeRating({{ $i }})" type="button" class="hs-repeater__remove">
                                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button wire:click="addRating" type="button" class="hs-btn hs-btn--ghost hs-btn--sm">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="hs-btn__icon"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                    Add Rating
                </button>
            </div>

        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         TAB: GUIDE CARD
    ═══════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'guide'" x-transition:enter="hs-fade-in" style="display:none">
        <div class="hs-grid-2">
            <div class="hs-card">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--brand-secondary)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Guide / Testimonial Card</h2>
                        <p class="hs-card__subtitle">Social proof card shown in the hero area</p>
                    </div>
                </div>
                <div class="hs-field">
                    <label class="hs-label">Tag / Badge</label>
                    <input wire:model.live="guide_tag" type="text" class="hs-input" placeholder="e.g. ✦ Featured Guide">
                </div>
                <div class="hs-field">
                    <label class="hs-label">Name</label>
                    <input wire:model.live="guide_name" type="text" class="hs-input" placeholder="e.g. Sarah Johnson">
                </div>
                <div class="hs-field">
                    <label class="hs-label">Quote / text</label>
                    <textarea wire:model.live="guide_text" rows="3" class="hs-input hs-textarea"
                              placeholder="Short quote or description…"></textarea>
                </div>
            </div>

            {{-- Live preview --}}
            <div class="hs-card hs-card--preview-bg">
                <p class="hs-preview-label">Live Preview</p>
                <div class="hs-guide-preview">
                    @if($guide_tag)
                        <span class="hs-guide-preview__tag">{{ $guide_tag }}</span>
                    @endif
                    @if($guide_name)
                        <strong class="hs-guide-preview__name">{{ $guide_name }}</strong>
                    @endif
                    @if($guide_text)
                        <p class="hs-guide-preview__text">{{ $guide_text }}</p>
                    @endif
                    @if(!$guide_tag && !$guide_name && !$guide_text)
                        <p class="hs-guide-preview__empty">Fill in the fields to preview</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         TAB: SETTINGS
    ═══════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'settings'" x-transition:enter="hs-fade-in" style="display:none">
        <div class="hs-grid-2">
            <div class="hs-card">
                <div class="hs-card__header">
                    <div class="hs-card__header-icon" style="--icon-color: var(--info)">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h2 class="hs-card__title">Visibility</h2>
                        <p class="hs-card__subtitle">Control whether the hero section is shown</p>
                    </div>
                </div>
                <label class="hs-toggle">
                    <input wire:model="is_active" type="checkbox" class="hs-toggle__input">
                    <span class="hs-toggle__track"></span>
                    <span class="hs-toggle__label">
                        <strong>{{ $is_active ? 'Hero is visible' : 'Hero is hidden' }}</strong>
                        <small>{{ $is_active ? 'The section appears on your homepage.' : 'The section is hidden from visitors.' }}</small>
                    </span>
                </label>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         STICKY SAVE BAR
    ═══════════════════════════════════════════════════════════ --}}
    <div class="hs-save-bar">
        <span class="hs-save-bar__hint">Unsaved changes will be lost on refresh</span>
        <button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="hs-btn hs-btn--primary">
            <span wire:loading.remove wire:target="save">Save Changes</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     SweetAlert2 + JS
═══════════════════════════════════════════════════════════ --}}
@push('scripts')

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('saved', () => {
        Swal.fire({
            title: 'Saved!',
            text: 'Hero section updated successfully.',
            icon: 'success',
            confirmButtonText: 'Great',
            confirmButtonColor: '#0947a8',
            background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#111d2e' : '#fff',
            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#f5f9ff' : '#0e1f36',
            iconColor: '#16a34a',
            timer: 3000,
            timerProgressBar: true,
            showClass: { popup: 'swal2-show' },
            hideClass: { popup: 'swal2-hide' },
        });
    });
});
</script>
@endpush

<style>
/* ── Base ──────────────────────────────────────────────────── */
.hs-admin { padding: 0 0 80px; }
.hs-fade-in { animation: hsFadeIn .2s ease; }
@keyframes hsFadeIn { from { opacity:0; transform:translateY(6px) } to { opacity:1; transform:none } }
@keyframes hsSpin   { to { transform: rotate(360deg) } }
.hs-spin { animation: hsSpin .8s linear infinite; width:16px; height:16px; }

/* ── Page Header ───────────────────────────────────────────── */
.hs-page-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 24px 0 20px; gap: 16px; flex-wrap: wrap;
}
.hs-page-header__left { display: flex; align-items: center; gap: 14px; }
.hs-page-header__icon {
    width: 48px; height: 48px; border-radius: var(--radius-sm);
    background: var(--primary-glow); display: flex; align-items: center; justify-content: center;
    color: var(--brand-primary); flex-shrink: 0;
}
.hs-page-header__icon svg { width: 24px; height: 24px; }
.hs-page-header__title { font-size: 1.5rem; font-weight: 700; color: var(--text); margin: 0; }
.hs-page-header__sub   { font-size: .875rem; color: var(--muted); margin: 2px 0 0; }
.hs-page-header__right { display: flex; align-items: center; gap: 12px; }

/* ── Badge ─────────────────────────────────────────────────── */
.hs-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 999px; font-size: .8rem; font-weight: 600;
}
.hs-badge--active  { background: rgba(22,163,74,.12); color: var(--success); }
.hs-badge--draft   { background: rgba(217,119,6,.12);  color: var(--warning); }
.hs-badge__dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

/* ── Buttons ───────────────────────────────────────────────── */
.hs-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 20px; border-radius: var(--radius-xs);
    font-size: .875rem; font-weight: 600; cursor: pointer;
    border: 1.5px solid transparent; transition: all .18s ease;
}
.hs-btn--primary {
    background: var(--brand-primary); color: #fff;
    border-color: var(--brand-primary);
}
.hs-btn--primary:hover:not(:disabled) { background: var(--primary-dark); }
.hs-btn--primary:disabled { opacity: .65; cursor: not-allowed; }
.hs-btn--ghost {
    background: transparent; color: var(--brand-primary);
    border-color: var(--input-border);
}
.hs-btn--ghost:hover { background: var(--primary-glow); border-color: var(--brand-primary); }
.hs-btn--sm { padding: 7px 14px; font-size: .8125rem; }
.hs-btn__icon { width: 16px; height: 16px; flex-shrink: 0; }
.hs-btn__loading { display: inline-flex; align-items: center; gap: 6px; }

/* ── Error Banner ──────────────────────────────────────────── */
.hs-error-banner {
    display: flex; gap: 12px; align-items: flex-start;
    background: rgba(220,38,38,.08); border: 1.5px solid rgba(220,38,38,.25);
    border-radius: var(--radius-sm); padding: 14px 16px;
    color: var(--danger); margin-bottom: 20px;
}
.hs-error-banner svg { width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px; }
.hs-error-banner strong { display: block; font-size: .9rem; margin-bottom: 4px; }
.hs-error-banner ul { margin: 0; padding-left: 16px; font-size: .8125rem; }
.hs-error-banner li { margin: 2px 0; }

/* ── Tabs ──────────────────────────────────────────────────── */
.hs-tabs {
    display: flex; gap: 4px; border-bottom: 2px solid var(--line);
    margin-bottom: 24px; overflow-x: auto; padding-bottom: 0;
}
.hs-tab {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 16px 11px; font-size: .875rem; font-weight: 500;
    color: var(--muted); border: none; background: none; cursor: pointer;
    border-bottom: 2px solid transparent; margin-bottom: -2px;
    border-radius: var(--radius-xs) var(--radius-xs) 0 0;
    transition: color .15s, border-color .15s, background .15s;
    white-space: nowrap;
}
.hs-tab svg { width: 16px; height: 16px; }
.hs-tab:hover { color: var(--text); background: var(--primary-glow); }
.hs-tab--active { color: var(--brand-primary); border-bottom-color: var(--brand-primary); font-weight: 600; }

/* ── Grid ──────────────────────────────────────────────────── */
.hs-grid-2 { display: grid; grid-template-columns: repeat(auto-fill, minmax(420px, 1fr)); gap: 20px; }
.hs-grid-3 { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.hs-grid--gap-sm { gap: 14px; }
.hs-card--full { grid-column: 1 / -1; }

/* ── Card ──────────────────────────────────────────────────── */
.hs-card {
    background: var(--bg-card); border: 1.5px solid var(--line);
    border-radius: var(--radius); padding: 24px;
    box-shadow: var(--shadow-card);
    display: flex; flex-direction: column; gap: 18px;
}
.hs-card--preview-bg { background: var(--bg2); }
.hs-card__header { display: flex; align-items: flex-start; gap: 12px; }
.hs-card__header-icon {
    width: 38px; height: 38px; border-radius: var(--radius-xs);
    background: rgba(from var(--icon-color) r g b / .12);
    color: var(--icon-color, var(--brand-primary));
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.hs-card__header-icon svg { width: 18px; height: 18px; }
.hs-card__title { font-size: 1rem; font-weight: 700; color: var(--text); margin: 0 0 2px; }
.hs-card__subtitle { font-size: .8125rem; color: var(--muted); margin: 0; }

/* ── Heading preview ───────────────────────────────────────── */
.hs-heading-preview {
    background: var(--bg2); border-radius: var(--radius-sm);
    padding: 20px; font-size: 1.4rem; font-weight: 700;
    line-height: 1.3; color: var(--text); min-height: 64px;
    border: 1.5px dashed var(--line);
}
.hs-hp-prefix  { color: var(--text); }
.hs-hp-highlight { color: var(--brand-primary); margin: 0 4px; }
.hs-hp-bold    { font-weight: 800; margin: 0 4px; }
.hs-hp-suffix  { color: var(--muted); margin-left: 4px; }

/* ── Fields ────────────────────────────────────────────────── */
.hs-field { display: flex; flex-direction: column; gap: 6px; }
.hs-label { font-size: .8125rem; font-weight: 600; color: var(--text); }
.hs-label-hint { font-weight: 400; color: var(--muted); margin-left: 4px; }
.hs-required { color: var(--danger); }
.hs-input {
    width: 100%; padding: 10px 14px; font-size: .9rem;
    background: var(--input-bg); color: var(--text);
    border: 1.5px solid var(--input-border); border-radius: var(--radius-xs);
    transition: border-color .15s, box-shadow .15s; outline: none;
    font-family: inherit;
}
.hs-input:focus { border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--primary-glow); }
.hs-input--error { border-color: var(--danger); }
.hs-input--error:focus { box-shadow: 0 0 0 3px rgba(220,38,38,.12); }
.hs-input--mono { font-family: monospace; font-size: .95rem; font-weight: 700; }
.hs-textarea { resize: vertical; min-height: 80px; }
.hs-error-msg { font-size: .775rem; color: var(--danger); font-weight: 500; }
.hs-hint { font-size: .775rem; color: var(--muted); }
.hs-field-meta { display: flex; justify-content: flex-end; }

/* ── CTA grid ──────────────────────────────────────────────── */
.hs-cta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.hs-cta-block { background: var(--bg2); border-radius: var(--radius-sm); padding: 16px; display: flex; flex-direction: column; gap: 12px; border: 1.5px solid var(--line); }
.hs-cta-block--primary { border-color: rgba(9,71,168,.25); }
.hs-cta-block--secondary { border-color: var(--line); }
.hs-cta-block__label { display: flex; }
.hs-cta-pill { font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; padding: 3px 10px; border-radius: 999px; }
.hs-cta-pill--primary   { background: var(--brand-primary); color: #fff; }
.hs-cta-pill--secondary { background: var(--line); color: var(--muted); }

/* ── Upload zone ───────────────────────────────────────────── */
.hs-upload-zone {
    display: flex; align-items: center; justify-content: center;
    border: 2px dashed var(--input-border); border-radius: var(--radius-sm);
    padding: 28px 16px; cursor: pointer; transition: all .18s;
    background: var(--bg2);
}
.hs-upload-zone:hover { border-color: var(--brand-primary); background: var(--primary-glow); }
.hs-upload-zone__input { display: none; }
.hs-upload-zone__body { display: flex; flex-direction: column; align-items: center; gap: 6px; color: var(--muted); text-align: center; }
.hs-upload-zone__body svg { width: 28px; height: 28px; color: var(--brand-primary); margin-bottom: 4px; }
.hs-upload-zone__body span { font-size: .875rem; font-weight: 600; color: var(--text); }
.hs-upload-zone__body small { font-size: .775rem; }

/* ── Media preview ─────────────────────────────────────────── */
.hs-media-preview {
    position: relative; border-radius: var(--radius-sm); overflow: hidden;
    background: var(--bg2); border: 1.5px solid var(--line);
    height: 120px; display: flex; align-items: center; justify-content: center;
}
.hs-media-preview--tall { height: 200px; }
.hs-media-preview__img { max-width: 100%; max-height: 100%; object-fit: cover; }
.hs-media-preview__img--contain { object-fit: contain; padding: 12px; }
.hs-media-preview__tag {
    position: absolute; top: 8px; right: 8px;
    font-size: .7rem; font-weight: 700; padding: 2px 8px; border-radius: 999px;
    background: var(--muted); color: #fff;
}
.hs-media-preview__tag--new { background: var(--brand-primary); }

/* ── Hand images grid ──────────────────────────────────────── */
.hs-hand-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; }
.hs-hand-item { position: relative; border-radius: var(--radius-xs); overflow: hidden; aspect-ratio: 1; background: var(--bg2); border: 1.5px solid var(--line); }
.hs-hand-item__img { width: 100%; height: 100%; object-fit: cover; }
.hs-hand-item__remove {
    position: absolute; top: 4px; right: 4px;
    width: 22px; height: 22px; border-radius: 999px;
    background: rgba(220,38,38,.9); color: #fff;
    border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .15s;
}
.hs-hand-item:hover .hs-hand-item__remove { opacity: 1; }
.hs-hand-item__remove svg { width: 12px; height: 12px; }

/* ── Repeater ──────────────────────────────────────────────── */
.hs-repeater { display: flex; flex-direction: column; gap: 12px; }
.hs-repeater__row {
    display: flex; align-items: flex-start; gap: 12px;
    background: var(--bg2); border-radius: var(--radius-sm);
    padding: 14px; border: 1.5px solid var(--line);
}
.hs-repeater__index {
    width: 26px; height: 26px; border-radius: 50%;
    background: var(--primary-glow); color: var(--brand-primary);
    font-size: .75rem; font-weight: 700; display: flex;
    align-items: center; justify-content: center; flex-shrink: 0; margin-top: 28px;
}
.hs-repeater__fields { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; flex: 1; }
.hs-repeater__remove {
    width: 32px; height: 32px; border-radius: var(--radius-xs);
    border: 1.5px solid rgba(220,38,38,.3); background: rgba(220,38,38,.07);
    color: var(--danger); cursor: pointer; display: flex;
    align-items: center; justify-content: center; margin-top: 26px;
    flex-shrink: 0; transition: all .15s;
}
.hs-repeater__remove:hover { background: rgba(220,38,38,.15); border-color: var(--danger); }
.hs-repeater__remove svg { width: 15px; height: 15px; }

/* ── Guide preview ─────────────────────────────────────────── */
.hs-preview-label { font-size: .75rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); margin: 0; }
.hs-guide-preview {
    background: var(--bg-card); border-radius: var(--radius-sm);
    border: 1.5px solid var(--line); padding: 20px;
    display: flex; flex-direction: column; gap: 8px;
    box-shadow: var(--shadow-sm); min-height: 120px;
}
.hs-guide-preview__tag { font-size: .75rem; font-weight: 700; color: var(--brand-primary); }
.hs-guide-preview__name { font-size: 1rem; color: var(--text); }
.hs-guide-preview__text { font-size: .875rem; color: var(--muted); margin: 0; }
.hs-guide-preview__empty { font-size: .875rem; color: var(--line); font-style: italic; margin: auto; text-align: center; }

/* ── Toggle ────────────────────────────────────────────────── */
.hs-toggle { display: flex; align-items: center; gap: 14px; cursor: pointer; }
.hs-toggle__input { display: none; }
.hs-toggle__track {
    width: 48px; height: 26px; border-radius: 999px; flex-shrink: 0;
    background: var(--line); position: relative; transition: background .2s;
}
.hs-toggle__track::after {
    content: ''; position: absolute; top: 3px; left: 3px;
    width: 20px; height: 20px; border-radius: 50%; background: #fff;
    box-shadow: var(--shadow-sm); transition: transform .2s;
}
.hs-toggle__input:checked ~ .hs-toggle__track { background: var(--brand-primary); }
.hs-toggle__input:checked ~ .hs-toggle__track::after { transform: translateX(22px); }
.hs-toggle__label { display: flex; flex-direction: column; gap: 2px; }
.hs-toggle__label strong { font-size: .9rem; color: var(--text); font-weight: 600; }
.hs-toggle__label small { font-size: .8125rem; color: var(--muted); }

/* ── Save bar ──────────────────────────────────────────────── */
.hs-save-bar {
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 100;
    background: var(--bg-card); border-top: 1.5px solid var(--line);
    padding: 12px 24px; display: flex; align-items: center;
    justify-content: flex-end; gap: 14px;
    box-shadow: 0 -4px 20px rgba(14,31,54,.08);
}
.hs-save-bar__hint { font-size: .8125rem; color: var(--muted); }

/* ── Responsive ────────────────────────────────────────────── */
@media (max-width: 640px) {
    .hs-grid-2, .hs-grid-3 { grid-template-columns: 1fr; }
    .hs-cta-grid { grid-template-columns: 1fr; }
    .hs-repeater__fields { grid-template-columns: 1fr; }
    .hs-page-header { flex-direction: column; align-items: flex-start; }
}
</style>
