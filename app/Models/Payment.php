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
    'city',
    'state',
    'country',
    'amount',
    'paid_amount',
    'payment_id',
    'order_id',
    'transaction_id',
    'invoice_no',
    'gateway',
    'status',
    'coupon_code',
    'source',
    'notes',
    'paid_at'
];
}
