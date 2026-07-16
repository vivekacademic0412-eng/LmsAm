<?php

namespace App\Livewire\Student;

use App\Mail\AdminCoursePurchaseNotification;
use App\Mail\CoursePurchaseThankYou;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class CourseCatalog extends Component
{
    public $cartIds = [];

    public function mount()
    {
        $this->cartIds = session('course_cart', []);
    }

    protected function syncSession()
    {
        session(['course_cart' => $this->cartIds]);
    }

    public function addToCart($courseId)
    {
        if (! in_array($courseId, $this->cartIds)) {
            $this->cartIds[] = $courseId;
            $this->syncSession();
        }
        $this->dispatch('cart-updated');
    }

    public function removeFromCart($courseId)
    {
        $this->cartIds = array_values(array_diff($this->cartIds, [$courseId]));
        $this->syncSession();
        $this->dispatch('cart-updated');
    }

    public function buyNow($courseId)
    {
        $this->cartIds = [$courseId];
        $this->syncSession();
        $this->checkout();
    }

    public function getCartCoursesProperty()
    {
        return Course::whereIn('id', $this->cartIds)->get();
    }

    /** Sum of discounted course prices (before GST). */
    public function getCartSubtotalProperty()
    {
        return $this->cartCourses->sum(fn ($c) => $c->price ?? 0);
    }

    /** Sum of GST across all cart courses (each course can have its own gst %). */
    public function getCartGstProperty()
    {
        return round($this->cartCourses->sum(fn ($c) => ($c->price ?? 0) * (($c->gst ?? 0) / 100)), 2);
    }

    /** What actually gets charged — subtotal + GST. */
    public function getCartTotalProperty()
    {
        return round($this->cartSubtotal + $this->cartGst, 2);
    }

    /**
     * Step 1: create Razorpay order for the whole cart (GST included), open checkout modal.
     */
    public function checkout()
    {
        if (empty($this->cartIds)) {
            $this->addError('cart', 'Your cart is empty.');
            return;
        }

        $total = $this->cartTotal;

        if ($total <= 0) {
            $this->enrollCart(null, null, 0, 'Free');
            return;
        }

        $api = new Api(config('razorpay.key'), config('razorpay.secret'));

        $order = $api->order->create([
            'receipt'         => 'cart_' . auth()->id() . '_' . time(),
            'amount'          => $total * 100,
            'currency'        => 'INR',
            'payment_capture' => 1,
            'notes'           => [
                'user_id'    => auth()->id(),
                'course_ids' => implode(',', $this->cartIds),
            ],
        ]);

        $this->dispatch('razorpay-checkout-open', [
            'key'         => config('razorpay.key'),
            'amount'      => $total * 100,
            'currency'    => 'INR',
            'order_id'    => $order['id'],
            'name'        => 'Academic Mantra LMS',
            'description' => count($this->cartIds) . ' course(s) enrollment (incl. GST)',
            'prefill'     => [
                'name'    => auth()->user()->name,
                'email'   => auth()->user()->email,
                'contact' => auth()->user()->contact ?? '',
            ],
        ]);
    }

    /**
     * Step 2: verify signature, then enroll user in every course in the cart.
     */
    public function verifyPayment($response)
    {
        $api = new Api(config('razorpay.key'), config('razorpay.secret'));

        $attributes = [
            'razorpay_order_id'   => $response['razorpay_order_id'] ?? null,
            'razorpay_payment_id' => $response['razorpay_payment_id'] ?? null,
            'razorpay_signature'  => $response['razorpay_signature'] ?? null,
        ];

        try {
            $api->utility->verifyPaymentSignature($attributes);
        } catch (SignatureVerificationError $e) {
            Log::warning('Cart payment verification failed: ' . $e->getMessage());
            $this->addError('payment', 'Payment verification failed. Please try again.');
            return;
        }

        $this->enrollCart(
            $attributes['razorpay_order_id'],
            $attributes['razorpay_payment_id'],
            $this->cartTotal,
            'Razorpay'
        );
    }

    protected function enrollCart($orderId, $paymentId, $amount, $gateway)
    {
        $courses  = $this->cartCourses;
        $subtotal = $this->cartSubtotal;
        $gst      = $this->cartGst;
        $total    = $amount ?? $this->cartTotal;

        // Always record a payment row (even for Free) so invoice + emails stay consistent.
        $payment = Payment::create([
            'user_id'             => auth()->id(),
            'name'                => auth()->user()->name,
            'email'               => auth()->user()->email,
            'phone'               => auth()->user()->contact ?? null,
            'amount'              => $total,
            'subtotal'            => $subtotal,
            'gst_amount'          => $gst,
            'total_amount'        => $total,
            'paid_amount'         => $total,
            'gateway'             => $gateway,
            'razorpay_order_id'   => $orderId,
            'razorpay_payment_id' => $paymentId,
            'invoice_no'          => 'INV-' . now()->format('YmdHis') . '-' . auth()->id(),
            'status'              => 'success',
            'source'              => 'Website',
            'notes'               => json_encode(['course_ids' => $courses->pluck('id')]),
            'paid_at'             => now(),
        ]);

        foreach ($courses as $course) {
            CourseEnrollment::firstOrCreate(
                ['student_id' => auth()->id(), 'course_id' => $course->id],
                ['status' => 'active', 'enrolled_at' => now()]
            );
        }

        // Thank-you mail to student
        Mail::to(auth()->user()->email)->send(new CoursePurchaseThankYou($payment, $courses));

        // Notify admin to activate the account
        Mail::to('abhijeet@gmail.com')->send(new AdminCoursePurchaseNotification(auth()->user(), $payment, $courses));

        $this->cartIds = [];
        $this->syncSession();

        $this->dispatch('payment-success', [
            'courseCount' => $courses->count(),
            'invoiceUrl'  => route('student.invoice.download', $payment->id),
        ]);
    }

    public function paymentFailed($error = null)
    {
        Log::info('Cart checkout dismissed/failed', ['error' => $error]);
        $this->addError('payment', 'Payment was not completed. Please try again.');
    }

    public function render()
    {
        $enrolledCourseIds = auth()->user()->enrollmentsAsStudent()->pluck('course_id')->all();

        $categories = CourseCategory::with([
            'courses' => fn ($q) => $q->with(['category', 'subcategory'])->orderBy('title'),
            'children.courses' => fn ($q) => $q->with(['category', 'subcategory'])->orderBy('title'),
        ])->whereNull('parent_id')->orderBy('name')->get();

        return view('livewire.student.course-catalog', [
            'categories'        => $categories,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }
}