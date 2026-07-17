{{-- resources/views/livewire/student/course-catalog.blade.php --}}
<div class="courses-page">

<style>
/* ═══════════════════════════════════════════════
   PAGE LAYOUT + NEW LEVEL COLOR SYSTEM
═══════════════════════════════════════════════ */
.courses-page {
    display: flex; flex-direction: column; gap: 24px; position: relative;

    /* Level palette — scoped here so it doesn't collide with your global theme */
    --level-beginner: #16a34a;      --level-beginner-soft: rgba(22,163,74,.12);
    --level-intermediate: #d97706;  --level-intermediate-soft: rgba(217,119,6,.12);
    --level-advanced: #7c3aed;      --level-advanced-soft: rgba(124,58,237,.12);
    --level-expert: #e11d48;        --level-expert-soft: rgba(225,29,72,.12);
    --level-default: #64748b;       --level-default-soft: rgba(100,116,139,.12);
}
.price-gst { font-size: 10px; font-weight: 600; color: var(--text-muted); }

/* ═══════════════════════════════════════════════
   PAGE HERO HEADER
═══════════════════════════════════════════════ */
.courses-hero {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    padding: 28px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
    box-shadow: var(--shadow-card);
}
.courses-hero-left { display: flex; align-items: center; gap: 18px; }
.courses-hero-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #fff; flex-shrink: 0;
    box-shadow: 0 6px 18px rgba(9,71,168,.25);
}
.courses-hero-title { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
.courses-hero-sub   { font-size: 13px; color: var(--text-muted); }
.courses-hero-right { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.courses-hero-stats {
    display: flex; gap: 20px; flex-wrap: wrap;
}
.hero-stat {
    text-align: center;
    padding: 10px 18px;
    background: var(--bg2);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
}
.hero-stat-val { font-size: 20px; font-weight: 800; color: var(--brand-primary); line-height: 1; }
.hero-stat-label { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 3px; text-transform: uppercase; letter-spacing: .4px; }

/* ═══════════════════════════════════════════════
   THEME TOGGLE
═══════════════════════════════════════════════ */
.theme-toggle {
    width: 42px; height: 42px;
    border-radius: 50%;
    border: 1.5px solid var(--line);
    background: var(--bg-card2);
    color: var(--text-muted);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all .2s;
    flex-shrink: 0;
    font-size: 17px;
}
.theme-toggle:hover { color: var(--brand-primary); border-color: var(--brand-primary); transform: rotate(15deg); }

/* ═══════════════════════════════════════════════
   SECTION CARD
═══════════════════════════════════════════════ */
.section-card {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-card);
}
.section-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1.5px solid var(--line);
    gap: 12px; flex-wrap: wrap;
}
.section-card-title {
    font-size: 15px; font-weight: 700; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.section-card-title i { color: var(--brand-primary); font-size: 17px; }
.section-card-sub { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
.section-card-body { padding: 24px; }
.section-card-count {
    font-size: 12px; font-weight: 600;
    color: var(--brand-primary);
    background: var(--primary-glow);
    padding: 4px 10px;
    border-radius: 20px;
}
.sort-note {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700;
    color: var(--brand-primary);
    background: var(--primary-glow);
    border: 1px solid rgba(9,71,168,.18);
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}

/* ═══════════════════════════════════════════════
   CATEGORY + LEVEL OVERVIEW (first-time-user orientation)
═══════════════════════════════════════════════ */
.category-overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
    padding: 22px 24px 26px;
}
.category-overview-card {
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    padding: 18px;
    background: var(--bg-card);
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    display: flex; flex-direction: column; gap: 12px;
    text-align: left;
    font-family: inherit;
}
.category-overview-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
    border-color: var(--brand-primary);
}
.cov-top { display: flex; align-items: flex-start; gap: 12px; }
.cov-icon {
    width: 44px; height: 44px; flex-shrink: 0;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; color: #fff;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    box-shadow: 0 4px 12px rgba(9,71,168,.22);
}
.cov-title { font-size: 14.5px; font-weight: 800; color: var(--text); line-height: 1.3; }
.cov-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
.cov-levels { display: flex; gap: 6px; flex-wrap: wrap; }
.cov-level-chip {
    font-size: 10px; font-weight: 800;
    padding: 3px 9px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.cov-foot {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px dashed var(--line);
}
.cov-price-range { font-size: 13.5px; font-weight: 800; color: var(--brand-primary); }
.cov-price-range small { display: block; font-size: 10px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .3px; }
.cov-arrow { color: var(--text-muted); font-size: 16px; transition: transform .18s, color .18s; }
.category-overview-card:hover .cov-arrow { color: var(--brand-primary); transform: translateX(3px); }

/* ═══════════════════════════════════════════════
   SEARCH + FILTER BAR
═══════════════════════════════════════════════ */
.filter-bar {
    display: flex; align-items: center; gap: 12px;
    flex-wrap: wrap;
}
.filter-search {
    display: flex; align-items: center; gap: 8px;
    background: var(--input-bg);
    border: 1.5px solid var(--input-border);
    border-radius: var(--radius-xs);
    padding: 8px 14px;
    flex: 1; min-width: 220px;
    transition: border-color .2s;
}
.filter-search:focus-within {
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px var(--primary-glow);
}
.filter-search i { color: var(--text-muted); font-size: 15px; flex-shrink: 0; }
.filter-search input {
    background: none; border: none; outline: none;
    font-size: 13.5px; color: var(--text); font-family: inherit; width: 100%;
}
.filter-search input::placeholder { color: var(--text-muted); }

/* ═══════════════════════════════════════════════
   CATEGORY TABS (main)
═══════════════════════════════════════════════ */
.category-tabs-wrap {
    padding: 0 24px 0;
    border-bottom: 1.5px solid var(--line);
    display: flex; align-items: center; gap: 4px;
    overflow: scroll;
    scrollbar-width: 200px;
}
.category-tabs-wrap::-webkit-scrollbar { display: none; }

.cat-tab {
    display: flex; align-items: center; gap: 7px;
    padding: 14px 16px;
    font-size: 13px; font-weight: 600;
    color: var(--text-muted);
    background: none; border: none;
    border-bottom: 2.5px solid transparent;
    cursor: pointer; white-space: nowrap;
    transition: color .15s, border-color .15s;
    margin-bottom: -1.5px;
    font-family: inherit;
}
.cat-tab i { font-size: 15px; }
.cat-tab:hover { color: var(--brand-primary); }
.cat-tab.active {
    color: var(--brand-primary);
    border-bottom-color: var(--brand-primary);
    font-weight: 700;
}
.cat-tab .cat-count {
    font-size: 10px; font-weight: 700;
    background: var(--primary-glow);
    color: var(--brand-primary);
    padding: 2px 6px;
    border-radius: 20px;
}

/* ═══════════════════════════════════════════════
   LEVEL SNAPSHOT PILLS (per category)
═══════════════════════════════════════════════ */
.level-snapshot-row {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    padding: 16px 24px 0;
}
.level-snapshot-label {
    font-size: 10px; font-weight: 700;
    color: var(--text-muted); text-transform: uppercase;
    letter-spacing: .6px; flex-shrink: 0;
}
.level-pill {
    display: flex; align-items: center; gap: 7px;
    padding: 6px 13px;
    border-radius: 20px;
    font-size: 11.5px; font-weight: 700;
    border: 1.5px solid var(--line);
    background: var(--bg2);
    color: var(--text-muted);
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    font-family: inherit;
}
.level-pill:hover { transform: translateY(-1px); }
.level-pill .level-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.level-pill.active { background: var(--bg-card); box-shadow: 0 0 0 1.5px currentColor inset; }

/* ═══════════════════════════════════════════════
   SUBCATEGORY PILLS
═══════════════════════════════════════════════ */
.subcategory-row {
    display: flex; align-items: center; gap: 8px;
    flex-wrap: wrap;
    padding: 12px 24px 0;
}
.subcategory-label {
    font-size: 10px; font-weight: 700;
    color: var(--text-muted); text-transform: uppercase;
    letter-spacing: .6px; flex-shrink: 0;
}
.sub-pill {
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
    border: 1.5px solid var(--line);
    background: var(--bg2);
    color: var(--text-muted);
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    font-family: inherit;
}
.sub-pill:hover {
    border-color: var(--primary);
    color: var(--brand-primary);
    background: var(--primary-glow);
}
.sub-pill.active {
    border-color: var(--brand-primary);
    color: var(--brand-primary);
    background: var(--primary-glow);
    font-weight: 700;
}

/* ═══════════════════════════════════════════════
   TAB PANELS
═══════════════════════════════════════════════ */
.tab-panel          { display: none; }
.tab-panel.active   {
    display: block;
    animation: fadeUp 220ms ease;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ═══════════════════════════════════════════════
   LEVEL SECTIONS (grouped, sorted low→high price)
═══════════════════════════════════════════════ */
.level-section { padding: 0 24px; margin-top: 20px; }
.level-section:first-of-type { margin-top: 14px; }
.level-section-head {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    margin-bottom: 12px;
}
.level-section-head .level-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.level-section-head h4 { font-size: 14px; font-weight: 800; color: var(--text); }
.level-section-count {
    font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 20px;
    background: var(--bg2); color: var(--text-muted);
}
.level-section-price {
    font-size: 12px; font-weight: 800;
    color: var(--brand-primary);
    margin-left: auto;
    display: flex; align-items: center; gap: 5px;
}
.level-section .course-grid { padding: 0 0 4px; }

/* ═══════════════════════════════════════════════
   COURSE GRID
═══════════════════════════════════════════════ */
.course-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    padding: 20px 24px 24px;
}
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 48px 20px;
    color: var(--text-muted);
}
.empty-state i { font-size: 40px; opacity: .35; margin-bottom: 12px; display: block; }
.empty-state p { font-size: 14px; }

