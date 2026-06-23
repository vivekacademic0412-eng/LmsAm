@extends('layouts.app')



@php
    $roleLabels = \App\Models\User::roleOptions();
@endphp

@section('title', 'My Profile — Academic Mantra LMS')

@section('content')

<style>
/* ═══════════════════════════════════════════════
   PROFILE PAGE LAYOUT
═══════════════════════════════════════════════ */
.profile-page { display: flex; flex-direction: column; gap: 22px; }

/* ═══════════════════════════════════════════════
   HERO CARD
═══════════════════════════════════════════════ */
.profile-hero {
    background: linear-gradient(135deg, #0947a8 0%, #7a5cff 60%, #4a2fa8 100%);
    border-radius: var(--radius);
    padding: 32px;
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(9,71,168,.3);
}

/* Decorative blobs */
.profile-hero::before,
.profile-hero::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}
.profile-hero::before {
    width: 320px; height: 320px;
    background: rgba(255,255,255,.06);
    top: -100px; right: -60px;
}
.profile-hero::after {
    width: 180px; height: 180px;
    background: rgba(240,179,90,.1);
    bottom: -60px; left: 120px;
}

/* Avatar wrapper */
.profile-hero-avatar {
    position: relative;
    flex-shrink: 0;
    z-index: 1;
}
.profile-hero-avatar-ring {
    width: 96px; height: 96px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 3px solid rgba(255,255,255,.35);
    backdrop-filter: blur(8px);
    overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    font-size: 34px; font-weight: 800; color: #fff;
    box-shadow: 0 6px 24px rgba(0,0,0,.25);
}
.profile-hero-avatar-ring img {
    width: 100%; height: 100%; object-fit: cover;
}
.profile-hero-avatar-online {
    position: absolute;
    bottom: 4px; right: 4px;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: var(--success);
    border: 3px solid rgba(255,255,255,.5);
    box-shadow: 0 2px 6px rgba(0,0,0,.2);
}

/* Hero info */
.profile-hero-info {
    flex: 1;
    z-index: 1;
}
.profile-hero-name {
    font-size: 22px; font-weight: 800;
    color: #fff; margin-bottom: 4px;
    letter-spacing: -.3px;
}
.profile-hero-email {
    font-size: 13.5px;
    color: rgba(255,255,255,.7);
    margin-bottom: 12px;
}
.profile-hero-badges { display: flex; flex-wrap: wrap; gap: 8px; }
.profile-hero-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    backdrop-filter: blur(6px);
}
.profile-hero-badge.active  { background: rgba(22,163,74,.25);  border-color: rgba(22,163,74,.4); }
.profile-hero-badge.inactive{ background: rgba(220,38,38,.25); border-color: rgba(220,38,38,.4); }

/* Hero quick stats */
.profile-hero-stats {
    display: flex; gap: 12px; flex-wrap: wrap;
    z-index: 1;
}
.hero-quick-stat {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(8px);
    border-radius: var(--radius-sm);
    padding: 12px 20px;
    text-align: center;
    min-width: 80px;
}
.hero-quick-stat-val   { font-size: 18px; font-weight: 800; color: #fff; line-height: 1; }
.hero-quick-stat-label { font-size: 10px; color: rgba(255,255,255,.65); text-transform: uppercase; letter-spacing: .5px; margin-top: 3px; }

/* ═══════════════════════════════════════════════
   STATS ROW
═══════════════════════════════════════════════ */
.profile-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
}
.profile-stat-card {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
    box-shadow: var(--shadow-card);
    transition: box-shadow .15s, transform .15s;
}
.profile-stat-card:hover { box-shadow: var(--shadow); transform: translateY(-2px); }
.profile-stat-icon {
    width: 42px; height: 42px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 19px; flex-shrink: 0;
}
.profile-stat-icon.blue   { background: rgba(9,71,168,.1);   color: var(--brand-primary); }
.profile-stat-icon.green  { background: rgba(22,163,74,.1);  color: var(--success); }
.profile-stat-icon.purple { background: rgba(122,92,255,.1); color: var(--brand-secondary); }
.profile-stat-icon.amber  { background: rgba(240,179,90,.1); color: var(--brand-accent); }
.profile-stat-label { font-size: 11px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 3px; }
.profile-stat-value { font-size: 15px; font-weight: 700; color: var(--text); word-break: break-all; }

