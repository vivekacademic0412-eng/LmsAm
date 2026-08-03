<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkAbsentAttendance extends Command
{
    protected $signature = 'attendance:mark-absent {--date= : Y-m-d, defaults to yesterday}';

    protected $description = 'Marks Absent for every student who never checked in on the given date.';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now()->subDay();

        $studentIds = User::where('role', User::ROLE_STUDENT)->pluck('id');

        $alreadyMarked = Attendance::whereDate('date', $date->toDateString())
            ->whereIn('user_id', $studentIds)
            ->pluck('user_id');

        $missing = $studentIds->diff($alreadyMarked);

        foreach ($missing as $userId) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $date->toDateString(),
                'status' => Attendance::STATUS_ABSENT,
                'approval_status' => Attendance::APPROVAL_APPROVED,
                'marked_by' => Attendance::MARKED_SYSTEM,
            ]);
        }

        $this->info("Marked {$missing->count()} student(s) absent for {$date->toDateString()}.");

        return self::SUCCESS;
    }
}