/* ═══════════════════════════════════════════════
   COURSE TILE
═══════════════════════════════════════════════ */
.course-tile {
    border-radius: var(--radius-sm);
    overflow: hidden;
    border: 1.5px solid var(--line);
    background: var(--bg-card);
    box-shadow: var(--shadow-sm);
    text-decoration: none;
    color: inherit;
    display: flex; flex-direction: column;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    position: relative;
}
.course-tile:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
    border-color: var(--border);
}
.course-tile.is-locked {
    opacity: .82;
}
.course-tile.is-locked:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-card);
}

/* Thumbnail */
.course-thumb {
    min-height: 148px;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex; align-items: flex-end;
    padding: 14px;
}
.course-thumb-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(5,15,35,.72) 0%, rgba(5,15,35,.08) 60%, transparent 100%);
}
.course-thumb h3 {
    position: relative; z-index: 1;
    margin: 0;
    font-size: 15px; font-weight: 700;
    color: #fff;
    line-height: 1.3;
    text-shadow: 0 1px 4px rgba(0,0,0,.4);
}

/* Enrolled ribbon */
.course-enrolled-badge {
    position: absolute;
    top: 10px; left: 10px;
    display: flex; align-items: center; gap: 5px;
    font-size: 10.5px; font-weight: 700;
    color: var(--success);
    background: rgba(22,163,74,.12);
    border: 1px solid rgba(22,163,74,.3);
    backdrop-filter: blur(6px);
    padding: 3px 10px;
    border-radius: 20px;
    z-index: 2;
}

/* New badge */
.course-new-badge {
    position: absolute;
    top: 10px; right: 10px;
    font-size: 10px; font-weight: 800;
    color: #fff;
    background: var(--brand-accent);
    padding: 3px 9px;
    border-radius: 20px;
    z-index: 2;
    text-transform: uppercase;
    letter-spacing: .4px;
}

/* In-cart badge */
.course-cart-badge {
    position: absolute;
    top: 10px; right: 10px;
    display: flex; align-items: center; gap: 5px;
    font-size: 10.5px; font-weight: 700;
    color: var(--brand-primary);
    background: var(--primary-glow);
    border: 1px solid var(--primary);
    backdrop-filter: blur(6px);
    padding: 3px 10px;
    border-radius: 20px;
    z-index: 2;
}

