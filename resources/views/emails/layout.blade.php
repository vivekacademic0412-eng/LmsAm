<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
</head>

<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#334155;">

<div style="display:none;font-size:1px;color:#f4f7fb;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    {{ $preheader ?? '' }}
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:30px 10px;">
    <tr>
        <td align="center">

            <table role="presentation" width="700" cellpadding="0" cellspacing="0" style="width:700px;max-width:700px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb;">

                <!-- Header -->
                <tr>
                    <td style="padding:25px 35px;background:#0f172a;">

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>

                                <!-- Logo -->
                                <td align="left" valign="middle" width="40%">
                                    <img src="{{ asset('theme/images/logo.png') }}"
                                         alt="Academic Mantra LMS"
                                         style="height:55px;display:block;">
                                </td>

                                <!-- Company -->
                                <td align="right" valign="middle" width="60%" style="font-size:13px;line-height:22px;color:#ffffff;">

                                    <strong style="font-size:18px;color:#ffffff;">
                                        Academic Mantra Services
                                    </strong>

                                    <br>

                                    LMS | Training | Certification

                                    <br>

                                    📧 support@academicmantraservices.com

                                    <br>

                                    🌐 www.academicmantraservices.com

                                </td>

                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Hero -->
                <tr>
                    <td style="background:linear-gradient(135deg,#2563eb,#1d4ed8);padding:40px;text-align:center;">

                        @isset($heroLabel)
                        <div style="display:inline-block;background:#ffffff;color:#2563eb;padding:6px 14px;border-radius:50px;font-size:12px;font-weight:bold;">
                            {{ $heroLabel }}
                        </div>
                        @endisset

                        <h1 style="margin:18px 0 0;font-size:30px;font-weight:bold;color:#ffffff;">
                            {{ $heroTitle ?? '' }}
                        </h1>

                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:40px;font-size:16px;line-height:28px;color:#334155;">

                        @yield('body')

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f8fafc;padding:30px;border-top:1px solid #e2e8f0;">

                        <table width="100%" cellpadding="0" cellspacing="0">

                            <tr>

                                <td style="font-size:13px;color:#64748b;line-height:22px;">

                                    <strong style="font-size:15px;color:#0f172a;">
                                        Academic Mantra Services
                                    </strong>

                                    <br>

                                    Empowering Students Through Quality Learning & Professional Training.

                                    <br><br>

                                    <strong>Registered Office</strong>

                                    <br>

                                    Academic Mantra Services

                                    <br>

                                    India

                                    <br>

                                    Email: support@academicmantraservices.com

                                    <br>

                                    Website:
                                    <a href="https://www.academicmantraservices.com"
                                       style="color:#2563eb;text-decoration:none;">
                                        www.academicmantraservices.com
                                    </a>

                                </td>

                            </tr>

                            <tr>
                                <td style="padding-top:25px;font-size:12px;color:#94a3b8;line-height:20px;">

                                    This email was sent automatically. Please do not reply to this message.

                                    <br><br>

                                    <a href="https://www.academicmantraservices.com/terms-and-conditions"
                                       style="color:#2563eb;text-decoration:none;">
                                        Terms & Conditions
                                    </a>

                                    &nbsp; | &nbsp;

                                    <a href="https://www.academicmantraservices.com/privacy-policy"
                                       style="color:#2563eb;text-decoration:none;">
                                        Privacy Policy
                                    </a>

                                    &nbsp; | &nbsp;

                                    <a href="https://www.academicmantraservices.com/contact-us"
                                       style="color:#2563eb;text-decoration:none;">
                                        Contact Us
                                    </a>

                                    <br><br>

                                    © {{ date('Y') }} Academic Mantra Services. All Rights Reserved.

                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>