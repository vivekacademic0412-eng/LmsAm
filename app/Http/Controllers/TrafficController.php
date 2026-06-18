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

    public function landing(Request $request)
    {
        $courses = Course::all();
        $categories = CourseCategory::all();
        $feedbacks = DemoFeedback::with(['user', 'course'])
            ->whereNotNull('message')
            ->latest()
            ->take(10)
            ->get();

        return view('demo.lms.landing', compact(
            'categories',
            'courses',
            'feedbacks'
        ) + [
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
        return view('demo.lms.choose-type', [
            'currentStep'   => 0,
            'paidPrice'     => 999.00, // ₹99 — surfaced here so the Blade
            // never hardcodes price in two places
        ]);
    }

    public function storeDemoType(Request $request)
    {
        try {
            $request->validate([
                'demo_type' => ['required', 'in:free,paid'],
            ]);

            $demoType = $request->demo_type;
            $amount   = $demoType === 'paid' ? 99.00 : null;

            $selection = DemoTypeSelection::create([
                'traffic_source_id' => $request->session()->get('traffic_source_id'),
                'session_id'        => $request->session()->getId(),
                'user_ip'           => $request->ip(),
                'demo_type'         => $demoType,
                'amount'            => $amount,
            ]);

            $request->session()->put('demo_type_selection_id', $selection->id);
            $request->session()->put('demo_type', $demoType);

            Log::info('Demo type selected', [
                'selection_id' => $selection->id,
                'demo_type'    => $demoType,
            ]);

            if ($demoType === 'paid') {
                // Phase 3 — paid booking form + payment QR
                return redirect()->route('lms.paid.booking');
            }

            // Free demo skips straight to Phase 5 (basic info form)
            return redirect()->back();
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
