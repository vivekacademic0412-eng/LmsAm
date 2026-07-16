<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
     public function download(Payment $payment)
    {
        $isOwner = $payment->user_id === auth()->id();
        $isAdmin = auth()->user()?->role === 'admin';

        abort_unless($isOwner || $isAdmin, 403);

        $pdf = Pdf::loadView('invoices.pdf', ['payment' => $payment]);

        return $pdf->download($payment->invoice_no . '.pdf');
    }
    
     public function downloadCourseInvoice(Payment $payment)
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        $courseIds = json_decode($payment->notes, true)['course_ids'] ?? [];
        $courses   = Course::whereIn('id', $courseIds)->get();

        $pdf = Pdf::loadView('invoices.course-invoice', compact('payment', 'courses'));

        return $pdf->download("invoice-{$payment->invoice_no}.pdf");
    }
}