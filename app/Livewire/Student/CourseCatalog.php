<?php

namespace App\Livewire\Student;

use App\Mail\AdminCoursePurchaseNotification;
use App\Mail\CoursePurchaseThankYou;
use App\Models\Batch;
use App\Models\BatchStudent;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class CourseCatalog extends Component
{
    public $cartIds = [];

    /**
     * course_id => batch_id the student picked for that course.
     * Courses without batches (self-paced) simply have no entry here.
     */
    public $selectedBatch = [];

    /**
     * Which top-level category tab is currently open in the "Browse All Courses"
     * section. Kept as server state (instead of pure client-side JS toggling)
     * so it survives every Livewire re-render — previously, clicking a category
     * appeared to "do nothing" because any wire:click elsewhere (add to cart,
     * pick batch, etc.) caused Livewire to morph the DOM back to whatever the
     * Blade template rendered server-side, silently resetting the tab back to
     * the first category and wiping out the client-only selection.
     */
    public $activeCategoryId = null;

    public function mount()
    {
        $this->cartIds       = session('course_cart', []);
        $this->selectedBatch = session('course_cart_batches', []);
    }

    protected function syncSession()
    {
        session([
            'course_cart'        => $this->cartIds,
            'course_cart_batches' => $this->selectedBatch,
        ]);
    }

    /** Switch the active category tab in "Browse All Courses". */
    public function setActiveCategory($categoryId)
    {
        $this->activeCategoryId = (int) $categoryId;
    }

    /** Seats remaining on a batch. Public so the Blade view can show live counts. */


    public function seatsLeftFor(Batch $batch): int
    {
        $taken = $batch->students()
            ->where('status', '!=', 'cancelled')
            ->count();

        return max(0, ($batch->max_seats ?? 0) - $taken);
    }
    public function pickBatch($courseId, $batchId)
    {
        $course = Course::with('batches.batch')->find($courseId);
        $batch  = $course?->batches->firstWhere('id', (int) $batchId);

        if (! $batch) {
            return;
        }

        if ($this->seatsLeftFor($batch->batch) <= 0) {
            $this->addError('cart', 'That batch is full — pick another one.');
            return;
        }

        $this->selectedBatch[$courseId] = (int) $batchId;
        $this->syncSession();
    }

    public function addToCart($courseId)
    {
        $course = Course::with('batches.batch')->find($courseId);
        if (! $course) {
            return;
        }

        // If the course runs in batches, a seat must be selected before it can be carted.
        if ($course->batches->isNotEmpty() && empty($this->selectedBatch[$courseId])) {
            $firstOpen = $course->batches->first(fn($b) => $b->batch && $this->seatsLeftFor($b->batch) > 0);
            if (! $firstOpen) {
                $this->addError('cart', "\"{$course->title}\" has no open batches right now.");
                return;
            }
            $this->selectedBatch[$courseId] = $firstOpen->id;
        }

        if (! in_array($courseId, $this->cartIds)) {
            $this->cartIds[] = $courseId;
        }

        $this->syncSession();
        $this->dispatch('cart-added');
    }

    public function removeFromCart($courseId)
    {
        $this->cartIds = array_values(array_diff($this->cartIds, [$courseId]));
        unset($this->selectedBatch[$courseId]);
        $this->syncSession();
        $this->dispatch('cart-removed');
    }

    public function buyNow($courseId)
    {
        $this->cartIds = [$courseId];
        $this->selectedBatch = isset($this->selectedBatch[$courseId])
            ? [$courseId => $this->selectedBatch[$courseId]]
            : [];
        $this->syncSession();
        $this->addToCart($courseId); // resolves a default batch if one wasn't picked yet
        $this->checkout();
    }

    public function getCartCoursesProperty()
    {
        return Course::with('batches.batch')->whereIn('id', $this->cartIds)->get();
    }

    /** Sum of discounted course prices (before GST). */
    public function getCartSubtotalProperty()
    {
        return $this->cartCourses->sum(fn($c) => $c->price ?? 0);
    }

    /** Sum of GST across all cart courses (each course can have its own gst %). */
    public function getCartGstProperty()
    {
        return round($this->cartCourses->sum(fn($c) => ($c->price ?? 0) * (($c->gst ?? 0) / 100)), 2);
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

        // Re-validate seats right before payment so nobody pays for a seat that filled
        // up between adding to cart and checking out.
        foreach ($this->cartCourses as $course) {
            $batchId = $this->selectedBatch[$course->id] ?? null;
            if ($batchId) {
                $courseBatch = $course->batches->firstWhere('id', $batchId);
                if (! $courseBatch || ! $courseBatch->batch || $this->seatsLeftFor($courseBatch->batch) <= 0) {
                    $this->addError('cart', "Sorry, \"{$course->title}\" just filled up — please pick another batch.");
                    return;
                }
            }
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

        $payment = DB::transaction(function () use ($courses, $orderId, $paymentId, $total, $subtotal, $gst, $gateway) {

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
                // This course's own share of what was actually paid (price + its own GST),
                // so the per-course amounts still add up to the total charged.
                $courseGst    = round(($course->price ?? 0) * (($course->gst ?? 0) / 100), 2);
                $courseAmount = round(($course->price ?? 0) + $courseGst, 2);

                $batchId     = $this->selectedBatch[$course->id] ?? null;
                $courseBatch = $batchId ? $course->batches->firstWhere('id', $batchId) : null;
                $batch       = $courseBatch?->batch;

                $enrollment = CourseEnrollment::firstOrCreate(
                    ['student_id' => auth()->id(), 'course_id' => $course->id],
                    [
                        // ASSUMPTION: no CourseLevel picker exists yet on this page — see
                        // README-ASSUMPTIONS.md #4 if courses actually have level tiers.
                        'course_level_id'         => null,
                        'batch_id'                => $batch?->id,
                        'order_reference'         => $payment->invoice_no,
                        'amount_paid'             => $courseAmount,
                        'registered_at'           => now(),
                        'zero_day_start_at'       => $batch?->zero_day_date,
                        'progress_percent'        => 0,
                        'status'                  => 'active',
                        'certificate_unlocked_at' => null,
                    ]
                );

                if ($batch) {
                    BatchStudent::firstOrCreate(
                        ['course_batch_id' => $courseBatch?->id, 'user_id' => auth()->id()],
                        [
                            'batch_id' => $batch?->id,
                            'enrollment_id' => $enrollment->id,
                            'joined_at'     => now(),
                            'status'        => 'active',
                        ]
                    );
                }
            }

            return $payment;
        });

        // Thank-you mail to student
        Mail::to(auth()->user()->email)->send(new CoursePurchaseThankYou($payment, $courses));

        // Notify admin to activate the account
        // Mail::to('info.academicmantraservices@gmail.com')->send(new AdminCoursePurchaseNotification(auth()->user(), $payment, $courses));

        $this->cartIds       = [];
        $this->selectedBatch = [];
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
        $enrollments = CourseEnrollment::where('student_id', auth()->id())
            ->get()
            ->keyBy('course_id');

        $enrolledCourseIds = $enrollments->keys()->all();

        $courseWith = [
            'category',
            'subcategory',
            'courseType',
            'weeks.sessions',

            // FIX: `course_batch` (the CourseBatch pivot model) has NO `start_date`
            // column — that lives on `batches`. Ordering by `start_date` directly on
            // this relation threw:
            //   SQLSTATE[42S22]: Column not found: 1054 Unknown column 'start_date'
            //   in 'order clause'
            // so we join to `batches` to sort, eager-load the related Batch model,
            // and explicitly select `course_batch.*` to avoid column name collisions
            // (both tables have `id`, `status`, `created_at`, `updated_at`).
            'batches' => function ($q) {
                $q->with('batch')
                    ->join('batches', 'batches.id', '=', 'course_batch.batch_id')
                    ->orderBy('batches.start_date')
                    ->select('course_batch.*');
            },
        ];

        $categories = CourseCategory::with([
            'courses' => fn($q) => $q->with($courseWith)->orderBy('title'),
            'children.courses' => fn($q) => $q->with($courseWith)->orderBy('title'),
        ])->whereNull('parent_id')->orderBy('name')->get();

        // Default the active tab to the first category the first time the
        // component renders (server-side, so it's always correct — no reliance
        // on client JS running before the first paint).
        if ($this->activeCategoryId === null && $categories->isNotEmpty()) {
            $this->activeCategoryId = $categories->first()->id;
        }

        return view('livewire.student.course-catalog', [
            'categories'        => $categories,
            'enrolledCourseIds' => $enrolledCourseIds,
            'enrollments'       => $enrollments,
        ]);
    }
}
