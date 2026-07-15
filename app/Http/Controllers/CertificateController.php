<?php

namespace App\Http\Controllers;

use App\Models\SubmittedDemos;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function download(SubmittedDemos $demo)
    {
        $isOwner = $demo->demo_user_id === auth()->id() || $demo->user_id === auth()->id();
        $isAdmin = auth()->user()?->role === 'admin';

        abort_unless($isOwner || $isAdmin, 403);

        abort_unless(
            in_array($demo->status, ['approved', 'completed']),
            403,
            'Certificate is not available until your demo is approved.'
        );

        $pdf = Pdf::loadView('certificates.pdf', [
            'demo'     => $demo->load(['course', 'demoUser', 'user']),
            'duration' => $demo->duration ?? '3 months', // adjust field name if you store this
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Certificate-' . ($demo->demoUser->name ?? $demo->user->name ?? $demo->id) . '.pdf');
    }
}