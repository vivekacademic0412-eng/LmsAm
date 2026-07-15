<?php

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
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

    public function getCartTotalProperty()
    {
        return $this->cartCourses->sum(fn ($c) => $c->price ?? 0);
    }

    /**
     * Step 1: create Razorpay order for the whole cart, open checkout modal.
     */
    public function checkout()
    {
        if (empty($this->cartIds)) {
            $this->addError('cart', 'Your cart is empty.');
            return;
        }

        $amount = $this->cartTotal;

        // Free courses — skip payment gateway, enroll directly
        if ($amount <= 0) {
            $this->enrollCart(null, null, null, 'Free');
            return;
        }

        $api = new Api(config('razorpay.key'), config('razorpay.secret'));

        $order = $api->order->create([
            'receipt'         => 'cart_' . auth()->id() . '_' . time(),
            'amount'          => $amount * 100,
            'currency'        => 'INR',
            'payment_capture' => 1,
            'notes'           => [
                'user_id'    => auth()->id(),
                'course_ids' => implode(',', $this->cartIds),
            ],
        ]);

        $this->dispatch('razorpay-checkout-open', [
            'key'         => config('razorpay.key'),
            'amount'      => $amount * 100,
            'currency'    => 'INR',
            'order_id'    => $order['id'],
            'name'        => 'Academic Mantra LMS',
            'description' => count($this->cartIds) . ' course(s) enrollment',
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
        $courses = $this->cartCourses;

        if ($amount > 0) {
            Payment::create([
                'user_id'             => auth()->id(),
                'name'                => auth()->user()->name,
                'email'               => auth()->user()->email,
                'phone'               => auth()->user()->contact ?? null,
                'amount'              => $amount,
                'paid_amount'         => $amount,
                'gateway'             => $gateway,
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'invoice_no'          => 'INV-' . now()->format('YmdHis') . '-' . auth()->id(),
                'status'              => 'success',
                'source'              => 'Website',
                'notes'               => json_encode(['course_ids' => $courses->pluck('id')]),
                'paid_at'             => now(),
            ]);
        }

        foreach ($courses as $course) {
            CourseEnrollment::firstOrCreate(
                ['student_id' => auth()->id(), 'course_id' => $course->id],
                ['status' => 'active', 'enrolled_at' => now()]
            );
        }

        $this->cartIds = [];
        $this->syncSession();

        $this->dispatch('payment-success', courseCount: $courses->count());
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