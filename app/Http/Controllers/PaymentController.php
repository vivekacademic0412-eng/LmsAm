<?php

namespace App\Http\Controllers;

use App\Models\DemoTypeSelection;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paidBooking()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $exists = DemoTypeSelection::where('demo_user_id', auth()->id())
            ->where('is_confirm', 2)
            ->exists();

        if ($exists) {
            return redirect()->route('dashboard');
        }

        return view('demo.lms.paid.payment', [
            'currentStep' => 0,
            'paidPrice'   => 99.00,
        ]);
    }
}
