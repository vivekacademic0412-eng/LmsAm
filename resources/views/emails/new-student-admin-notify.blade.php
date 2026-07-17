{{-- resources/views/emails/new-student-registered.blade.php --}}
@extends('emails.layout')

@section('title', 'New Student Registration')

@php
    $preheader = 'A new student just registered: ' . $user->name . ' ' . $user->last_name;
    $heroLabel = 'New Registration';
    $heroTitle = 'A new student just signed up';
@endphp

@section('body')

    <p style="margin:0 0 16px 0;">Hi there,</p>

    <p style="margin:0 0 20px 0;">
        A new student account was just created on Academic Mantra LMS. Here are the details:
    </p>

    {{-- ── Details card ── --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;">
        <tr>
            <td style="padding:18px 20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;">
                    <tr>
                        <td style="padding:6px 0; color:#64748B; width:120px;">Name</td>
                        <td style="padding:6px 0; color:#0F172A; font-weight:600;">{{ $user->name }} {{ $user->last_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#64748B;">Email</td>
                        <td style="padding:6px 0; color:#0F172A; font-weight:600;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#64748B;">Contact</td>
                        <td style="padding:6px 0; color:#0F172A; font-weight:600;">{{ $user->contact }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#64748B;">Gender</td>
                        <td style="padding:6px 0; color:#0F172A; font-weight:600; text-transform:capitalize;">{{ $user->gender }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#64748B;">Registered</td>
                        <td style="padding:6px 0; color:#0F172A; font-weight:600;">{{ $user->created_at?->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:20px 0 0 0; font-size:13px; color:#64748B;">
        The student has been sent a verification link and will gain dashboard access once their email is confirmed.
    </p>

    {{-- ── CTA ── --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:26px 0 8px 0;">
        <tr>
            <td align="center">
                <a href="{{ route('login') }}"
                   style="display:inline-block; background-color:#1E3A8A; color:#FFFFFF; font-size:14px; font-weight:600; text-decoration:none; padding:12px 28px; border-radius:8px;">
                    Open Admin Panel
                </a>
            </td>
        </tr>
    </table>

@endsection
