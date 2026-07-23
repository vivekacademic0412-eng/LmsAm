@extends('emails.layout')

@section('title', 'Your Demo Access is Ready | Academic Mantra LMS')

@php
    $preheader = 'Your demo account has been created successfully. Verify your email to access your free LMS demo.';
    $heroLabel = 'ACADEMIC MANTRA LMS DEMO';
    $heroTitle = 'Your Demo Access is Ready, ' . $user->name . '!';
@endphp

@section('body')


    <div style="max-width:700px;margin:auto;background:#fff;padding:30px;border-radius:10px;">

        <h2 style="color:#0d6efd;">
            Academic Mantra Services
        </h2>

        <p>
            Dear {{ $user->name }},
        </p>

        <p>
            Your demo session has been activated successfully.
        </p>

        <p>
            Click the button below to start your demo session:
        </p>

        <p style="margin:30px 0;">
            <a href="{{ $demoUrl }}"
                style="
                    background:#0d6efd;
                    color:#fff;
                    text-decoration:none;
                    padding:12px 25px;
                    border-radius:5px;
                    display:inline-block;">
                Start Demo Session
            </a>
        </p>

        <h4>Important Instructions:</h4>

        <ul>
            <li>This demo link can be used only once.</li>
            <li>Access is restricted to a single browser session.</li>
            <li>Please do not share this link.</li>
            <li>After final submission, access will be permanently disabled.</li>
            <li>This link expires automatically.</li>
        </ul>

        <p>
            If the button does not work, copy and paste this URL:
        </p>

        <p>
            <a href="{{ $demoUrl }}">
                {{ $demoUrl }}
            </a>
        </p>

        <br>

        <p>
            Regards,<br>
            Academic Mantra Services Team
        </p>

    </div>


@endsection