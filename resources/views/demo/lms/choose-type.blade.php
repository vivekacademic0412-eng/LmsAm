{{-- resources/views/demo/lms/choose-type.blade.php --}}
@extends('layouts.app')
@section('title', 'Choose Your Demo Type')

@section('content')
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-primary: #1a3faa;
            --brand-secondary: #7a5cff;
            --brand-accent: #f0b35a;
            --brand-green: #16a34a;
            --primary-glow: rgba(26, 63, 170, .12);
            --bg: #f4f6fb;
            --bg-card: #ffffff;
            --text: #0f1724;
            --text-muted: #6b7a99;
            --line: #e2e7f0;
            --radius-lg: 18px;
            --radius-md: 12px;
        }

        /* ── PAGE SHELL ─────────────────────────────────────────── */
        .ct-page {
            min-height: 100vh;
            background: var(--bg);
            display: flex;
            align-items: stretch;
        }

        /* ── LEFT FORM PANEL ────────────────────────────────────── */
        .ct-form-panel {
            width: 52%;
            display: flex;
            flex-direction: column;
            padding: 44px 52px;
            background: var(--bg-card);
            overflow-y: auto;
        }

        /* LOGO */
        .ct-form-logo {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 36px;
        }

        .ct-form-logo span {
            font-size: 14px;
            font-weight: 800;
            color: var(--brand-primary);
            letter-spacing: .2px;
        }

        .logo-dot {
            width: 30px;
            height: 30px;
            background: var(--brand-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-dot svg {
            color: #fff;
        }

        /* STEP BAR */
        .steps-bar {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .step-dot {
            width: 27px;
            height: 27px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            border: 2px solid var(--line);
            color: var(--text-muted);
            background: var(--bg);
            flex-shrink: 0;
            transition: all .2s;
        }

        .step-item.active .step-dot {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
            color: #fff;
        }

        .step-item.done .step-dot {
            background: var(--brand-green);
            border-color: var(--brand-green);
            color: #fff;
        }

        .step-label {
            color: var(--text-muted);
            font-size: 11.5px;
            font-weight: 600;
        }

        .step-item.active .step-label {
            color: var(--text);
            font-weight: 700;
        }

        .step-line {
            flex: 1;
            height: 1.5px;
            background: var(--line);
            margin: 0 10px;
            min-width: 22px;
        }

        /* HEADER */
        .ct-form-header {
            margin-bottom: 24px;
        }

        .ct-form-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.4px;
            margin-bottom: 5px;
        }

        .ct-form-header p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ERROR / SUCCESS BANNERS */
        .banner-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #991b1b;
        }

        .banner-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #166534;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── TYPE CARDS ─────────────────────────────────────────── */
        .type-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .type-card {
            background: var(--bg-card);
            border: 2px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 20px 18px 18px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s, transform .18s;
            text-align: left;
        }

        .type-card:hover {
            transform: translateY(-3px);
        }

        .paid-card:hover {
            border-color: var(--brand-primary);
            box-shadow: 0 10px 32px var(--primary-glow);
        }

        .qr-card:hover {
            border-color: var(--brand-secondary);
            box-shadow: 0 10px 32px rgba(122, 92, 255, .15);
        }

        .paid-card.selected {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .qr-card.selected {
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 4px rgba(122, 92, 255, .15);
        }

        .card-ribbon {
            position: absolute;
            top: 11px;
            right: -26px;
            background: var(--brand-accent);
            color: #4a2600;
            font-size: 9px;
            font-weight: 800;
            padding: 4px 36px;
            transform: rotate(35deg);
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .card-radio {
            position: absolute;
            top: 14px;
            left: 14px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid var(--line);
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .18s;
        }

        .paid-card.selected .card-radio {
            border-color: var(--brand-primary);
            background: var(--brand-primary);
        }

        .qr-card.selected .card-radio {
            border-color: var(--brand-secondary);
            background: var(--brand-secondary);
        }

        .card-radio-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #fff;
            opacity: 0;
            transition: opacity .18s;
        }

        .type-card.selected .card-radio-dot {
            opacity: 1;
        }

        .card-body {
            padding-left: 28px;
        }

        .card-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 11px;
        }

        .paid-card .card-icon-wrap {
            background: var(--primary-glow);
            color: var(--brand-primary);
        }

        .qr-card .card-icon-wrap {
            background: rgba(122, 92, 255, .1);
            color: var(--brand-secondary);
        }

        .card-name {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 3px;
        }

        .card-price {
            font-size: 19px;
            font-weight: 900;
            margin-bottom: 12px;
            line-height: 1.1;
        }

        .paid-card .card-price {
            color: var(--brand-primary);
        }

        .qr-card .card-price {
            color: var(--brand-secondary);
        }

        .card-price span {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .card-feature {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .card-feature i {
            font-size: 13px;
            color: var(--brand-green);
        }

        /* ── FREE SECTION ───────────────────────────────────────── */
        .free-section {
            display: none;
            margin-bottom: 14px;
        }

        .free-section.visible {
            display: block;
        }

        .free-card-full {
            background: var(--bg-card);
            border: 2px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 18px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s, transform .18s;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .free-card-full:hover {
            transform: translateY(-2px);
            border-color: var(--brand-green);
            box-shadow: 0 8px 24px rgba(22, 163, 74, .12);
        }

        .free-card-full.selected {
            border-color: var(--brand-green);
            box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
        }

        .free-card-radio {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid var(--line);
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .18s;
        }

        .free-card-full.selected .free-card-radio {
            border-color: var(--brand-green);
            background: var(--brand-green);
        }

        .free-card-radio-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #fff;
            opacity: 0;
            transition: opacity .18s;
        }

        .free-card-full.selected .free-card-radio-dot {
            opacity: 1;
        }

        .free-icon {
            width: 42px;
            height: 42px;
            background: rgba(22, 163, 74, .1);
            color: var(--brand-green);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .free-info {
            flex: 1;
        }

        .free-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 2px;
        }

        .free-sub {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .free-badge {
            background: rgba(22, 163, 74, .12);
            color: #0d6e31;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 99px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── SKIP TRIGGER ───────────────────────────────────────── */
        .skip-trigger {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 11px 18px;
            background: transparent;
            border: 1.5px dashed var(--line);
            border-radius: var(--radius-md);
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s;
            margin-bottom: 18px;
        }

        .skip-trigger:hover {
            border-color: var(--brand-green);
            color: var(--brand-green);
            background: rgba(22, 163, 74, .04);
        }

        /* ── SUBMIT BTN ─────────────────────────────────────────── */
        .submit-btn {
            width: 100%;
            padding: 14px 20px;
            background: var(--brand-primary);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: filter .18s, transform .18s;
            margin-bottom: 16px;
        }

        .submit-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .submit-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none;
        }

        .form-note {
            font-size: 11.5px;
            color: var(--text-muted);
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .form-note i {
            font-size: 13px;
            color: var(--brand-green);
        }

        /* ── RIGHT HERO ─────────────────────────────────────────── */
        .ct-hero {
            flex: 1;
            background: linear-gradient(160deg, #1a3faa 0%, #062f75 55%, #050f28 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 44px;
            position: relative;
            overflow: hidden;
        }

        .ct-hero::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: rgba(122, 92, 255, .18);
            pointer-events: none;
        }

        .ct-hero::after {
            content: '';
            position: absolute;
            bottom: -70px;
            right: -60px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(240, 179, 90, .10);
            pointer-events: none;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, .1);
            color: rgba(255, 255, 255, .88);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .8px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 99px;
            margin-bottom: 22px;
            width: fit-content;
            backdrop-filter: blur(4px);
            position: relative;
            z-index: 1;
        }

        .hero-eyebrow .pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse-live 2s infinite;
        }

        @keyframes pulse-live {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, .5);
            }

            50% {
                box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
            }
        }

        .hero-heading {
            font-size: clamp(1.6rem, 2.4vw, 2.3rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            letter-spacing: -.4px;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .hero-heading em {
            font-style: normal;
            color: var(--brand-accent);
        }

        .hero-sub {
            font-size: 13.5px;
            color: rgba(255, 255, 255, .65);
            line-height: 1.75;
            margin-bottom: 32px;
            max-width: 300px;
            position: relative;
            z-index: 1;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 11px;
            position: relative;
            z-index: 1;
        }

        .hero-stat {
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 14px;
            padding: 14px 15px;
            backdrop-filter: blur(4px);
        }

        .hero-stat .val {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            display: block;
        }

        .hero-stat .lbl {
            font-size: 11px;
            color: rgba(255, 255, 255, .5);
            margin-top: 2px;
            display: block;
        }

        /* ══════════════════════════════════════════════════════════
                       QR MODAL
                    ══════════════════════════════════════════════════════════ */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 18, 40, .72);
            backdrop-filter: blur(6px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .qr-modal {
            background: var(--bg-card);
            border-radius: 24px;
            width: 100%;
            max-width: 800px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .25);
            overflow: hidden;
            animation: modal-in .28s cubic-bezier(.34, 1.56, .64, 1);
            position: relative;
        }

        @keyframes modal-in {
            from {
                opacity: 0;
                transform: scale(.92) translateY(18px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* modal header */
        .modal-header {
            background: linear-gradient(135deg, #1a3faa, #7a5cff);
            padding: 24px 24px 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .modal-header-text h3 {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }

        .modal-header-text p {
            font-size: 12px;
            color: rgba(255, 255, 255, .7);
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, .15);
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .18s;
            flex-shrink: 0;
            margin-left: 12px;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, .28);
        }

        /* modal body */
        .modal-body {
            padding: 24px;
        }

        /* amount pill */
        .amount-pill {
            background: var(--primary-glow);
            border: 1.5px solid rgba(26, 63, 170, .2);
            border-radius: 99px;
            padding: 8px 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .amount-pill .amt {
            font-size: 22px;
            font-weight: 900;
            color: var(--brand-primary);
        }

        .amount-pill .tax {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* QR frame */
        .qr-frame {
            border: 3px solid var(--line);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 16px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .qr-image-wrap {
            width: 200px;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
        }

        .qr-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Placeholder QR if no real image */
        .qr-placeholder {
            width: 180px;
            height: 180px;
            border-radius: 12px;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
        }

        .qr-placeholder i {
            font-size: 64px;
            color: var(--brand-secondary);
        }

        .qr-badge-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .qr-badge {
            background: var(--bg);
            border: 1px solid var(--line);
            border-radius: 99px;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .qr-badge i {
            font-size: 12px;
            color: var(--brand-green);
        }

        /* instruction steps */
        .qr-steps {
            margin-bottom: 20px;
        }

        .qr-step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
        }

        .qr-step:last-child {
            border-bottom: none;
        }

        .qr-step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--brand-secondary);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .qr-step-text {
            font-size: 13px;
            color: var(--text);
            line-height: 1.5;
        }

        .qr-step-text strong {
            color: var(--brand-primary);
        }

        /* confirm checkbox */
        .confirm-box {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 18px;
            display: none;
        }

        .confirm-box.visible {
            display: block;
        }

        .confirm-check-row {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            user-select: none;
        }

        .confirm-check-row input[type=checkbox] {
            display: none;
        }

        .check-box-ui {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 2px solid var(--brand-green);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all .18s;
        }

        .confirm-check-row input:checked~.check-box-ui {
            background: var(--brand-green);
        }

        .check-icon {
            display: none;
        }

        .confirm-check-row input:checked~.check-box-ui .check-icon {
            display: block;
        }

        .check-label {
            font-size: 13px;
            color: #166534;
            font-weight: 600;
            line-height: 1.5;
        }

        /* modal CTAs */
        .modal-ctas {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-confirm-pay {
            width: 100%;
            padding: 13px 20px;
            background: var(--brand-green);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: filter .18s, transform .18s;
        }

        .btn-confirm-pay:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .btn-confirm-pay:disabled {
            opacity: .45;
            cursor: not-allowed;
            transform: none;
        }

        .btn-invoice {
            width: 100%;
            padding: 11px 20px;
            background: transparent;
            color: var(--brand-primary);
            font-size: 13px;
            font-weight: 700;
            border: 2px solid var(--brand-primary);
            border-radius: var(--radius-md);
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .18s, color .18s;
            text-decoration: none;
        }

        .btn-invoice.visible {
            display: flex;
        }

        .btn-invoice:hover {
            background: var(--brand-primary);
            color: #fff;
        }

        .btn-cancel-modal {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 13px;
            cursor: pointer;
            padding: 6px;
            text-align: center;
            width: 100%;
            transition: color .18s;
        }

        .btn-cancel-modal:hover {
            color: var(--text);
        }

        /* ── SUCCESS STATE ──────────────────────────────────────── */
        .qr-success-overlay {
            display: none;
            text-align: center;
            padding: 32px 24px;
        }

        .qr-success-overlay.visible {
            display: block;
        }

        .success-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(22, 163, 74, .12);
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon-wrap i {
            font-size: 36px;
            color: var(--brand-green);
        }

        .success-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
        }

        .success-sub {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .success-note {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 12px;
            color: #166534;
            margin-bottom: 20px;
        }

        /* ── RESPONSIVE ─────────────────────────────────────────── */
        @media(max-width: 900px) {
            .ct-page {
                flex-direction: column;
            }

            .ct-form-panel {
                width: 100%;
                padding: 28px 20px;
            }

            .ct-hero {
                padding: 36px 20px;
            }

            .type-cards {
                grid-template-columns: 1fr;
            }

            .hero-stats {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media(max-width: 480px) {
            .ct-form-panel {
                padding: 22px 16px;
            }

            .qr-modal {
                border-radius: 18px;
            }

            .modal-body {
                padding: 18px 16px;
            }

            .qr-image-wrap,
            .qr-placeholder {
                width: 150px;
                height: 150px;
            }
        }
    </style>

    <div class="ct-page">

        {{-- ══ LEFT: FORM ══════════════════════════════════════════ --}}
        <div class="ct-form-panel">

            <div class="ct-form-logo">
                <div class="logo-dot">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z" />
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                </div>
                <span>LearnPro LMS</span>
            </div>

            <div class="steps-bar">
                <div class="step-item active">
                    <div class="step-dot">1</div><span class="step-label">Demo Type</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-dot">2</div><span class="step-label">Your Info</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-dot">3</div><span class="step-label">Confirm</span>
                </div>
            </div>

            <div class="ct-form-header">
                <h2>Book Your LMS Demo</h2>
                <p>Choose how you'd like to experience the platform — instantly or with a dedicated session.</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="banner-err">{{ $errors->first() }}</div>
            @endif
            @if (session('error'))
                <div class="banner-err">{{ session('error') }}</div>
            @endif

            <form action="{{ route('lms.choose-type.store') }}" method="POST" id="demoTypeForm">
                @csrf
                <input type="hidden" name="demo_type" id="demoTypeInput" value="{{ old('demo_type', '') }}">

                {{-- PAID + QR CARDS --}}
                <div class="type-cards">

                    <div class="type-card paid-card {{ old('demo_type') === 'paid_online' ? 'selected' : '' }}"
                        onclick="selectType('paid_online', this)">
                        <div class="card-ribbon">Popular</div>
                        <div class="card-radio">
                            <div class="card-radio-dot"></div>
                        </div>
                        <div class="card-body">
                            <div class="card-icon-wrap"><i class="fas fa-credit-card"></i></div>
                            <div class="card-name">Online Payment</div>
                            <div class="card-price">₹{{ number_format($paidPrice, 0) }} <span>one-time</span></div>
                            <div class="card-feature"><i class="fas fa-check"></i> Instant booking confirmation</div>
                            <div class="card-feature"><i class="fas fa-check"></i> 1-on-1 dedicated session</div>
                            <div class="card-feature"><i class="fas fa-check"></i> Secure UPI / Card / Net banking</div>
                        </div>
                    </div>

                    <div class="type-card qr-card {{ old('demo_type') === 'paid_qr' ? 'selected' : '' }}"
                        onclick="handleQrClick(this)">
                        <div class="card-radio">
                            <div class="card-radio-dot"></div>
                        </div>
                        <div class="card-body">
                            <div class="card-icon-wrap"><i class="fas fa-qrcode"></i></div>
                            <div class="card-name">Pay via QR</div>
                            <div class="card-price">₹{{ number_format($paidPrice, 0) }} <span>one-time</span></div>
                            <div class="card-feature"><i class="fas fa-check"></i> Scan & pay with any UPI app</div>
                            <div class="card-feature"><i class="fas fa-check"></i> 1-on-1 dedicated session</div>
                            <div class="card-feature"><i class="fas fa-check"></i> Instant booking on payment</div>
                        </div>
                    </div>

                </div>

                {{-- FREE section --}}
                <div class="free-section" id="freeSection">
                    <div class="free-card-full {{ old('demo_type') === 'free' ? 'selected' : '' }}"
                        onclick="selectType('free', this)" id="freeCard">
                        <div class="free-card-radio">
                            <div class="free-card-radio-dot"></div>
                        </div>
                        <div class="free-icon"><i class="fas fa-gift"></i></div>
                        <div class="free-info">
                            <div class="free-name">Free Self-Guided Demo</div>
                            <div class="free-sub">Explore at your own pace — no payment, no commitment.</div>
                        </div>
                        <div class="free-badge">₹0 Free</div>
                    </div>
                </div>

                <button type="button" class="skip-trigger" id="skipBtn" onclick="revealFree()">
                    <i class="fas fa-arrow-right"></i>
                    Skip — I'll try the free demo instead
                </button>

                <button type="submit" class="submit-btn" id="submitBtn" disabled>
                    <i class="fas fa-arrow-right"></i>
                    <span id="submitLabel">Select a demo type to continue</span>
                </button>

                <p class="form-note">
                    <i class="fas fa-lock-filled"></i>
                    Secure checkout &nbsp;·&nbsp; No hidden charges &nbsp;·&nbsp; Cancel anytime
                </p>
            </form>

        </div>{{-- /ct-form-panel --}}

        {{-- ══ RIGHT HERO ══════════════════════════════════════════ --}}
        <div class="ct-hero">
            <div class="hero-eyebrow">
                <div class="pulse"></div>Live Demo Available Now
            </div>
            <h1 class="hero-heading">Experience <em>smarter</em><br>learning — live</h1>
            <p class="hero-sub">See how LearnPro transforms training into measurable impact with AI-driven insights and
                beautiful interfaces.</p>
            <div class="hero-stats">
                <div class="hero-stat"><span class="val">12,000+</span><span class="lbl">Students onboarded</span>
                </div>
                <div class="hero-stat"><span class="val">98%</span><span class="lbl">Satisfaction rate</span></div>
                <div class="hero-stat"><span class="val">3 min</span><span class="lbl">Avg. setup time</span></div>
                <div class="hero-stat"><span class="val">50+</span><span class="lbl">Enterprise clients</span>
                </div>
            </div>
        </div>

    </div>{{-- /ct-page --}}


    {{-- ══════════════════════════════════════════════════════════
         QR PAYMENT MODAL
    ══════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" id="qrModal" role="dialog" aria-modal="true" aria-labelledby="qrModalTitle">
        <div class="qr-modal">

            {{-- Normal QR flow --}}
            <div id="qrFlowContent">
                <div class="modal-header">
                    <div class="modal-header-text">
                        <h3 id="qrModalTitle">Scan & Pay</h3>
                        <p>Complete your ₹{{ number_format($paidPrice, 0) }} payment via UPI</p>
                    </div>
                    <button class="modal-close" onclick="closeQrModal()" aria-label="Close">&times;</button>
                </div>

                <div class="modal-body">
                    {{-- Amount pill --}}
                    <div style="text-align:center;margin-bottom:20px;">
                        <div class="amount-pill" style="display:inline-flex;">
                            <i class="fas fa-currency-rupee" style="font-size:18px;color:var(--brand-primary);"></i>
                            <span class="amt">{{ number_format($paidPrice, 0) }}</span>
                            <span class="tax">one-time · incl. GST</span>
                        </div>
                    </div>

                    {{-- QR frame --}}
                    <div class="qr-frame">
                        {{-- Replace the placeholder with a real QR image: --}}
                        <div class="qr-image-wrap"><img src="{{ asset('theme/images/scanner.png') }}"
                                alt="Payment QR Code"></div>
                        {{-- <div class="qr-placeholder">
                            <i class="ti ti-qrcode"></i>
                            <span>UPI QR Code</span>
                        </div> --}}
                        <div class="qr-badge-row">
                            <span class="qr-badge"><i class="fas fa-check"></i> GPay</span>
                            <span class="qr-badge"><i class="fas fa-check"></i> PhonePe</span>
                            <span class="qr-badge"><i class="fas fa-check"></i> Paytm</span>
                            <span class="qr-badge"><i class="fas fa-check"></i> BHIM</span>
                        </div>
                    </div>

                    {{-- Steps --}}
                    <div class="qr-steps">
                        <div class="qr-step">
                            <div class="qr-step-num">1</div>
                            <div class="qr-step-text">Open any UPI app — <strong>GPay, PhonePe, Paytm</strong> or BHIM
                            </div>
                        </div>
                        <div class="qr-step">
                            <div class="qr-step-num">2</div>
                            <div class="qr-step-text">Tap <strong>Scan QR</strong> and point your camera at the code above
                            </div>
                        </div>
                        <div class="qr-step">
                            <div class="qr-step-num">3</div>
                            <div class="qr-step-text">Enter amount <strong>₹{{ number_format($paidPrice, 0) }}</strong>
                                and confirm your payment</div>
                        </div>
                        <div class="qr-step">
                            <div class="qr-step-num">4</div>
                            <div class="qr-step-text">Once paid, tick the confirmation below</div>
                        </div>
                    </div>

                    {{-- Confirmation checkbox --}}
                    <div class="confirm-box visible" id="confirmBox">
                        <label class="confirm-check-row">
                            <input type="checkbox" id="payConfirmCheck" onchange="toggleConfirm(this)">
                            <div class="check-box-ui">
                                <svg class="check-icon" width="13" height="10" viewBox="0 0 13 10"
                                    fill="none">
                                    <path d="M1 5L4.5 8.5L12 1" stroke="#fff" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span class="check-label">Yes, I have successfully scanned and completed the payment of
                                <strong>₹{{ number_format($paidPrice, 0) }}</strong></span>
                        </label>
                    </div>

                    {{-- CTAs --}}
                    <div class="modal-ctas">
                        <button type="button" class="btn-confirm-pay" id="btnConfirmPay" disabled
                            onclick="submitQrPayment()">
                            <i class="fas fa-circle-check"></i>
                            Confirm Payment & Continue
                        </button>

                        {{-- Invoice download — shown after confirmation --}}
                        <a href="{{ route('lms.qr.invoice') }}" class="btn-invoice" id="btnInvoice" target="_blank">
                            <i class="fas fa-file-invoice"></i>
                            Download Payment Invoice (PDF)
                        </a>

                        <button type="button" class="btn-cancel-modal" onclick="closeQrModal()">
                            ← Go back and choose differently
                        </button>
                    </div>
                </div>
            </div>

            {{-- Success overlay (shown after confirm) --}}
            <div class="qr-success-overlay" id="qrSuccessOverlay">
                <div class="success-icon-wrap">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div class="success-title">Payment Confirmed!</div>
                <div class="success-sub">
                    Thank you for your payment. Your demo slot is being arranged.<br>
                    Our team will reach you via <strong>email</strong> with all details.
                </div>
                <div class="success-note">
                    📧 A confirmation email will be sent to your registered address within a few minutes.
                </div>
                <a href="{{ route('lms.qr.invoice') }}" class="btn-invoice visible"
                    style="display:flex;margin:0 auto 12px;" target="_blank">
                    <i class="fas fa-file-invoice"></i>
                    Download Your Invoice (PDF)
                </a>
                <button type="button" class="btn-cancel-modal" onclick="redirectAfterQr()">
                    Continue to dashboard →
                </button>
            </div>

        </div>
    </div>{{-- /qr modal --}}


    <script>
        const qrPaymentStatus = "{{ $existingQrStatus ?? 'pending' }}";
       

        /* ── DOM REFS ─────────────────────────────────────────── */
        const typeInput = document.getElementById('demoTypeInput');
        const submitBtn = document.getElementById('submitBtn');
        const submitLbl = document.getElementById('submitLabel');
        const skipBtn = document.getElementById('skipBtn');
        const freeSection = document.getElementById('freeSection');
        const qrModal = document.getElementById('qrModal');
        const confirmCheck = document.getElementById('payConfirmCheck');
        const btnConfirmPay = document.getElementById('btnConfirmPay');
        const btnInvoice = document.getElementById('btnInvoice');
        const qrFlowContent = document.getElementById('qrFlowContent');
        const qrSuccessOverlay = document.getElementById('qrSuccessOverlay');

        const labels = {
            paid_online: 'Continue to Secure Payment →',
            paid_qr: 'Confirm & Continue →',
            free: 'Continue with Free Demo →',
        };

        /* ── SELECT TYPE ───────────────────────────────────────── */
        function selectType(type, card) {
            document.querySelectorAll('.type-card, .free-card-full')
                .forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            typeInput.value = type;
            submitBtn.disabled = false;
            submitLbl.textContent = labels[type] || 'Continue →';
        }

        /* ── QR CARD CLICK → open modal ─────────────────────────── */
        function handleQrClick(card) {
            selectType('paid_qr', card);
            openQrModal();
        }

        /* ── FREE REVEAL ──────────────────────────────────────── */
        function revealFree() {
            freeSection.classList.add('visible');
            skipBtn.style.display = 'none';
            freeSection.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            const freeCard = document.getElementById('freeCard');
            selectType('free', freeCard);
        }

        /* ── MODAL OPEN / CLOSE ───────────────────────────────── */
        function openQrModal() {

           const qrPaymentType = "{{ $existingType ?? '' }}";

if (
    qrPaymentType &&
    qrPaymentStatus === 'completed' &&
    (qrPaymentType === 'paid_qr' || qrPaymentType === 'paid_online')
) {
    Swal.fire({
        icon: 'success',
        title: 'Payment Already Completed',
        text: 'Your payment has already been confirmed. Our team will contact you via email.',
        confirmButtonText: 'Continue'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('lms.thankyou') }}";
        }
    });

    return;
} else {
                qrModal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

        }

        function closeQrModal() {
            qrModal.classList.remove('open');
            document.body.style.overflow = '';
            /* deselect QR card if user cancels before confirming */
            document.querySelectorAll('.type-card, .free-card-full')
                .forEach(c => c.classList.remove('selected'));
            typeInput.value = '';
            submitBtn.disabled = true;
            submitLbl.textContent = 'Select a demo type to continue';
        }

        /* close modal on backdrop click */
        qrModal.addEventListener('click', function(e) {
            if (e.target === qrModal) closeQrModal();
        });

        /* ── CHECKBOX TOGGLE ──────────────────────────────────── */
        function toggleConfirm(cb) {
            btnConfirmPay.disabled = !cb.checked;
        }

        /* ── SUBMIT QR PAYMENT ────────────────────────────────── */
        function submitQrPayment() {
            btnConfirmPay.disabled = true;
            btnConfirmPay.innerHTML =
                '<i class="fas fa-loader-2" style="animation:spin .7s linear infinite"></i> Processing…';

            /* POST to server — mark QR as confirmed */
            fetch('{{ route('lms.qr.confirm') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        demo_type: 'paid_qr'
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        /* Show success overlay */
                        qrFlowContent.style.display = 'none';
                        qrSuccessOverlay.classList.add('visible');
                    } else {
                        alert(data.message || 'Something went wrong. Please try again.');
                        btnConfirmPay.disabled = false;
                        btnConfirmPay.innerHTML = '<i class="fas fa-circle-check"></i> Confirm Payment & Continue';
                    }
                })
                .catch(() => {

                    btnConfirmPay.disabled = false;
                    btnConfirmPay.innerHTML = '<i class="fas fa-circle-check"></i> Confirm Payment & Continue';
                });
        }

        /* Redirect after success overlay */
        function redirectAfterQr() {
            window.location.href = '{{ route('lms.thankyou') }}';
        }

        /* Spinner keyframe */
        const style = document.createElement('style');
        style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
        document.head.appendChild(style);

        /* ── RESTORE STATE ON VALIDATION ERROR ─────────────────── */
        const oldType = typeInput.value;
        if (oldType) {
            if (oldType === 'free') revealFree();
            const card = document.querySelector(`[onclick*="${oldType}"]`);
            if (card) selectType(oldType, card);
        }
    </script>
@endsection
