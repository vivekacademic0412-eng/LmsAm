{{-- resources/views/emails/daily-registration-summary.blade.php --}}
@extends('emails.layout')

@section('title', 'Daily Registration Summary')

@php
    $preheader = "{$count} new student(s) registered on {$forDate}.";
    $heroLabel = 'ADMIN DAILY DIGEST';
    $heroTitle = $count . ' New Registration' . ($count === 1 ? '' : 's') . ' Today';
@endphp

@section('body')

<p style="margin:0 0 18px;font-size:16px;color:#334155;">
    Hi Admin,
</p>

<p style="margin:0 0 28px;font-size:15px;line-height:28px;color:#475569;">
    Here's a summary of student registrations on <strong>Academic Mantra LMS</strong> for
    <strong>{{ $forDate }}</strong>.
</p>

@if ($count === 0)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;">
        <tr>
            <td style="padding:22px;font-size:15px;color:#64748B;text-align:center;">
                No new student registrations today.
            </td>
        </tr>
    </table>
@else
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;margin-bottom:10px;">
        <thead>
            <tr>
                <td style="padding:10px 12px;background:#0e1f5c;color:#ffffff;font-size:12px;font-weight:bold;border-radius:8px 0 0 0;">Name</td>
                <td style="padding:10px 12px;background:#0e1f5c;color:#ffffff;font-size:12px;font-weight:bold;">Email</td>
                <td style="padding:10px 12px;background:#0e1f5c;color:#ffffff;font-size:12px;font-weight:bold;">Contact</td>
                <td style="padding:10px 12px;background:#0e1f5c;color:#ffffff;font-size:12px;font-weight:bold;border-radius:0 8px 0 0;">Registered At</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr style="background:{{ $loop->even ? '#F8FAFC' : '#ffffff' }};">
                    <td style="padding:10px 12px;font-size:13px;color:#334155;border-bottom:1px solid #E2E8F0;">
                        {{ $student->name }} {{ $student->last_name }}
                    </td>
                    <td style="padding:10px 12px;font-size:13px;color:#334155;border-bottom:1px solid #E2E8F0;">
                        {{ $student->email }}
                    </td>
                    <td style="padding:10px 12px;font-size:13px;color:#334155;border-bottom:1px solid #E2E8F0;">
                        {{ $student->contact }}
                    </td>
                    <td style="padding:10px 12px;font-size:13px;color:#334155;border-bottom:1px solid #E2E8F0;">
                        {{ $student->created_at->format('g:i A') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:35px 0;">
    <tr>
        <td align="center">
            <a href="{{ route('login') }}"
               style="display:inline-block;
                      background:#0e1f5c;
                      color:#ffffff;
                      font-size:15px;
                      font-weight:bold;
                      text-decoration:none;
                      padding:14px 32px;
                      border-radius:999px;">
                Open Admin Panel
            </a>
        </td>
    </tr>
</table>

<p style="margin-top:10px;font-size:13px;color:#94A3B8;">
    This is an automated daily digest sent once per day. Individual registration notifications
    have been consolidated into this summary to keep your inbox clean.
</p>

@endsection
