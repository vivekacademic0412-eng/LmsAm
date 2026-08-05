<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        'paid_at',

        // ── NEW fields ──
        'token',
        'type',            // demo | course
        'category_id',
        'course_id',
        'created_by',
        'receipt_pdf_path',
        'link_expires_at',
       
      
    ];

    protected $casts = [
        'paid_at'         => 'datetime',
        'link_expires_at' => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED  = 'failed';

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $payment->token ??= Str::random(40);
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(CourseCategory::class, 'category_id'); }
    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function publicUrl(): string
    {
        return route('payment.checkout', $this->token);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }
}