
{{-- resources/views/emails/student-thank-you.blade.php --}}
@extends('emails.layout')

@section('title', 'Welcome to Academic Mantra LMS')

@php
    $preheader = 'Welcome to Academic Mantra LMS. Your account has been created successfully.';
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

<!-- Verification Notice -->

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:8px;margin:30px 0;">

    <tr>

        <td style="padding:22px;">

            <div style="font-size:18px;font-weight:bold;color:#C2410C;margin-bottom:10px;">
                📧 Verify Your Email Address
            </div>

            <div style="font-size:15px;line-height:28px;color:#7C2D12;">

                Before accessing your dashboard, please verify your email address by clicking the verification link sent to your inbox.

                <br><br>

                Once verified, your account will be fully activated and you'll be able to access all LMS features.

            </div>

        </td>

    </tr>

</table>

<h3 style="margin:35px 0 18px;color:#0F172A;font-size:20px;">
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
style="display:inline-block;
background:#2563EB;
color:#ffffff;
font-size:16px;
font-weight:bold;
text-decoration:none;
padding:15px 36px;
border-radius:8px;">

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

