<x-mail::message>
# New Enrollment Received

**{{ $student->name }}** just paid for {{ $courses->count() }} course{{ $courses->count() !== 1 ? 's' : '' }}. Activate their account access if it isn't automatic yet.

## Student

| | |
|---|---|
| Name | {{ $student->name }} |
| Email | {{ $student->email }} |
| Phone | {{ $student->contact ?? '—' }} |

## Payment

| | |
|---|---|
| Invoice | {{ $payment->invoice_no }} |
| Gateway | {{ $payment->gateway }} |
| Subtotal | ₹{{ number_format($payment->subtotal, 2) }} |
| GST | ₹{{ number_format($payment->gst_amount, 2) }} |
| **Total paid** | **₹{{ number_format($payment->total_amount, 2) }}** |
| Paid at | {{ optional($payment->paid_at)->format('d M Y, h:i A') }} |

## Courses

@foreach ($courses as $course)
- **{{ $course->title }}** — {{ ($course->price ?? 0) > 0 ? '₹' . number_format($course->price) : 'Free' }}
@endforeach

<x-mail::button :url="route('admin.payments.show', $payment->id)">
View Payment in Admin
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>