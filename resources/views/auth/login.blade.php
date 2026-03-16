@extends('layouts.auth')

@section('content')
    <h2>Secure Login</h2>
    <p class="muted mt-0">Sign in to open your dashboard, profile, and role workspace.</p>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <div class="field remember-row">
            <input id="remember" type="checkbox" name="remember" value="1">
            <label for="remember">Remember me</label>
        </div>
        <button class="btn" type="submit">Login</button>
    </form>

    <p class="muted mt-14 m-0">
        Default seeded password: <strong>password</strong>
    </p>
@endsection
