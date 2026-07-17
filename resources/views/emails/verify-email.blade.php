@extends('emails.layout')

@section('title', 'Verify Your Email Address')

@php
    $preheader = 'Verify your email address to activate your Academic Mantra LMS account.';
    $heroLabel = 'ACCOUNT VERIFICATION';
    $heroTitle = 'Verify Your Email Address';
@endphp

@section('body')

<p style="margin:0 0 18px;font-size:16px;">
    Dear <strong>{{ $notifiable->name }}</strong>,
</p>

<p style="margin:0 0 18px;">
    Welcome to <strong>Academic Mantra LMS</strong>.
</p>

<p style="margin:0 0 18px;">
    Thank you for registering with us. To ensure the security of your account and complete your registration, please verify your email address by clicking the button below.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:35px 0;">
    <tr>
        <td align="center">

            <a href="{{ $url }}"
               style="display:inline-block;
                      background:#2563eb;
                      color:#ffffff;
                      text-decoration:none;
                      font-size:16px;
                      font-weight:bold;
                      padding:15px 38px;
                      border-radius:8px;">
                Verify Email Address
            </a>

        </td>
    </tr>
</table>

<div style="background:#f8fafc;padding:20px;border-left:4px solid #2563eb;border-radius:6px;">

    <strong style="color:#0f172a;">Important</strong>

    <ul style="margin:12px 0 0 18px;padding:0;color:#475569;line-height:26px;">
        <li>This verification link is valid for <strong>60 minutes</strong>.</li>
        <li>If you did not create this account, you can safely ignore this email.</li>
        <li>Your account will remain inactive until your email is verified.</li>
    </ul>

</div>

<p style="margin:30px 0 10px;">
    If the button above does not work, copy and paste the following link into your web browser:
</p>

<p style="word-break:break-word;">
    <a href="{{ $url }}" style="color:#2563eb;">
        {{ $url }}
    </a>
</p>

<p style="margin-top:35px;">
    If you have any questions or require assistance, our support team will be happy to help.
</p>

<p style="margin-top:35px;line-height:28px;">

Kind Regards,

<br>

<strong>Academic Mantra Services Team</strong>

<br>

Email: support@academicmantraservices.com

<br>

Website:
<a href="https://www.academicmantraservices.com"
   style="color:#2563eb;text-decoration:none;">
    www.academicmantraservices.com
</a>

</p>

@endsection