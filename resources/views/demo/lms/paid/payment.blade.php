{{-- resources/views/demo/lms/payment.blade.php --}}
@extends('layouts.app')
@section('title', 'Secure Enrollment Payment')

@section('content')
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .pay-page {
        min-height: 100vh;
        background: var(--bg);
        display: flex;
        align-items: stretch;
    }

    /* ══════════════════════════════
       LEFT HERO PANEL
    ══════════════════════════════ */
    .pay-hero {
        width: 44%;
        background: linear-gradient(155deg, #062f75 0%, var(--brand-primary) 45%, #0d1e50 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 44px;
        position: relative;
        overflow: hidden;
    }

    .pay-hero::before {
        content: '';
        position: absolute;
        top: -100px; right: -100px;
        width: 340px; height: 340px;
        border-radius: 50%;
        background: rgba(122,92,255,.2);
        pointer-events: none;
    }
    .pay-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: -60px;
        width: 240px; height: 240px;
        border-radius: 50%;
        background: rgba(240,179,90,.12);
        pointer-events: none;
    }
    .pay-hero-deco {
        position: absolute;
        top: 50%; left: -80px;
        transform: translateY(-50%);
        width: 200px; height: 200px;
        border-radius: 50%;
        border: 40px solid rgba(255,255,255,.04);
        pointer-events: none;
    }

    /* Logo */
    .pay-logo {
        display: flex; align-items: center; gap: 10px;
        position: relative; z-index: 1;
    }
    .pay-logo-mark {
        width: 38px; height: 38px;
        background: rgba(255,255,255,.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; color: #fff;
        backdrop-filter: blur(6px);
    }
    .pay-logo span { font-size: 14px; font-weight: 700; color: rgba(255,255,255,.85); }

    /* Main hero content */
    .pay-hero-body { position: relative; z-index: 1; }

    .pay-hero-tag {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255,255,255,.12);
        color: rgba(255,255,255,.9);
        font-size: 10.5px; font-weight: 700;
        letter-spacing: .8px; text-transform: uppercase;
        padding: 5px 13px; border-radius: 99px;
        margin-bottom: 20px;
        backdrop-filter: blur(4px);
    }

    .pay-hero-body h1 {
        font-size: clamp(1.6rem, 2.4vw, 2.2rem);
        font-weight: 800; color: #fff;
        line-height: 1.22; letter-spacing: -.4px;
        margin-bottom: 14px;
    }
    .pay-hero-body h1 em {
        font-style: normal;
        color: var(--brand-accent);
    }
    .pay-hero-body > p {
        font-size: 13px; color: rgba(255,255,255,.65);
        line-height: 1.72; max-width: 330px;
        margin-bottom: 30px;
    }

    /* Feature grid */
    .feat-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 32px;
    }
    .feat-box {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.13);
        border-radius: 14px;
        padding: 15px 14px;
        display: flex; align-items: flex-start; gap: 11px;
        backdrop-filter: blur(4px);
        transition: background .2s;
    }
    .feat-box:hover { background: rgba(255,255,255,.13); }
    .feat-icon {
        width: 36px; height: 36px; flex-shrink: 0;
        background: rgba(240,179,90,.18);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; color: var(--brand-accent);
    }
    .feat-box h4 { font-size: 12.5px; font-weight: 700; color: #fff; margin-bottom: 2px; }
    .feat-box p  { font-size: 11px; color: rgba(255,255,255,.55); line-height: 1.45; }

    /* Testimonial */
    .hero-testimonial {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 14px;
        padding: 16px;
        backdrop-filter: blur(4px);
    }
    .hero-testimonial p {
        font-size: 12px; color: rgba(255,255,255,.75);
        font-style: italic; line-height: 1.6; margin-bottom: 10px;
    }
    .testi-author {
        display: flex; align-items: center; gap: 9px;
    }
    .testi-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: linear-gradient(135deg, var(--brand-accent), var(--brand-secondary));
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800; color: #fff;
    }
    .testi-name { font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,.85); }
    .testi-role { font-size: 10.5px; color: rgba(255,255,255,.5); }
    .testi-stars { margin-left: auto; color: var(--brand-accent); font-size: 11px; }

    /* Hero footer */
    .pay-hero-footer { position: relative; z-index: 1; }
    .hero-pills { display: flex; gap: 9px; flex-wrap: wrap; }
    .hero-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.7);
        font-size: 11px; font-weight: 600;
        padding: 6px 12px; border-radius: 99px;
    }
    .hero-pill i { color: var(--brand-accent); font-size: 11px; }

    /* ══════════════════════════════
       RIGHT FORM PANEL
    ══════════════════════════════ */
    .pay-form-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 44px 48px;
        background: var(--bg);
        overflow-y: auto;
    }

    .pf-header { margin-bottom: 24px; }
    .pf-header h2 {
        font-size: 1.4rem; font-weight: 800;
        color: var(--text); letter-spacing: -.3px; margin-bottom: 4px;
    }
    .pf-header p { font-size: 13px; color: var(--text-muted); }

    /* Price banner */
    .price-banner {
        display: flex; align-items: center; justify-content: space-between;
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
        border-radius: 16px;
        padding: 18px 22px;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
    }
    .price-banner::after {
        content: '';
        position: absolute;
        right: -40px; top: -40px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,.07);
    }
    .price-banner-left { position: relative; z-index: 1; }
    .price-banner-left span { font-size: 11px; color: rgba(255,255,255,.7); font-weight: 600; display: block; margin-bottom: 3px; }
    .price-banner-left strong { font-size: 28px; font-weight: 900; color: #fff; }
    .price-banner-right { position: relative; z-index: 1; text-align: right; }
    .price-banner-right .badge-access {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,.15);
        color: #fff; font-size: 11px; font-weight: 700;
        padding: 5px 12px; border-radius: 99px;
        backdrop-filter: blur(4px);
    }

    /* Form fields */
    .field-group {
        margin-bottom: 16px;
    }
    .field-group label {
        font-size: 12px; font-weight: 700;
        color: var(--text); display: block; margin-bottom: 6px;
        letter-spacing: .2px;
    }
    .custom-input {
        width: 100%;
        padding: 11px 14px;
        font-size: 13.5px;
        color: var(--text);
        background: var(--input-bg, #f4f8ff);
        border: 1.5px solid var(--input-border, #c8daf0);
        border-radius: 11px;
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        font-family: inherit;
        appearance: none;
    }
    .custom-input::placeholder { color: var(--text-muted); }
    .custom-input:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
        background: var(--bg-card);
    }
    .error-text { font-size: 11.5px; color: var(--danger, #dc2626); margin-top: 4px; display: block; }

    .field-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
    }

    /* Benefits strip */
    .benefit-strip {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 8px; margin-bottom: 20px; margin-top: 4px;
    }
    .benefit-item {
        display: flex; align-items: center; gap: 8px;
        background: var(--bg-card);
        border: 1px solid var(--line);
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 12px; color: var(--text);
        font-weight: 600;
    }
    .benefit-item i { color: var(--brand-green); font-size: 13px; }

    /* Pay button */
    .pay-btn {
        width: 100%;
        display: flex; align-items: center; justify-content: center; gap: 10px;
        padding: 15px 24px;
        font-size: 15px; font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
        border: none; border-radius: 13px;
        cursor: pointer;
        transition: transform .16s, filter .16s, opacity .16s;
        box-shadow: 0 8px 28px var(--primary-glow);
        font-family: inherit;
        margin-bottom: 14px;
    }
    .pay-btn:hover:not([disabled]) { transform: translateY(-2px); filter: brightness(1.07); }
    .pay-btn[disabled] { opacity: .5; cursor: not-allowed; transform: none; }
    .pay-btn i { font-size: 15px; }

    /* Trust row */
    .trust-row {
        display: flex; align-items: center; justify-content: center; gap: 18px;
        flex-wrap: wrap;
    }
    .trust-row span {
        font-size: 11px; color: var(--text-muted);
        display: flex; align-items: center; gap: 5px;
    }
    .trust-row i { color: var(--brand-green); font-size: 12px; }

    /* ══════════════════════════════
       SUCCESS OVERLAY
    ══════════════════════════════ */
    .success-overlay {
        position: fixed; inset: 0;
        background: rgba(8,17,31,.7);
        display: flex; align-items: center; justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(6px);
    }
    .success-card {
        background: var(--bg-card);
        border-radius: 22px;
        padding: 48px 40px;
        text-align: center;
        max-width: 400px; width: 90%;
        box-shadow: 0 30px 80px rgba(0,0,0,.3);
    }
    .success-icon-wrap {
        width: 72px; height: 72px; border-radius: 50%;
        background: linear-gradient(135deg, #16a34a, #22c55e);
        display: flex; align-items: center; justify-content: center;
        font-size: 30px; color: #fff;
        margin: 0 auto 20px;
        box-shadow: 0 8px 28px rgba(22,163,74,.3);
    }
    .success-card h2 { font-size: 1.4rem; font-weight: 800; color: var(--text); margin-bottom: 8px; }
    .success-card p  { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.6; }
    .progress-track {
        background: var(--line); border-radius: 99px; height: 5px;
        overflow: hidden; margin-bottom: 14px;
    }
    .progress-fill {
        height: 100%; width: 0%;
        background: linear-gradient(90deg, #16a34a, #22c55e);
        border-radius: 99px;
        animation: fillBar 3s linear forwards;
    }
    @keyframes fillBar { to { width: 100%; } }
    .redirect-txt { font-size: 12px; color: var(--text-muted); }

    /* Responsive */
    @media (max-width: 920px) {
        .pay-page { flex-direction: column; }
        .pay-hero { width: 100%; padding: 36px 28px; }
        .pay-hero::before, .pay-hero::after, .pay-hero-deco { display: none; }
        .pay-form-panel { padding: 36px 24px; }
    }
    @media (max-width: 540px) {
        .feat-grid { grid-template-columns: 1fr; }
        .field-row { grid-template-columns: 1fr; }
        .benefit-strip { grid-template-columns: 1fr; }
        .price-banner { flex-direction: column; gap: 12px; align-items: flex-start; }
    }

</style>
  <livewire:payment-form />
@endsection