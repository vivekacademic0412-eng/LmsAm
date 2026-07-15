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
}