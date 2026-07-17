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

    

     private const PAID_DEMO_PRICE = 999.00;
 
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
 
        // ── Guard: demo already completed & approved → straight to demo details page ──
        $completedDemo = SubmittedDemos::where('user_id', auth()->id())
            // ->whereIn('status', ['approved', 'completed'])
            ->latest()
            ->first();
 
        if ($completedDemo) {
            return redirect()->route('demos');
        }
 
        // ── Guard: check existing demo type selection ────────────────────
        $existing = DemoTypeSelection::where('demo_user_id', auth()->id())->latest()->first();
 
        $freeDemoBlocked = false;
        $freeDemoMessage = null;
 
        if ($existing) {
 
            // Paid (online or QR) already completed → thank you page
            if (in_array($existing->demo_type, ['paid_online', 'paid_qr']) && $existing->status === 'completed') {
                $request->session()->put('demo_type_selection_id', $existing->id);
                $request->session()->put('demo_type', $existing->demo_type);
                return redirect()->route('lms.thankyou');
            }
 
            // Paid online payment still pending → resume the booking/payment step
            if ($existing->demo_type === 'paid_online' && $existing->status === 'pending') {
                $request->session()->put('demo_type_selection_id', $existing->id);
                $request->session()->put('demo_type', 'paid_online');
                return redirect()->route('lms.paid.booking');
            }
 
            // ── Free demo already-availed guard ──
            // Free is written as status = 'completed' the instant it's chosen
            // (see storeDemoType()). Any lingering 'pending' free row is treated
            // as stale/expired and closed out below rather than left dangling.
            if ($existing->demo_type === 'free') {
 
                if ($existing->status === 'completed') {
                    // Free demo fully availed → no second free demo, but paid is still open
                    $freeDemoBlocked = true;
                    $freeDemoMessage = 'Need faster access? If your Free Demo is taking a little longer to be scheduled due to high demand, you can book a Priority 1-on-1 Paid Demo for just ₹'
                        . number_format(self::PAID_DEMO_PRICE, 0)
                        . ' and connect with an expert at your preferred time.';
                } elseif ($existing->status === 'pending') {
                    // Stale pending free row — close it out and push to paid
                    $existing->update(['status' => 'expired']);
 
                    $freeDemoBlocked = true;
                    $freeDemoMessage = 'Need faster access? If your Free Demo is taking a little longer to be scheduled due to high demand, you can book a Priority 1-on-1 Paid Demo for just ₹'
                        . number_format(self::PAID_DEMO_PRICE, 0)
                        . ' and connect with an expert at your preferred time.';
                }
            }
        }
 
        return view('demo.lms.choose-type', [
            'currentStep'      => 0,
            'paidPrice'        => self::PAID_DEMO_PRICE,
            'existingQrStatus' => $existing?->status ?? 'pending',
            'existingType'     => $existing?->demo_type ?? null,
            'freeDemoBlocked'  => $freeDemoBlocked,
            'freeDemoMessage'  => $freeDemoMessage,
        ]);
    }
 
    public function storeDemoType(Request $request)
    {
        try {
            $request->validate([
                'demo_type' => ['required', 'in:paid_online,paid_qr,free'],
            ]);
 
            $demoType = $request->demo_type;
 
            // ── Guard: prevent double-payment ──────────────────
            // Only blocks when the user already has a PAID selection that is
            // already COMPLETED. A user coming from a completed FREE demo is
            // deliberately NOT blocked here — they're allowed to go pay ₹999.
            $existing = DemoTypeSelection::where('demo_user_id', auth()->id())->latest()->first();
 
            if (
                $existing
                && in_array($existing->demo_type, ['paid_online', 'paid_qr'])
                && $existing->status === 'completed'
            ) {
                $request->session()->put('demo_type_selection_id', $existing->id);
                $request->session()->put('demo_type', $existing->demo_type);
 
                return redirect()->route('lms.thankyou')
                    ->with('info', 'You have already completed your payment.');
            }
 
            // ── Resolve payment attributes ──────────────────────
            $amount = match ($demoType) {
                'paid_online', 'paid_qr' => self::PAID_DEMO_PRICE,
                default                  => null,
            };
 
            $paymentMethod = match ($demoType) {
                'paid_online' => 'online',
                'paid_qr'     => 'qr',
                default       => null,
            };
 
            // Free demo needs no confirmation step → completed the moment it's chosen.
            // Paid methods stay "pending" until payment is actually confirmed
            // (paid_online via the booking/payment form, paid_qr via the AJAX confirm).
            $status = match ($demoType) {
                'free'        => 'completed',
                'paid_online' => 'pending',
                'paid_qr'     => 'pending',
            };
 
            $selection = DemoTypeSelection::updateOrCreate(
                ['demo_user_id' => auth()->id()],
                [
                    'traffic_source_id' => session('traffic_source_id'),
                    'demo_type'      => $demoType,
                    'payment_method' => $paymentMethod,
                    'status'         => $status,
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
                'status'       => $status,
            ]);
 
            return match ($demoType) {
                // Always send to the payment form — whether this is a fresh
                // paid selection or a free-demo user upgrading to paid.
                'paid_online' => redirect()->route('lms.paid.booking'),
                // QR: handled via AJAX confirmQrPayment() — but if JS disabled, go to thank-you
                'paid_qr'     => redirect()->route('lms.thankyou'),
                // Free demo is complete the instant it's chosen
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
 
    public function thankyou(Request $request)
    {
        $selectionId = $request->session()->get('demo_type_selection_id');
 
        if (!$selectionId) {
            return redirect()->route('lms.choose-type');
        }
 
        $demoType = $request->session()->get('demo_type');
 
        if ($demoType === 'paid_online') {
            $selection = DemoTypeSelection::where('id', $selectionId)
                ->where('demo_user_id', auth()->id())
                ->first();
 
            if ($selection) {
                $selection->update([
                    'status' => 'completed',
                ]);
            }
        }
 
        return view('demo.lms.thankyou', [
            'demoType' => $demoType,
            'name'     => $request->session()->get('user_name'),
            'email'    => $request->session()->get('user_email'),
        ]);
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
    
}
