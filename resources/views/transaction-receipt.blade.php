<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: A4 portrait; }
        body { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 40px 50px; color: #16233d; }

        .header { display: table; width: 100%; margin-bottom: 30px; border-bottom: 2px solid #16233d; padding-bottom: 16px; }
        .header .logo { display: table-cell; width: 50%; font-size: 20px; font-weight: bold; color: #0947a8; }
        .header .meta { display: table-cell; width: 50%; text-align: right; font-size: 12px; color: #5a718a; }

        .title { font-size: 22px; font-weight: bold; letter-spacing: 2px; margin-bottom: 4px; }
        .subtitle { font-size: 12px; color: #5a718a; margin-bottom: 24px; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .info-table td { padding: 6px 0; font-size: 13px; vertical-align: top; }
        .info-table .label { color: #5a718a; width: 160px; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th {
            background: #eaf2ff; text-align: left; padding: 10px 12px;
            font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #5a718a;
            border-bottom: 1px solid #d6e4f5;
        }
        .items-table td { padding: 12px; font-size: 13px; border-bottom: 1px solid #d6e4f5; }
        .items-table .amount-cell { text-align: right; font-weight: bold; }

        .total-row td { font-size: 15px; font-weight: bold; padding-top: 16px; border: none; }
        .total-row .amount-cell { color: #0947a8; font-size: 18px; }

        .status-badge {
            display: inline-block; background: rgba(22,163,74,.12); color: #16a34a;
            padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: bold; letter-spacing: .5px;
        }

        .footer { margin-top: 60px; padding-top: 16px; border-top: 1px solid #d6e4f5; font-size: 11px; color: #5a718a; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">ACADEMIC MANTRA</div>
        <div class="meta">
            Invoice #{{ $transaction->invoice_number }}<br>
            {{ optional($transaction->paid_at)->format('d M Y, h:i A') }}
        </div>
    </div>

    <div class="title">Payment Receipt</div>
    <div class="subtitle">This confirms that a payment has been received by Academic Mantra.</div>

    <table class="info-table">
        <tr>
            <td class="label">Billed To</td>
            <td>{{ $link->user->name }}<br>{{ $link->user->email }}<br>{{ $link->user->contact_no }}</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td>Razorpay ({{ $transaction->gateway_payment_id }})</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td><span class="status-badge">PAID</span></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ ucfirst($link->type) }} Enrollment — {{ $link->category?->name }}
                    @if ($link->course)<br><span style="color:#5a718a; font-size:11.5px;">{{ $link->course->title }}</span>@endif
                </td>
                <td class="amount-cell">₹{{ number_format($transaction->amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Paid</td>
                <td class="amount-cell">₹{{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        This is a system-generated receipt from Academic Mantra and does not require a physical signature.<br>
        For queries, contact support@academicmantra.com
    </div>

</body>
</html>