{{-- resources/views/invoices/course-invoice.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color:#0a2240; }
    .header { display:flex; justify-content:space-between; margin-bottom: 24px; }
    .header h1 { font-size: 20px; margin:0; }
    table { width:100%; border-collapse: collapse; margin-top: 16px; }
    th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
    th { background:#f8fafc; }
    .totals td { border: none; padding: 4px 10px; }
    .totals .label { text-align: right; font-weight: 600; }
    .grand { font-size: 15px; font-weight: 800; color:#0947a8; }
</style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Academic Mantra LMS</h1>
            <div>Invoice No: {{ $payment->invoice_no }}</div>
            <div>Date: {{ optional($payment->paid_at)->format('d M Y') }}</div>
        </div>
        <div style="text-align:right;">
            <strong>{{ $payment->name }}</strong><br>
            {{ $payment->email }}<br>
            {{ $payment->phone }}
        </div>
    </div>

    <table>
        <thead>
            <tr><th>Course</th><th>Original Price</th><th>Price</th></tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
                <tr>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->original_price ? '₹'.number_format($course->original_price) : '—' }}</td>
                    <td>{{ ($course->price ?? 0) > 0 ? '₹'.number_format($course->price) : 'Free' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:20px;width:280px;margin-left:auto;">
        <tr class="totals"><td class="label">Subtotal</td><td>₹{{ number_format($payment->subtotal ?? 0, 2) }}</td></tr>
        <tr class="totals"><td class="label">GST</td><td>₹{{ number_format($payment->gst_amount ?? 0, 2) }}</td></tr>
        <tr class="totals grand"><td class="label">Total Paid</td><td>₹{{ number_format($payment->total_amount ?? $payment->amount, 2) }}</td></tr>
    </table>
</body>
</html>