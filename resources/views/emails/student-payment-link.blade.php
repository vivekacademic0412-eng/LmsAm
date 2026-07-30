@extends('emails.layout-report')

@section('title', 'Complete Your Course Registration')

@section('body')

    <div style="text-align:center; margin-bottom:25px;">
        <h1 style="margin:0; color:#0B3D91; font-size:28px;">
            🎉 Welcome to {{ config('app.name') }}
        </h1>
        <p style="margin-top:10px; color:#64748B; font-size:16px;">
            Your student account has been created successfully.
        </p>
    </div>

    <p style="font-size:16px;">
        Hello <strong>{{ $student->name }}</strong>,
    </p>

    <p>
        Your account has been created by our admissions team. To activate your enrollment, please complete your course
        payment using the secure payment link below.
    </p>

    <table width="100%" cellpadding="12" cellspacing="0"
        style="background:#F8FAFC;border:1px solid #E5E7EB;border-radius:8px;margin:25px 0;">
        <tr>
            <td>
                <h3 style="margin-top:0;color:#0B3D91;">Student Login Details</h3>

                <p style="margin:8px 0;">
                    <strong>Email:</strong><br>
                    {{ $student->email }}
                </p>

                <p style="margin:8px 0;">
                    <strong>Temporary Password:</strong><br>
                    {{ $password }}
                </p>

                <p style="margin:8px 0;color:#DC2626;">
                    Please change your password after your first login.
                </p>
            </td>
        </tr>

    </table>

    <div style="text-align:center;margin:35px 0;">


        <h3 style="color:#0B3D91;margin-bottom:15px;">
            Complete Your Payment
        </h3>

        <a href="{{ $payment->publicUrl() }}"
            style="display:inline-block;background:#0B3D91;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:bold;font-size:16px;">
            Pay Now
        </a>


    </div>

    <div style="background:#FFF7ED;border-left:4px solid #F58220;padding:16px;margin-top:30px;">
        <strong>Payment Link</strong>


        <p style="margin-top:10px;word-break:break-all;">
            {{ $payment->publicUrl() }}
        </p>

        <p style="margin-bottom:0;font-size:14px;color:#64748B;">
            If the button above does not work, copy and paste this link into your browser.
        </p>


    </div>

    <hr style="margin:35px 0;border:none;border-top:1px solid #E5E7EB;">

    <h3 style="color:#0B3D91;">What's Next?</h3>

    <ul style="line-height:1.8;">
        <li>Complete your course payment.</li>
        <li>Log in to your student dashboard.</li>
        <li>Access your enrolled course after payment confirmation.</li>
        <li>Attend your scheduled classes and begin learning.</li>
    </ul>

    <p style="margin-top:30px;">
        If you have any questions or need assistance, simply reply to this email or contact our support team.
    </p>

    <p style="margin-top:35px;">
        Thank you for choosing <strong>{{ config('app.name') }}</strong>. We look forward to helping you achieve your
        learning goals.
    </p>

    <p style="margin-top:40px;">
        Regards,<br>
        <strong>{{ config('app.name') }}</strong><br>
        Admissions & Student Support Team
    </p>

@endsection