/* ═══════════════════════════════════════════════
   SECTION CARD
═══════════════════════════════════════════════ */
.p-card {
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-card);
}
.p-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 24px;
    border-bottom: 1.5px solid var(--line);
    gap: 10px; flex-wrap: wrap;
}
.p-card-head-left { display: flex; align-items: center; gap: 10px; }
.p-card-head-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: var(--primary-glow);
    color: var(--brand-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0;
}
.p-card-title { font-size: 14px; font-weight: 700; color: var(--text); }
.p-card-sub   { font-size: 12.5px; color: var(--text-muted); margin-top: 1px; }
.p-card-body  { padding: 24px; }

/* ═══════════════════════════════════════════════
   VALIDATION ERRORS
═══════════════════════════════════════════════ */
.error-alert {
    display: flex; align-items: flex-start; gap: 12px;
    background: rgba(220,38,38,.07);
    border: 1.5px solid rgba(220,38,38,.2);
    border-radius: var(--radius-sm);
    padding: 14px 18px;
}
.error-alert i { font-size: 18px; color: var(--danger); flex-shrink: 0; margin-top: 1px; }
.error-alert ul { margin: 0; padding-left: 16px; }
.error-alert li { font-size: 13px; color: var(--danger); font-weight: 500; margin-bottom: 2px; }
.error-alert li:last-child { margin-bottom: 0; }

/* ═══════════════════════════════════════════════
   FORM ELEMENTS
═══════════════════════════════════════════════ */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}
.form-grid.full { grid-template-columns: 1fr; }
.form-col-full  { grid-column: 1 / -1; }

.form-group { display: flex; flex-direction: column; gap: 6px; }

.form-label {
    font-size: 13px; font-weight: 600;
    color: var(--text);
    display: flex; align-items: center; gap: 4px;
}
.form-label .req { color: var(--danger); }

.input-wrap { position: relative; display: flex; align-items: center; }
.input-icon {
    position: absolute; left: 12px;
    color: var(--text-muted); font-size: 16px;
    pointer-events: none; z-index: 1;
    transition: color .2s;
}
.input-wrap:focus-within .input-icon { color: var(--brand-primary); }

.form-input {
    width: 100%;
    padding: 10px 14px 10px 40px;
    background: var(--input-bg);
    border: 1.5px solid var(--input-border);
    border-radius: var(--radius-xs);
    font-size: 13.5px; color: var(--text);
    font-family: inherit;
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}
.form-input::placeholder { color: var(--text-muted); }
.form-input:focus {
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px var(--primary-glow);
    background: var(--bg-card);
}
.form-input.is-invalid { border-color: var(--danger); box-shadow: 0 0 0 3px rgba(220,38,38,.1); }

.form-input-file {
    width: 100%;
    padding: 9px 14px;
    background: var(--input-bg);
    border: 1.5px dashed var(--input-border);
    border-radius: var(--radius-xs);
    font-size: 13px; color: var(--text);
    font-family: inherit;
    cursor: pointer;
    transition: border-color .2s;
    outline: none;
}
.form-input-file:hover  { border-color: var(--input-focus); }
.form-input-file:focus  { border-color: var(--input-focus); box-shadow: 0 0 0 3px var(--primary-glow); }

.field-hint  { font-size: 11.5px; color: var(--text-muted); }
.field-error { font-size: 12px; color: var(--danger); font-weight: 500; display: flex; align-items: center; gap: 4px; }
.field-error i { font-size: 11px; }