/* Level badge (locked/browse tiles only — top-left) */
.course-level-badge {
    position: absolute;
    top: 10px; left: 10px;
    font-size: 10px; font-weight: 800;
    color: #fff;
    padding: 3px 10px;
    border-radius: 20px;
    z-index: 4;
    text-transform: uppercase;
    letter-spacing: .4px;
    backdrop-filter: blur(4px);
}

/* Inline level tag (enrolled tiles — inside meta row) */
.course-level-tag {
    font-size: 10px; font-weight: 800;
    padding: 2px 8px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* Lock overlay */
.course-lock-overlay {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(8,17,31,.45);
    backdrop-filter: blur(2px);
    z-index: 3;
}
.course-lock-icon {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 2px solid rgba(255,255,255,.3);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff;
}

/* Body */
.course-body {
    padding: 14px 16px;
    display: flex; flex-direction: column;
    gap: 10px; flex: 1;
}
.course-meta {
    display: flex; align-items: center; gap: 6px;
    font-size: 11.5px; color: var(--text-muted);
    flex-wrap: wrap;
}
.course-meta-dot {
    width: 3px; height: 3px; border-radius: 50%;
    background: var(--text-muted); opacity: .4;
}

/* Progress bar */
.course-progress-wrap { display: flex; flex-direction: column; gap: 4px; }
.course-progress-row  { display: flex; justify-content: space-between; font-size: 11.5px; }
.course-progress-label { color: var(--text-muted); }
.course-progress-pct   { color: var(--brand-primary); font-weight: 600; }
.course-progress-track {
    height: 5px;
    background: var(--line);
    border-radius: 3px;
    overflow: hidden;
}
.course-progress-bar {
    height: 100%;
    border-radius: 3px;
    background: linear-gradient(90deg, var(--brand-primary), var(--brand-secondary));
    transition: width .6s ease;
}

/* Footer */
.course-foot {
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: auto;
    flex-wrap: wrap;
}

/* ═══════════════════════════════════════════════
   BUTTONS
═══════════════════════════════════════════════ */
.btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 16px;
    border-radius: var(--radius-xs);
    font-size: 13px; font-weight: 600;
    border: 1.5px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .15s;
    text-decoration: none;
    white-space: nowrap;
}
.btn:disabled { opacity: .5; cursor: not-allowed; }
.btn-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    color: #fff;
    box-shadow: 0 3px 10px rgba(9,71,168,.25);
}
.btn-success {
    background: rgba(22,163,74,.1);
    color: var(--success);
    border-color: rgba(22,163,74,.3);
}
.btn-success:hover { background: rgba(22,163,74,.18); }
.btn-outline {
    background: transparent;
    color: var(--text-muted);
    border-color: var(--line);
}
.btn-outline:hover { border-color: var(--primary); color: var(--brand-primary); background: var(--primary-glow); }
.btn-unlock {
    background: linear-gradient(135deg, var(--brand-accent), #e8943a);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 3px 10px rgba(240,179,90,.3);
    flex: 1;
}
.btn-unlock:hover:not(:disabled) { opacity: .88; transform: translateY(-1px); box-shadow: 0 5px 16px rgba(240,179,90,.4); }
.btn-sm { padding: 6px 12px; font-size: 12px; }
.btn-icon { padding: 6px 10px; }

/* ═══════════════════════════════════════════════
   PRICE TAG
═══════════════════════════════════════════════ */
.price-tag {
    display: flex; align-items: baseline; gap: 4px;
    font-size: 14px; font-weight: 800;
    color: var(--text);
}
.price-tag .price-currency { font-size: 11px; font-weight: 600; color: var(--text-muted); }
.price-tag .price-free     { color: var(--success); font-size: 13px; }
.price-tag .price-old {
    font-size: 11px; font-weight: 500;
    color: var(--text-muted);
    text-decoration: line-through;
}

/* Badge locked pill */
.badge-locked {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700;
    color: var(--text-muted);
    background: var(--bg2);
    border: 1px solid var(--line);
    border-radius: 20px;
    padding: 4px 10px;
}

/* ═══════════════════════════════════════════════
   MODAL BASE (used by close icon styling)
═══════════════════════════════════════════════ */
.modal-close {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: var(--bg2);
    border: 1.5px solid var(--line);
    color: var(--text-muted);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 15px;
    transition: all .15s;
}
.modal-close:hover { background: var(--primary-glow); color: var(--brand-primary); border-color: var(--primary); }
.error-text { color: var(--danger); font-size: 12px; display: block; margin-top: 6px; }

/* ═══════════════════════════════════════════════
   CART FAB
═══════════════════════════════════════════════ */
.cart-fab {
    position: fixed;
    bottom: 28px; right: 28px;
    width: 58px; height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    box-shadow: 0 10px 28px rgba(9,71,168,.35);
    cursor: pointer;
    z-index: 800;
    transition: transform .2s;
    border: none;
}
.cart-fab:hover { transform: translateY(-3px) scale(1.05); }
.cart-fab-badge {
    position: absolute; top: -4px; right: -4px;
    background: var(--danger);
    color: #fff;
    font-size: 11px; font-weight: 800;
    width: 22px; height: 22px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--bg);
}

/* ═══════════════════════════════════════════════
   CART DRAWER
═══════════════════════════════════════════════ */
.cart-drawer-backdrop {
    position: fixed; inset: 0;
    background: rgba(5,12,28,.55);
    backdrop-filter: blur(3px);
    z-index: 950;
    display: none;
}
.cart-drawer-backdrop.open { display: block; animation: backdropIn .2s ease; }
@keyframes backdropIn { from { opacity: 0; } to { opacity: 1; } }

