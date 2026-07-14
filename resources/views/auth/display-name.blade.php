<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Display name') }} | StageLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
</head>
<body>
<div class="login-page auth-center-page">
    <div class="right-panel auth-center-panel">
        <div class="login-card">
            <h2>{{ __('How should we call you?') }}</h2>
            <p class="subtitle">{{ __('This name will be shown on your RH dashboard.') }}</p>

            @error('display_name')
                <div class="alert-error">{{ $message }}</div>
            @enderror

            <form action="{{ route('display-name.update') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>{{ __('Display name') }}</label>
                    <input type="text" name="display_name" value="{{ old('display_name', Auth::user()->prenom) }}" required autofocus>
                </div>
                <button type="submit" class="login-btn">{{ __('Save') }}</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
