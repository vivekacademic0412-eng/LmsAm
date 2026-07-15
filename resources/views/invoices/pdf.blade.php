<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $payment->invoice_no }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #222; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background: #f5f5f5; }
        .totals { margin-top: 20px; width: 300px; margin-left: auto; }
        .totals td { border: none; padding: 4px 10px; }
        .totals .grand { font-weight: bold; font-size: 15px; border-top: 2px solid #333; }
        .status { color: #16a34a; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Live Skills Training Program</h1>
            <p>Invoice #{{ $payment->invoice_no }}</p>
        </div>
        <div style="text-align: right;">
            <p><strong>Date:</strong> {{ $payment->paid_at?->format('d M Y, h:i A') }}</p>
            <p class="status">PAID</p>
        </div>
    </div>

    <table>
        <tr>
            <th colspan="2">Billed To</th>
        </tr>
        <tr>
            <td>Name</td>
            <td>{{ $payment->name }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>{{ $payment->email }}</td>
        </tr>
        <tr>
            <td>Phone</td>
            <td>{{ $payment->phone }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Description</th>
            <th>Payment ID</th>
            <th>Order ID</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>One-Time Enrollment Fee</td>
            <td>{{ $payment->razorpay_payment_id }}</td>
            <td>{{ $payment->razorpay_order_id }}</td>
            <td>₹{{ number_format($payment->amount) }}</td>
        </tr>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td>₹{{ number_format($payment->amount) }}</td>
        </tr>
        <tr class="grand">
            <td>Total Paid</td>
            <td>₹{{ number_format($payment->paid_amount) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>This is a system-generated invoice and does not require a signature.</p>
        <p>Gateway: {{ $payment->gateway }} · Status: {{ ucfirst($payment->status) }}</p>
    </div>
</body>
</html>