<div class="mc-page">

    <div class="mc-page-head">
        <div>
            <h1 class="mc-title">My Certificates</h1>
            <p class="mc-subtitle">Track your progress and download certificates as you unlock them.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="mc-stat-row">
        <div class="mc-stat-card">
            <div class="mc-stat-icon mc-stat-icon--unlocked">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 11V7a4 4 0 018 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="mc-stat-val">{{ $this->stats['unlocked'] }}</div>
                <div class="mc-stat-lbl">Unlocked</div>
            </div>
        </div>
        <div class="mc-stat-card">
            <div class="mc-stat-icon mc-stat-icon--pending">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="mc-stat-val">{{ $this->stats['pending'] }}</div>
                <div class="mc-stat-lbl">Pending review</div>
            </div>
        </div>
        <div class="mc-stat-card">
            <div class="mc-stat-icon mc-stat-icon--progress">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 12a8 8 0 1 1 8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 12l3-3M4 12l3 3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <div class="mc-stat-val">{{ $this->stats['in_progress'] }}</div>
                <div class="mc-stat-lbl">In progress</div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mc-tabs" role="tablist">
        @foreach (['all' => 'All', 'course' => 'Course', 'week' => 'Week', 'level' => 'Level', 'demo' => 'Demo'] as $key => $label)
            <button
                type="button"
                role="tab"
                class="mc-tab {{ $activeTab === $key ? 'mc-tab-active' : '' }}"
                wire:click="setTab('{{ $key }}')"
                aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}"
            >{{ $label }}</button>
        @endforeach
    </div>

    {{-- Grid --}}
    <div class="mc-grid" wire:loading.class="mc-loading">
        @forelse ($this->certificates as $cert)
            @php $pct = $this->livePercent($cert); @endphp
            <div class="mc-card mc-card--{{ $cert->isUnlocked() ? 'unlocked' : ($cert->status === 'pending_admin_approval' ? 'pending' : 'locked') }}" wire:key="my-cert-{{ $cert->id }}">

                <div class="mc-card-top">
                    <div class="mc-type-icon mc-type-icon--{{ $cert->type }}">
                        @if ($cert->type === 'course')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 6.5A2.5 2.5 0 016.5 4H20v14.5A2.5 2.5 0 0117.5 21H4V6.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M4 6.5A2.5 2.5 0 016.5 4H8v17H6.5A2.5 2.5 0 014 18.5V6.5z" stroke="currentColor" stroke-width="1.6"/></svg>
                        @elseif ($cert->type === 'week')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M4 10h16M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        @elseif ($cert->type === 'level')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.6 5.6L21 9.6l-4.5 4.2L17.6 21 12 17.8 6.4 21l1.1-7.2L3 9.6l6.4-1z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.6"/><path d="M9.5 12l1.8 1.8L15 10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        @endif
                    </div>
                    <span class="mc-badge mc-badge-type-{{ $cert->type }}">{{ ucfirst($cert->type) }}</span>
                </div>

                <h3 class="mc-cert-title">{{ $cert->subjectTitle() }}</h3>
                <div class="mc-cert-num">{{ $cert->certificate_number }}</div>

                <div class="mc-progress">
                    <div class="mc-progress-track"><span style="width: {{ $pct }}%"></span></div>
                    <div class="mc-progress-pct">{{ $pct }}%</div>
                </div>

                <div class="mc-card-foot">
                    @if ($cert->isUnlocked())
                        <button class="btn btn-primary btn-sm mc-download-btn" wire:click="download({{ $cert->id }})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 4v11m0 0l-4-4m4 4l4-4M5 20h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Download
                        </button>
                        <span class="mc-note">Unlocked {{ $cert->issued_at?->format('M d, Y') }}</span>
                    @elseif ($cert->status === 'pending_admin_approval')
                        <span class="mc-badge mc-badge-status-pending"><i></i>Pending approval</span>
                        <span class="mc-note">Awaiting admin review</span>
                    @else
                        <span class="mc-badge mc-badge-status-locked"><i></i>Locked</span>
                        <span class="mc-note">Keep going to unlock</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="mc-empty">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4M7.8 4.2h8.4a2 2 0 012 2v13.4l-6.2-3.2-6.2 3.2V6.2a2 2 0 012-2z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p>No certificates in this category yet.</p>
            </div>
        @endforelse
    </div>
</div>
<style>
    /* ═══════════════════════════════════════════════
   MY CERTIFICATES (student view) — built on existing :root tokens
═══════════════════════════════════════════════ */



/* Page head */
.mc-page-head { margin-bottom: 26px; }
.mc-title { font-size: 26px; font-weight: 700; letter-spacing: -0.02em; color: var(--text); }
.mc-subtitle { margin-top: 4px; font-size: 14.5px; color: var(--text-muted); }

