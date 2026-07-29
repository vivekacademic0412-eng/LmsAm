{{-- resources/views/emails/daily-registration-summary.blade.php --}}
@extends('emails.layout')

@section('title', 'Daily Student Registration Summary - ' . now()->format('d M Y'))

@section('body')

<table width="650" align="center" cellpadding="0" cellspacing="0"
    style="background:#ffffff;
           border:1px solid #dbe4f0;
           border-top:6px solid #F58220;
           border-radius:12px;
           padding:35px;
           font-family:Arial,Helvetica,sans-serif;">

    <tr>
        <td>

            <!-- Heading -->
            <h2 style="
                margin:0 0 20px;
                color:#0B3D91;
                font-size:28px;
                font-weight:700;
                border-left:6px solid #F58220;
                padding-left:15px;">
                Daily Student Registration Summary
            </h2>

           
            <p style="
                margin:0 0 20px;
                font-size:15px;
                line-height:26px;
                color:#4B5563;">
                Please find attached today's
                <strong style="color:#0B3D91;">Student Registration Summary Report</strong>
                for your review.
                The attached Excel file contains the complete details of students registered today,
                including their contact information, course details, registration source, payment
                status, and registration date &amp; time.
            </p>

            <!-- Summary Table -->
            <table width="100%" cellpadding="12" cellspacing="0"
                style="
                border-collapse:collapse;
                border:1px solid #D9E2EC;
                margin:25px 0;
                border-radius:8px;">

                <tr style="background:#0B3D91;color:#ffffff;">
                    <th align="left"
                        style="font-size:15px;font-weight:600;">
                        Registration Summary
                    </th>

                    <th align="center"
                        style="font-size:15px;font-weight:600;">
                        Count
                    </th>
                </tr>

                <tr style="background:#ffffff;">
                    <td style="color:#374151;">
                        Total Students Registered Today
                    </td>

                    <td align="center">
                        <span style="
                            color:#F58220;
                            font-size:20px;
                            font-weight:bold;">
                            {{ $summary['total'] }}
                        </span>
                    </td>
                </tr>

                <tr style="background:#FFF4E8;">
                    <td style="color:#374151;">
                        Report Date
                    </td>

                    <td align="center"
                        style="color:#0B3D91;font-weight:600;">
                        {{ now()->format('d M Y') }}
                    </td>
                </tr>

            </table>

         
           
            <!-- Footer -->
            <table width="100%" cellpadding="0" cellspacing="0"
                style="
                margin-top:30px;
                border-top:1px solid #E5E7EB;
                padding-top:20px;">

                <tr>

                    <td>

                        <p style="
                            margin:0;
                            color:#374151;
                            font-size:15px;">
                            Thank you,
                        </p>

                        <p style="
                            margin:15px 0 0;
                            color:#0B3D91;
                            font-size:17px;
                            font-weight:bold;">
                            Academic Mantra Services
                        </p>

                        <p style="
                            margin:5px 0 0;
                            color:#F58220;
                            font-size:14px;
                            font-weight:600;">
                            LMS Development Team
                        </p>

                    </td>

                </tr>

            </table>

        </td>
    </tr>

</table>

@endsection