<?php

namespace App\Livewire\Student;

use App\Models\Payment;
use Livewire\Component;

class PaymentCheckout extends Component
{
    public Payment $payment;
    public $razorpayKey;
    public $razorpayOrderId;

    public function mount(string $token): void
    {
        $this->payment = Payment::with(['user', 'category', 'course'])
            ->where('token', $token)
            ->firstOrFail();

        if ($this->payment->status === Payment::STATUS_SUCCESS) {
            return; // view shows "already paid" state
        }

        if ($this->payment->link_expires_at && $this->payment->link_expires_at->isPast()) {
            return; // view shows "link expired" state
        }

        // Create a Razorpay order via RazorpayService
        $order = app(\App\Services\RazorpayService::class)->createOrder(
            amountInRupees: (float) $this->payment->amount,
            receipt: 'pay_' . $this->payment->id
        );

        $this->razorpayKey = config('services.razorpay.key');
        $this->razorpayOrderId = $order['id'];

        // Save the gateway order id immediately (useful if student abandons checkout)
        $this->payment->update(['razorpay_order_id' => $order['id']]);
    }

    public function render()
    {
        return view('livewire.student.payment-checkout');
    }
}