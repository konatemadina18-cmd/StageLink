<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Two-factor verification') }} | StageLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
</head>
<body>
<div class="login-page auth-center-page">
    <div class="right-panel auth-center-panel">
        <div class="login-card">
            <h2>{{ __('Two-factor verification') }}</h2>
            <p class="subtitle">
                {{ ($method ?? 'email') === 'app' ? __('Enter the code from your authentication app.') : __('Enter the code received by email.') }}
            </p>

            @if(session('status'))
                <div class="alert-error" style="background:#EFF6FF;color:#1D4ED8;border-color:#BFDBFE;">{{ session('status') }}</div>
            @endif
            @error('code')
                <div class="alert-error">{{ $message }}</div>
            @enderror

            <form action="{{ route('two-factor.verify') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>{{ ($method ?? 'email') === 'app' ? __('Authentication code') : __('Received code') }}</label>
                    <input type="text" name="code" inputmode="numeric" maxlength="8" required autofocus>
                </div>
                <button type="submit" class="login-btn">{{ __('Validate') }}</button>
            </form>

            @if(($method ?? 'email') === 'email')
                <form action="{{ route('two-factor.resend') }}" method="POST" style="margin-top:12px;">
                    @csrf
                    <button type="submit" class="login-btn" style="background:#0F172A;">{{ __('Resend code') }}</button>
                </form>
            @endif
        </div>
    </div>
</div>
</body>
</html>
