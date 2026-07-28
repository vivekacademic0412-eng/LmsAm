<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentLinkController extends Controller
{
    //
    public function Checkout($token){
        return view('payments.checkout',compact('token'));
    }
    public function Success($token){
        return view('payments.success',compact('token'));

    }
}
