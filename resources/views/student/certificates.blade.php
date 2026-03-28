@extends('layouts.app')

@section('content')
    @php
        $latestIssuedAt = $certificates->max('issued_at');
        $latestIssuedLabel = $latestIssuedAt instanceof \Illuminate\Support\Carbon
            ? $latestIssuedAt->format('M d, Y')
            : 'Not yet';
        $totalHours = (int) $certificates->sum('hours_total');
        $brandLogo = data_get($certificates->first(), 'brand_logo_data_uri', '');
        if ($brandLogo === '' && is_file(public_path('images/logo.webp'))) {
            $brandLogo = asset('images/logo.webp');
        }
        $brandName = trim((string) data_get($certificates->first(), 'brand_name', config('app.name', '')));
        if ($brandName === '' || $brandName === 'Laravel') {
            $brandName = 'Academic Mantra Services';
        }
    @endphp

    <style>
        .cert-page {
            display: grid;
            gap: 16px;
        }
        .cert-hero {
            border: 1px solid #d7e4f8;
            border-radius: 16px;
            background: radial-gradient(circle at top right, rgba(84, 167, 255, 0.16), transparent 35%), linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            padding: 18px;
            display: grid;
            gap: 16px;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            box-shadow: 0 18px 34px rgba(15, 64, 140, 0.08);
        }
        .cert-hero h1 {
            margin: 0 0 6px;
            color: #102849;
            font-size: 30px;
        }
        .cert-hero p {
            margin: 0;
            color: #5a6b84;
            line-height: 1.7;
        }
        .cert-brand {
            justify-self: end;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 190px;
            min-height: 96px;
            padding: 16px 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(214, 225, 243, 0.95);
            box-shadow: 0 14px 24px rgba(17, 46, 96, 0.1);
        }
        .cert-brand img {
            display: block;
            max-width: 180px;
            max-height: 56px;
            width: auto;
            height: auto;
        }
        .cert-brand span {
            color: #102849;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
        }
        .cert-kpis {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .cert-kpi {
            border: 1px solid #d8e4f5;
            border-radius: 14px;
            background: #fff;
            padding: 16px;
            box-shadow: 0 10px 22px rgba(18, 42, 86, 0.05);
        }
        .cert-kpi span {
            display: block;
            color: #66758d;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .cert-kpi strong {
            display: block;
            margin-top: 8px;
            color: #102849;
            font-size: 26px;
            line-height: 1.1;
        }
        .cert-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        .cert-card {
            border: 1px solid #d7e4f8;
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            padding: 18px;
            display: grid;
            gap: 12px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(18, 42, 86, 0.06);
        }
        .cert-card::before {
            content: '';
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #0f4d9c, #52a4ff, #d4a33f);
        }
        .cert-card-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 10px;
        }
        .cert-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            background: #edf4ff;
            color: #1f56ad;
            border: 1px solid #d3e1f7;
        }
        .cert-code {
            color: #73829b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .cert-card h2 {
            margin: 0;
            color: #102849;
            font-size: 22px;
            line-height: 1.2;
        }
        .cert-meta {
            margin: 0;
            color: #5a6b84;
            font-size: 13px;
            line-height: 1.7;
        }
        .cert-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .cert-empty {
            border: 1px dashed #cad7ea;
            border-radius: 16px;
            background: #f9fbff;
            padding: 24px;
            color: #5a6b84;
            line-height: 1.7;
        }
        @media (max-width: 960px) {
            .cert-hero {
                grid-template-columns: 1fr;
            }
            .cert-brand {
                justify-self: start;
            }
            .cert-kpis {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="cert-page">
        <section class="cert-hero">
            <div>
                <h1>My Certificates</h1>
                <p>Completed courses unlock downloadable certificate files. Each certificate now uses your brand styling and logo and can be downloaded as both PDF and SVG.</p>
            </div>
            <div class="cert-brand" aria-label="{{ $brandName }}">
                @if ($brandLogo !== '')
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}">
                @else
                    <span>{{ $brandName }}</span>
                @endif
            </div>
        </section>

        <section class="cert-kpis">
            <article class="cert-kpi">
                <span>Certificates Earned</span>
                <strong>{{ $certificates->count() }}</strong>
            </article>
            <article class="cert-kpi">
                <span>Certified Hours</span>
                <strong>{{ $totalHours }}h</strong>
            </article>
            <article class="cert-kpi">
                <span>Latest Issue Date</span>
                <strong>{{ $latestIssuedLabel }}</strong>
            </article>
        </section>

        @if ($certificates->isNotEmpty())
            <section class="cert-grid">
                @foreach ($certificates as $certificate)
                    <article class="cert-card">
                        <div class="cert-card-top">
                            <span class="cert-chip">{{ $certificate['category'] }}</span>
                            <span class="cert-code">{{ $certificate['certificate_code'] }}</span>
                        </div>

                        <div>
                            <h2>{{ $certificate['course_title'] }}</h2>
                            <p class="cert-meta">
                                Issued {{ $certificate['issued_at_human'] }}
                                &middot; {{ $certificate['hours_total'] }}h of guided learning
                                &middot; Trainer: {{ $certificate['trainer_name'] }}
                            </p>
                        </div>

                        <div class="cert-actions">
                            <a class="btn btn-soft" href="{{ $certificate['course_route'] }}">Open Course</a>
                            <a class="btn" href="{{ $certificate['download_pdf_route'] }}">Download PDF</a>
                            <a class="btn btn-soft" href="{{ $certificate['download_svg_route'] }}">Download SVG</a>
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <section class="cert-empty">
                Complete an enrolled course to unlock your first certificate. When a course reaches 100% completion, both PDF and SVG downloads will appear here automatically.
            </section>
        @endif
    </div>
@endsection
