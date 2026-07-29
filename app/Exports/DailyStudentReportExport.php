<?php

namespace App\Exports;

use App\Models\Lead;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DailyStudentReportExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    public function __construct(
        protected Carbon $from,
        protected Carbon $to
    ) {
    }

    /**
     * FromQuery + WithChunkReading (instead of FromCollection->get()) so the
     * package reads the result set in batches rather than loading everything
     * into memory at once — this is what guarantees no rows get silently
     * dropped on days with a large number of registrations.
     */
    public function query()
    {
        return Lead::query()
            ->with('trafficSource') // correct relation name — was 'trafficSources'
            ->whereBetween('created_at', [$this->from, $this->to])
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Email',
            'Phone',
            'Lead Type',
            'Company',
            'Designation',
            'Message',
            'Traffic Source',
            'Email Verified',
            'Status',
            'Registered At',
        ];
    }

    /**
     * One-to-one with headings() above — this is what was missing before
     * (collection() returned 7 fields against 8 headings, shifting every
     * column sideways in the sheet).
     */
    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->lead_type,
            $lead->company,
            $lead->designation,
            $lead->message,
            optional($lead->trafficSource)->source_label ?? 'Direct / Unknown',
            $lead->email_verified_at ? 'Yes' : 'No',
            $lead->status,
            $lead->created_at->format('d-m-Y H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}