/* ═══════════════════════════════════════════════
   UPLOAD DROPZONE PREVIEW
═══════════════════════════════════════════════ */
.upload-zone {
    border: 2px dashed var(--input-border);
    border-radius: var(--radius-sm);
    padding: 24px;
    text-align: center;
    background: var(--input-bg);
    transition: border-color .2s, background .2s;
    cursor: pointer;
    position: relative;
}
.upload-zone:hover { border-color: var(--input-focus); background: var(--primary-glow); }
.upload-zone input[type="file"] {
    position: absolute; inset: 0;
    opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.upload-zone-icon { font-size: 32px; color: var(--text-muted); margin-bottom: 8px; }
.upload-zone-title { font-size: 13.5px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.upload-zone-sub   { font-size: 12px; color: var(--text-muted); }

/* ═══════════════════════════════════════════════
   AVATAR GRID
═══════════════════════════════════════════════ */
.avatar-grid {
    display: flex; flex-wrap: wrap; gap: 12px;
}
.avatar-item {
    width: 68px; height: 68px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    padding: 0; background: none;
    transition: transform .18s, border-color .18s, box-shadow .18s;
    position: relative;
    flex-shrink: 0;
}
.avatar-item img {
    width: 100%; height: 100%; object-fit: cover;
    border-radius: 50%;
    display: block;
    pointer-events: none;
}
.avatar-item:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 14px rgba(9,71,168,.25);
    border-color: var(--primary);
}
.avatar-item.selected {
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 3px var(--primary-glow), 0 4px 14px rgba(9,71,168,.25);
    transform: scale(1.1);
}
.avatar-item.selected::after {
    content: '\e9e6'; /* ti-check */
    font-family: 'tabler-icons';
    position: absolute;
    bottom: 2px; right: 2px;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: var(--brand-primary);
    color: #fff;
    font-size: 11px;
    display: flex; align-items: center; justify-content: center;
    line-height: 18px;
    text-align: center;
    border: 2px solid var(--bg-card);
}

/* ═══════════════════════════════════════════════
   SAVE BUTTON ROW
═══════════════════════════════════════════════ */
.save-row {
    display: flex; align-items: center;
    justify-content: flex-end;
    gap: 12px;
    padding: 18px 24px;
    border-top: 1.5px solid var(--line);
    background: var(--bg2);
}
.btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 22px;
    border-radius: var(--radius-xs);
    font-size: 13.5px; font-weight: 600;
    border: 1.5px solid transparent;
    cursor: pointer; font-family: inherit;
    transition: all .15s;
    text-decoration: none;
}
.btn-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    color: #fff;
    box-shadow: 0 4px 14px rgba(9,71,168,.28);
}
.btn-primary:hover  { opacity: .88; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(9,71,168,.35); }
.btn-primary:active { transform: translateY(0); }
.btn-primary:disabled { opacity: .55; cursor: not-allowed; transform: none; }

.btn-ghost {
    background: transparent;
    color: var(--text-muted);
    border-color: var(--line);
}
.btn-ghost:hover { border-color: var(--primary); color: var(--brand-primary); background: var(--primary-glow); }

.btn-spinner {
    width: 16px; height: 16px;
    border: 2.5px solid rgba(255,255,255,.35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin .7s linear infinite;
    flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ═══════════════════════════════════════════════
   DANGER ZONE
═══════════════════════════════════════════════ */
.danger-zone {
    border: 1.5px solid rgba(220,38,38,.25);
    border-radius: var(--radius);
    background: rgba(220,38,38,.03);
    overflow: hidden;
}
.danger-zone .p-card-head { border-bottom-color: rgba(220,38,38,.15); }
.danger-zone .p-card-head-icon {
    background: rgba(220,38,38,.1);
    color: var(--danger);
}
.danger-zone .p-card-title { color: var(--danger); }
.danger-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 0;
    border-bottom: 1px solid rgba(220,38,38,.1);
    gap: 16px; flex-wrap: wrap;
}
.danger-row:last-child { border-bottom: none; padding-bottom: 0; }
.danger-row-text strong { font-size: 13.5px; color: var(--text); font-weight: 600; display: block; margin-bottom: 2px; }
.danger-row-text span   { font-size: 12px; color: var(--text-muted); }
.btn-danger {
    background: rgba(220,38,38,.08);
    color: var(--danger);
    border-color: rgba(220,38,38,.25);
    white-space: nowrap;
}
.btn-danger:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media (max-width: 768px) {
    .form-grid            { grid-template-columns: 1fr; }
    .form-col-full        { grid-column: 1; }
    .profile-hero         { flex-direction: column; text-align: center; padding: 24px 20px; }
    .profile-hero-badges  { justify-content: center; }
    .profile-hero-stats   { justify-content: center; }
    .profile-stats-grid   { grid-template-columns: 1fr 1fr; }
    .p-card-body          { padding: 16px; }
    .p-card-head          { padding: 14px 16px; }
    .save-row             { padding: 14px 16px; }
}
@media (max-width: 480px) {
    .profile-stats-grid { grid-template-columns: 1fr; }
}
</style>

<livewire:user.profile-studio />

<style>
    .swal-rounded      { border-radius: 18px !important; }
    .swal-btn-danger   { border-radius: 10px !important; font-size: 13.5px !important; font-weight: 600 !important; }
    .swal-btn-cancel   { border-radius: 10px !important; font-size: 13.5px !important; font-weight: 600 !important; border: 1.5px solid var(--line) !important; color: var(--text-muted) !important; }
    .btn-sm { padding: 7px 14px; font-size: 12.5px; }
</style>

@endsection

