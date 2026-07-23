{{-- resources/views/emails/student-thank-you.blade.php --}}
@extends('emails.layout')

@section('title', 'Welcome to Academic Mantra LMS')

@php
    $preheader = 'Welcome to Academic Mantra LMS — verify your email and log in to get started.';
    $heroLabel = 'WELCOME TO ACADEMIC MANTRA LMS';
    $heroTitle = 'Welcome, ' . $user->name . '!';
@endphp

@section('body')

<p style="margin:0 0 18px;font-size:16px;color:#334155;">
    Dear <strong>{{ $user->name }}</strong>,
</p>

<p style="margin:0 0 18px;font-size:15px;line-height:28px;color:#475569;">
    Thank you for choosing <strong>Academic Mantra LMS</strong>.
    Your account has been created successfully, and we are delighted to welcome you to our learning community.
</p>

<p style="margin:0 0 25px;font-size:15px;line-height:28px;color:#475569;">
    At Academic Mantra LMS, you'll gain access to professional courses, industry-focused training, progress tracking, assignments, certifications, and many more resources designed to help you achieve your academic and career goals.
</p>

<!-- Verification Notice + CTA -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:8px;margin:30px 0;">
    <tr>
        <td style="padding:22px;">
            <div style="font-size:18px;font-weight:bold;color:#C2410C;margin-bottom:10px;">
                📧 Verify Your Email Address
            </div>
            <div style="font-size:15px;line-height:28px;color:#7C2D12;margin-bottom:18px;">
                Before you can log in, please confirm this is really your inbox. Click the button below to verify your email address — this link is valid for <strong>7 days</strong>.
            </div>
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" style="border-radius:8px;background:#EA580C;">
                        <a href="{{ $verificationUrl }}"
                           style="display:inline-block;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;padding:13px 30px;border-radius:8px;">
                            Verify Email Address
                        </a>
                    </td>
                </tr>
            </table>
            <div style="font-size:12px;line-height:20px;color:#9A3412;margin-top:14px;word-break:break-all;">
                Button not working? Copy and paste this link into your browser:<br>
                <a href="{{ $verificationUrl }}" style="color:#C2410C;">{{ $verificationUrl }}</a>
            </div>
        </td>
    </tr>
</table>

{{--
    LOGIN CREDENTIALS
    ------------------
    Security note: emailing a plaintext auto-generated password is common but
    not best practice. Consider instead sending a "Set your password" signed
    link (same pattern as $verificationUrl) so the password is never
    transmitted in plain text or sitting in an inbox. Kept here since the
    original flow generates and stores a random password — remove this block
    entirely if you switch to a set-password-link flow.
--}}
@isset($password)
{{-- <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;margin:0 0 30px;">
    <tr>
        <td style="padding:22px;">
            <div style="font-size:16px;font-weight:bold;color:#1D4ED8;margin-bottom:12px;">
                🔑 Your Login Details
            </div>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#1E3A8A;width:110px;">Email</td>
                    <td style="padding:6px 0;font-size:14px;color:#0F172A;font-weight:bold;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#1E3A8A;">Password</td>
                    <td style="padding:6px 0;font-size:14px;color:#0F172A;font-weight:bold;font-family:'Courier New',monospace;">{{ $password }}</td>
                </tr>
            </table>
            <div style="font-size:12px;line-height:20px;color:#1E40AF;margin-top:12px;">
                For your security, please log in and change this password as soon as possible.
            </div>
        </td>
    </tr>
</table> --}}
@endisset

<h3 style="margin:0 0 18px;color:#0F172A;font-size:20px;">
    What You'll Get
</h3>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="padding:10px 0;width:32px;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCFCE7;color:#16A34A;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Access to professional live and self-paced training programs.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCFCE7;color:#16A34A;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Interactive assignments, quizzes and practical learning materials.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCFCE7;color:#16A34A;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Track your learning progress from your personal dashboard.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCFCE7;color:#16A34A;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Download certificates after successful course completion.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCFCE7;color:#16A34A;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Receive support from our experienced academic and technical team.
        </td>
    </tr>
</table>

<!-- Login Button -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:40px 0;">
    <tr>
        <td align="center">
            <a href="{{ route('login') }}"
               style="display:inline-block;background:#2563EB;color:#ffffff;font-size:16px;font-weight:bold;text-decoration:none;padding:15px 36px;border-radius:8px;">
                Login to Academic Mantra LMS
            </a>
        </td>
    </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;">
    <tr>
        <td style="padding:20px;">
            <p style="margin:0 0 10px;font-size:15px;color:#0F172A;font-weight:bold;">
                Need Help?
            </p>
            <p style="margin:0;font-size:14px;line-height:26px;color:#64748B;">
                If you have any questions regarding your account, courses, or email verification, our support team is always ready to assist you.
            </p>
        </td>
    </tr>
</table>

<p style="margin:35px 0 0;font-size:15px;line-height:28px;color:#475569;">
    We appreciate your trust in Academic Mantra Services and look forward to being part of your learning journey.
</p>

<p style="margin:35px 0 0;font-size:15px;line-height:30px;color:#334155;">
    Warm Regards,
    <br><br>
    <strong>Academic Mantra Services Team</strong>
    <br>
    Empowering Students Through Professional Learning
</p>

<p style="margin-top:30px;font-size:13px;color:#94A3B8;">
    If you did not register for this account, please ignore this email. No further action is required.
</p>

@endsection