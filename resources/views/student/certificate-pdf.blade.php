<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $certificate['course_title'] }} Certificate</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            background: #eef3f9;
            color: #102849;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        body {
            font-size: 14px;
        }

        .page-shell {
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            background: #eef3f9;
            padding: 7mm 8mm 5mm;
        }

        .certificate {
            background: #fffdf8;
            border: 4px solid #d4a33f;
            box-sizing: border-box;
            height: 188mm;
            padding: 4mm;
        }

        .certificate-inner {
            border: 2px solid #143f7f;
            box-sizing: border-box;
            height: 100%;
            padding: 22px 28px 18px;
            text-align: center;
        }

        .logo {
            margin-bottom: 10px;
        }

        .logo img {
            max-width: 180px;
            max-height: 60px;
            width: auto;
            height: auto;
        }

        .brand-fallback {
            display: inline-block;
            padding: 10px 18px;
            border: 1px solid #d8e2f1;
            background: #f4f7fc;
            color: #143f7f;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .brand-name {
            margin: 0 0 16px;
            color: #143f7f;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .doc-title {
            margin: 0 0 6px;
            color: #102849;
            font-size: 32px;
            font-family: DejaVu Serif, Georgia, serif;
            font-weight: bold;
        }

        .doc-kicker {
            margin: 0 0 8px;
            color: #7b8ea9;
            font-size: 14px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .doc-subtitle {
            margin: 0 0 14px;
            color: #5f7189;
            font-size: 17px;
        }

        .student-name {
            margin: 0 0 10px;
            color: #0e3f86;
            font-size: 36px;
            font-family: DejaVu Serif, Georgia, serif;
            font-weight: bold;
            line-height: 1.18;
        }

        .student-rule {
            width: 360px;
            height: 2px;
            margin: 0 auto 14px;
            background: #d6dfeb;
        }

        .completion-text {
            margin: 0 0 14px;
            color: #51657f;
            font-size: 19px;
        }

        .course-box {
            margin: 0 auto 18px;
            width: 80%;
            border: 2px solid #d9e3f1;
            background: #f7faff;
            padding: 12px 16px;
        }

        .course-title {
            margin: 0;
            color: #102849;
            font-size: 25px;
            font-weight: bold;
            line-height: 1.28;
        }

        .meta-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin: 0 0 18px;
        }

        .meta-table td {
            width: 33.33%;
        }

        .meta-card {
            border: 1px solid #d6e1f0;
            background: #f8fbff;
            padding: 12px 14px;
            text-align: left;
        }

        .meta-label {
            margin: 0 0 6px;
            color: #7a8da7;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .meta-value {
            margin: 0;
            color: #102849;
            font-size: 17px;
            font-weight: bold;
            line-height: 1.3;
        }

        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .bottom-left,
        .bottom-right {
            width: 36%;
            vertical-align: top;
            text-align: left;
        }

        .bottom-center {
            width: 28%;
            vertical-align: middle;
            text-align: center;
        }

        .bottom-right {
            text-align: right;
        }

        .bottom-heading {
            margin: 0 0 6px;
            color: #6a7d96;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .bottom-value {
            margin: 0 0 8px;
            color: #102849;
            font-size: 22px;
            font-family: DejaVu Serif, Georgia, serif;
            font-weight: bold;
        }

        .bottom-note {
            margin: 0;
            color: #60738c;
            font-size: 10px;
            line-height: 1.45;
        }

        .seal {
            width: 92px;
            height: 92px;
            margin: 0 auto;
            border: 4px solid #d4a33f;
            background: #fff8e8;
            color: #8a6112;
            text-align: center;
        }

        .seal-year {
            padding-top: 18px;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .seal-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
        }

        .seal-subtitle {
            font-size: 9px;
            margin-top: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            width: 220px;
            height: 2px;
            margin: 0 auto 8px;
            background: #bccadf;
        }

        .signature-label {
            margin: 0;
            color: #52657f;
            font-size: 14px;
        }

        .footer-note {
            margin: 0;
            color: #6a7d96;
            font-size: 10px;
            line-height: 1.5;
        }

        .pdf-footnote {
            margin-top: 2.5mm;
            text-align: center;
            font-size: 10px;
            color: #6d7f98;
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="certificate">
            <div class="certificate-inner">
                <div class="logo">
                    @if (!empty($certificate['brand_logo_data_uri']))
                        <img src="{{ $certificate['brand_logo_data_uri'] }}" alt="{{ $certificate['brand_name'] }}">
                    @else
                        <div class="brand-fallback">AMS</div>
                    @endif
                </div>

                <p class="brand-name">{{ strtoupper($certificate['brand_name']) }}</p>
                <p class="doc-kicker">Certificate of Achievement</p>
                <h1 class="doc-title">Certificate of Completion</h1>
                <p class="doc-subtitle">This certifies that</p>

                <p class="student-name">{{ $certificate['student_name'] }}</p>
                <div class="student-rule"></div>

                <p class="completion-text">has successfully completed the course</p>

                <div class="course-box">
                    <p class="course-title">{{ $certificate['course_title'] }}</p>
                </div>

                <table class="meta-table">
                    <tr>
                        <td>
                            <div class="meta-card">
                                <p class="meta-label">Specialization</p>
                                <p class="meta-value">{{ $certificate['category'] }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="meta-card">
                                <p class="meta-label">Guided Learning</p>
                                <p class="meta-value">{{ $certificate['hours_total'] }} Hours</p>
                            </div>
                        </td>
                        <td>
                            <div class="meta-card">
                                <p class="meta-label">Program Mentor</p>
                                <p class="meta-value">{{ $certificate['trainer_name'] }}</p>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="bottom-table">
                    <tr>
                        <td class="bottom-left">
                            <p class="bottom-heading">Issued On</p>
                            <p class="bottom-value">{{ $certificate['issued_at_full'] }}</p>
                            <p class="bottom-note">Completion date captured from final course progress.</p>
                        </td>
                        <td class="bottom-center">
                            <div class="seal">
                                <div class="seal-year">{{ $certificate['issued_at']->format('Y') }}</div>
                                <div class="seal-title">CERTIFIED</div>
                                <div class="seal-subtitle">Official</div>
                            </div>
                        </td>
                        <td class="bottom-right">
                            <p class="bottom-heading">Certificate Code</p>
                            <p class="bottom-value">{{ $certificate['certificate_code'] }}</p>
                            <p class="bottom-note">Verified certificate generated by {{ $certificate['brand_name'] }}.</p>
                        </td>
                    </tr>
                </table>

                <table class="signature-table">
                    <tr>
                        <td class="signature-cell">
                            <div class="signature-line"></div>
                            <p class="signature-label">Program Mentor</p>
                        </td>
                        <td class="signature-cell">
                            <div class="signature-line"></div>
                            <p class="signature-label">{{ $certificate['brand_name'] }} Authorized Signature</p>
                        </td>
                    </tr>
                </table>

                <p class="footer-note">
                    This certificate recognizes the successful completion of the listed course requirements under {{ $certificate['brand_name'] }}.
                </p>
            </div>
        </div>
        <div class="pdf-footnote">
            {{ $certificate['certificate_code'] }} | Generated by {{ $certificate['brand_name'] }}
        </div>
    </div>
</body>
</html>