.cart-drawer {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 380px; max-width: 92vw;
    background: var(--bg-card);
    border-left: 1.5px solid var(--line);
    box-shadow: var(--shadow);
    z-index: 951;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .25s ease;
}
.cart-drawer.open { transform: translateX(0); }
.cart-drawer-head {
    padding: 20px 22px;
    border-bottom: 1.5px solid var(--line);
    display: flex; justify-content: space-between; align-items: center;
}
.cart-drawer-head h3 { font-size: 16px; color: var(--text-main); display:flex; align-items:center; gap:8px; }
.cart-drawer-body { flex: 1; overflow-y: auto; padding: 16px 22px; }
.cart-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--line);
}
.cart-item-thumb { width: 44px; height: 44px; border-radius: 8px; background-size: cover; background-position: center; flex-shrink: 0; background-color: var(--bg2); }
.cart-item-info { flex: 1; }
.cart-item-name { font-size: 13px; font-weight: 600; color: var(--text-main); }
.cart-item-price { font-size: 12px; color: var(--brand-primary); font-weight: 700; }
.cart-item-remove { color: var(--danger); cursor: pointer; font-size: 16px; background: none; border: none; padding: 6px; }
.cart-drawer-foot {
    padding: 18px 22px;
    border-top: 1.5px solid var(--line);
    background: var(--bg2);
}
.cart-total-row { display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 15px; font-weight: 700; color: var(--text-main); }
.cart-empty { text-align: center; padding: 40px 0; color: var(--text-muted); }
.cart-empty i { font-size: 32px; opacity: .4; display: block; margin-bottom: 8px; }

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media (max-width: 1024px) {
    .course-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 640px) {
    .course-grid { grid-template-columns: 1fr; padding: 14px; }
    .courses-hero { padding: 20px; }
    .courses-hero-stats { display: none; }
    .section-card-head { padding: 14px 16px; }
    .section-card-body  { padding: 14px; }
    .category-tabs-wrap { padding: 0 14px; }
    .subcategory-row    { padding: 12px 14px 0; }
    .level-snapshot-row { padding: 12px 14px 0; }
    .level-section       { padding: 0 14px; }
    .category-overview-grid { padding: 16px 14px 20px; grid-template-columns: 1fr; }
    .cart-drawer { width: 100vw; max-width: 100vw; }
}
.filter-select {
    width: 100%;
    border: 1.5px solid var(--line);
    border-radius: var(--radius-xs);
    padding: 10px 14px;
    font-size: 13.5px;
    color: var(--text);
    background: var(--input-bg);
    font-family: inherit;
}
.category-dropdown-wrap { display: none; padding: 0 24px; margin-bottom: 12px; }

@media (max-width: 640px) {
    .category-tabs-wrap { display: none; }
    .category-dropdown-wrap { display: block; padding: 12px 14px 0; }
}
</style>

@php
    use Illuminate\Support\Str;

    // ── Level color map (single source of truth for badges/pills/sections) ──
    $levelColorMap = [
        'beginner'     => ['fg' => '#16a34a', 'soft' => 'rgba(22,163,74,.14)'],
        'intermediate' => ['fg' => '#d97706', 'soft' => 'rgba(217,119,6,.14)'],
        'advanced'     => ['fg' => '#7c3aed', 'soft' => 'rgba(124,58,237,.14)'],
        'expert'       => ['fg' => '#e11d48', 'soft' => 'rgba(225,29,72,.14)'],
        'pro'          => ['fg' => '#e11d48', 'soft' => 'rgba(225,29,72,.14)'],
    ];
    $defaultLevelColor = ['fg' => '#64748b', 'soft' => 'rgba(100,116,139,.14)'];
    $levelColor = fn ($level) => $levelColorMap[strtolower(trim($level ?: 'beginner'))] ?? $defaultLevelColor;

    // Sort order for known level names; anything unrecognized sorts last, alphabetically.
    $levelPriority = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4, 'pro' => 4];

    $enrolledCourses = $categories
        ->flatMap(fn ($cat) => $cat->courses)
        ->filter(fn ($course) => in_array($course->id, $enrolledCourseIds, true))
        ->unique('id')
        ->values();

    $totalCourses = $categories->flatMap(fn($c) => $c->courses->concat($c->children->flatMap->courses))->unique('id')->count();

    // ── Category + level overview (first-time-user orientation) ──
    $categoryOverview = $categories->map(function ($category) use ($levelPriority) {
        $courses = $category->courses->concat($category->children->flatMap->courses)->unique('id');
        $levels  = $courses->map(fn ($c) => trim($c->level ?: 'Beginner'))->unique()
            ->sortBy(fn ($lvl) => $levelPriority[strtolower($lvl)] ?? 99)
            ->values();
        $prices  = $courses->map(fn ($c) => $c->price ?? 0);

        return (object) [
            'category'  => $category,
            'count'     => $courses->count(),
            'levels'    => $levels,
            'minPrice'  => $prices->min() ?? 0,
            'maxPrice'  => $prices->max() ?? 0,
        ];
    })->filter(fn ($row) => $row->count > 0)->values();

    $categoryIcons = ['ti-code', 'ti-chart-bar', 'ti-palette', 'ti-speakerphone', 'ti-briefcase', 'ti-camera', 'ti-device-laptop', 'ti-brand-figma'];
@endphp

{{-- ══════════════════════════════════════════════════════════
     HERO HEADER
══════════════════════════════════════════════════════════ --}}
<div class="courses-hero">
    <div class="courses-hero-left">
        <div class="courses-hero-icon" aria-hidden="true">
            <i class="ti ti-books"></i>
        </div>
        <div>
            <div class="courses-hero-title">My Courses</div>
            <div class="courses-hero-sub">
                Browse every category and skill level — sorted low to high price so you always see the best entry point first.
            </div>
        </div>
    </div>

    <div class="courses-hero-right">
        <div class="courses-hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $enrolledCourses->count() }}</div>
                <div class="hero-stat-label">Enrolled</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $totalCourses }}</div>
                <div class="hero-stat-label">Available</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-val">{{ $categories->count() }}</div>
                <div class="hero-stat-label">Categories</div>
            </div>
        </div>

        <button class="theme-toggle" onclick="toggleAmTheme()" aria-label="Toggle dark mode">
            <i class="ti ti-moon" id="amThemeIcon"></i>
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     NEW: CATEGORY + LEVEL OVERVIEW — orientation for every visitor,
     especially first-time users who haven't picked a category yet.
