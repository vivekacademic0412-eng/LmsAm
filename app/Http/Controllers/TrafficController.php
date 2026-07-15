<?php
// app/Http/Controllers/TrafficController.php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\DemoTypeSelection;
use App\Models\TrafficSource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SubmittedDemos; // add to imports
class TrafficController extends Controller
{
    // ──────────────────────────────────────────
    // PHASE 1 — Landing entry point
    // ──────────────────────────────────────────
    //
    // This is the very first route a visitor hits:
    //   https://demo.yoursite.com/?source=partner1
    //   https://demo.yoursite.com/?utm_source=facebook&utm_campaign=jan_promo
    //
    // It silently logs traffic attribution, then shows Phase 2's
    // demo-type chooser. No login/registration required — that
    // happens later in Phase 5.



    public function dashboardReport(Request $request)
    {
        $range = $request->query('range', 'today'); // today | week | month

        $query = TrafficSource::query();

        match ($range) {
            'week'  => $query->where('created_at', '>=', now()->subDays(7)),
            'month' => $query->where('created_at', '>=', now()->subDays(30)),
            default => $query->whereDate('created_at', today()),
        };

        $bySource = $query->selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                $temp = new TrafficSource(['source' => $row->source]);
                return [
                    'source' => $row->source,
                    'label'  => $temp->source_label,
                    'color'  => $temp->source_color,
                    'total'  => $row->total,
                ];
            });

        $totalVisitors = $bySource->sum('total');

        $byDevice = (clone $query)->selectRaw('device, COUNT(*) as total')
            ->groupBy('device')
            ->pluck('total', 'device');

        return response()->json([
            'range'           => $range,
            'total_visitors'  => $totalVisitors,
            'by_source'       => $bySource,
            'by_device'       => $byDevice,
        ]);
    }

    // public function chooseDemoType(Request $request)
    // {
    //     // ── Traffic attribution ─────────────────────────────────
    //     try {
    //         $attributes = TrafficSource::attributesFromRequest($request);
    //         $traffic    = TrafficSource::create($attributes);
    //         $request->session()->put('traffic_source_id', $traffic->id);

    //         Log::info('Traffic source captured', [
    //             'traffic_source_id' => $traffic->id,
    //             'source'            => $traffic->source,
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error('Traffic tracking failed', [
    //             'message' => $e->getMessage(),
    //             'file'    => $e->getFile(),
    //             'line'    => $e->getLine(),
    //         ]);
    //     }

    //     // ── Guard: check existing selection ────────────────────
    //     $existing = DemoTypeSelection::where('demo_user_id', auth()->user()->id)->latest()->first();

    //     if ($existing) {

    //         // Completed paid payment → hard block, go to thank-you
    //         if (in_array($existing->demo_type, ['paid_online', 'paid_qr']) && $existing->status === 'completed') {
    //             // Restore session keys so thank-you page works
    //             $request->session()->put('demo_type_selection_id', $existing->id);
    //             $request->session()->put('demo_type', $existing->demo_type);
    //             return redirect()->route('lms.thankyou');
    //         }

    //         // Online payment pending → send back to payment gateway
    //         if ($existing->demo_type === 'paid_online' && $existing->status === 'pending') {
    //             $request->session()->put('demo_type_selection_id', $existing->id);
    //             $request->session()->put('demo_type', 'paid_online');
    //             return redirect()->route('lms.paid.booking');
    //         }

    //         // QR pending OR free → allow re-access (fall through to show page)
    //     }

    //     return view('demo.lms.choose-type', [
    //         'currentStep' => 0,
    //         'paidPrice'   => 999.00,
    //         'existingQrStatus' => $existing?->status ?? 'pending',
    //         'existingType' => $existing?->demo_type ?? null,
    //     ]);
    // }


    public function chooseDemoType(Request $request)
    {
        // ── Traffic attribution ─────────────────────────────────
        try {
            $attributes = TrafficSource::attributesFromRequest($request);
            $traffic    = TrafficSource::create($attributes);
            $request->session()->put('traffic_source_id', $traffic->id);

            Log::info('Traffic source captured', [
                'traffic_source_id' => $traffic->id,
                'source'            => $traffic->source,
            ]);
        } catch (Exception $e) {
            Log::error('Traffic tracking failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }

        // ── NEW: Guard — demo already completed → go straight to demo details page ──
        $completedDemo = SubmittedDemos::where('user_id', auth()->user()->id)
            // ->whereIn('status', ['approved', 'completed'])
            ->latest()
            ->first();
        
        if ($completedDemo) {
            return redirect()->route('demos');
        }

        // ── Guard: check existing selection ────────────────────
        $existing = DemoTypeSelection::where('demo_user_id', auth()->user()->id)->latest()->first();

        if ($existing) {

            if (in_array($existing->demo_type, ['paid_online', 'paid_qr']) && $existing->status === 'completed') {
                $request->session()->put('demo_type_selection_id', $existing->id);
                $request->session()->put('demo_type', $existing->demo_type);
                return redirect()->route('lms.thankyou');
            }

            if ($existing->demo_type === 'paid_online' && $existing->status === 'pending') {
                $request->session()->put('demo_type_selection_id', $existing->id);
                $request->session()->put('demo_type', 'paid_online');
                return redirect()->route('lms.paid.booking');
            }
        }

        return view('demo.lms.choose-type', [
            'currentStep' => 0,
            'paidPrice'   => 999.00,
            'existingQrStatus' => $existing?->status ?? 'pending',
            'existingType' => $existing?->demo_type ?? null,
        ]);
    }
    /**
     * Store the chosen demo type (form POST).
     */
    public function storeDemoType(Request $request)
    {
        try {
            $request->validate([
                'demo_type' => ['required', 'in:paid_online,paid_qr,free'],
            ]);

            $demoType = $request->demo_type;

            // ── Guard: prevent double-payment ──────────────────
            $existing = DemoTypeSelection::where('demo_user_id', auth()->id())->latest()->first();
            if ($existing) {
                // Already completed a paid method → block
                if ($existing->demo_type === 'paid_online' && $existing->status === 'completed') {
                    // Payment already completed
                  
                } {
                    $request->session()->put('demo_type_selection_id', $existing->id);
                    $request->session()->put('demo_type', $existing->demo_type);
                    if($demoType =='paid_online'){
                       
                    }
                    return redirect()->route('lms.thankyou')
                        ->with('info', 'You have already completed your payment.');
                }
            }

            // ── Resolve payment attributes ──────────────────────
            $amount = match ($demoType) {
                'paid_online', 'paid_qr' => 999.00,
                default                  => null,
            };

            $paymentMethod = match ($demoType) {
                'paid_online' => 'online',
                'paid_qr'     => 'qr',
                default       => null,
            };

            // QR stays "pending" until user confirms via AJAX; free is instantly "completed"
            $paymentStatus = match ($demoType) {
                'free'        => 'pending',
                'paid_online' => 'pending',
                'paid_qr'     => 'pending',
            };

            $selection = DemoTypeSelection::updateOrCreate(
                ['demo_user_id' => auth()->id()],
                [
                    'traffic_source_id' => session('traffic_source_id'),
                    'demo_type'      => $demoType,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'amount'         => $amount,
                    'session_id'     => $request->session()->getId(),
                    'user_ip'        => $request->ip(),
                ]
            );

            $request->session()->put('demo_type_selection_id', $selection->id);
            $request->session()->put('demo_type', $demoType);

            Log::info('Demo type selected', [
                'selection_id' => $selection->id,
                'demo_type'    => $demoType,
            ]);

            return match ($demoType) {
                'paid_online' => redirect()->route('lms.paid.booking'),
                // QR: handled via AJAX confirmQrPayment() — but if JS disabled, go to thank-you
                'paid_qr'     => redirect()->route('lms.thankyou'),
                'free'        => redirect()->route('lms.thankyou'),
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('storeDemoType failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * AJAX: User ticks "I have paid" checkbox for QR flow.
     * Marks the QR selection as completed and returns JSON.
     */
    // public function confirmQrPayment(Request $request)
    // {
    //     try {
    //         $selection = DemoTypeSelection::where('demo_user_id', auth()->id())
    //             ->where('demo_type', 'paid_qr')
    //             ->latest()
    //             ->first();

    //         if (! $selection) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No pending QR payment found. Please start over.',
    //             ], 404);
    //         }

    //         if ($selection->payment_status === 'completed') {
    //             // Already confirmed — idempotent
    //             $request->session()->put('demo_type_selection_id', $selection->id);
    //             $request->session()->put('demo_type', 'paid_qr');
    //             return response()->json(['success' => true, 'already_confirmed' => true]);
    //         }

    //         $selection->update(['payment_status' => 'completed']);

    //         $request->session()->put('demo_type_selection_id', $selection->id);
    //         $request->session()->put('demo_type', 'paid_qr');

    //         Log::info('QR payment confirmed by user', [
    //             'selection_id' => $selection->id,
    //             'user_id'      => auth()->id(),
    //         ]);

    //         // TODO: Fire confirmation email here
    //         // Mail::to(auth()->user()->email)->send(new DemoConfirmationMail($selection));

    //         return response()->json(['success' => true]);

    //     } catch (Exception $e) {
    //         Log::error('confirmQrPayment failed', [
    //             'message' => $e->getMessage(),
    //             'file'    => $e->getFile(),
    //             'line'    => $e->getLine(),
    //         ]);
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Server error. Please try again.',
    //         ], 500);
    //     }
    // }
    public function confirmQrPayment(Request $request)
    {
        try {
            $selection = DemoTypeSelection::where('demo_user_id', auth()->id())
                ->where('demo_type', 'paid_qr')
                ->latest()
                ->first();

            if ($selection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your demo slot is reserved and a confirmation email is on its way. Your payment is already paid',
                ], 404);
            }

            $selection = DemoTypeSelection::firstOrCreate(
                [
                    'demo_user_id' => auth()->id(),
                    'demo_type'    => 'paid_qr',
                ],
                [
                    'status' => 'pending',
                    'payment_method' => 'qr',
                    'amount'         => 999.00,
                    'session_id'     => $request->session()->getId(),
                    'user_ip'        => $request->ip(),
                ]
            );

            if ($selection->status === 'completed') {

                $request->session()->put('demo_type_selection_id', $selection->id);
                $request->session()->put('demo_type', 'paid_qr');

                return response()->json([
                    'success' => true,
                    'already_confirmed' => true
                ]);
            }

            $selection->update([
                'status' => 'completed'
            ]);

            $request->session()->put('demo_type_selection_id', $selection->id);
            $request->session()->put('demo_type', 'paid_qr');

            return response()->json(['success' => true]);
        } catch (Exception $e) {

            Log::error('confirmQrPayment failed', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }
    /**
     * Generate and stream a simple PDF invoice for QR payments.
     * Requires a PDF package (e.g. barryvdh/laravel-dompdf).
     */
    public function downloadQrInvoice(Request $request)
    {
        $selection = DemoTypeSelection::where('demo_user_id', auth()->id())
            ->whereIn('demo_type', ['paid_qr', 'paid_online'])
            ->where('status', 'completed')
            ->latest()
            ->first();

        if (! $selection) {
            return redirect()->route('lms.choose-type')
                ->with('error', 'No confirmed payment found to generate invoice.');
        }

        $user = auth()->user();
        $data = [
            'invoice_number' => 'LMS-' . str_pad($selection->id, 6, '0', STR_PAD_LEFT),
            'date'           => $selection->updated_at->format('d M Y'),
            'user_name'      => $user->name,
            'user_email'     => $user->email,
            'amount'         => $selection->amount,
            'demo_type'      => $selection->demo_type,
            'payment_method' => $selection->payment_method,
        ];

        // Using barryvdh/laravel-dompdf:
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('demo.lms.invoice', $data);
        return $pdf->download('LearnPro-Invoice-' . $data['invoice_number'] . '.pdf');
    }

    /**
     * Thank-you page.
     */
    public function thankyou(Request $request)
    {
        $selectionId = $request->session()->get('demo_type_selection_id');
        if (! $selectionId) {
            return redirect()->route('lms.choose-type');
        }

        $demoType = $request->session()->get('demo_type');

        return view('demo.lms.thankyou', [
            'demoType' => $demoType,
            'name'     => $request->session()->get('user_name'),
            'email'    => $request->session()->get('user_email'),
        ]);
    }
}
