<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px 40px; }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1a1a1a;
            font-size: 13px;
            line-height: 1.5;
        }
        table { width: 100%; border-collapse: collapse; }

        .header-table td { vertical-align: top; padding-bottom: 20px; }
        .brand-name { font-size: 20px; font-weight: bold; color: #0947a8; }
        .brand-sub { font-size: 11px; color: #666; margin-top: 2px; }
        .invoice-meta { text-align: right; font-size: 12px; color: #444; }
        .invoice-meta strong { color: #000; font-size: 15px; }

        .divider { border-top: 2px solid #0947a8; margin: 10px 0 20px; }

        .status-badge {
            display: inline-block;
            background: #e6f4ea;
            color: #1e7e34;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 3px;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            font-weight: bold;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e5e5;
            margin-bottom: 8px;
        }

        .info-table td { padding: 3px 0; font-size: 12.5px; }
        .info-label { color: #666; width: 40%; }
        .info-value { font-weight: 600; text-align: right; }

        .items-table { margin-top: 10px; }
        .items-table th {
            background: #f5f7fa;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .items-table th.amount, .items-table td.amount { text-align: right; }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12.5px;
        }

        .total-table { margin-top: 10px; }
        .total-table td { padding: 4px 10px; font-size: 12.5px; }
        .total-row td {
            font-size: 15px;
            font-weight: bold;
            border-top: 2px solid #0947a8;
            padding-top: 10px;
            color: #0947a8;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e5e5;
            font-size: 10.5px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="60%">
                <div class="brand-name">Academic Mantra</div>
                <div class="brand-sub">Payment Receipt</div>
            </td>
            <td width="40%" class="invoice-meta">
                <strong>{{ $payment->invoice_no }}</strong><br>
                Date: {{ optional($payment->paid_at)->format('d M Y, h:i A') }}
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    <span class="status-badge">✓ PAYMENT SUCCESSFUL</span>

    <table style="margin-top:20px;">
        <tr>
            <td width="48%" style="vertical-align:top;">
                <div class="section-title">Billed To</div>
                <table class="info-table">
                    <tr><td class="info-label">Name</td><td class="info-value">{{ $payment->name }}</td></tr>
                    <tr><td class="info-label">Email</td><td class="info-value">{{ $payment->email }}</td></tr>
                    <tr><td class="info-label">Phone</td><td class="info-value">{{ $payment->phone }}</td></tr>
                </table>
            </td>
            <td width="4%"></td>
            <td width="48%" style="vertical-align:top;">
                <div class="section-title">Payment Info</div>
                <table class="info-table">
                    <tr><td class="info-label">Payment ID</td><td class="info-value">{{ $payment->razorpay_payment_id }}</td></tr>
                    <tr><td class="info-label">Order ID</td><td class="info-value">{{ $payment->razorpay_order_id }}</td></tr>
                    <tr><td class="info-label">Method</td><td class="info-value">Razorpay</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title" style="margin-top:20px;">Order Details</div>
    <table class="items-table">
        <tr>
            <th>Description</th>
            <th>Type</th>
            <th class="amount">Amount</th>
        </tr>
        <tr>
            <td>
                {{ $payment->category?->name }}
                @if ($payment->course)
                    <br><span style="color:#888; font-size:11px;">{{ $payment->course->title }}</span>
                @endif
            </td>
            <td>{{ ucfirst($payment->type) }}</td>
            <td class="amount">₹{{ number_format($payment->paid_amount ?? $payment->amount, 2) }}</td>
        </tr>
    </table>

    <table class="total-table">
        <tr>
            <td width="70%"></td>
            <td width="30%" class="total-row">Total Paid: ₹{{ number_format($payment->paid_amount ?? $payment->amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        This is a computer-generated receipt and does not require a signature.<br>
        Academic Mantra &middot; support@academicmantra.example
    </div>

</body>
</html>