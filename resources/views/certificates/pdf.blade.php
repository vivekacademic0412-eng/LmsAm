<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        body {
            font-family: 'DejaVu Serif', serif;
            margin: 0;
            padding: 0;
        }

        /* ── Outer gradient-look frame ── */
        .cert-outer {
            width: 100%;
            height: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #1a3a6e 0%, #4a6fa5 20%, #e8a94a 45%, #d4691e 55%, #1a3a6e 75%, #0d1f3c 100%);
        }

        .cert-inner {
            background: #e9edf3;
            border: 2px solid #0d1f3c;
            padding: 50px 60px;
            height: 100%;
            box-sizing: border-box;
            text-align: center;
        }

        /* ── Logos row ── */
        .cert-logos {
            width: 100%;
            margin-bottom: 25px;
        }
        .cert-logos table { width: 100%; border-collapse: collapse; }
        .cert-logos td { width: 50%; text-align: center; vertical-align: middle; }
        .cert-logos img { max-height: 55px; }

        /* ── Title ── */
        .cert-title {
            font-size: 46px;
            letter-spacing: 6px;
            color: #16233d;
            font-weight: bold;
            margin: 0;
        }
        .cert-subtitle {
            font-size: 16px;
            letter-spacing: 5px;
            color: #16233d;
            margin: 6px 0 0;
        }
        .cert-divider {
            width: 90px;
            border-top: 1.5px solid #16233d;
            margin: 14px auto 30px;
        }

        /* ── Certify line ── */
        .cert-certify {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #16233d;
            margin-bottom: 8px;
        }
        .cert-name-line {
            width: 400px;
            margin: 10px auto 6px;
            border-bottom: 1px solid #16233d;
            font-size: 26px;
            font-style: italic;
            color: #16233d;
            padding-bottom: 6px;
        }

        /* ── Body paragraph ── */
        .cert-body {
            font-size: 14px;
            font-style: italic;
            color: #16233d;
            line-height: 2;
            margin: 30px auto 10px;
            width: 460px;
        }
        .cert-blank {
            display: inline-block;
            border-bottom: 1px solid #16233d;
            min-width: 140px;
            padding: 0 4px;
            font-style: normal;
            font-weight: bold;
        }

        /* ── Ribbon / Grade seal ── */
        .cert-ribbon-wrap {
            margin: 40px auto 30px;
            width: 140px;
            position: relative;
            margin-top:5px;
        }
        .cert-ribbon-tails {
            width: 140px;
            height: 40px;
        }
        .cert-tail-left, .cert-tail-right {
            display: inline-block;
            width: 0;
            height: 0;
            border-left: 22px solid transparent;
            border-right: 22px solid transparent;
            border-top: 55px solid #16233d;
        }
        .cert-tail-left  { margin-right: 40px; }

        .cert-seal {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #f4f1e9;
            border: 6px solid #16233d;
            margin: -60px auto 0;
            text-align: center;
            line-height: 108px;
            font-weight: bold;
            font-size: 15px;
            color: #16233d;
            box-shadow: 0 0 0 3px #f4f1e9, 0 0 0 4px #16233d;
        }

        /* ── Footer: Date / Signature ── */
        .cert-footer {
            margin-top: 50px;
            width: 100%;
        }
        .cert-footer table { width: 100%; border-collapse: collapse; }
        .cert-footer td {
            width: 50%;
            text-align: center;
            font-size: 12px;
            color: #16233d;
            padding-top: 6px;
        }
        .cert-line {
            width: 160px;
            border-top: 1px solid #16233d;
            margin: 0 auto 6px;
        }
    </style>
</head>
<body>
    <div class="cert-outer">
        <div class="cert-inner">

            {{-- ── Logos ── --}}
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

            {{-- ── Title ── --}}
            <h1 class="cert-title">CERTIFICATE</h1>
            <p class="cert-subtitle">OF COMPLETION</p>
            <div class="cert-divider"></div>

            {{-- ── Certify ── --}}
            <p class="cert-certify">THIS IS TO CERTIFY THAT</p>
            <div class="cert-name-line">
                {{ $demo->demoUser->name ?? $demo->user->name ?? 'Student Name' }}
            </div>

            {{-- ── Body ── --}}
            <div class="cert-body">
                Has completed
                <span class="cert-blank">{{ $duration ?? '3 months' }}</span>
                hours/months/years with us.
                We thank you for showing your trust and being a valued member
                while showing exemplary performance. He/She has acquired
                experience in the
                <span class="cert-blank">{{ $demo->course->title ?? 'Department' }}</span>
                Department.
            </div>

            {{-- ── Ribbon ── --}}
            <div class="cert-ribbon-wrap">
                <div class="cert-seal">
                    {{ $demo->score ?? 'Grade' }}
                </div>
                <div class="cert-ribbon-tails">
                    <span class="cert-tail-left"></span><span class="cert-tail-right"></span>
                </div>
            </div>

            {{-- ── Footer ── --}}
            <div class="cert-footer">
                <table>
                    <tr>
                        <td>
                            <div class="cert-line"></div>
                            {{ optional($demo->updated_at)->format('d M Y') }}<br>
                            Date
                        </td>
                        <td>
                            <div class="cert-line"></div>
                            Signature
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</body>
</html>