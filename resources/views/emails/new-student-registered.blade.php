
@extends('emails.layout')

@section('title', 'Welcome to Academic Mantra LMS — Verify Your Email')

@php
    $preheader = 'One quick step left — verify your email to activate your Academic Mantra LMS account.';
    $heroLabel = 'WELCOME TO ACADEMIC MANTRA LMS';
    $heroTitle = 'Welcome, ' . $user->name . '!';
@endphp

@section('body')

<p style="margin:0 0 18px;font-size:16px;color:#334155;">
    Dear <strong>{{ $user->name }}</strong>,
</p>

<p style="margin:0 0 18px;font-size:15px;line-height:28px;color:#475569;">
    Thank you for creating your <strong>Academic Mantra LMS</strong> account. We're delighted to
    welcome you into a community built around live, career-focused training and expert-led mentorship.
</p>

<p style="margin:0 0 25px;font-size:15px;line-height:28px;color:#475569;">
    Just one quick step stands between you and your dashboard — verifying your email address.
</p>

<!-- Verify CTA -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:10px 0 30px;">
    <tr>
        <td align="center">
            <a href="{{ $verificationUrl }}"
               style="display:inline-block;
                      background:#0e1f5c;
                      background-image:linear-gradient(135deg,#0e1f5c,#2952e3);
                      color:#ffffff;
                      font-size:16px;
                      font-weight:bold;
                      text-decoration:none;
                      padding:16px 40px;
                      border-radius:999px;
                      box-shadow:0 10px 24px rgba(14,31,92,.28);">
                Verify My Email Address
            </a>
        </td>
    </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:8px;margin:0 0 30px;">
    <tr>
        <td style="padding:18px 22px;font-size:13px;line-height:22px;color:#7C2D12;">
            This verification link expires in <strong>60 minutes</strong>. If the button above doesn't
            work, copy and paste this URL into your browser:
            <br>
            <a href="{{ $verificationUrl }}" style="color:#C2410C;word-break:break-all;">{{ $verificationUrl }}</a>
        </td>
    </tr>
</table>

<h3 style="margin:10px 0 18px;color:#0F172A;font-size:20px;">
    What You'll Get
</h3>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="padding:10px 0;width:32px;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCE3FF;color:#2952e3;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Access to professional live and self-paced training programs.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCE3FF;color:#2952e3;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Interactive assignments, quizzes and practical learning materials.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCE3FF;color:#2952e3;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Track your learning progress from your personal dashboard.
        </td>
    </tr>
    <tr>
        <td style="padding:10px 0;vertical-align:top;">
            <span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:#DCE3FF;color:#2952e3;font-size:13px;font-weight:bold;text-align:center;line-height:22px;">✓</span>
        </td>
        <td style="padding:10px 0;font-size:15px;color:#475569;">
            Download certificates after successful course completion.
        </td>
    </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;margin-top:30px;">
    <tr>
        <td style="padding:20px;">
            <p style="margin:0 0 10px;font-size:15px;color:#0F172A;font-weight:bold;">
                Need Help?
            </p>
            <p style="margin:0;font-size:14px;line-height:26px;color:#64748B;">
                If you have any questions regarding verification, your account, or courses, our support
                team is always ready to assist you.
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
    If you did not create this account, please ignore this email. No further action is required.
</p>

@endsection
