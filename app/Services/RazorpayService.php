<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Minimal Razorpay wrapper using plain HTTP calls (no SDK dependency).
 * Add to config/services.php:
 *   'razorpay' => [
 *       'key'    => env('RAZORPAY_KEY'),
 *       'secret' => env('RAZORPAY_SECRET'),
 *   ],
 */
class RazorpayService
{
    protected string $baseUrl = 'https://api.razorpay.com/v1';

    public function createOrder(float $amountInRupees, string $receipt): array
    {
        $response = Http::withBasicAuth(config('razorpay.key'), config('razorpay.secret'))
            ->post("{$this->baseUrl}/orders", [
                'amount'   => (int) round($amountInRupees * 100), // paise
                'currency' => 'INR',
                'receipt'  => $receipt,
            ]);

        $response->throw();

        return $response->json();
    }

    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    {
        $expected = hash_hmac('sha256', "{$orderId}|{$paymentId}", config('razorpay.secret'));
        return hash_equals($expected, $signature);
    }
}