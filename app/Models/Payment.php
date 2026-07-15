<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
   protected $fillable = [
    'user_id',
    'name',
    'email',
    'phone',
    'city_id',
    'state_id',
    'country',
    'amount',
    'paid_amount',
    'payment_id',
    'order_id',
    'transaction_id',
    'razorpay_order_id',
    'razorpay_payment_id',
    'invoice_no',
    'gateway',
    'status',
    'coupon_code',
    'source',
    'notes',
    'paid_at'
];
protected $casts = [
    'paid_at'    => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
}
