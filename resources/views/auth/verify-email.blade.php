@extends('layouts.app')

@section('title', 'Verify Your Email — Academic Mantra')

@section('content')
<div class="verify-page">
<style>
.verify-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.verify-card {
    max-width: 460px;
    width: 100%;
    background: var(--bg-card);
    border: 1.5px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    padding: 40px 36px;
    text-align: center;
}

.verify-icon-wrap {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 22px;
    box-shadow: 0 10px 28px rgba(9,71,168,.25);
}
.verify-icon-wrap i { font-size: 30px; color: #fff; }

.verify-title {
    font-size: 21px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 10px;
}

.verify-sub {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.7;
    margin-bottom: 6px;
}

.verify-email-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--primary-glow);
    color: var(--brand-primary);
    font-weight: 700;
    font-size: 13.5px;
    padding: 6px 16px;
    border-radius: 20px;
    margin: 14px 0 24px;
}

.verify-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(22,163,74,.1);
    border: 1px solid rgba(22,163,74,.3);
    color: var(--success);
    font-size: 13px;
    font-weight: 600;
    padding: 12px 16px;
    border-radius: var(--radius-xs);
    margin-bottom: 20px;
    text-align: left;
}
.verify-alert i { font-size: 18px; flex-shrink: 0; }

.verify-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px 22px;
    border-radius: var(--radius-xs);
    font-size: 14px; font-weight: 700;
    border: 1.5px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .15s;
    text-decoration: none;
    width: 100%;
}
.btn-primary {
    background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary));
    color: #fff;
    box-shadow: 0 3px 10px rgba(9,71,168,.25);
}
.btn-primary:hover:not(:disabled) { opacity: .9; transform: translateY(-1px); }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }

.btn-outline {
    background: transparent;
    color: var(--text-muted);
    border-color: var(--line);
}
.btn-outline:hover { border-color: var(--primary); color: var(--brand-primary); background: var(--primary-glow); }

.verify-help {
    margin-top: 26px;
    padding-top: 20px;
    border-top: 1px solid var(--line);
    font-size: 12.5px;
    color: var(--text-muted);
}
.verify-help a { color: var(--brand-primary); font-weight: 600; text-decoration: none; }
.verify-help a:hover { text-decoration: underline; }

.verify-spinner {
    animation: verifySpin .8s linear infinite;
}
@keyframes verifySpin { to { transform: rotate(360deg); } }
</style>

<div class="verify-card">
    <div class="verify-icon-wrap">
        <i class="ti ti-mail-check"></i>
    </div>

    <h1 class="verify-title">Verify Your Email Address</h1>

    <p class="verify-sub">
        Thanks for registering, {{ auth()->user()->name }}! We've sent a verification link to:
    </p>

    <div class="verify-email-chip">
        <i class="ti ti-mail"></i> {{ auth()->user()->email }}
    </div>

    <p class="verify-sub">
        Please click the link in that email to activate your account. If you didn't receive it, you can request a new one below.
    </p>

    @if (session('message'))
        <div class="verify-alert">
            <i class="ti ti-circle-check"></i>
            {{ session('message') }}
        </div>
    @endif

    <div class="verify-actions">
        <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
            @csrf
            <button type="submit" class="btn btn-primary" id="resendBtn">
                <i class="ti ti-send" id="resendIcon"></i>
                <span id="resendLabel">Resend Verification Email</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline">
                <i class="ti ti-logout"></i> Log Out
            </button>
        </form>
    </div>

    <div class="verify-help">
        Wrong email address? <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-help').submit();">Log out</a> and register again with the correct one.
        <form id="logout-form-help" method="POST" action="{{ route('logout') }}" style="display:none;">
            @csrf
        </form>
    </div>
</div>

</div>

<script>
    document.getElementById('resendForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('resendBtn');
        const icon = document.getElementById('resendIcon');
        const label = document.getElementById('resendLabel');
        btn.disabled = true;
        icon.className = 'ti ti-loader-2 verify-spinner';
        label.textContent = 'Sending...';
    });
</script>
@endsection