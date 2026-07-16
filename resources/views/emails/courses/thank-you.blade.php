{{-- resources/views/emails/courses/thank-you.blade.php --}}
@component('mail::message')
# Thank you for choosing Academic Mantra, {{ $payment->name }}! 🎉

Your enrollment is confirmed for:

@component('mail::table')
| Course | Price |
|:-------|------:|
@foreach($courses as $course)
| {{ $course->title }} | {{ ($course->price ?? 0) > 0 ? '₹'.number_format($course->price) : 'Free' }} |
@endforeach
@endcomponent

**Subtotal:** ₹{{ number_format($payment->subtotal ?? 0, 2) }}
**GST:** ₹{{ number_format($payment->gst_amount ?? 0, 2) }}
**Total Paid:** ₹{{ number_format($payment->total_amount ?? $payment->amount, 2) }}
**Invoice No:** {{ $payment->invoice_no }}

@component('mail::button', ['url' => route('student.invoice.download', $payment->id)])
Download Invoice
@endcomponent

Your course access is unlocked immediately — head to your dashboard to start learning.

Thanks,<br>{{ config('app.name') }}
@endcomponent