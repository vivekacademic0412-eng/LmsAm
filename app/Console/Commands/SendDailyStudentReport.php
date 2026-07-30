<?php

namespace App\Console\Commands;

use App\Exports\DailyStudentReportExport;
use App\Mail\DailyStudentSummaryMail;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class SendDailyStudentReport extends Command
{
    /**
     * --date lets you re-run/backfill a specific day for testing
     * without waiting for the schedule, e.g.:
     *   php artisan report:daily-students --date=2026-07-28
     */
    protected $signature = 'report:daily-students {--date= : Report date (Y-m-d), defaults to yesterday}';

    protected $description = 'Send Daily Student Registration Report';

    protected const RECIPIENT = 'rajkeins@gmail.com';
    protected const CC = ['muktiacademicmantra@gmail.com', 'shikhakapoor558@gmail.com'];

    public function handle()
    {
        // Report on the full previous day, not "today so far". Running at
        // 9 AM against Carbon::today() only ever captured leads from
        // midnight-9AM and permanently missed everyone who signed up later
        // that day — this is the fix for "don't miss any lead".
        $reportDate = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::yesterday();

        $from = $reportDate->copy()->startOfDay();
        $to   = $reportDate->copy()->endOfDay();

        try {
            $leads = Lead::with('trafficSource') // was 'trafficSources' — didn't exist, threw
                ->whereBetween('created_at', [$from, $to])
                ->get();

            $summary = $this->buildSummary($leads);

            $fileName = 'daily_student_report_' . $reportDate->format('d-m-Y') . '.xlsx';

            Excel::store(
                new DailyStudentReportExport($from, $to),
                $fileName,
                'local'
            );

            if (! Storage::disk('local')->exists($fileName)) {
                throw new \RuntimeException("Export file was not created: {$fileName}");
            }

            $filePath = Storage::disk('local')->path($fileName);

            Mail::to(self::RECIPIENT)
                ->cc(self::CC)
                ->send(
                    (new DailyStudentSummaryMail($summary))->attach($filePath)
                );

            // Clean up the local copy now that it's been emailed, so these
            // don't quietly pile up on disk.
            Storage::disk('local')->delete($fileName);

            $this->info(sprintf(
                'Daily Student Report for %s sent successfully (%d leads).',
                $reportDate->format('d-m-Y'),
                $summary['total']
            ));
        } catch (Throwable $e) {
            Log::error('Daily student report failed', [
                'date'      => $reportDate->format('Y-m-d'),
                'message'   => $e->getMessage(),
                'exception' => $e,
            ]);

            $this->error('Daily Student Report failed: ' . $e->getMessage());

            // Make a failure visible instead of the report just silently
            // not arriving — this alone would have caught the original bug.
            try {
                Mail::raw(
                    "The daily student report for {$reportDate->format('d-m-Y')} failed to generate.\n\nError: {$e->getMessage()}",
                    fn ($message) => $message->to(self::RECIPIENT)->subject('Daily Student Report FAILED')
                );
            } catch (Throwable $mailException) {
                Log::error('Failed to send daily report failure alert: ' . $mailException->getMessage());
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Built dynamically from whatever lead_type / traffic source values
     * actually exist in the data (lab, campanion, contact-us, etc.) instead
     * of a hardcoded google/website/facebook/other switch that didn't match
     * any of your real lead_type values and ignored lead_type entirely.
     */
    protected function buildSummary(Collection $leads): array
    {
        $byType = $leads
            ->groupBy(fn ($lead) => $lead->lead_type ?: 'Unspecified')
            ->map->count()
            ->sortDesc();

        $bySource = $leads
            ->groupBy(fn ($lead) => optional($lead->trafficSource)->source_label ?? 'Direct / Unknown')
            ->map->count()
            ->sortDesc();

        $rows = $leads->map(fn ($lead) => [
            'id'         => $lead->id,
            'name'       => $lead->name,
            'email'      => $lead->email,
            'phone'      => $lead->phone,
            'lead_type'  => $lead->lead_type ?: '—',
            'source'     => optional($lead->trafficSource)->source_label ?? 'Direct / Unknown',
            'status'     => $lead->status ?: '—',
            'created_at' => $lead->created_at->format('d-m-Y h:i A'),
        ])->values();

        return [
            'total'     => $leads->count(),
            'by_type'   => $byType->toArray(),
            'by_source' => $bySource->toArray(),
            'leads'     => $rows->toArray(),
        ];
    }
}