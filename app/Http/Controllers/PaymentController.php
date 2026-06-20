<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    function paidBooking(){
        return view('demo.lms.paid.payment',[
            'currentStep'   => 0,
            'paidPrice'     => 99.00, // ₹99 — surfaced here so the Blade
            // never hardcodes price in two places
        ]);
    }
}
