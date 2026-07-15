<?php

namespace App\Livewire;

use App\Mail\DemoLoginCredentialsMail;
use Livewire\Component;
use App\Models\State;
use App\Models\City;
use App\Models\DemoAccessToken;
use App\Models\DemoTypeSelection;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentForm extends Component
{
    public $name;
    public $email;
    public $phone;

    public $state_id;
    public $city_id;

    public $states = [];
    public $cities = [];
    public $amount = 999;

    public $razorpayKey;
    public $razorpayOrderId;

    public $showSuccess = false;
    public $redirectSeconds = 1;

    // NEW: holds the created Payment's id so the Blade view can build the download link
    public $paymentId = null;

    public function mount()
    {
        if (auth()->check()) {
            $this->name  = auth()->user()->name . ' ' . auth()->user()->last_name;
            $this->email = auth()->user()->email;
            $this->phone = auth()->user()->contact;
        }
        $this->states = State::orderBy('name')->get();
        $this->razorpayKey = config('razorpay.key');
    }

    public function updatedStateId($value)
    {
        $this->cities = City::where('state_id', $value)->orderBy('name')->get();
        $this->city_id = '';
    }

    protected $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email',
        'phone'    => 'required|min:10|max:15',
        'state_id' => 'required',
        'city_id'  => 'required',
    ];

    /**
     * Step 1: validate form + create a Razorpay order.
     */
    public function save()
    {
        $this->validate();

        $api = new Api(config('razorpay.key'), config('razorpay.secret'));

        $order = $api->order->create([
            'receipt'         => 'demo_' . auth()->id() . '_' . time(),
            'amount'          => $this->amount * 100, // paise
            'currency'        => 'INR',
            'payment_capture' => 1,
            'notes'           => [
                'user_id' => auth()->id(),
                'name'    => $this->name,
                'email'   => $this->email,
            ],
        ]);

        Log::info('Razorpay order created', ['order' => $order->toArray()]);
        $this->razorpayOrderId = $order['id'];

        $this->dispatch('razorpay-checkout-open', [
            'key'         => config('razorpay.key'),
            'amount'      => $this->amount * 100,
            'currency'    => 'INR',
            'order_id'    => $this->razorpayOrderId,
            'name'        => 'Live Skills Training Program',
            'description' => 'One-Time Enrollment Fee',
            'prefill'     => [
                'name'    => $this->name,
                'email'   => $this->email,
                'contact' => $this->phone,
            ],
        ]);
    }

    /**
     * Step 2: called from JS after successful checkout.
     * Verifies signature server-side before granting access.
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
            Log::warning('Razorpay signature verification failed: ' . $e->getMessage());
            $this->addError('payment', 'Payment verification failed. Please try again or contact support.');
            return;
        }

        $payment = Payment::create([
            'user_id'             => auth()->id(),
            'name'                => $this->name,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'state_id'            => $this->state_id,
            'city_id'             => $this->city_id,
            'country'             => 'India',

            'amount'              => $this->amount,
            'paid_amount'         => $this->amount,

            'gateway'             => 'Razorpay',

            'razorpay_order_id'   => $attributes['razorpay_order_id'],
            'razorpay_payment_id' => $attributes['razorpay_payment_id'],

            'order_id'            => $attributes['razorpay_order_id'],
            'payment_id'          => $attributes['razorpay_payment_id'],
            'transaction_id'      => $attributes['razorpay_payment_id'],

            'invoice_no'          => 'INV-' . now()->format('YmdHis') . '-' . auth()->id(),

            'status'              => 'success',
            'coupon_code'         => null,
            'source'              => 'Website',

            'notes'               => json_encode([
                'payment_gateway' => 'Razorpay',
                'currency'        => 'INR',
                'user_agent'      => request()->userAgent(),
                'ip'              => request()->ip(),
            ]),

            'paid_at'             => now(),
        ]);

        // NEW: capture the payment id for the invoice download link
        $this->paymentId = $payment->id;

        DemoTypeSelection::where('demo_user_id', auth()->id())->update([
            'is_confirm' => 2,
            'status'     => 'completed',
        ]);

        $user  = User::findOrFail(auth()->id());
        $token = Str::uuid();

        DemoAccessToken::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addDays(3),
        ]);

        $url = route('demo.secure.login', $token);

        Mail::to($user->email)->send(new DemoLoginCredentialsMail($user, $url));

        $this->showSuccess = true;
        $this->dispatch('payment-success');
    }

    public function paymentFailed($error = null)
    {
        Log::info('Razorpay checkout dismissed/failed', ['error' => $error]);
        $this->addError('payment', 'Payment was not completed. Please try again.');
    }

    public function render()
    {
        return view('livewire.payment-form');
    }
}