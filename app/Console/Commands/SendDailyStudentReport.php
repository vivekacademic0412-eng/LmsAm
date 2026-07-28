<?php

namespace App\Console\Commands;

use App\Exports\DailyStudentReportExport;
use App\Mail\DailyStudentSummaryMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class SendDailyStudentReport extends Command
{
    protected $signature = 'report:daily-students';

    protected $description = 'Send Daily Student Registration Report';

    public function handle()
    {
        $today = Carbon::today();

        // Get today's users with traffic source
        $users = User::with('trafficSources')
            ->whereDate('created_at', $today)
            ->get();

        // Count traffic sources
        $google = 0;
        $website = 0;
        $facebook = 0;
        $other = 0;

        foreach ($users as $user) {

            $source = strtolower(optional($user->trafficSources->first())->utm_source ?? 'other');

            switch ($source) {
                case 'google':
                    $google++;
                    break;

                case 'website':
                case 'direct':
                    $website++;
                    break;

                case 'facebook':
                    $facebook++;
                    break;

                default:
                    $other++;
                    break;
            }
        }

        $summary = [
            'google'  => $google,
            'website' => $website,
            'facebook' => $facebook,
            'other'   => $other,
            'total'   => $users->count(),
        ];

        // Generate Excel
        $fileName = 'daily_student_report_' . now()->format('d-m-Y') . '.xlsx';

        Excel::store(
            new DailyStudentReportExport(),
            $fileName,
            'local'
        );

        $file = Storage::disk('local')->path($fileName);



        // Mail::to('info.academicmantraservices@gmail.com')
        //     ->send(
        //         (new DailyStudentSummaryMail($summary))
        //             ->attach($file)
        //     );

Mail::to('rajkeins@gmail.com')
    ->cc('muktiacademicmantra@gmail.com')
    ->send(
        (new DailyStudentSummaryMail($summary))
            ->attach($file)
    );
        $this->info('Daily Student Report sent successfully.');
    }
}
