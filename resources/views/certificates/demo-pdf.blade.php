
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: A4 portrait; }
        body { font-family: 'DejaVu Serif', serif; margin: 0; padding: 0; }

        .cert-outer {
            width: 100%; height: 100%; padding: 10px;
            background: linear-gradient(135deg, #1a3a6e 0%, #4a6fa5 20%, #e8a94a 45%, #d4691e 55%, #1a3a6e 75%, #0d1f3c 100%);
        }
        .cert-inner {
            background: #e9edf3; border: 2px solid #0d1f3c;
            padding: 50px 60px; height: 100%; box-sizing: border-box; text-align: center;
        }

        .cert-logos { width: 100%; margin-bottom: 20px; }
        .cert-logos table { width: 100%; border-collapse: collapse; }
        .cert-logos td { width: 50%; text-align: center; vertical-align: middle; }
        .cert-logos img { max-height: 55px; }

        .cert-badge-type {
            display: inline-block;
            font-size: 11px; letter-spacing: 3px; font-weight: bold;
            color: #d4691e; border: 1px solid #d4691e; border-radius: 20px;
            padding: 4px 16px; margin-bottom: 10px;
        }

        .cert-title { font-size: 46px; letter-spacing: 6px; color: #16233d; font-weight: bold; margin: 0; }
        .cert-subtitle { font-size: 16px; letter-spacing: 5px; color: #16233d; margin: 6px 0 0; }
        .cert-divider { width: 90px; border-top: 1.5px solid #16233d; margin: 14px auto 26px; }

        .cert-certify { font-size: 15px; font-weight: bold; letter-spacing: 2px; color: #16233d; margin-bottom: 8px; }
        .cert-name-line {
            width: 400px; margin: 10px auto 6px; border-bottom: 1px solid #16233d;
            font-size: 26px; font-style: italic; color: #16233d; padding-bottom: 6px;
        }

        .cert-body {
            font-size: 14px; font-style: italic; color: #16233d; line-height: 2;
            margin: 24px auto 6px; width: 480px;
        }
        .cert-blank {
            display: inline-block; border-bottom: 1px solid #16233d;
            min-width: 140px; padding: 0 4px; font-style: normal; font-weight: bold;
        }

        /* Subject % strip */
        .cert-stats {
            width: 360px; margin: 18px auto 8px;
            border: 1px solid #16233d; border-radius: 6px; padding: 8px 0;
        }
        .cert-stats table { width: 100%; border-collapse: collapse; }
        .cert-stats td { text-align: center; font-size: 12px; color: #16233d; }
        .cert-stats .stat-value { font-size: 20px; font-weight: bold; color: #d4691e; display: block; }

        .cert-ribbon-wrap { margin: 30px auto 30px; width: 140px; position: relative; margin-top: 5px; }
        .cert-ribbon-tails { width: 140px; height: 40px; }
        .cert-tail-left, .cert-tail-right {
            display: inline-block; width: 0; height: 0;
            border-left: 22px solid transparent; border-right: 22px solid transparent; border-top: 55px solid #16233d;
        }
        .cert-tail-left { margin-right: 40px; }
        .cert-seal {
            width: 120px; height: 120px; border-radius: 50%; background: #f4f1e9; border: 6px solid #16233d;
            margin: -60px auto 0; text-align: center; line-height: 108px; font-weight: bold; font-size: 15px;
            color: #16233d; box-shadow: 0 0 0 3px #f4f1e9, 0 0 0 4px #16233d;
        }

        .cert-footer { margin-top: 40px; width: 100%; }
        .cert-footer table { width: 100%; border-collapse: collapse; }
        .cert-footer td { width: 33.3%; text-align: center; font-size: 12px; color: #16233d; padding-top: 6px; }
        .cert-line { width: 150px; border-top: 1px solid #16233d; margin: 0 auto 6px; }
    </style>
</head>
<body>
    <div class="cert-outer">
        <div class="cert-inner">

            <div class="cert-logos">
                <table>
                    <tr>
                        <td>
                            @if(file_exists(public_path('images/job-suraksha-logo.png')))
                                <img src="{{ public_path('images/job-suraksha-logo.png') }}" alt="Logo">
                            @endif
                        </td>
                        <td>
                            @if(file_exists(public_path('images/academic-mantra-logo.png')))
                                <img src="{{ public_path('images/academic-mantra-logo.png') }}" alt="Logo">
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="cert-badge-type">DEMO CERTIFICATE — {{ strtoupper($isPaid ?? false ? 'PAID' : 'FREE') }}</div>

            <h1 class="cert-title">CERTIFICATE</h1>
            <p class="cert-subtitle">OF COMPLETION</p>
            <div class="cert-divider"></div>

            <p class="cert-certify">THIS IS TO CERTIFY THAT</p>
            <div class="cert-name-line">{{ $studentName ?? 'Student Name' }}</div>

            <div class="cert-body">
                Has successfully completed the demo session in
                <span class="cert-blank">{{ $subjectName ?? 'Subject' }}</span>
                and submitted the required demo task, reviewed and approved by our mentors.
                We thank you for showing your trust and exemplary performance.
            </div>

            <div class="cert-stats">
                <table>
                    <tr>
                        <td><span class="stat-value">{{ $completionPercent ?? '100' }}%</span>Demo Completion</td>
                        <td><span class="stat-value">{{ $grade ?? 'A' }}</span>Grade</td>
                        <td><span class="stat-value">{{ $certificateNumber ?? '—' }}</span>Certificate No.</td>
                    </tr>
                </table>
            </div>

            <div class="cert-ribbon-wrap">
                <div class="cert-seal">{{ $grade ?? 'PASS' }}</div>
                <div class="cert-ribbon-tails">
                    <span class="cert-tail-left"></span><span class="cert-tail-right"></span>
                </div>
            </div>

            <div class="cert-footer">
                <table>
                    <tr>
                        <td><div class="cert-line"></div>{{ $issuedAt ?? now()->format('d M Y') }}<br>Date</td>
                        <td><div class="cert-line"></div>Subject: {{ $subjectName ?? '—' }}</td>
                        <td><div class="cert-line"></div>Signature</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</body>
</html>
