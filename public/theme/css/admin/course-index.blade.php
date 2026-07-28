/* ═══════════════════════════════════════════════
   COURSE INDEX PAGE — hero, filters, tabs, grid, modal
   Append to course-components.css (uses the same tokens/cc- classes)
═══════════════════════════════════════════════ */

/* ── Hero banner ── */
.cc-hero {
    border-radius: var(--radius);
    padding: 28px;
    background: linear-gradient(120deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
    color: #fff;
    box-shadow: var(--shadow);
    margin-bottom: 20px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;
}
.cc-hero-title { font-size: 26px; font-weight: 700; margin-bottom: 6px; }
.cc-hero-meta { font-size: 13.5px; opacity: .92; }

/* ── Filter bar ── */
.cc-filter-bar {
    display: flex; justify-content: space-between; align-items: center;
    gap: 12px; margin-bottom: 18px; position: relative; flex-wrap: wrap;
}
.cc-filter-panel {
    position: absolute; top: 46px; left: 0; z-index: 20;
    background: var(--bg-card); border: 1px solid var(--line); border-radius: var(--radius-sm);
    box-shadow: var(--shadow); padding: 18px; min-width: 320px;
    display: none;
}
.cc-filter-panel.open { display: block; }
.cc-filter-panel .cc-field { margin-bottom: 14px; }
.cc-filter-panel .cc-actions { margin-top: 4px; }

/* ── Tabs (trainer browse view) ── */
.cc-tabs { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-bottom: 18px; }
.cc-tab-btn {
    padding: 9px 20px; border-radius: 999px; font-size: 13.5px; font-weight: 600;
    border: 1.5px solid var(--line); background: var(--bg-card); color: var(--text-muted);
    transition: all .15s;
}
.cc-tab-btn.active, .cc-tab-btn:hover { background: var(--brand-primary); color: #fff; border-color: var(--brand-primary); }

.cc-subtab-label { font-size: 11.5px; text-transform: uppercase; letter-spacing: .04em; color: var(--text-muted); margin-bottom: 8px; font-weight: 700; }
.cc-subtabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
.cc-subtab-btn {
    padding: 6px 14px; border-radius: 999px; font-size: 12.5px; font-weight: 600;
    border: 1px solid var(--line); background: var(--bg-card2); color: var(--text-muted);
}
.cc-subtab-btn.active { background: var(--primary-glow); color: var(--brand-primary); border-color: var(--brand-primary); }

.cc-divider { height: 1px; background: var(--line); margin: 16px 0; }

.cc-tab-panel { display: none; }
.cc-tab-panel.active { display: block; }

/* ── Course grid / tiles ── */
.cc-course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 18px; }
.cc-course-tile {
    display: block; border-radius: var(--radius-sm); overflow: hidden;
    background: var(--bg-card); border: 1px solid var(--line); box-shadow: var(--shadow-card);
    transition: transform .15s, box-shadow .15s;
}
.cc-course-tile:not(.disabled):hover { transform: translateY(-3px); box-shadow: var(--shadow); }
.cc-course-tile.disabled { opacity: .6; cursor: not-allowed; }

.cc-tile-top {
    height: 110px; background-size: cover; background-position: center;
    display: flex; align-items: flex-end; padding: 12px;
    background-color: var(--brand-secondary);
}
.cc-tile-top h3 { color: #fff; font-size: 14.5px; font-weight: 700; text-shadow: 0 2px 6px rgba(0,0,0,.4); line-height: 1.3; }

.cc-tile-body { padding: 14px; }
.cc-tile-meta { font-size: 12px; color: var(--text-muted); margin-bottom: 10px; }

/* ── Course list row (admin table thumbnail) ── */
.cc-course-thumb { width: 56px; height: 56px; border-radius: var(--radius-xs); object-fit: cover; flex-shrink: 0; }
.cc-course-thumb-placeholder {
    width: 56px; height: 56px; border-radius: var(--radius-xs); background: var(--bg2);
    color: var(--text-muted); font-size: 9px; font-weight: 700; display: flex; align-items: center; justify-content: center; text-align: center; flex-shrink: 0;
}
.cc-course-title-cell { display: flex; align-items: center; gap: 12px; }
.cc-course-title-cell .name { font-weight: 600; color: var(--text); display: block; }
.cc-course-title-cell .meta { font-size: 12px; color: var(--text-muted); display: block; margin-top: 2px; }

/* ── Modal ── */
.cc-modal-overlay {
    position: fixed; inset: 0; background: rgba(14,31,54,.55);
    display: flex; align-items: center; justify-content: center; z-index: 60; padding: 20px;
}
.cc-modal {
    background: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow);
    max-width: 680px; width: 100%; max-height: 90vh; overflow-y: auto;
}
.cc-modal-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 24px; border-bottom: 1px solid var(--line);
}
.cc-modal-head h3 { font-size: 17px; font-weight: 700; color: var(--text); }
.cc-modal-body { padding: 24px; }
.cc-modal-footer { padding: 16px 24px; border-top: 1px solid var(--line); display: flex; justify-content: flex-end; gap: 10px; }
.cc-modal-close {
    width: 30px; height: 30px; border-radius: 50%; border: 1px solid var(--line);
    background: var(--bg-card2); color: var(--text-muted); font-size: 14px;
}
.cc-modal-close:hover { background: var(--bg2); color: var(--text); }

/* Thumbnail preview in forms */
.cc-thumb-preview {
    width: 100%; height: 120px; border-radius: var(--radius-xs); object-fit: cover;
    border: 1px solid var(--line); margin-top: 8px;
}