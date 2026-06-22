@extends('demo.layout')


@section('title', 'Explore More Courses')


@section('bitmoji-message', '🌟 You can be the next success story! Explore more courses and keep your momentum going!')
@section('bitmoji-emoji', '🚀')

@section('content')
<style>.completion-page{
    padding:40px;
}

.completion-top{
    text-align:center;
    margin-bottom:40px;
}

.success-icon{
    font-size:90px;
    color:var(--success);
    margin-bottom:20px;
}

.completion-top h1{
    font-size:42px;
    font-weight:800;
    color:var(--text-main);
}

.completion-top p{
    color:var(--text-muted);
}

.completion-badge{
    margin-top:20px;
}

.completion-badge span{
    background:linear-gradient(
        135deg,
        var(--success),
        #22c55e
    );

    color:#fff;
    padding:12px 24px;
    border-radius:50px;
    font-weight:700;
}

.completion-grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:25px;
}

.certificate{
    background:#fff;
    border-radius:30px;
    box-shadow:var(--shadow-card);
    padding:20px;
}

.certificate-border{
    border:12px solid var(--brand-primary);
    padding:60px;
    text-align:center;
    border-radius:20px;
}

.certificate h1{
    font-size:52px;
    font-weight:900;
    color:var(--brand-primary);
}

.certificate h3{
    color:var(--accent);
    letter-spacing:4px;
}

.certificate h2{
    font-size:40px;
    margin:25px 0;
    color:var(--text-main);
}

.certificate h4{
    font-size:28px;
    color:var(--brand-secondary);
}

.cert-footer{
    display:flex;
    justify-content:space-between;
    margin-top:50px;
}

.completion-sidebar{
    display:flex;
    flex-direction:column;
    gap:20px;
}

.info-card{
    background:#fff;
    border-radius:20px;
    padding:24px;
    box-shadow:var(--shadow-card);
}

.info-card h4{
    margin-bottom:15px;
    color:var(--brand-primary);
}

.info-card ul{
    padding-left:18px;
}

.btn-main{
    display:block;
    text-align:center;
    padding:14px;
    background:var(--brand-primary);
    color:#fff;
    border-radius:12px;
    text-decoration:none;
    margin-bottom:12px;
}

.btn-outline{
    display:block;
    text-align:center;
    padding:14px;
    border:2px solid var(--brand-primary);
    color:var(--brand-primary);
    border-radius:12px;
    text-decoration:none;
}</style>

 <div class="stepper">@include('demo.stepper')</div>
{{-- ── Banner ── --}}



<section class="completion-page">

    <div class="completion-top">

        <div class="success-icon">
            <i class="fas fa-circle-check"></i>
        </div>

        <h1>Congratulations {{ auth()->user()->name }} 🎉</h1>

        <p>
            You have successfully completed your LMS Demo Program.
        </p>

        <div class="completion-badge">
            <span>100% Completed</span>
        </div>

    </div>


    <div class="completion-grid">

        <!-- Left -->

        <div class="certificate-wrapper">

            <div class="certificate">

                <div class="certificate-border">

                    <div class="cert-logo">
                        <img src="{{ asset('logo.png') }}">
                    </div>

                    <h1>CERTIFICATE</h1>

                    <h3>OF COMPLETION</h3>

                    <p>This certificate is proudly awarded to</p>

                    <h2>{{ auth()->user()->name }}</h2>

                    <p>
                        For successfully completing
                    </p>

                    <h4>
                        {{ $course->title }}
                    </h4>

                    <div class="cert-footer">

                        <div>
                            <strong>Date</strong>
                            <br>
                            {{ now()->format('d M Y') }}
                        </div>

                        <div>
                            <strong>Certificate ID</strong>
                            <br>
                            {{-- CERT-{{ $certificate->id }} --}}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Right -->

        <div class="completion-sidebar">

            <div class="info-card">

                <h4>Student Details</h4>

                <ul>
                    <li>
                        <strong>Name:</strong>
                        {{ auth()->user()->name }}
                    </li>

                    <li>
                        <strong>Email:</strong>
                        {{ auth()->user()->email }}
                    </li>

                    <li>
                        <strong>Course:</strong>
                        {{ $course->title }}
                    </li>

                    <li>
                        <strong>Status:</strong>
                        Completed
                    </li>
                </ul>

            </div>


            <div class="info-card">

                <h4>Actions</h4>

                <a href="#"
                   class="btn-main">

                    <i class="fas fa-download"></i>
                    Download Certificate

                </a>

                <a href="#"
                   class="btn-outline">

                    <i class="fas fa-shield-check"></i>
                    Verify Certificate

                </a>

            </div>


            <div class="info-card">

                <h4>Recommended Courses</h4>

                <ul>
                    <li>Advanced Digital Marketing</li>
                    <li>AI Marketing Automation</li>
                    <li>SEO Mastery Program</li>
                    <li>Performance Marketing</li>
                </ul>

            </div>

        </div>

    </div>

</section>


@endsection
