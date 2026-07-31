<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\RazorpayService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\EnrollmentService;

class PaymentVerifyController extends Controller
{
    public function __invoke(Request $request, RazorpayService $razorpay ,EnrollmentService $enrollmentService)
    {
        $validated = $request->validate([
            'token'                => 'required|string|exists:payments,token',
            'razorpay_order_id'    => 'required|string',
            'razorpay_payment_id'  => 'required|string',
            'razorpay_signature'   => 'required|string',
        ]);

        $payment = Payment::with(['user', 'category', 'course'])
            ->where('token', $validated['token'])
            ->firstOrFail();

        if ($payment->status === Payment::STATUS_SUCCESS) {
            // already processed (e.g. duplicate webhook) — just return success
            return response()->json([
                'success'      => true,
                'redirect_url' => route('payment.success', $payment->token),
            ]);
        }

        $valid = $razorpay->verifySignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature']
        );

        if (!$valid) {
            $payment->update(['status' => Payment::STATUS_FAILED]);
            return response()->json(['success' => false, 'message' => 'Signature verification failed'], 422);
        }

        // ── Update the SAME payment row (your existing table) ──
        $payment->update([
            'status'               => Payment::STATUS_SUCCESS,
            'paid_amount'          => $payment->amount,
            'payment_id'           => $validated['razorpay_payment_id'],
            'order_id'             => $validated['razorpay_order_id'],
            'transaction_id'       => $validated['razorpay_payment_id'],
            'razorpay_order_id'    => $validated['razorpay_order_id'],
            'razorpay_payment_id'  => $validated['razorpay_payment_id'],
            'invoice_no'           => $payment->invoice_no ?? ('INV-' . now()->format('Ym') . '-' . strtoupper(Str::random(6))),
            'paid_at'              => now(),
        ]);

        // ── Generate PDF receipt ──
        $pdf = Pdf::loadView('pdf.transaction-receipt', ['payment' => $payment->fresh(['user', 'category', 'course'])]);

        $path = "receipts/{$payment->invoice_no}.pdf";
        Storage::disk('public')->put($path, $pdf->output());
        $payment->update(['receipt_pdf_path' => $path]);



        // Enroll the student now that payment is confirmed
        $enrollmentService->enroll($payment->fresh(['user', 'course']));
        // TODO: enroll the student now that payment is confirmed —
        // e.g. app(EnrollmentService::class)->enroll($payment);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('payment.success', $payment->token),
        ]);
    }
}
