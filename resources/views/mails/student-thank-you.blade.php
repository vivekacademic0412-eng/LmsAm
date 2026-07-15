<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #eaf2ff; margin: 0; padding: 0; }
        .wrap { max-width: 560px; margin: 0 auto; padding: 30px 20px; }
        .card { background: #ffffff; border-radius: 16px; padding: 36px 32px; box-shadow: 0 10px 30px rgba(13,93,209,.08); }
        .brand { font-size: 13px; font-weight: 700; color: #7a5cff; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 6px; }
        h1 { font-size: 22px; color: #0e1f36; margin: 0 0 16px; }
        p { font-size: 14px; color: #5a718a; line-height: 1.7; margin: 0 0 14px; }
        .btn { display: inline-block; background: #0947a8; color: #fff !important; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; color: #91a7c5; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="brand">Academic Mantra Services</div>
            <h1>Welcome aboard, {{ $user->name }}!</h1>
            <p>Thank you for registering with us. We're excited to have you start your live skills training journey.</p>
            <p>Please verify your email address to activate your account fully and unlock all features.</p>
            <p>Our team will be in touch shortly with your next steps. In the meantime, feel free to explore the courses available on your dashboard.</p>
            <a href="{{ route('dashboard') }}" class="btn">Go to Dashboard</a>
        </div>
        <div class="footer">© {{ date('Y') }} Academic Mantra Services. All rights reserved.</div>
    </div>
</body>
</html>