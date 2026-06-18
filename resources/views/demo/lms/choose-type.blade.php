{{-- resources/views/demo/lms/choose-type.blade.php --}}
@extends('demo.layout')
@section('title', 'Choose Your Demo Type')

@section('content')
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .ct-page {
        min-height: 100vh;
        background: var(--bg);
        display: flex;
        align-items: stretch;
    }

    /* ── LEFT HERO PANEL ─────────────────────────────────── */
    .ct-hero {
        width: 42%;
        background: linear-gradient(160deg, var(--brand-primary) 0%, #062f75 55%, #050f28 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 48px 44px;
        position: relative;
        overflow: hidden;
    }
    .ct-hero::before {
        content: '';
        position: absolute;
        top: -120px; right: -120px;
        width: 380px; height: 380px;
        border-radius: 50%;
        background: rgba(122,92,255,.18);
        pointer-events: none;
    }
    .ct-hero::after {
        content: '';
        position: absolute;
        bottom: -80px; left: -80px;
        width: 280px; height: 280px;
        border-radius: 50%;
        background: rgba(240,179,90,.10);
        pointer-events: none;
    }

    .ct-hero-logo {
        display: flex; align-items: center; gap: 10px;
        position: relative; z-index: 1;
    }
    .ct-hero-logo .logo-mark {
        width: 36px; height: 36px;
        background: rgba(255,255,255,.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: #fff;
        backdrop-filter: blur(6px);
    }
    .ct-hero-logo span {
        font-size: 14px; font-weight: 700;
        color: rgba(255,255,255,.85);
        letter-spacing: .3px;
    }

    .ct-hero-main { position: relative; z-index: 1; }

    .ct-hero-eyebrow {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255,255,255,.12);
        color: rgba(255,255,255,.9);
        font-size: 11px; font-weight: 700;
        letter-spacing: .8px; text-transform: uppercase;
        padding: 5px 13px; border-radius: 99px;
        margin-bottom: 22px;
        backdrop-filter: blur(4px);
    }

    .ct-hero-main h1 {
        font-size: clamp(1.7rem, 2.5vw, 2.4rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
        letter-spacing: -.4px;
        margin-bottom: 16px;
    }
    .ct-hero-main h1 em {
        font-style: normal;
        color: var(--brand-accent);
    }
    .ct-hero-main p {
        font-size: 13.5px;
        color: rgba(255,255,255,.68);
        line-height: 1.7;
        max-width: 320px;
        margin-bottom: 32px;
    }

    .hero-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .hero-stat {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 14px;
        padding: 16px;
        backdrop-filter: blur(6px);
    }
    .hero-stat .val {
        font-size: 22px; font-weight: 800;
        color: #fff; display: block;
    }
    .hero-stat .lbl {
        font-size: 11px;
        color: rgba(255,255,255,.55);
        margin-top: 3px; display: block;
    }

    .ct-hero-footer {
        position: relative; z-index: 1;
    }
    .trust-pills {
        display: flex; gap: 10px; flex-wrap: wrap;
    }
    .trust-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.7);
        font-size: 11.5px;
        padding: 6px 12px;
        border-radius: 99px;
    }
    .trust-pill i { color: var(--brand-accent); font-size: 12px; }

    /* ── RIGHT FORM PANEL ─────────────────────────────────── */
    .ct-form-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 48px 52px;
        background: var(--bg);
        overflow-y: auto;
    }

    .ct-form-header {
        margin-bottom: 30px;
    }
    .ct-form-header h2 {
        font-size: 1.5rem; font-weight: 800;
        color: var(--text);
        letter-spacing: -.3px;
        margin-bottom: 6px;
    }
    .ct-form-header p {
        font-size: 13px; color: var(--text-muted);
    }

    /* STEP INDICATOR */
    .steps-bar {
        display: flex; align-items: center; gap: 0;
        margin-bottom: 32px;
    }
    .step-item {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px;
    }
    .step-dot {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 800;
        border: 2px solid var(--line);
        color: var(--text-muted);
        background: var(--bg-card);
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
    .step-label { color: var(--text-muted); font-size: 11.5px; font-weight: 600; }
    .step-item.active .step-label { color: var(--text); }
    .step-line {
        flex: 1; height: 1.5px;
        background: var(--line);
        margin: 0 10px;
        min-width: 28px;
    }

    /* CARDS */
    .type-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .type-card {
        background: var(--bg-card);
        border: 2px solid var(--line);
        border-radius: 18px;
        padding: 22px 20px 20px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: border-color .2s, box-shadow .2s, transform .18s;
        text-align: left;
    }
    .type-card:hover {
        border-color: var(--primary);
        box-shadow: 0 12px 40px var(--primary-glow);
        transform: translateY(-3px);
    }
    .type-card.selected {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 4px var(--primary-glow), 0 12px 40px var(--primary-glow);
    }

    .card-ribbon {
        position: absolute; top: 12px; right: -28px;
        background: var(--brand-accent);
        color: #4a2600;
        font-size: 9px; font-weight: 800;
        padding: 4px 38px;
        transform: rotate(35deg);
        letter-spacing: .7px; text-transform: uppercase;
    }

    .card-radio {
        position: absolute; top: 16px; left: 16px;
        width: 18px; height: 18px;
        border-radius: 50%;
        border: 2px solid var(--line);
        background: var(--bg-card);
        display: flex; align-items: center; justify-content: center;
        transition: all .18s;
    }
    .type-card.selected .card-radio {
        border-color: var(--brand-primary);
        background: var(--brand-primary);
    }
    .card-radio-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: #fff;
        opacity: 0; transition: opacity .18s;
    }
    .type-card.selected .card-radio-dot { opacity: 1; }

    .card-body { padding-left: 28px; }

    .card-icon-wrap {
        width: 46px; height: 46px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        margin-bottom: 13px;
    }
    .free-card .card-icon-wrap { background: rgba(22,163,74,.10); color: #16a34a; }
    .paid-card .card-icon-wrap { background: var(--primary-glow); color: var(--brand-primary); }

    .card-name { font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 3px; }
    .card-price {
        font-size: 22px; font-weight: 900;
        color: var(--brand-primary);
        margin-bottom: 14px;
        line-height: 1;
    }
    .free-card .card-price { color: #16a34a; }
    .card-price small { font-size: 11.5px; font-weight: 600; color: var(--text-muted); }

    .card-features { list-style: none; display: flex; flex-direction: column; gap: 7px; }
    .card-features li {
        font-size: 12px; color: var(--text-muted);
        display: flex; align-items: flex-start; gap: 7px; line-height: 1.45;
    }
    .card-features li i { font-size: 11px; margin-top: 2px; flex-shrink: 0; }
    .free-card .card-features li i { color: #16a34a; }
    .paid-card .card-features li i { color: var(--brand-primary); }

    /* PAYMENT SECTION — shows when paid selected */
    .payment-section {
        background: var(--bg-card);
        border: 1.5px solid var(--line);
        border-radius: 18px;
        padding: 24px;
        margin-bottom: 22px;
        display: none;
    }
    .payment-section.visible { display: block; }

    .ps-header {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--line);
    }
    .ps-header-icon {
        width: 38px; height: 38px;
        background: var(--primary-glow);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: var(--brand-primary); font-size: 17px;
    }
    .ps-header-text h4 { font-size: 14px; font-weight: 800; color: var(--text); }
    .ps-header-text p { font-size: 11.5px; color: var(--text-muted); }

    .order-summary {
        background: var(--bg2, #f0f5ff);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .order-row {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 12.5px;
    }
    .order-row + .order-row { margin-top: 8px; }
    .order-row.total {
        border-top: 1px dashed var(--line);
        margin-top: 12px; padding-top: 12px;
        font-weight: 800; font-size: 14px;
    }
    .order-row .key { color: var(--text-muted); }
    .order-row .val { color: var(--text); font-weight: 700; }
    .order-row.total .val { color: var(--brand-primary); font-size: 18px; }

    .pay-methods {
        display: grid; grid-template-columns: repeat(3,1fr); gap: 10px;
        margin-bottom: 20px;
    }
    .pay-method {
        background: var(--bg2, #f0f5ff);
        border: 1.5px solid var(--line);
        border-radius: 11px;
        padding: 12px 8px;
        cursor: pointer;
        text-align: center;
        transition: border-color .18s, background .18s;
    }
    .pay-method:hover { border-color: var(--brand-primary); }
    .pay-method.active {
        border-color: var(--brand-primary);
        background: var(--primary-glow);
    }
    .pay-method i { font-size: 20px; color: var(--brand-primary); display: block; margin-bottom: 4px; }
    .pay-method span { font-size: 10.5px; color: var(--text-muted); font-weight: 600; }
    .pay-method.active span { color: var(--brand-primary); }

    .secure-note {
        display: flex; align-items: center; gap: 7px;
        font-size: 11px; color: var(--text-muted);
        background: rgba(22,163,74,.06);
        border: 1px solid rgba(22,163,74,.15);
        border-radius: 9px; padding: 9px 13px;
    }
    .secure-note i { color: #16a34a; }

    /* CTA */
    .cta-block { margin-top: auto; }

    .btn-main {
        width: 100%;
        display: flex; align-items: center; justify-content: center; gap: 10px;
        padding: 15px 28px;
        font-size: 14.5px; font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
        border: none; border-radius: 13px;
        cursor: pointer;
        transition: transform .16s, filter .16s, opacity .16s;
        box-shadow: 0 8px 28px var(--primary-glow);
        font-family: inherit;
    }
    .btn-main:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.07); }
    .btn-main:disabled { opacity: .45; cursor: not-allowed; transform: none; box-shadow: none; }
    .btn-main i { font-size: 15px; }

    .form-footer {
        margin-top: 16px;
        display: flex; align-items: center; justify-content: center; gap: 18px;
        flex-wrap: wrap;
    }
    .form-footer span {
        font-size: 11px; color: var(--text-muted);
        display: flex; align-items: center; gap: 5px;
    }
    .form-footer i { font-size: 12px; color: #16a34a; }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .ct-page { flex-direction: column; }
        .ct-hero { width: 100%; padding: 36px 28px; min-height: auto; }
        .ct-hero::before, .ct-hero::after { display: none; }
        .ct-form-panel { padding: 36px 24px; }
    }
    @media (max-width: 520px) {
        .type-cards { grid-template-columns: 1fr; }
        .pay-methods { grid-template-columns: 1fr 1fr; }
        .steps-bar { display: none; }
    }
</style>

<div class="ct-page">

    {{-- LEFT HERO --}}
    <div class="ct-hero">

        <div class="ct-hero-logo">
              <span>Live Skills Traning Program</span>
        </div>

        <div class="ct-hero-main">
            <div class="ct-hero-eyebrow">
                <i class="fas fa-bolt"></i> Quick demo access
            </div>
            <h1>Start learning<br><em>the smart way</em></h1>
            <p>Pick a demo experience tailored to you. Get hands-on guidance, personalised recommendations, and a clear path to your next certification.</p>

            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="val">12,400+</span>
                    <span class="lbl">Active learners</span>
                </div>
                <div class="hero-stat">
                    <span class="val">4.9 ★</span>
                    <span class="lbl">Average rating</span>
                </div>
                <div class="hero-stat">
                    <span class="val">98%</span>
                    <span class="lbl">Completion rate</span>
                </div>
                <div class="hero-stat">
                    <span class="val">200+</span>
                    <span class="lbl">Courses available</span>
                </div>
            </div>
        </div>

        <div class="ct-hero-footer">
            <div class="trust-pills">
                <div class="trust-pill"><i class="fas fa-shield-alt"></i> 100% Secure</div>
                <div class="trust-pill"><i class="fas fa-certificate"></i> Certified</div>
                <div class="trust-pill"><i class="fas fa-undo"></i> Easy refund</div>
            </div>
        </div>

    </div>

    {{-- RIGHT FORM PANEL --}}
    <div class="ct-form-panel">
        <img src="{{ asset('theme/images/am21.png') }}" alt="Mentor" title="Menntor" class="image-back-mentor3 shape">

        <div class="ct-form-header">
            <h2>Choose your experience</h2>
            <p>Select the demo type that fits your goals. You can always upgrade later.</p>
        </div>

        {{-- STEP BAR --}}
        <div class="steps-bar">
            <div class="step-item active">
                <div class="step-dot">1</div>
                <span class="step-label">Choose type</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <div class="step-dot">2</div>
                <span class="step-label">Your details</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <div class="step-dot">3</div>
                <span class="step-label">Payment</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <div class="step-dot">4</div>
                <span class="step-label">Confirm</span>
            </div>
        </div>

        <form action="{{ route('lms.choose-type.store') }}" method="POST" id="typeForm">
            @csrf

            {{-- DEMO TYPE CARDS --}}
            <div class="type-cards">

                {{-- FREE CARD --}}
                {{-- <div class="type-card free-card" id="card-free" onclick="selectType('free', this)">
                    <div class="card-radio"><div class="card-radio-dot"></div></div>
                    <div class="card-body">
                        <div class="card-icon-wrap"><i class="fas fa-gift"></i></div>
                        <div class="card-name">Free Demo</div>
                        <div class="card-price free-price">₹0 <small>/ no cost</small></div>
                        <ul class="card-features">
                            <li><i class="fas fa-check-circle"></i> Course overview video</li>
                            <li><i class="fas fa-check-circle"></i> Browse all categories</li>
                            <li><i class="fas fa-check-circle"></i> 1 recommendation</li>
                            <li><i class="fas fa-times-circle" style="color:#dc2626"></i> No certificate</li>
                        </ul>
                    </div>
                </div> --}}

                {{-- PAID CARD --}}
                <div class="type-card paid-card selected" id="card-paid" onclick="selectType('paid', this)">
                    <div class="card-ribbon">Popular</div>
                    <div class="card-radio"><div class="card-radio-dot"></div></div>
                    <div class="card-body">
                        <div class="card-icon-wrap"><i class="fas fa-crown"></i></div>
                        <div class="card-name">Paid Demo</div>
                        <div class="card-price">₹{{ number_format($paidPrice, 0) }} <small>/ one-time</small></div>
                        <ul class="card-features">
                            <li><i class="fas fa-check-circle"></i> Everything in Free</li>
                            <li><i class="fas fa-check-circle"></i> Hands-on assignment</li>
                            <li><i class="fas fa-check-circle"></i> Priority guidance</li>
                            <li><i class="fas fa-check-circle"></i> Certificate eligible</li>
                        </ul>
                    </div>
                </div>

            </div>

            {{-- PAYMENT SUMMARY (visible by default — paid selected) --}}
            <div class="payment-section visible" id="paymentSection">

                <div class="ps-header">
                    <div class="ps-header-icon"><i class="fas fa-receipt"></i></div>
                    <div class="ps-header-text">
                        <h4>Order Summary</h4>
                        <p>Review your selection before proceeding</p>
                    </div>
                </div>

                <div class="order-summary">
                    <div class="order-row">
                        <span class="key">Paid Demo Access</span>
                        <span class="val">₹{{ number_format($paidPrice, 0) }}</span>
                    </div>
                    <div class="order-row">
                        <span class="key">Platform fee</span>
                        <span class="val">₹0</span>
                    </div>
                    <div class="order-row">
                        <span class="key">GST (18%)</span>
                        <span class="val">₹{{ number_format($paidPrice * 0.18, 0) }}</span>
                    </div>
                    <div class="order-row total">
                        <span class="key">Total payable</span>
                        <span class="val">₹{{ number_format($paidPrice * 1.18, 0) }}</span>
                    </div>
                </div>

                <p style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:12px;">Choose payment method</p>
                <div class="pay-methods">
                    <div class="pay-method active" onclick="pickPay(this)">
                        <i class="fas fa-credit-card"></i>
                        <span>Card</span>
                    </div>
                    <div class="pay-method" onclick="pickPay(this)">
                        <i class="fas fa-mobile-alt"></i>
                        <span>UPI</span>
                    </div>
                    <div class="pay-method" onclick="pickPay(this)">
                        <i class="fas fa-university"></i>
                        <span>Net Banking</span>
                    </div>
                </div>

                <div class="secure-note">
                    <i class="fas fa-lock"></i>
                    Your payment is 100% encrypted and secured by Razorpay. No card details stored.
                </div>

            </div>

            <input type="hidden" name="demo_type" id="demo_type_input" value="paid">

            {{-- CTA --}}
            <div class="cta-block">
                <button type="submit" class="btn-main" id="confirmBtn">
                    <i class="fas fa-lock"></i>
                    <span id="confirmLabel">Continue to Booking & Payment</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="form-footer">
                    <span><i class="fas fa-shield-alt"></i> SSL Secured</span>
                    <span><i class="fas fa-users"></i> 12,400+ Learners</span>
                    <span><i class="fas fa-star"></i> 4.9 Rated</span>
                    <span><i class="fas fa-undo"></i> Easy Refund</span>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
function selectType(type, el) {
    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('demo_type_input').value = type;

    const paySection = document.getElementById('paymentSection');
    const label = document.getElementById('confirmLabel');

    if (type === 'paid') {
        paySection.classList.add('visible');
        label.textContent = 'Continue to Booking & Payment';
    } else {
        paySection.classList.remove('visible');
        label.textContent = 'Continue with Free Demo';
    }
}

function pickPay(el) {
    document.querySelectorAll('.pay-method').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
}

document.getElementById('typeForm').addEventListener('submit', function(e) {
    const type = document.getElementById('demo_type_input').value;
    if (!type) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Select a demo type',
            text: 'Please choose Free or Paid Demo to continue.',
            confirmButtonColor: '#0947a8'
        });
        return;
    }
    const btn = document.getElementById('confirmBtn');
    btn.disabled = true;
    document.getElementById('confirmLabel').textContent = 'Setting things up…';
});
</script>
@endsection