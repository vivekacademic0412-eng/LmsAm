<div style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--bg); padding:20px;">
    <div class="cc-wrap" style="max-width:480px; width:100%; text-align:center;">

        <div style="width:64px; height:64px; border-radius:50%; background:rgba(22,163,74,.12); color:var(--success); display:flex; align-items:center; justify-content:center; font-size:32px; margin:0 auto 18px;">
            ✓
        </div>

        <div class="cc-title" style="font-size:20px;">Payment Successful!</div>
        <p style="color:var(--text-muted); font-size:13.5px; margin:8px 0 24px;">
            Thank you, <strong>{{ $link->user->name }}</strong>. Your payment with <strong>Academic Mantra</strong> has been received.
        </p>

        <div class="cc-section" style="text-align:left;">
            <table style="width:100%; font-size:13.5px;">
                <tr><td style="padding:6px 0; color:var(--text-muted);">Invoice No.</td><td style="text-align:right; font-weight:600;">{{ $link->invoice_no  }}</td></tr>
                <tr><td style="padding:6px 0; color:var(--text-muted);">Enrollment</td><td style="text-align:right;">{{ ucfirst($link->type) }} — {{ $link->category?->name }}</td></tr>
                <tr><td style="padding:6px 0; color:var(--text-muted);">Amount Paid</td><td style="text-align:right; font-weight:700; color:var(--brand-primary);">₹{{ number_format($link->amount) }}</td></tr>
                <tr><td style="padding:6px 0; color:var(--text-muted);">Paid On</td><td style="text-align:right;">{{ optional($link->paid_at)->format('d M Y, h:i A') }}</td></tr>
            </table>
        </div>

        @if ($link->transaction?->receipt_pdf_path)
            <a href="{{ Storage::url($link->transaction->receipt_pdf_path) }}" target="_blank"
               class="cc-btn cc-btn-primary" style="width:100%; justify-content:center; margin-top:6px;">
                ⬇ Download Receipt (PDF)
            </a>
        @endif

        <p style="font-size:12px; color:var(--text-muted); margin-top:18px;">
            Your login details were shared with you separately. Use them to access your dashboard.
        </p>
    </div>
</div>