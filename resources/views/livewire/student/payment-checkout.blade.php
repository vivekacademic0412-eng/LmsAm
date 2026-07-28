<div
    style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--bg); padding:20px;">
    <div class="cc-wrap" style="max-width:480px; width:100%;">

        <div class="cc-header" style="border-bottom:none; margin-bottom:10px;">
            <div class="cc-header-left">
                <div class="cc-icon">💳</div>
                <div>
                    <div class="cc-title">Academic Mantra</div>
                    <div class="cc-subtitle">Complete your payment to continue</div>
                </div>
            </div>
        </div>

        @if ($payment->status === 'success')
            <div class="cc-alert cc-alert-success">✓ This payment has already been completed. Thank you!</div>
            <a href="{{ route('payment.success', $payment->token) }}" class="cc-btn cc-btn-primary"
                style="width:100%; justify-content:center;">
                View Receipt
            </a>
        @elseif ($payment->link_expires_at && $payment->link_expires_at->isPast())
            <div class="cc-alert cc-alert-error">✕ This payment link has expired. Please contact Academic Mantra for a
                new link.</div>
        @else
            <div class="cc-section">
                <div class="cc-section-title">Order Summary</div>
                <table style="width:100%; font-size:13.5px;">
                    <tr>
                        <td style="padding:6px 0; color:var(--text-muted);">Student</td>
                        <td style="padding:6px 0; text-align:right; font-weight:600;">{{ $payment->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:var(--text-muted);">Enrollment</td>
                        <td style="padding:6px 0; text-align:right; font-weight:600;">{{ ucfirst($payment->type) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:var(--text-muted);">Subject</td>
                        <td style="padding:6px 0; text-align:right;">{{ $payment->category?->name }}</td>
                    </tr>
                    @if ($payment->course)
                        <tr>
                            <td style="padding:6px 0; color:var(--text-muted);">Course</td>
                            <td style="padding:6px 0; text-align:right;">{{ $payment->course->title }}</td>
                        </tr>
                    @endif
                    <tr style="border-top:1px solid var(--line);">
                        <td style="padding:10px 0; font-weight:700;">Total Amount</td>
                        <td
                            style="padding:10px 0; text-align:right; font-weight:700; font-size:20px; color:var(--brand-primary);">
                            ₹{{ number_format($payment->amount) }}
                        </td>
                    </tr>
                </table>
            </div>

            <button id="rzp-pay-btn" class="cc-btn cc-btn-primary"
                style="width:100%; justify-content:center; padding:14px;">
                Pay ₹{{ number_format($payment->amount) }} Now
            </button>

            <p style="text-align:center; font-size:11.5px; color:var(--text-muted); margin-top:14px;">
                🔒 Secured by Razorpay
            </p>
        @endif
    </div>
</div>

@if ($payment->status !== 'success' && !($payment->link_expires_at && $payment->link_expires_at->isPast()))
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('rzp-pay-btn').addEventListener('click', function() {
            var options = {
                key: "{{ $razorpayKey }}",
                order_id: "{{ $razorpayOrderId }}",
                amount: {{ (int) round($payment->amount * 100) }},
                currency: "INR",
                name: "Academic Mantra",
                description: "{{ ucfirst($payment->type) }} — {{ $payment->category?->name }}",
                prefill: {
                    name: "{{ $payment->name }}",
                    email: "{{ $payment->email }}",
                    contact: "{{ $payment->phone }}"
                },
                handler: function(response) {
                    fetch("{{ route('payment.verify') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                token: "{{ $payment->token }}",
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Reload the page so Livewire gets the updated payment status
                                window.location.reload();
                            } else {
                                alert(data.message || "Payment verification failed.");
                            }
                        })
                        .catch(() => {
                            alert("Unable to verify payment. Please try again.");
                        });
                },
                theme: {
                    color: "#0947a8"
                }
            };
            var rzp = new Razorpay(options);
            rzp.open();
        });
    </script>
@endif