══════════════════════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-card-head">
        <div>
            <div class="section-card-title">
                <i class="ti ti-layout-grid" aria-hidden="true"></i>
                Explore by Category &amp; Level
            </div>
            <div class="section-card-sub">See how many skill levels each category has, and their price range, before you dive in.</div>
        </div>
        <span class="section-card-count">{{ $categoryOverview->count() }} categor{{ $categoryOverview->count() === 1 ? 'y' : 'ies' }}</span>
    </div>

    <div class="category-overview-grid">
        @foreach ($categoryOverview as $i => $row)
            <button type="button" class="category-overview-card" data-jump="{{ $row->category->id }}">
                <div class="cov-top">
                    <div class="cov-icon"><i class="ti {{ $categoryIcons[$i % count($categoryIcons)] }}"></i></div>
                    <div>
                        <div class="cov-title">{{ $row->category->name }}</div>
                        <div class="cov-sub">{{ $row->count }} course{{ $row->count !== 1 ? 's' : '' }} · {{ $row->levels->count() }} level{{ $row->levels->count() !== 1 ? 's' : '' }}</div>
                    </div>
                </div>

                <div class="cov-levels">
                    @foreach ($row->levels as $lvl)
                        @php $lc = $levelColor($lvl); @endphp
                        <span class="cov-level-chip" style="color:{{ $lc['fg'] }};background:{{ $lc['soft'] }};">{{ $lvl }}</span>
                    @endforeach
                </div>

                <div class="cov-foot">
                    <div class="cov-price-range">
                        <small>Price range</small>
                        {{ $row->minPrice > 0 ? '₹'.number_format($row->minPrice) : 'Free' }}
                        @if($row->maxPrice > $row->minPrice)
                            – ₹{{ number_format($row->maxPrice) }}
                        @endif
                    </div>
                    <i class="ti ti-arrow-right cov-arrow" aria-hidden="true"></i>
                </div>
            </button>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ENROLLED COURSES
