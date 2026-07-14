<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | StageLink</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/connexion.css') }}">
</head>

<body>

<div class="login-page">

    <div class="left-panel">

        <div class="glow glow-1"></div>
        <div class="glow glow-2"></div>

        <div class="content">

            <div class="logo-box">
                <img src="{{ asset('images/logo.jpeg') }}" alt="">
                <h2>StageLink</h2>
            </div>

            <span class="badge">
                Plateforme intelligente de stages
            </span>

            <h1>
                Trouvez votre opportunité,
                créez votre avenir.
            </h1>

            <p>
                Connectez-vous pour suivre vos candidatures,
                découvrir des offres adaptées et échanger
                avec les recruteurs.
            </p>

            <div class="stats">

                <div class="stat-card">
                    <h3>0+</h3>
                    <span>Étudiants</span>
                </div>

                <div class="stat-card">
                    <h3>0+</h3>
                    <span>Entreprises</span>
                </div>

                <div class="stat-card">
                    <h3>0+</h3>
                    <span>Candidatures</span>
                </div>

            </div>

        </div>

    </div>

    <div class="right-panel">

        <div class="login-card">

            <h2>{{ __('Login') }}</h2>

            <p class="subtitle">
                Heureux de vous revoir.
            </p>

            {{-- Erreur générale (identifiants incorrects) --}}
            @if($errors->has('email'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first('email') }}
                </div>
            @endif

            <form id="loginForm" action="{{ route('connexion.store') }}" method="POST">
                @csrf

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="exemple@email.com"
                        value="{{ old('email') }}" required>
                </div>

                <div class="input-group">
                    <label>{{ __('Password') }}</label>
                    <div class="password-box">
                        <input type="password" name="password" id="password" placeholder="********" required>
                        <button type="button" id="togglePassword">👁</button>
                    </div>
                </div>

                <a href="#" class="forgot">
                    Mot de passe oublié ?
                </a>

                <button type="submit" class="login-btn" id="submitBtn">
                    {{ __('Login') }}
                </button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <p class="register-text">
                Pas encore de compte ?
                <a href="/choix-profil">{{ __('Register') }}</a>
            </p>

            <a href="/" class="back-home">
                ← Retour à l'accueil
            </a>

        </div>

    </div>

</div>

{{-- Overlay de succès --}}
<div id="success-overlay" class="success-overlay" style="display: none;">
    <div class="success-content">
        <div class="check-circle">
            <i class="fas fa-check"></i>
        </div>
        <p>Connexion réussie</p>
    </div>
</div>

{{-- Script de détection du succès Laravel --}}
@if(session('success_login'))
<script>
    document.getElementById('success-overlay').style.display = 'flex';
    setTimeout(function() {
        window.location.href = "{{ Auth::check() && in_array(Auth::user()->role, ['rh_admin', 'rh_user']) ? route('dashboard.rh') : route('dashboard.candidat') }}";
    }, 1500);
</script>
@endif

<script src="{{ asset('js/connexion.js') }}"></script>

</body>
</html>
