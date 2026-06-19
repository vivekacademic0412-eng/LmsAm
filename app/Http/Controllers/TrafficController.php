<?php
// app/Http/Controllers/TrafficController.php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\DemoFeedback;
use App\Models\DemoTypeSelection;
use App\Models\TrafficSource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function Home(Request $request)
    {
        try {
            $attributes = TrafficSource::attributesFromRequest($request);

            $traffic = TrafficSource::create($attributes);

            $request->session()->put('traffic_source_id', $traffic->id);

            Log::info('Traffic source captured', [
                'traffic_source_id' => $traffic->id,
                'source' => $traffic->source,
            ]);
        } catch (Exception $e) {
            Log::error('Traffic tracking failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
        return auth()->check()
            ? view('demo.lms.choose-type', [
                'currentStep' => 0,
                'paidPrice'   => 999.00,
            ])
            : view('demo.lms.register', [
                'currentStep' => 0
            ]);
    }
    // ──────────────────────────────────────────
    // PHASE 2 — Demo type selection page
    // ──────────────────────────────────────────

    public function chooseDemoType(Request $request)
    {
        try {
            $attributes = TrafficSource::attributesFromRequest($request);
            $traffic    = TrafficSource::create($attributes);
            $request->session()->put('traffic_source_id', $traffic->id);

            Log::info('Traffic source captured', [
                'traffic_source_id' => $traffic->id,
                'source'            => $traffic->source,
            ]);
            $alredySubmit = DemoTypeSelection::where('demo_user_id', auth()->user()->id)->whereIn('demo_type', ['paid_qr', 'paid_online'])->first();
            if ($alredySubmit) {
               return redirect()->route('lms.thankyou');
            }
        } catch (Exception $e) {
            Log::error('Traffic tracking failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }

        return view('demo.lms.choose-type', [
            'currentStep' => 0,
            'paidPrice'   => 999.00, // ₹999 — single source of truth
            'submitDetails'  => $alredySubmit
        ]);
    }

    public function storeDemoType(Request $request)
    {
        try {
            $request->validate([
                'demo_type' => ['required', 'in:paid_online,paid_qr,free',],
            ]);



            $demoType = $request->demo_type;

            $amount = match ($demoType) {
                'paid_online', 'paid_qr' => 999.00,
                default => 0,
            };

            $paymentMethod = match ($demoType) {
                'paid_online' => 'online',
                'paid_qr'     => 'qr',
                default       => null,
            };

            $paymentStatus = match ($demoType) {
                'free'        => 'completed',
                'paid_online' => 'pending',
                'paid_qr'     => 'pending',
            };
            // Resolve amount
            $amount = match ($demoType) {
                'paid_online', 'paid_qr' => 999.00,
                default                  => null,
            };

            $selection = DemoTypeSelection::updateOrCreate(
                [
                    'demo_user_id' => auth()->id()
                ],
                [
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
                // Online card payment → payment gateway page
                'paid_online' => redirect()->route('lms.paid.booking'),

                // QR payment + Free → thank you page
                'paid_qr', 'free' => redirect()->route('lms.thankyou'),
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('storeDemoType failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()->with('error', 'Something went wrong. Please try again.' . $e->getMessage());
        }
    }

    public function thankyou(Request $request)
    {
        // Guard: only allow if a selection exists in session
        $selectionId = $request->session()->get('demo_type_selection_id');
        if (! $selectionId) {
            return redirect()->route('lms.choose-type');
        }

        $demoType = $request->session()->get('demo_type');

        return view('demo.lms.thankyou', [
            'demoType' => $demoType,
            'name'     => $request->session()->get('user_name'),   // set earlier if captured
            'email'    => $request->session()->get('user_email'),  // set earlier if captured
        ]);
    }
    // ──────────────────────────────────────────
    // ADMIN — Traffic dashboard data (Phase 1 reporting)
    // ──────────────────────────────────────────
    //
    // Powers the "Visitors Today: Facebook 120, Partner A 45..." report.
    // Kept here rather than a separate AdminController since it's tightly
    // coupled to TrafficSource — split out later if the admin panel grows.

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
}