/* Stats */
.mc-stat-row {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
    margin-bottom: 24px;
}
.mc-stat-card {
    display: flex; align-items: center; gap: 14px;
    background: var(--bg-card); border: 1px solid var(--line);
    border-radius: var(--radius-sm); padding: 18px 20px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow .2s ease, transform .2s ease;
}
.mc-stat-card:hover { box-shadow: var(--shadow-card); transform: translateY(-1px); }
.mc-stat-icon {
    flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.mc-stat-icon--unlocked { background: rgba(22, 163, 74, .12); color: var(--success); }
.mc-stat-icon--pending  { background: rgba(217, 119, 6, .12); color: var(--warning); }
.mc-stat-icon--progress { background: rgba(122, 92, 255, .12); color: var(--brand-secondary); }
.mc-stat-val { font-size: 22px; font-weight: 700; color: var(--text); line-height: 1.1; }
.mc-stat-lbl { font-size: 12.5px; color: var(--text-muted); margin-top: 3px; }

/* Tabs */
.mc-tabs {
    display: flex; gap: 6px; margin-bottom: 22px; flex-wrap: wrap;
    border-bottom: 1px solid var(--line); padding-bottom: 0;
}
.mc-tab {
    border: none; background: transparent; cursor: pointer;
    padding: 10px 16px; font-size: 14px; font-weight: 600; color: var(--text-muted);
    border-radius: var(--radius-xs) var(--radius-xs) 0 0;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: color .15s ease, border-color .15s ease, background .15s ease;
}
.mc-tab:hover { color: var(--text); background: var(--bg-card2); }
.mc-tab-active { color: var(--brand-primary); border-bottom-color: var(--brand-primary); }

/* Grid */
.mc-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 18px;
}
.mc-grid.mc-loading { opacity: .5; }

/* Card */
.mc-card {
    background: var(--bg-card); border: 1px solid var(--line);
    border-radius: var(--radius-sm); padding: 20px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow .2s ease, transform .2s ease;
    display: flex; flex-direction: column;
    position: relative;
    overflow: hidden;
}
.mc-card::before {
    content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.mc-card--unlocked::before { background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary)); }
.mc-card--pending::before { background: var(--warning); }
.mc-card--locked::before { background: var(--line); }
.mc-card--locked { opacity: .78; }
.mc-card:hover { box-shadow: var(--shadow-card); transform: translateY(-2px); }

.mc-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.mc-type-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
}
.mc-type-icon--course { background: rgba(9, 71, 168, .1); color: var(--brand-primary); }
.mc-type-icon--week   { background: rgba(122, 92, 255, .12); color: var(--brand-secondary); }
.mc-type-icon--level  { background: rgba(240, 179, 90, .18); color: #97650f; }
.mc-type-icon--demo   { background: rgba(90, 113, 138, .12); color: var(--text-muted); }

.mc-cert-title { font-size: 15.5px; font-weight: 700; color: var(--text); line-height: 1.35; margin-bottom: 4px; }
.mc-cert-num { font-size: 12px; color: var(--text-muted); font-variant-numeric: tabular-nums; margin-bottom: 16px; }

.mc-progress { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.mc-progress-track { flex: 1; height: 6px; border-radius: 100px; background: var(--line); overflow: hidden; }
.mc-progress-track span {
    display: block; height: 100%; border-radius: 100px;
    background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
}
.mc-progress-pct { font-size: 12.5px; color: var(--text-muted); width: 34px; text-align: right; flex-shrink: 0; }

.mc-card-foot {
    margin-top: auto; padding-top: 14px; border-top: 1px solid var(--line);
    display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;
}
.mc-download-btn { display: inline-flex; align-items: center; gap: 6px; }
.mc-note { font-size: 12px; color: var(--text-muted); }

/* Badges (shared visual language with admin badges) */
.mc-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 100px;
    font-size: 12px; font-weight: 600;
}
.mc-badge i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; }

.mc-badge-type-course { background: rgba(9, 71, 168, .1); color: var(--brand-primary); }
.mc-badge-type-week   { background: rgba(122, 92, 255, .12); color: var(--brand-secondary); }
.mc-badge-type-level  { background: rgba(240, 179, 90, .18); color: #97650f; }
.mc-badge-type-demo   { background: rgba(90, 113, 138, .12); color: var(--text-muted); }

.mc-badge-status-pending { background: rgba(217, 119, 6, .12); color: var(--warning); }
.mc-badge-status-locked  { background: rgba(90, 113, 138, .12); color: var(--text-muted); }

/* Empty state */
.mc-empty {
    grid-column: 1 / -1;
    display: flex; flex-direction: column; align-items: center; gap: 10px;
    padding: 56px 20px; color: var(--text-muted);
    background: var(--bg-card); border: 1px dashed var(--line); border-radius: var(--radius-sm);
}
.mc-empty svg { color: var(--line); }
.mc-empty p { font-size: 14px; }

/* Responsive */
@media (max-width: 720px) {
    .mc-stat-row { grid-template-columns: 1fr; }
    .mc-grid { grid-template-columns: 1fr; }
}
    </style>
