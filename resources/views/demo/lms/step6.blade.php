@extends('demo.layout')

@section('title', 'Certificate')

@section('bitmoji-message', '🌟 You can be the next success story! Explore more courses and keep your momentum going!')
@section('bitmoji-emoji', '🚀')

@section('content')
@php
    $status     = $demo->status ?? 'pending';   // pending | approved | rejected
    $isApproved = $status === 'approved';
    $isRejected = $status === 'rejected';
@endphp

<style>
.completion-page{ padding:40px; }

.completion-top{ text-align:center; margin-bottom:40px; }

.success-icon{ font-size:90px; margin-bottom:20px; }
.success-icon.ok{ color:var(--success); }
.success-icon.pending{ color:var(--warning, #d97706); }
.success-icon.bad{ color:var(--danger, #dc2626); }

.completion-top h1{ font-size:38px; font-weight:800; color:var(--text-main); }
.completion-top p{ color:var(--text-muted); max-width:560px; margin:8px auto 0; }

.completion-badge{ margin-top:20px; }
.completion-badge span{
    display:inline-block; padding:12px 24px; border-radius:50px; font-weight:700; color:#fff;
}
.badge-ok{ background:linear-gradient(135deg, var(--success), #22c55e); }
.badge-pending{ background:linear-gradient(135deg, #d97706, #f59e0b); }
.badge-bad{ background:linear-gradient(135deg, #dc2626, #ef4444); }

.completion-grid{ display:grid; grid-template-columns:2fr 1fr; gap:25px; }
@media (max-width:900px){ .completion-grid{ grid-template-columns:1fr; } }

/* ══════════════════════════════════════════════════
   CERTIFICATE — mirrors the PDF design pixel-for-pixel
   (same gradient frame, seal, serif styling) so what the
   user previews here is exactly what they'll download.
═══════════════════════════════════════════════════ */
.certificate-wrapper{ position:relative; }

.certificate{
    background:linear-gradient(135deg, #1a3a6e 0%, #4a6fa5 20%, #e8a94a 45%, #d4691e 55%, #1a3a6e 75%, #0d1f3c 100%);
    border-radius:14px;
    box-shadow:var(--shadow-card);
    padding:10px;
}

.certificate-inner{
    background:#e9edf3;
    border:2px solid #0d1f3c;
    border-radius:8px;
    padding:44px 50px;
    text-align:center;
    font-family:Georgia, 'DejaVu Serif', serif;
}

.cert-logos-row{ display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; min-height:40px; }
.cert-logos-row img{ max-height:44px; }

.cert-title2{ font-size:38px; letter-spacing:6px; color:#16233d; font-weight:bold; margin:0; }
.cert-subtitle2{ font-size:14px; letter-spacing:5px; color:#16233d; margin:6px 0 0; }
.cert-divider2{ width:80px; border-top:1.5px solid #16233d; margin:12px auto 26px; }

.cert-certify2{ font-size:13px; font-weight:bold; letter-spacing:2px; color:#16233d; margin-bottom:6px; }
.cert-name-line2{
    max-width:380px; margin:8px auto 6px; border-bottom:1px solid #16233d;
    font-size:24px; font-style:italic; color:#16233d; padding-bottom:6px;
}

.cert-body2{
    font-size:13.5px; font-style:italic; color:#16233d; line-height:1.9;
    margin:26px auto 6px; max-width:440px;
}
.cert-blank2{
    display:inline-block; border-bottom:1px solid #16233d; min-width:120px;
    padding:0 4px; font-style:normal; font-weight:bold;
}

.cert-ribbon-wrap2{ margin:34px auto 20px; width:120px; position:relative; }
.cert-seal2{
    width:104px; height:104px; border-radius:50%; background:#f4f1e9; border:5px solid #16233d;
    margin:0 auto; text-align:center; display:flex; align-items:center; justify-content:center;
    font-weight:bold; font-size:14px; color:#16233d; box-shadow:0 0 0 3px #f4f1e9, 0 0 0 4px #16233d;
}
.cert-ribbon-tails2{ display:flex; justify-content:center; gap:36px; margin-top:-4px; }
.cert-tail-left2, .cert-tail-right2{
    width:0; height:0; border-left:18px solid transparent; border-right:18px solid transparent; border-top:44px solid #16233d;
}

.cert-footer2{ display:flex; justify-content:space-between; margin-top:36px; padding:0 10px; }
.cert-footer2 .col{ text-align:center; font-size:11.5px; color:#16233d; }
.cert-line2{ width:140px; border-top:1px solid #16233d; margin:0 auto 6px; }

/* Locked overlay when not yet approved */
.cert-lock-overlay{
    position:absolute; inset:0; border-radius:14px;
    background:rgba(14,25,45,.55); backdrop-filter:blur(2px);
    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px;
    color:#fff; text-align:center; padding:20px;
}
.cert-lock-overlay i{ font-size:38px; }
.cert-lock-overlay strong{ font-size:15px; }
.cert-lock-overlay span{ font-size:12.5px; opacity:.85; max-width:280px; }

.completion-sidebar{ display:flex; flex-direction:column; gap:20px; }

.info-card{ background:var(--bg-card,#fff); border-radius:20px; padding:24px; box-shadow:var(--shadow-card); }
.info-card h4{ margin-bottom:15px; color:var(--brand-primary); }
.info-card ul{ padding-left:18px; }

.status-line{
    display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; margin-top:2px;
}
.status-line.ok{ color:var(--success); }
.status-line.pending{ color:#d97706; }
.status-line.bad{ color:#dc2626; }

.btn-main{
    display:block; text-align:center; padding:14px; background:var(--brand-primary); color:#fff;
    border-radius:12px; text-decoration:none; margin-bottom:12px; border:none; width:100%; font-size:14px; cursor:pointer;
}
.btn-main.btn-disabled{ background:var(--text-muted); cursor:not-allowed; opacity:.7; }

.btn-outline{
    display:block; text-align:center; padding:14px; border:2px solid var(--brand-primary);
    color:var(--brand-primary); border-radius:12px; text-decoration:none;
}
</style>

<div class="stepper">@include('demo.stepper')</div>

<section class="completion-page">

    <div class="completion-top">
        <div class="success-icon {{ $isApproved ? 'ok' : ($isRejected ? 'bad' : 'pending') }}">
            <i class="fas {{ $isApproved ? 'fa-circle-check' : ($isRejected ? 'fa-circle-xmark' : 'fa-hourglass-half') }}"></i>
        </div>

        <h1>
            @if($isApproved)
                Congratulations {{ auth()->user()->name }} 🎉
            @elseif($isRejected)
                Submission Not Approved
            @else
                Almost there, {{ auth()->user()->name }}!
            @endif
        </h1>

        <p>
            @if($isApproved)
                Your submission has been approved and your certificate is ready to download below.
            @elseif($isRejected)
                Your submission was not approved. Please contact support for next steps.
            @else
              Thank you for completing your demo! Your submission has been successfully received and is currently under review. Once your trainer approves your performance, your certificate will be unlocked in your dashboard. You'll receive a notification, and a Download Certificate button will become available so you can access your certificate instantly.
            @endif
        </p>

        <div class="completion-badge">
            <span class="{{ $isApproved ? 'badge-ok' : ($isRejected ? 'badge-bad' : 'badge-pending') }}">
                @if($isApproved) ✔ Approved · Certificate Issued
                @elseif($isRejected) ✘ Not Approved
                @else ⏳ Pending Admin Approval
                @endif
            </span>
        </div>
    </div>


    <div class="completion-grid">

        {{-- ── Left: Certificate preview (identical design to the PDF) ── --}}
        <div class="certificate-wrapper">

            <div class="certificate">
                <div class="certificate-inner">

                    <div class="cert-logos-row">
                        @if(file_exists(public_path('images/job-suraksha-logo.png')))
                            <img src="{{ asset('images/job-suraksha-logo.png') }}" alt="Logo">
                        @else
                            <span></span>
                        @endif
                        @if(file_exists(public_path('images/academic-mantra-logo.png')))
                            <img src="{{ asset('images/academic-mantra-logo.png') }}" alt="Logo">
                        @else
                            <span></span>
                        @endif
                    </div>

                    <h1 class="cert-title2">CERTIFICATE</h1>
                    <p class="cert-subtitle2">OF COMPLETION</p>
                    <div class="cert-divider2"></div>

                    <p class="cert-certify2">THIS IS TO CERTIFY THAT</p>
                    <div class="cert-name-line2">{{ $demo->demoUser->name ?? auth()->user()->name }}</div>

                    <div class="cert-body2">
                        Has completed
                        <span class="cert-blank2">{{ $demo->duration ?? '3 months' }}</span>
                        hours/months/years with us.
                        We thank you for showing your trust and being a valued member
                        while showing exemplary performance. He/She has acquired
                        experience in the
                        <span class="cert-blank2">{{ $demo->course->title ?? ($course->title ?? 'Department') }}</span>
                        Department.
                    </div>

                    <div class="cert-ribbon-wrap2">
                        <div class="cert-seal2">{{ $demo->score ?? 'Grade' }}</div>
                        <div class="cert-ribbon-tails2">
                            <span class="cert-tail-left2"></span><span class="cert-tail-right2"></span>
                        </div>
                    </div>

                    <div class="cert-footer2">
                        <div class="col">
                            <div class="cert-line2"></div>
                            {{-- {{ optional($demo->updated_at)->format('d M Y') ?? now()->format('d M Y') }}<br> --}}
                            Date
                        </div>
                        <div class="col">
                            <div class="cert-line2"></div>
                            Signature
                        </div>
                    </div>

                </div>
            </div>

            @unless($isApproved)
            <div class="cert-lock-overlay">
                <i class="fas {{ $isRejected ? 'fa-circle-xmark' : 'fa-lock' }}"></i>
                <strong>{{ $isRejected ? 'Certificate Not Issued' : 'Certificate Locked' }}</strong>
                <span>
                    @if($isRejected)
                        Your submission wasn't approved, so a certificate hasn't been issued for this attempt.
                    @else
                        This unlocks automatically once an admin approves your submission.
                    @endif
                </span>
            </div>
            @endunless

        </div>

        {{-- ── Right: sidebar ── --}}
        <div class="completion-sidebar">

            <div class="info-card">
                <h4>Student Details</h4>
                <ul>
                    <li><strong>Name:</strong> {{ auth()->user()->name }}</li>
                    <li><strong>Email:</strong> {{ auth()->user()->email }}</li>
                    <li><strong>Course:</strong> {{ $demo->course->title ?? ($course->title ?? '—') }}</li>
                    <li>
                        <strong>Status:</strong>
                        <div class="status-line {{ $isApproved ? 'ok' : ($isRejected ? 'bad' : 'pending') }}">
                            <i class="fas {{ $isApproved ? 'fa-circle-check' : ($isRejected ? 'fa-circle-xmark' : 'fa-hourglass-half') }}"></i>
                            {{ $isApproved ? 'Approved' : ($isRejected ? 'Not Approved' : 'Pending Review') }}
                        </div>
                    </li>
                </ul>
            </div>

            <div class="info-card">
                <h4>Actions</h4>

                @if($isApproved)
                    <a href="{{ route('lms.certificate.download', $demo->id) }}" class="btn-main">
                        <i class="fas fa-download"></i> Download Certificate
                    </a>
                @else
                    <button type="button" class="btn-main btn-disabled" disabled
                        title="{{ $isRejected ? 'Not available — submission was not approved' : 'Available once admin approves your submission' }}">
                        <i class="fas fa-lock"></i> Download Certificate
                    </button>
                @endif

                <a href="#" class="btn-outline">
                    <i class="fas fa-shield-check"></i> Verify Certificate
                </a>
            </div>

            <div class="info-card">
                <h4>Recommended Courses</h4>
                <ul>
                    <li>Advanced Digital Marketing</li>
                    <li>AI Marketing Automation</li>
                    <li>SEO Mastery Program</li>
                    <li>Performance Marketing</li>
                </ul>
            </div>

        </div>

    </div>

</section>
@endsection