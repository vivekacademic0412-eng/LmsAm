<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyStudentReportExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return User::with([
                'studentProfile',
                'trafficSources',
                'payments',
                'programEnrollments.course'
            ])
            ->whereDate('created_at', Carbon::today())
            ->get()
            ->map(function ($user) {

                return [
                    'ID'               => $user->id,
                    'Student Name'     => $user->name,
                    'Email'            => $user->email,
                    'Phone'            => optional($user->studentProfile)->phone,
                    'Course'           => optional(optional($user->programEnrollments->first())->course)->name,
                    'Traffic Source'   => optional($user->trafficSources->first())->utm_source ?? 'Direct',
                    'Payment Status'   => optional($user->payments->first())->status ?? 'Pending',
                    'Registered At'    => $user->created_at->format('d-m-Y H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Email',
            'Phone',
            'Course',
            'Traffic Source',
            'Payment Status',
            'Registered At',
        ];
    }
}