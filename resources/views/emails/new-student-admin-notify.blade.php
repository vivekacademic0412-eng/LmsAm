<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f4f8ff; margin: 0; padding: 0; }
        .wrap { max-width: 560px; margin: 0 auto; padding: 30px 20px; }
        .card { background: #fff; border-radius: 14px; padding: 30px; box-shadow: 0 8px 24px rgba(14,31,54,.08); }
        h2 { font-size: 18px; color: #0e1f36; margin: 0 0 18px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px 0; font-size: 13.5px; border-bottom: 1px solid #e2e8f0; }
        td.label { color: #5a718a; width: 130px; }
        td.value { color: #0e1f36; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h2>🎓 New Student Registered</h2>
            <table>
                <tr><td class="label">Name</td><td class="value">{{ $student->name }} {{ $student->last_name }}</td></tr>
                <tr><td class="label">Email</td><td class="value">{{ $student->email }}</td></tr>
                <tr><td class="label">Contact</td><td class="value">{{ $student->contact }}</td></tr>
                <tr><td class="label">Gender</td><td class="value">{{ ucfirst($student->gender) }}</td></tr>
                <tr><td class="label">Registered At</td><td class="value">{{ $student->created_at->format('d M Y, h:i A') }}</td></tr>
            </table>
        </div>
    </div>
</body>
</html>