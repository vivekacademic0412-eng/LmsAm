<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#eaf2ff;font-family:'Inter',system-ui,-apple-system,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0"
                       style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 20px 50px rgba(13,93,209,.08);">
                    <tr>
                        <td style="background:#0947a8;padding:24px 32px;">
                            <span style="color:#f0b35a;font-size:12px;letter-spacing:.08em;text-transform:uppercase;">Announcement</span>
                            <h1 style="color:#ffffff;font-size:20px;margin:6px 0 0;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;color:#0e1f36;font-size:15px;line-height:1.6;">
                            {!! nl2br(e($body)) !!}
                        </td>
                    </tr>
                    @if ($senderName)
                        <tr>
                            <td style="padding:0 32px 28px;color:#5a718a;font-size:13px;">
                                Sent by {{ $senderName }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</body>
</html>