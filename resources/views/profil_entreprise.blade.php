<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Profil Entreprise</title>
    <meta name="description" content="Complétez le profil de votre entreprise sur StageLink.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/profil_entreprise.css') }}">
</head>

<body>
    <div class="login-page">

        {{-- ===== PARTIE GAUCHE ===== --}}
        <div class="left-panel">
            <div class="glow glow-1"></div>
            <div class="glow glow-2"></div>

            <div class="content">
                {{-- Logo --}}
                <div class="logo-box">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo StageLink">
                    <h2>StageLink</h2>
                </div>

                <span class="badge">Espace Recruteur / RH</span>

                <h1>Votre entreprise,<br>mise en valeur.</h1>

                <p>
                    Un profil entreprise complet inspire confiance aux candidats
                    et augmente la visibilité de vos offres de stage.
                </p>

                {{-- Indicateur d'étapes --}}
                <div class="steps">
                    <div class="step done">
                        <span class="step-num">✓</span>
                        <div class="step-text">
                            <strong>Compte créé</strong>
                            <span>Informations personnelles</span>
                        </div>
                    </div>
                    <div class="step-line"></div>
                    <div class="step active">
                        <span class="step-num">2</span>
                        <div class="step-text">
                            <strong>Profil entreprise</strong>
                            <span>En cours</span>
                        </div>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-num">3</span>
                        <div class="step-text">
                            <strong>Dashboard RH</strong>
                            <span>Bientôt disponible</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== PARTIE DROITE : formulaire ===== --}}
        <div class="right-panel">
            <div class="login-card">

                <h2>Votre entreprise</h2>
                <p class="subtitle">Étape 2 sur 2 — Profil de votre entreprise</p>

                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST"
                      action="{{ route('profil.entreprise.store') }}"
                      enctype="multipart/form-data"
                      novalidate>
                    @csrf

                    {{-- Upload logo --}}
                    <div class="input-group">
                        <label>Logo de l'entreprise</label>
                        <div class="upload-zone" id="uploadZone">
                            <div class="upload-preview" id="uploadPreview">
                                <span class="upload-icon">🏢</span>
                                <span class="upload-text">Cliquez ou glissez votre logo ici</span>
                                <span class="upload-hint">PNG, JPG — max 2 Mo</span>
                            </div>
                            <input
                                type="file"
                                id="logo"
                                name="logo"
                                accept="image/*"
                                class="upload-input"
                            >
                        </div>
                        @error('logo')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nom entreprise --}}
                    <div class="input-group">
                        <label for="nom_entreprise">Nom de l'entreprise <span class="required">*</span></label>
                        <input
                            type="text"
                            id="nom_entreprise"
                            name="nom_entreprise"
                            placeholder="Ex : TechCorp Côte d'Ivoire"
                            value="{{ old('nom_entreprise') }}"
                            required
                        >
                        @error('nom_entreprise')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email + Téléphone entreprise --}}
                    <div class="row-2">
                        <div class="input-group">
                            <label for="email_entreprise">Email entreprise <span class="required">*</span></label>
                            <input
                                type="email"
                                id="email_entreprise"
                                name="email_entreprise"
                                placeholder="contact@entreprise.com"
                                value="{{ old('email_entreprise') }}"
                                required
                            >
                            @error('email_entreprise')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="input-group">
                            <label for="telephone_entreprise">Téléphone <span class="required">*</span></label>
                            <input
                                type="tel"
                                id="telephone_entreprise"
                                name="telephone_entreprise"
                                placeholder="+225 27 00 00 00 00"
                                value="{{ old('telephone_entreprise') }}"
                                required
                            >
                            @error('telephone_entreprise')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Adresse --}}
                    <div class="input-group">
                        <label for="adresse">Adresse <span class="required">*</span></label>
                        <input
                            type="text"
                            id="adresse"
                            name="adresse"
                            placeholder="Ex : Plateau, Abidjan, Côte d'Ivoire"
                            value="{{ old('adresse') }}"
                            required
                        >
                        @error('adresse')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Site web --}}
                    <div class="input-group">
                        <label for="site_web">Site web</label>
                        <input
                            type="url"
                            id="site_web"
                            name="site_web"
                            placeholder="https://www.entreprise.com"
                            value="{{ old('site_web') }}"
                        >
                        @error('site_web')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="input-group">
                        <label for="description">Description <span class="required">*</span></label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="Décrivez votre entreprise, votre secteur d'activité, votre culture…"
                            required
                        >{{ old('description') }}</textarea>
                        <span class="char-count" id="charCount">0 / 500</span>
                        @error('description')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Bouton --}}
                    <button type="submit" class="login-btn">
                        Accéder à mon dashboard →
                    </button>

                </form>

                {{-- Passer cette étape --}}
                <a href="{{ route('dashboard.rh') }}" class="skip-link">
                    Passer cette étape pour l'instant
                </a>

            </div>
        </div>

    </div>

    <script src="{{ asset('js/profil_entreprise.js') }}"></script>
</body>

</html>