<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name', 'LMS') }}</title>
</head>
<body style="margin:0; padding:0; background:#eef4fb; font-family:Arial, Helvetica, sans-serif; color:#16324f;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef4fb; margin:0; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px; background:#ffffff; border-radius:18px; overflow:hidden; border:1px solid #d9e3f0;">
                    <tr>
                        <td style="padding:28px 32px; background:linear-gradient(135deg, #0f59c7, #2d7be4); color:#ffffff;">
                            @if (!empty($eyebrow))
                                <div style="font-size:12px; letter-spacing:1.2px; text-transform:uppercase; opacity:0.85; margin-bottom:10px;">{{ $eyebrow }}</div>
                            @endif
                            <div style="font-size:28px; line-height:1.2; font-weight:700;">{{ $title ?? config('app.name', 'LMS') }}</div>
                            @if (!empty($subtitle))
                                <div style="font-size:14px; line-height:1.7; opacity:0.92; margin-top:10px;">{{ $subtitle }}</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            @yield('content')
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px 28px; color:#617089; font-size:12px; line-height:1.7; border-top:1px solid #e4ebf5;">
                            <div>{{ config('app.name', 'LMS') }}</div>
                            @if (!empty($footerText))
                                <div style="margin-top:6px;">{{ $footerText }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
