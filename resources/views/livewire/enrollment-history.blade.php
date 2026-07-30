@php
    // Deterministic accent color per category, drawn from the theme palette.
    $historyAccents = ['var(--brand-primary)', 'var(--brand-secondary)', 'var(--brand-accent)', 'var(--success)', 'var(--info)'];
    $accentFor = fn ($label) => $historyAccents[crc32((string) $label) % count($historyAccents)];
@endphp

<div class="eh-wrap">

    {{-- ═══════════════ Hero ═══════════════ --}}
    <section class="eh-hero">
        <div class="eh-hero-text">
            <span class="eh-eyebrow">Your Learning Journey</span>
            <h1>Enrollment History</h1>
            <p>Every course you've been assigned, in one place.</p>
        </div>
        <div class="eh-hero-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <input type="search" wire:model.live.debounce.400ms="search" placeholder="Search your courses...">
        </div>
    </section>

    {{-- ═══════════════ KPI stats ═══════════════ --}}
    <section class="eh-kpis">
        <div class="eh-kpi" style="--eh-accent: var(--brand-primary);">
            <div class="eh-kpi-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div>
                <span>Total Enrollments</span>
                <b>{{ $stats['total'] }}</b>
            </div>
        </div>
        <div class="eh-kpi" style="--eh-accent: var(--brand-accent);">
            <div class="eh-kpi-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3 9 5-9 5-9-5 9-5z"/><path d="M3 13v5c0 1 4 3 9 3s9-2 9-3v-5"/></svg>
            </div>
            <div>
                <span>Active Courses</span>
                <b>{{ $stats['courses'] }}</b>
            </div>
        </div>
        <div class="eh-kpi" style="--eh-accent: var(--success);">
            <div class="eh-kpi-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            </div>
            <div>
                <span>Trainers Assigned</span>
                <b>{{ $stats['trainers'] }}</b>
            </div>
        </div>
    </section>

    {{-- ═══════════════ Filter chips ═══════════════ --}}
    <section class="eh-filterbar">
        <div class="eh-chips">
            <button type="button" class="eh-chip {{ $sort === 'latest' ? 'active' : '' }}" wire:click="setSort('latest')">Newest first</button>
            <button type="button" class="eh-chip {{ $sort === 'oldest' ? 'active' : '' }}" wire:click="setSort('oldest')">Oldest first</button>
            <button type="button" class="eh-chip toggle {{ $showFilters ? 'active' : '' }}" wire:click="toggleFilters">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"></path>
                </svg>
                More filters
            </button>
            @if($categoryFilter || $subcategoryFilter || $courseFilter || $trainerFilter)
                <button type="button" class="eh-chip clear" wire:click="clearFilters">Clear all &times;</button>
            @endif
        </div>

        <div class="eh-filterpanel {{ $showFilters ? 'open' : '' }}" x-show="$wire.showFilters" x-transition.duration.180ms>
            <div class="eh-filterfield">
                <label>Category</label>
                <select wire:model.live="categoryFilter">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="eh-filterfield">
                <label>Subcategory</label>
                <select wire:model.live="subcategoryFilter">
                    <option value="">All</option>
                    @foreach ($this->subcategoryOptions as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="eh-filterfield">
                <label>Course</label>
                <select wire:model.live="courseFilter">
                    <option value="">All</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="eh-filterfield">
                <label>Trainer</label>
                <select wire:model.live="trainerFilter">
                    <option value="">All</option>
                    @foreach ($trainers as $trainer)
                        <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    {{-- ═══════════════ Enrollment cards ═══════════════ --}}
    <section class="eh-list" wire:loading.class="eh-fade" wire:target="search,categoryFilter,subcategoryFilter,courseFilter,trainerFilter,sort">
        @forelse ($enrollments as $enrollment)
            @php
                $label = $enrollment->course?->category?->name ?? 'General';
                $accent = $accentFor($label);
                $detailPayload = [
                    'course'       => $enrollment->course?->title ?? 'Course',
                    'category'     => $label,
                    'subcategory'  => $enrollment->course?->subcategory?->name,
                    'trainer'      => $enrollment->trainer?->name,
                    'assignedBy'   => $enrollment->assignedBy?->name ?? 'System',
                    'enrolledOn'   => $enrollment->created_at?->format('F d, Y'),
                ];
            @endphp
            <article class="eh-card" style="--eh-accent: {{ $accent }};" wire:key="history-{{ $enrollment->id }}">
                <div class="eh-card-accent"></div>
                <div class="eh-card-body">
                    <div class="eh-card-top">
                        <span class="eh-pill">{{ $label }}</span>
                        <span class="eh-status">
                            <i></i> Active
                        </span>
                    </div>
                    <h3>{{ $enrollment->course?->title ?? 'Course' }}</h3>
                    @if($enrollment->course?->subcategory)
                        <p class="eh-subline">{{ $enrollment->course->subcategory->name }}</p>
                    @endif
                    <div class="eh-card-meta">
                        <div>
                            <span>Trainer</span>
                            <strong>{{ $enrollment->trainer?->name ?? 'Not assigned' }}</strong>
                        </div>
                        <div>
                            <span>Assigned by</span>
                            <strong>{{ $enrollment->assignedBy?->name ?? 'System' }}</strong>
                        </div>
                        <div>
                            <span>Enrolled on</span>
                            <strong>{{ $enrollment->created_at?->format('M d, Y') ?? '-' }}</strong>
                        </div>
                    </div>
                    <button type="button" class="eh-detail-btn"
                            onclick='showEnrollmentDetail(@json($detailPayload))'>
                        View Details
                    </button>
                </div>
            </article>
        @empty
            <div class="eh-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <p>No enrollments match your filters yet.</p>
                @if($categoryFilter || $subcategoryFilter || $courseFilter || $trainerFilter || $search)
                    <button type="button" class="eh-chip clear" wire:click="clearFilters">Clear filters</button>
                @endif
            </div>
        @endforelse
    </section>

    <div class="mt-10">
        {{ $enrollments->links('pagination.custom') }}
    </div>

    {{-- ═══════════════ Transactions (unchanged, informational) ═══════════════ --}}
    {{-- <section class="card">
        <div class="page-head">
            <h2>Transactions</h2>
        </div>
        <p class="muted">No transaction data available in this system.</p>
    </section> --}}
</div>

{{-- ═══════════════ Scoped styles — intentionally distinct from the manager theme ═══════════════ --}}
@once
<style>
    .eh-wrap { display: grid; gap: 18px; font-family: 'Inter', system-ui, sans-serif; }

    .eh-hero {
        display: flex; align-items: center; justify-content: space-between; gap: 20px;
        flex-wrap: wrap;
        padding: 28px 30px;
        border-radius: 22px;
        background: linear-gradient(120deg, var(--brand-primary), var(--brand-secondary));
        color: #fff;
        box-shadow: var(--shadow);
    }
    .eh-eyebrow {
        display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: .12em;
        text-transform: uppercase; opacity: .8; margin-bottom: 6px;
    }
    .eh-hero-text h1 { font-size: 26px; font-weight: 800; letter-spacing: -.02em; margin: 0 0 4px; }
    .eh-hero-text p { font-size: 14px; opacity: .85; margin: 0; }
    .eh-hero-search {
        display: flex; align-items: center; gap: 8px;
        background: rgba(255,255,255,.16);
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 999px;
        padding: 10px 16px;
        min-width: 260px;
    }
    .eh-hero-search svg { width: 16px; height: 16px; flex-shrink: 0; opacity: .85; }
    .eh-hero-search input {
        background: transparent; border: none; outline: none; color: #fff;
        font-size: 14px; width: 100%;
    }
    .eh-hero-search input::placeholder { color: rgba(255,255,255,.75); }

    .eh-kpis { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 14px; }
    .eh-kpi {
        display: flex; align-items: center; gap: 12px;
        background: var(--card); border: 1px solid var(--line);
        border-radius: 16px; padding: 16px; box-shadow: var(--shadow-card);
    }
    .eh-kpi-icon {
        width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: color-mix(in srgb, var(--eh-accent) 16%, transparent);
        color: var(--eh-accent);
    }
    .eh-kpi-icon svg { width: 20px; height: 20px; }
    .eh-kpi span { display: block; font-size: 12px; color: var(--text-muted); font-weight: 600; }
    .eh-kpi b { font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -.02em; }

    .eh-filterbar { display: grid; gap: 10px; }
    .eh-chips { display: flex; flex-wrap: wrap; gap: 8px; }
    .eh-chip {
        border: 1px solid var(--line); background: var(--card); color: var(--text-muted);
        font-size: 13px; font-weight: 600; padding: 8px 14px; border-radius: 999px;
        display: inline-flex; align-items: center; gap: 6px; transition: all .15s;
    }
    .eh-chip svg { width: 14px; height: 14px; }
    .eh-chip:hover { border-color: var(--brand-primary); color: var(--brand-primary); }
    .eh-chip.active { background: var(--brand-primary); border-color: var(--brand-primary); color: #fff; }
    .eh-chip.clear { color: var(--danger); border-color: var(--danger); }

    .eh-filterpanel {
        display: none; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px;
        background: var(--bg-card2); border: 1px solid var(--line); border-radius: 16px; padding: 16px;
    }
    .eh-filterpanel.open { display: grid; }
    .eh-filterfield { display: grid; gap: 6px; }
    .eh-filterfield label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); }
    .eh-filterfield select {
        background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 10px;
        padding: 9px 10px; font-size: 13px; color: var(--text);
    }
    @media (max-width: 900px) { .eh-filterpanel { grid-template-columns: 1fr 1fr; } }

    .eh-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; transition: opacity .15s; }
    .eh-fade { opacity: .45; }

    .eh-card {
        position: relative; overflow: hidden;
        border: 1px solid var(--line); border-radius: 18px; background: var(--card);
        box-shadow: var(--shadow-card); display: flex;
    }
    .eh-card-accent { width: 5px; background: var(--eh-accent); flex-shrink: 0; }
    .eh-card-body { padding: 18px; display: grid; gap: 10px; flex: 1; }
    .eh-card-top { display: flex; align-items: center; justify-content: space-between; }
    .eh-pill {
        font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px;
        background: color-mix(in srgb, var(--eh-accent) 14%, transparent); color: var(--eh-accent);
    }
    .eh-status { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: var(--success); }
    .eh-status i { width: 7px; height: 7px; border-radius: 50%; background: var(--success); display: inline-block; }
    .eh-card h3 { font-size: 17px; font-weight: 700; letter-spacing: -.01em; color: var(--text); margin: 0; }
    .eh-subline { font-size: 12.5px; color: var(--text-muted); margin: -6px 0 0; }
    .eh-card-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 8px; margin-top: 4px; }
    .eh-card-meta div:last-child { grid-column: 1 / -1; }
    .eh-card-meta span { display: block; font-size: 10.5px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); }
    .eh-card-meta strong { font-size: 13px; color: var(--text); font-weight: 600; }
    .eh-detail-btn {
        margin-top: 6px; align-self: start;
        border: 1px solid var(--eh-accent); color: var(--eh-accent); background: transparent;
        font-size: 12.5px; font-weight: 700; padding: 8px 14px; border-radius: 10px; transition: all .15s;
    }
    .eh-detail-btn:hover { background: var(--eh-accent); color: #fff; }

    .eh-empty {
        grid-column: 1 / -1; text-align: center; padding: 50px 20px;
        color: var(--text-muted); display: grid; justify-items: center; gap: 10px;
    }
    .eh-empty svg { width: 40px; height: 40px; opacity: .4; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showEnrollmentDetail(data) {
        Swal.fire({
            title: data.course,
            html: `
                <div style="text-align:left; font-size:14px; line-height:1.9;">
                    <div><b>Category:</b> ${data.category}</div>
                    ${data.subcategory ? `<div><b>Subcategory:</b> ${data.subcategory}</div>` : ''}
                    <div><b>Trainer:</b> ${data.trainer ?? 'Not assigned'}</div>
                    <div><b>Assigned by:</b> ${data.assignedBy}</div>
                    <div><b>Enrolled on:</b> ${data.enrolledOn ?? '-'}</div>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Close',
            confirmButtonColor: '#0947a8'
        });
    }
</script>
@endonce