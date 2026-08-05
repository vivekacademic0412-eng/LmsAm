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
<div id="verify-overlay" class="cc-modal-overlay" style="display:none;">
    <div class="cc-modal" style="max-width:320px; padding:32px 24px; text-align:center;">
        <div class="cc-spinner" style="margin:0 auto 16px;"></div>
        <div style="font-weight:600; font-size:15px;">Verifying your payment…</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:6px;">Please don't close this window.</div>
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

{{-- ═══════════ Verifying overlay (add inside the outer wrap, right after the header) ═══════════ --}}

</div>
@if ($payment->status !== 'success' && !($payment->link_expires_at && $payment->link_expires_at->isPast()))
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const verifyOverlay = document.getElementById('verify-overlay');
        const payBtn = document.getElementById('rzp-pay-btn');

        function showVerifying() {
            verifyOverlay.style.display = 'flex';
        }
        function hideVerifying() {
            verifyOverlay.style.display = 'none';
        }

        payBtn.addEventListener('click', function() {
            payBtn.disabled = true;

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
                    // Razorpay's own modal has already shown its brief
                    // "Payment Successful" animation and closed itself —
                    // this fires right after. Show our own verifying state
                    // immediately so there's no dead gap before the API call.
                    showVerifying();

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
                            hideVerifying();

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Payment Verified!',
                                    text: 'Redirecting you to your receipt…',
                                    timer: 1600,
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                }).then(() => {
                                    window.location.href = data.redirect_url;
                                });
                            } else {
                                payBtn.disabled = false;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Verification Failed',
                                    text: data.message || 'We could not verify your payment. Please try again or contact support.',
                                });
                            }
                        })
                        .catch(() => {
                            hideVerifying();
                            payBtn.disabled = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Something went wrong',
                                text: 'Unable to verify payment. Please check your connection and try again.',
                            });
                        });
                },
                modal: {
                    // Runs if the user closes Razorpay's modal without paying
                    ondismiss: function() {
                        payBtn.disabled = false;
                    }
                },
                theme: {
                    color: "#0947a8"
                }
            };

            var rzp = new Razorpay(options);

            // Runs if the payment itself fails (card declined, etc.) —
            // your original code had no handling for this at all.
            rzp.on('payment.failed', function(response) {
                payBtn.disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Failed',
                    text: response.error?.description || 'Your payment could not be completed. Please try again.',
                });
            });

            rzp.open();
        });
    </script>

    <style>
        .cc-spinner {
            width: 34px;
            height: 34px;
            border: 3px solid var(--line, #e2e8f0);
            border-top-color: var(--brand-primary, #0947a8);
            border-radius: 50%;
            animation: cc-spin 0.7s linear infinite;
        }
        @keyframes cc-spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endif
