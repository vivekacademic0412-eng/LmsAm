<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
</head>

<body style="margin:0;padding:0;background:#f3f7fb;font-family:Arial,Helvetica,sans-serif;color:#334155;">

    <div
        style="display:none;font-size:1px;color:#f3f7fb;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
        {{ $preheader ?? '' }}
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f3f7fb" style="padding:40px 15px;">
        <tr>
            <td align="center">

                <table width="700" cellpadding="0" cellspacing="0" border="0"
                    style="width:700px;max-width:700px;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e5e7eb;box-shadow:0 12px 40px rgba(15,23,42,.08);">

                    <!-- HEADER -->
                    <tr>
                        <td style="background:#ffffff;padding:28px 35px;border-bottom:1px solid #edf2f7;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left" valign="middle">
                                        {{--
                                        LOGO FIX
                                        ---------
                                        1. Always set explicit width/height so the layout doesn't collapse
                                           even if the image is blocked.
                                        2. Use a CID-embedded image instead of a remote URL (see Mailable)
                                           — this is the #1 reason logos "don't show" in email clients,
                                           because Gmail/Outlook/Apple Mail block remote images by default
                                           until the user clicks "show images", or the host blocks hotlinking
                                           for non-browser user agents (which is common on shared hosting
                                           with mod_security/hotlink protection enabled).
                                        3. $logoUrl below falls back to the remote URL if no CID is passed,
                                           so this template works either way.
                                    --}}
                                        <img src="{{ $logoUrl ?? 'https://www.academicmantraservices.com/theme/images/logo.png' }}"
                                            alt="Academic Mantra Services" width="180" height="60"
                                            style="height:60px;width:auto;max-width:180px;display:block;border:0;outline:none;">
                                    </td>

                                    <td align="right">
                                        <div style="font-size:13px;color:#64748b;line-height:24px;">
                                            <strong style="font-size:22px;color:#0f172a;">
                                                Academic Mantra Services
                                            </strong>
                                            <br>
                                            Learning Management System
                                            <br>
                                            Live Industry Training | Certification | Placement Assistance
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- HERO -->
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#2563eb,#3b82f6);padding:50px 45px;text-align:center;">
                            @isset($heroLabel)
                                <div
                                    style="display:inline-block;background:#ffffff;color:#2563eb;font-size:12px;font-weight:bold;padding:8px 18px;border-radius:50px;letter-spacing:.5px;">
                                    {{ $heroLabel }}
                                </div>
                            @endisset

                            <h1
                                style="margin:22px 0 15px;font-size:34px;font-weight:bold;color:#ffffff;line-height:42px;">
                                {{ $heroTitle ?? '' }}
                            </h1>

                            @isset($heroDescription)
                                <p style="margin:0 auto;color:#dbeafe;font-size:17px;line-height:28px;max-width:540px;">
                                    {{ $heroDescription }}
                                </p>
                            @endisset
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:45px;font-size:16px;line-height:30px;color:#334155;">
                            @yield('body')
                        </td>
                    </tr>

                    <!-- CONTACT STRIP -->
                    {{--
                    BUG FIX: original strip had color:#ffffff (white text) on a
                    background of #eff6ff (near-white light blue) — the text was
                    technically rendering, it was just invisible. Switched to a
                    solid brand-blue background with white text so it actually
                    reads, and added icon spacing.
                --}}
                    <tr>
                        <td style="padding:22px 40px;background:#2563eb;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td
                                                    style="color:#ffffff !important;font-size:14px;padding:0 18px;white-space:nowrap;">
                                                    &#9742;&nbsp;<strong>+91 97794 55997</strong>
                                                </td>
                                                <td
                                                    style="color:#ffffff !important;font-size:14px;padding:0 18px;white-space:nowrap;">
                                                    &#9993;&nbsp;support@academicmantraservices.com
                                                </td>
                                                <td
                                                    style="color:#ffffff !important;font-size:14px;padding:0 18px;white-space:nowrap;">
                                                    &#127760;&nbsp;www.academicmantraservices.com
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#ffffff;padding:40px;">
                            <table width="100%">
                                <tr>
                                    <td style="font-size:14px;color:#64748b;line-height:28px;">
                                        <strong style="font-size:18px;color:#0f172a;">
                                            Academic Mantra Services
                                        </strong>
                                        <br><br>

                                        Empowering Students with AI-Integrated, Industry-Focused Live Training Programs,
                                        Professional Certifications and Career Development.

                                        <br><br>

                                        <strong style="color:#0f172a;">Registered Office</strong>
                                        <br>
                                        4th Floor, Phase C-126, Building II
                                        <br>
                                        Industrial Area, Phase 8
                                        <br>
                                        SAS Nagar (Mohali), Punjab – 160071

                                        <br><br>

                                        <strong>Phone:</strong>
                                        <a href="tel:+919779455997" style="color:#2563eb;text-decoration:none;">
                                            +91 97794 55997
                                        </a>

                                        <br>

                                        <strong>WhatsApp:</strong>
                                        <a href="https://wa.me/919779455997" style="color:#25D366;text-decoration:none;"
                                            target="_blank">
                                            Chat on WhatsApp
                                        </a>

                                        <br>

                                        <strong>Email:</strong>
                                        <a href="mailto:support@academicmantraservices.com"
                                            style="color:#2563eb;text-decoration:none;">
                                            support@academicmantraservices.com
                                        </a>

                                        <br>

                                        <strong>Website:</strong>
                                        <a href="https://www.academicmantraservices.com"
                                            style="color:#2563eb;text-decoration:none;" target="_blank">
                                            www.academicmantraservices.com
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding-top:30px;">
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding-right:18px;">
                                                    <a href="https://www.facebook.com/academicmantraservices"
                                                        target="_blank"
                                                        style="color:#2563eb;text-decoration:none;font-weight:600;">
                                                        Facebook
                                                    </a>
                                                </td>

                                                <td style="padding-right:18px;">
                                                    <a href="https://www.instagram.com/academicmantraservices"
                                                        target="_blank"
                                                        style="color:#2563eb;text-decoration:none;font-weight:600;">
                                                        Instagram
                                                    </a>
                                                </td>

                                                <td style="padding-right:18px;">
                                                    <a href="https://www.linkedin.com/company/academic-mantra-services"
                                                        target="_blank"
                                                        style="color:#2563eb;text-decoration:none;font-weight:600;">
                                                        LinkedIn
                                                    </a>
                                                </td>

                                                <td>
                                                    <a href="https://www.youtube.com/@AcademicMantraServices"
                                                        target="_blank"
                                                        style="color:#2563eb;text-decoration:none;font-weight:600;">
                                                        YouTube
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding-top:30px;border-top:1px solid #e2e8f0;">
                                        <table width="100%">
                                            <tr>
                                                <td style="font-size:13px;color:#94a3b8;line-height:24px;">
                                                    This is an automated email generated by the Academic Mantra Services
                                                    Learning Management System. Please do not reply to this email.
                                                    <br><br>
                                                    <a href="https://www.academicmantraservices.com/terms-and-conditions"
                                                        style="color:#2563eb;text-decoration:none;">Terms &
                                                        Conditions</a>
                                                    &nbsp;&nbsp;|&nbsp;&nbsp;
                                                    <a href="https://www.academicmantraservices.com/privacy-policy"
                                                        style="color:#2563eb;text-decoration:none;">Privacy Policy</a>
                                                    &nbsp;&nbsp;|&nbsp;&nbsp;
                                                    <a href="https://www.academicmantraservices.com/contact-us"
                                                        style="color:#2563eb;text-decoration:none;">Contact Us</a>
                                                    <br><br>
                                                    © {{ date('Y') }} Academic Mantra Services. All Rights Reserved.
                                                </td>
                                                <td align="right" valign="top">
                                                    <img src="{{ $logoUrl ?? 'https://www.academicmantraservices.com/theme/images/logo.png' }}"
                                                        alt="Academic Mantra Services" width="42" height="42"
                                                        style="height:42px;width:auto;opacity:.7;">
                                                </td>
                                            </tr>
                                        </table>
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
