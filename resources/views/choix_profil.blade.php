<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Choisissez votre profil</title>
    <meta name="description" content="Rejoignez StageLink en tant que Candidat ou Recruteur et accédez à votre espace personnalisé.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/choix_profil.css') }}">
</head>

<body>
    <div class="login-page">

        {{-- ===== PARTIE GAUCHE : présentation StageLink ===== --}}
        <div class="left-panel">
            <div class="glow glow-1"></div>
            <div class="glow glow-2"></div>

            <div class="content">
                {{-- Logo --}}
                <div class="logo-box">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo StageLink">
                    <h2>StageLink</h2>
                </div>

                {{-- Badge --}}
                <span class="badge">Inscription en quelques secondes</span>

                {{-- Titre principal --}}
                <h1>Un profil,<br>une destination.</h1>

                {{-- Description --}}
                <p>
                    Que vous cherchiez un stage ou que vous recrutiez,
                    StageLink vous propose un espace taillé pour vos besoins.
                    Choisissez votre rôle et commencez dès maintenant.
                </p>

                {{-- Statistiques --}}
                <div class="stats">
                    <div class="stat-card">
                        <h3>0+</h3>
                        <span>Candidats inscrits</span>
                    </div>
                    <div class="stat-card">
                        <h3>0+</h3>
                        <span>Recruteurs actifs</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== PARTIE DROITE : choix du profil ===== --}}
        <div class="right-panel">
            <div class="login-card choix-card">

                {{-- En-tête --}}
                <h2>Vous êtes&nbsp;…</h2>
                <p class="subtitle">Sélectionnez le profil qui correspond à votre situation.</p>

                {{-- Cartes de choix --}}
                <div class="profil-grid">

                    {{-- Carte Candidat --}}
                    <a href="/inscription/candidat" class="profil-item" aria-label="S'inscrire en tant que Candidat">
                        <div class="profil-icon-wrap candidat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="profil-text">
                            <strong>Candidat</strong>
                            <span>Recherchez et postulez à des offres de stage adaptées à votre profil.</span>
                        </div>
                        <span class="profil-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </a>

                    {{-- Carte Recruteur / RH --}}
                    <a href="/inscription/rh" class="profil-item" aria-label="S'inscrire en tant que Recruteur RH">
                        <div class="profil-icon-wrap recruteur-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                        </div>
                        <div class="profil-text">
                            <strong>Recruteur / RH</strong>
                            <span>Publiez vos offres, gérez vos candidatures et trouvez les meilleurs profils.</span>
                        </div>
                        <span class="profil-arrow" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </a>

                </div>

                {{-- Déjà un compte --}}
                <p class="register-text" style="margin-top: 30px;">
                    Déjà inscrit ? <a href="/connexion">Se connecter</a>
                </p>

                {{-- Retour accueil --}}
                <a href="/" class="back-home">← Retour à l'accueil</a>
            </div>
        </div>

    </div>

    {{-- Overlay loader affiché après le clic sur un profil --}}
    <div id="select-overlay" aria-live="polite" aria-label="Chargement en cours">
        <div class="overlay-spinner"></div>
        <span id="overlay-label">Chargement…</span>
    </div>

    <script src="{{ asset('js/choix_profil.js') }}"></script>
</body>

</html>