══════════════════════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-card-head">
        <div class="section-card-title">
            <i class="ti ti-star" aria-hidden="true"></i>
            My Enrolled Courses
        </div>
        <span class="section-card-count">{{ $enrolledCourses->count() }} course{{ $enrolledCourses->count() !== 1 ? 's' : '' }}</span>
    </div>

    <div class="course-grid">
        @forelse ($enrolledCourses as $course)
            @php
                $thumb = $course->thumbnail_url ?: '';
                $bg    = $thumb ? "url('{$thumb}')" : 'linear-gradient(135deg, #0947a8 0%, #7a5cff 100%)';
                $progress = $course->progress ?? 0;
                $lc = $levelColor($course->level ?? 'Beginner');
            @endphp

            <a class="course-tile"
               href="{{ route('student.courses.show', $course) }}"
               aria-label="Open {{ $course->title }}">

                <div class="course-thumb" style="background-image: {{ $bg }};">
                    <div class="course-thumb-overlay" aria-hidden="true"></div>
                    <span class="course-enrolled-badge" aria-label="Enrolled">
                        <i class="ti ti-check" aria-hidden="true"></i> Enrolled
                    </span>
                    @if ($course->is_new ?? false)
                        <span class="course-new-badge">New</span>
                    @endif
                    <h3>{{ $course->title }}</h3>
                </div>

                <div class="course-body">
                    <div class="course-meta">
                        <span class="course-level-tag" style="color:{{ $lc['fg'] }};background:{{ $lc['soft'] }};">{{ $course->level ?? 'Beginner' }}</span>
                        <span class="course-meta-dot" aria-hidden="true"></span>
                        <i class="ti ti-folder" aria-hidden="true" style="font-size:13px"></i>
                        {{ $course->category?->name ?? 'General' }}
                        <span class="course-meta-dot" aria-hidden="true"></span>
                        <i class="ti ti-clock" aria-hidden="true" style="font-size:13px"></i>
                        {{ $course->duration ?? '—' }}
                    </div>

                    @if ($progress > 0)
                        <div class="course-progress-wrap">
                            <div class="course-progress-row">
                                <span class="course-progress-label">Progress</span>
                                <span class="course-progress-pct">{{ $progress }}%</span>
                            </div>
                            <div class="course-progress-track">
                                <div class="course-progress-bar"
                                     style="width: {{ $progress }}%"
                                     role="progressbar"
                                     aria-valuenow="{{ $progress }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endif

                    <div class="course-foot">
                        <span class="btn btn-success btn-sm">
                            <i class="ti ti-player-play" aria-hidden="true"></i>
                            {{ $progress > 0 ? 'Continue' : 'Start Course' }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <i class="ti ti-books" aria-hidden="true"></i>
                <p>You haven't enrolled in any courses yet.<br>Browse below to find your first one!</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     BROWSE ALL COURSES — grouped by level, sorted low → high price
══════════════════════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-card-head">
        <div class="section-card-title">
            <i class="ti ti-layout-grid" aria-hidden="true"></i>
            Browse All Courses
        </div>
        <div class="filter-bar">
            <span class="sort-note"><i class="ti ti-sort-ascending" aria-hidden="true"></i> Sorted: Low → High Price</span>
            <div class="filter-search">
                <i class="ti ti-search" aria-hidden="true"></i>
                <input type="search"
                       id="courseSearch"
                       placeholder="Search courses…"
                       autocomplete="off"
                       aria-label="Search courses">
            </div>
        </div>
    </div>

    {{-- Category tab bar --}}
    <div class="category-tabs-wrap" id="categoryTabs" role="tablist" aria-label="Course categories">
        @foreach ($categories as $index => $category)
            @php
                $catCount = $category->courses
                    ->concat($category->children->flatMap->courses)
                    ->unique('id')->count();
            @endphp
            <button class="cat-tab {{ $index === 0 ? 'active' : '' }}"
                    type="button"
                    role="tab"
                    data-tab="{{ $category->id }}"
                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                    aria-controls="panel-{{ $category->id }}">
                {{ $category->name }}
                <span class="cat-count">{{ $catCount }}</span>
            </button>
        @endforeach
    </div>
<div class="category-dropdown-wrap">
    <select id="categoryDropdown" class="filter-select">
        @foreach ($categories as $index => $category)
            <option value="{{ $category->id }}" {{ $index === 0 ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>
    {{-- Tab panels --}}
    @foreach ($categories as $index => $category)
        @php
            $tabCourses = $category->courses
                ->concat($category->children->flatMap->courses)
                ->unique('id')
                ->values();

            $levelGroups = $tabCourses
                ->groupBy(fn ($c) => trim($c->level ?: 'Beginner'))
                ->sortBy(fn ($courses, $levelName) => $levelPriority[strtolower($levelName)] ?? 99, SORT_REGULAR, false);
        @endphp

        <div class="tab-panel {{ $index === 0 ? 'active' : '' }}"
             id="panel-{{ $category->id }}"
             role="tabpanel"
             data-tab-panel="{{ $category->id }}">

            {{-- Level snapshot pills --}}
            @if ($levelGroups->count() > 0)
                <div class="level-snapshot-row">
                    <span class="level-snapshot-label" aria-hidden="true">Levels:</span>
                    <button class="level-pill active" type="button" data-level="all" style="color:var(--brand-primary);">
                        <span class="level-dot" style="background:var(--brand-primary);"></span> All
                    </button>
                    @foreach ($levelGroups as $levelName => $levelCourses)
                        @php $lc = $levelColor($levelName); $slug = Str::slug($levelName); @endphp
                        <button class="level-pill" type="button" data-level="{{ $slug }}" style="color:{{ $lc['fg'] }};">
                            <span class="level-dot" style="background:{{ $lc['fg'] }};"></span>
                            {{ $levelName }} · {{ $levelCourses->count() }} · ₹{{ number_format($levelCourses->min('price')) }}–₹{{ number_format($levelCourses->max('price')) }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Sub-category pills --}}
            @if ($category->children->isNotEmpty())
                <div class="subcategory-row">
                    <span class="subcategory-label" aria-hidden="true">Filter:</span>
                    <button class="sub-pill active" type="button" data-subtab="all">All</button>
                    @foreach ($category->children as $child)
                        <button class="sub-pill" type="button" data-subtab="{{ $child->id }}">
                            {{ $child->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Level-grouped course sections --}}
            @forelse ($levelGroups as $levelName => $levelCourses)
                @php
                    $lc = $levelColor($levelName);
                    $slug = Str::slug($levelName);
                    $sortedCourses = $levelCourses->sortBy('price')->values();
                @endphp

                <div class="level-section" data-level-section="{{ $slug }}">
                    <div class="level-section-head">
                        <span class="level-dot" style="background:{{ $lc['fg'] }};"></span>
                        <h4>{{ $levelName }}</h4>
                        <span class="level-section-count">{{ $sortedCourses->count() }} course{{ $sortedCourses->count() !== 1 ? 's' : '' }}</span>
                        <span class="level-section-price">
                            <i class="ti ti-sort-ascending" aria-hidden="true"></i>
                            ₹{{ number_format($sortedCourses->min('price')) }} – ₹{{ number_format($sortedCourses->max('price')) }}
                        </span>
                    </div>

                    <div class="course-grid">
                        @foreach ($sortedCourses as $course)
                            @php
                                $thumb      = $course->thumbnail_url ?: '';
                                $bg         = $thumb ? "url('{$thumb}')" : 'linear-gradient(135deg, #0947a8 0%, #7a5cff 100%)';
                                $enrolled   = in_array($course->id, $enrolledCourseIds, true);
                                $inCart     = in_array($course->id, $cartIds, true);
                                $catLabel   = $course->subcategory?->name ?? $course->category?->name ?? $category->name;
                                $subCatId   = $course->subcategory?->id ? (string) $course->subcategory->id : 'none';
                                $price      = $course->price ?? 0;
                                $priceLabel = $price > 0 ? '₹' . number_format($price) : 'Free';
                            @endphp

                            @if ($enrolled)
                                {{-- ENROLLED TILE --}}
                                <a class="course-tile"
                                   href="{{ route('student.courses.show', $course) }}"
                                   data-subcat="{{ $subCatId }}"
                                   data-level="{{ $slug }}"
                                   data-title="{{ strtolower($course->title) }}"
                                   aria-label="Open {{ $course->title }}">

                                    <div class="course-thumb" style="background-image: {{ $bg }};">
                                        <div class="course-thumb-overlay" aria-hidden="true"></div>
                                        <span class="course-enrolled-badge">
                                            <i class="ti ti-check" aria-hidden="true"></i> Enrolled
                                        </span>
                                        <h3>{{ $course->title }}</h3>
                                    </div>

                                    <div class="course-body">
                                        <div class="course-meta">
                                            <span class="course-level-tag" style="color:{{ $lc['fg'] }};background:{{ $lc['soft'] }};">{{ $levelName }}</span>
                                            <span class="course-meta-dot" aria-hidden="true"></span>
                                            <i class="ti ti-folder" style="font-size:13px" aria-hidden="true"></i>
                                            {{ $catLabel }}
                                        </div>
                                        <div class="course-foot">
                                            <span class="btn btn-success btn-sm">
                                                <i class="ti ti-player-play" aria-hidden="true"></i>
                                                Open Course
                                            </span>
                                        </div>
                                    </div>
                                </a>

                            @else
                                {{-- LOCKED TILE --}}
                                <div class="course-tile is-locked"
                                     wire:key="course-tile-{{ $course->id }}"
                                     data-subcat="{{ $subCatId }}"
                                     data-level="{{ $slug }}"
                                     data-title="{{ strtolower($course->title) }}"
                                     tabindex="0"
                                     role="article"
                                     aria-label="{{ $course->title }} — locked, price {{ $priceLabel }}, level {{ $levelName }}">

                                    <a href="{{ route('student.courses.preview', $course) }}" style="text-decoration:none;color:inherit;">
                                        <div class="course-thumb" style="background-image: {{ $bg }};">
                                            <div class="course-thumb-overlay" aria-hidden="true"></div>
                                            <div class="course-lock-overlay" aria-hidden="true">
                                                <div class="course-lock-icon">
                                                    <i class="ti ti-lock"></i>
                                                </div>
                                            </div>
                                            <span class="course-level-badge" style="background:{{ $lc['fg'] }};">{{ $levelName }}</span>
                                            @if ($inCart)
                                                <span class="course-cart-badge"><i class="ti ti-shopping-cart"></i> In Cart</span>
                                            @endif
                                            <h3>{{ $course->title }}</h3>
                                        </div>
                                    </a>

                                    <div class="course-body">
                                        <div class="course-meta">
                                            <i class="ti ti-folder" style="font-size:13px" aria-hidden="true"></i>
                                            {{ $catLabel }}
                                            @if ($course->duration)
                                                <span class="course-meta-dot" aria-hidden="true"></span>
                                                <i class="ti ti-clock" style="font-size:13px" aria-hidden="true"></i>
                                                {{ $course->duration }}
                                            @endif
                                        </div>

                                        <div class="course-foot">
                                            <div class="price-tag" aria-label="Price: {{ $priceLabel }}">
                                                @if ($price > 0)
                                                    <span class="price-currency">₹</span>
                                                    <span>{{ number_format($price) }}</span>
                                                    @if ($course->original_price && $course->original_price > $price)
                                                        <span class="price-old">₹{{ number_format($course->original_price) }}</span>
                                                    @endif
                                                @else
                                                    <span class="price-free">Free</span>
                                                @endif
                                            </div>

                                            <div style="display:flex;gap:6px;">
                                                <a href="{{ route('student.courses.preview', $course) }}" class="btn btn-outline btn-sm btn-icon" aria-label="Preview {{ $course->title }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if ($inCart)
                                                    <button class="btn btn-success btn-sm" type="button" wire:click="removeFromCart({{ $course->id }})">
                                                        <i class="ti ti-check"></i> In Cart
                                                    </button>
                                                @else
                                                    <button class="btn btn-unlock btn-sm"
                                                            type="button"
                                                            wire:click="addToCart({{ $course->id }})"
                                                            aria-label="Add {{ $course->title }} to cart, price {{ $priceLabel }}">
                                                        <i class="ti ti-shopping-cart-plus" aria-hidden="true"></i>
                                                        Add to Cart
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="course-grid">
                    <div class="empty-state">
                        <i class="ti ti-mood-empty" aria-hidden="true"></i>
                        <p>No courses available in this category yet.</p>
                    </div>
                </div>
            @endforelse

        </div>{{-- /.tab-panel --}}
    @endforeach

</div>{{-- /.section-card --}}

{{-- ══════════════════════════════════════════════════════════
     CART DRAWER + FAB
══════════════════════════════════════════════════════════ --}}
<div class="cart-drawer-backdrop" id="amCartBackdrop" onclick="closeAmCart()"></div>

<div class="cart-drawer" id="amCartDrawer">
    <div class="cart-drawer-head">
        <h3><i class="ti ti-shopping-cart"></i> Your Cart</h3>
        <button class="modal-close" onclick="closeAmCart()" aria-label="Close cart"><i class="ti ti-x"></i></button>
    </div>

    <div class="cart-drawer-body">
        @forelse ($this->cartCourses as $course)
            <div class="cart-item" wire:key="cart-item-{{ $course->id }}">
                <div class="cart-item-thumb" style="background-image: url('{{ $course->thumbnail_url }}')"></div>
                <div class="cart-item-info">
                    <div class="cart-item-name">{{ $course->title }}</div>
                    <div class="cart-item-price">{{ ($course->price ?? 0) > 0 ? '₹'.number_format($course->price) : 'Free' }}</div>
                </div>
                <button class="cart-item-remove" type="button" wire:click="removeFromCart({{ $course->id }})" aria-label="Remove {{ $course->title }} from cart">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        @empty
            <div class="cart-empty">
                <i class="ti ti-shopping-cart-off"></i>
                Your cart is empty.<br>Add a course to get started.
            </div>
        @endforelse

        @error('cart') <small class="error-text">{{ $message }}</small> @enderror
        @error('payment') <small class="error-text">{{ $message }}</small> @enderror
    </div>

   <div class="cart-drawer-foot">
    <div class="cart-total-row" style="font-size:13px;font-weight:500;">
        <span>Subtotal</span>
        <span>₹{{ number_format($this->cartSubtotal, 2) }}</span>
    </div>
    <div class="cart-total-row" style="font-size:13px;font-weight:500;">
        <span>GST</span>
        <span>₹{{ number_format($this->cartGst, 2) }}</span>
    </div>
    <div class="cart-total-row">
        <span>Total</span>
        <span>{{ $this->cartTotal > 0 ? '₹'.number_format($this->cartTotal, 2) : 'Free' }}</span>
    </div>
    <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:10px;"
            type="button" wire:click="checkout" wire:loading.attr="disabled" wire:target="checkout"
            @if(empty($cartIds)) disabled @endif>
        <span wire:loading.remove wire:target="checkout"><i class="ti ti-credit-card"></i> Checkout</span>
        <span wire:loading wire:target="checkout"><i class="ti ti-loader-2"></i> Processing...</span>
    </button>
</div>
</div>

<button class="cart-fab" type="button" onclick="openAmCart()" aria-label="Open cart">
    <i class="ti ti-shopping-cart"></i>
    @if(count($cartIds) > 0)
        <span class="cart-fab-badge">{{ count($cartIds) }}</span>
    @endif
</button>

{{-- ══════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════ --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

@script
<script>
    /* ── Combined filter: subcategory + level + search, all AND'd together ── */
    function amApplyFilters(panel) {
        const activeSub   = panel.querySelector('.sub-pill.active')?.dataset.subtab || 'all';
        const activeLevel = panel.querySelector('.level-pill.active')?.dataset.level || 'all';
        const query       = (document.getElementById('courseSearch')?.value || '').toLowerCase().trim();

        panel.querySelectorAll('.course-tile').forEach(tile => {
            const subMatch    = activeSub   === 'all' || tile.dataset.subcat === activeSub;
            const levelMatch  = activeLevel === 'all' || tile.dataset.level  === activeLevel;
            const title       = tile.dataset.title || '';
            const searchMatch = !query || title.includes(query);
            tile.style.display = (subMatch && levelMatch && searchMatch) ? '' : 'none';
        });

        panel.querySelectorAll('.level-section').forEach(section => {
            const visible = [...section.querySelectorAll('.course-tile')].some(t => t.style.display !== 'none');
            section.style.display = visible ? '' : 'none';
        });

        amCheckEmpty(panel);
    }

    /* ── Category tabs, level pills, subcategory pills ─────────── */
    function amInitTabs() {
        document.querySelectorAll('.cat-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.tab;

                document.querySelectorAll('.cat-tab').forEach(b => {
                    b.classList.toggle('active', b === btn);
                    b.setAttribute('aria-selected', b === btn ? 'true' : 'false');
                });

                document.querySelectorAll('.tab-panel').forEach(p => {
                    p.classList.toggle('active', p.dataset.tabPanel === id);
                });

                const activePanel = document.querySelector(`.tab-panel[data-tab-panel="${id}"]`);
                if (activePanel) {
                    activePanel.querySelectorAll('.sub-pill').forEach(p => p.classList.toggle('active', p.dataset.subtab === 'all'));
                    activePanel.querySelectorAll('.level-pill').forEach(p => p.classList.toggle('active', p.dataset.level === 'all'));
                    amApplyFilters(activePanel);
                }
            });
        });

        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.querySelectorAll('.sub-pill').forEach(pill => {
                pill.addEventListener('click', () => {
                    panel.querySelectorAll('.sub-pill').forEach(p => p.classList.remove('active'));
                    pill.classList.add('active');
                    amApplyFilters(panel);
                });
            });

            panel.querySelectorAll('.level-pill').forEach(pill => {
                pill.addEventListener('click', () => {
                    panel.querySelectorAll('.level-pill').forEach(p => p.classList.remove('active'));
                    pill.classList.add('active');
                    amApplyFilters(panel);
                });
            });
        });
    }

    /* ── Search (applies across every panel so state stays consistent when switching tabs) ── */
    function amInitSearch() {
        const input = document.getElementById('courseSearch');
        if (!input) return;
        input.addEventListener('input', function () {
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.querySelectorAll('.sub-pill').forEach(p => p.classList.toggle('active', p.dataset.subtab === 'all'));
                panel.querySelectorAll('.level-pill').forEach(p => p.classList.toggle('active', p.dataset.level === 'all'));
                amApplyFilters(panel);
            });
        });
    }

    function amCheckEmpty(panel) {
        const visible = [...panel.querySelectorAll('.course-tile')].filter(t => t.style.display !== 'none');
        let empty = panel.querySelector('.course-grid .empty-state-dynamic');
        if (visible.length === 0) {
            if (!empty) {
                const firstGrid = panel.querySelector('.course-grid');
                if (firstGrid) {
                    empty = document.createElement('div');
                    empty.className = 'empty-state empty-state-dynamic';
                    empty.innerHTML = '<i class="ti ti-mood-empty"></i><p>No courses match your search.</p>';
                    firstGrid.appendChild(empty);
                }
            }
        } else {
            empty?.remove();
        }
    }

    /* ── Category overview cards jump to the matching tab ─────────── */
    function amInitCategoryOverview() {
        document.querySelectorAll('.category-overview-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.dataset.jump;
                document.querySelector(`.cat-tab[data-tab="${id}"]`)?.click();
                document.getElementById('categoryTabs')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    amInitTabs();
    amInitSearch();
    amInitCategoryOverview();

    /* ── Cart drawer ─────────────────────────────────────── */
    window.openAmCart = function () {
        document.getElementById('amCartDrawer').classList.add('open');
        document.getElementById('amCartBackdrop').classList.add('open');
    };
    window.closeAmCart = function () {
        document.getElementById('amCartDrawer').classList.remove('open');
        document.getElementById('amCartBackdrop').classList.remove('open');
    };

    /* ── Dark / light mode ─────────────────────────────────── */
    function amApplyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        const icon = document.getElementById('amThemeIcon');
        if (icon) icon.className = theme === 'dark' ? 'ti ti-sun' : 'ti ti-moon';
        localStorage.setItem('am-theme', theme);
    }
    window.toggleAmTheme = function () {
        const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        amApplyTheme(current === 'dark' ? 'light' : 'dark');
    };
    amApplyTheme(localStorage.getItem('am-theme') || 'light');

    /* ── Razorpay checkout ─────────────────────────────────── */
    $wire.on('razorpay-checkout-open', (payload) => {
        const data = Array.isArray(payload) ? payload[0] : payload;

        const options = {
            key: data.key,
            amount: data.amount,
            currency: data.currency,
            name: data.name,
            description: data.description,
            order_id: data.order_id,
            prefill: data.prefill,
            theme: { color: '#0947a8' },
            handler: function (response) {
                $wire.verifyPayment({
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature: response.razorpay_signature,
                });
            },
            modal: {
                ondismiss: function () {
                    $wire.paymentFailed('Checkout closed by user');
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
            $wire.paymentFailed(response.error.description);
        });
        rzp.open();
    });

 $wire.on('payment-success', (e) => {
    closeAmCart();

    const data = Array.isArray(e) ? e[0] : e;
    const count = data?.courseCount;
    const invoiceUrl = data?.invoiceUrl;

    Swal.fire({
        icon: 'success',
        title: 'Payment Successful!',
        html: `
            <p>You have successfully enrolled in <strong>${count ?? 0}</strong> course(s).</p>
            <p>Do you want to download your invoice?</p>
        `,
        confirmButtonText: 'Download Invoice',
        showCancelButton: true,
        cancelButtonText: 'Later',
        confirmButtonColor: '#0947a8',
        cancelButtonColor: '#6c757d',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed && invoiceUrl) {
            window.open(invoiceUrl, '_blank');
        }

        window.location.reload();
    });
});
    function amInitCategoryDropdown() {
    const dropdown = document.getElementById('categoryDropdown');
    if (!dropdown) return;
    dropdown.addEventListener('change', function () {
        document.querySelector(`.cat-tab[data-tab="${this.value}"]`)?.click();
    });
}
amInitCategoryDropdown();
</script>
@endscript

</div>