<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Inscription Recruteur / RH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/inscription_rh.css') }}">
</head>
<body>
<div class="login-page">

    {{-- ===== PARTIE GAUCHE ===== --}}
    <div class="left-panel">
        <div class="glow glow-1"></div>
        <div class="glow glow-2"></div>
        <div class="content">
            <div class="logo-box">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo StageLink">
                <h2>StageLink</h2>
            </div>
            <span class="badge">Espace Recruteur / RH</span>
            <h1>Créez l'espace<br>de votre entreprise.</h1>
            <p>
                Enregistrez votre entreprise sur StageLink, publiez vos offres
                de stage et gérez vos candidatures depuis un espace dédié.
            </p>

            {{-- Une seule étape maintenant --}}
            <div class="steps">
                <div class="step active">
                    <span class="step-num">1</span>
                    <div class="step-text">
                        <strong>Créer votre espace</strong>
                        <span>Informations de l'entreprise</span>
                    </div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-num">2</span>
                    <div class="step-text">
                        <strong>Dashboard RH</strong>
                        <span>Gérer vos offres</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PARTIE DROITE : formulaire ===== --}}
    <div class="right-panel">
        <div class="login-card">

            <h2>Créer l'espace entreprise</h2>
            <p class="subtitle">Remplissez les informations de votre entreprise pour commencer.</p>

            @if($errors->any())
                <div class="alert-error" style="background:#FEF2F2;color:#B91C1C;border:1px solid #FECACA;border-radius:12px;padding:12px 16px;font-size:14px;margin-bottom:20px;">
                    @foreach($errors->all() as $error)
                        <span style="display:block;">{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('inscription.rh.store') }}" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- ===== INFOS ENTREPRISE ===== --}}
                <div class="section-label">Informations de l'entreprise</div>

                {{-- Logo --}}
                <div class="input-group">
                    <label>Logo <span style="font-size:12px;color:#64748B;">(optionnel)</span></label>
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-preview" id="uploadPreview">
                            <span class="upload-icon">🏢</span>
                            <span class="upload-text">Cliquez ou glissez votre logo</span>
                            <span class="upload-hint">PNG, JPG — max 2 Mo</span>
                        </div>
                        <input type="file" id="logo" name="logo" accept="image/*" class="upload-input">
                    </div>
                    @error('logo') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Nom entreprise --}}
                <div class="input-group">
                    <label for="nom_entreprise">Nom de l'entreprise <span class="required">*</span></label>
                    <input type="text" id="nom_entreprise" name="nom_entreprise"
                        placeholder="Ex : TechCorp Côte d'Ivoire"
                        value="{{ old('nom_entreprise') }}" required>
                    @error('nom_entreprise') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Email + Téléphone --}}
                <div class="row-2">
                    <div class="input-group">
                        <label for="email_entreprise">Email professionnel <span class="required">*</span></label>
                        <input type="email" id="email_entreprise" name="email_entreprise"
                            placeholder="contact@entreprise.com"
                            value="{{ old('email_entreprise') }}" required>
                        @error('email_entreprise') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-group">
                        <label for="telephone_entreprise">Téléphone <span class="required">*</span></label>
                        <input type="tel" id="telephone_entreprise" name="telephone_entreprise"
                            placeholder="+225 27 00 00 00 00"
                            value="{{ old('telephone_entreprise') }}" required>
                        @error('telephone_entreprise') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Adresse --}}
                <div class="input-group">
                    <label for="adresse">Adresse <span class="required">*</span></label>
                    <input type="text" id="adresse" name="adresse"
                        placeholder="Ex : Plateau, Abidjan, Côte d'Ivoire"
                        value="{{ old('adresse') }}" required>
                    @error('adresse') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Site web --}}
                <div class="input-group">
                    <label for="site_web">Site web <span style="font-size:12px;color:#64748B;">(optionnel)</span></label>
                    <input type="url" id="site_web" name="site_web"
                        placeholder="https://www.entreprise.com"
                        value="{{ old('site_web') }}">
                    @error('site_web') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Description --}}
                <div class="input-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" rows="3"
                        placeholder="Décrivez votre entreprise, secteur d'activité…"
                        required>{{ old('description') }}</textarea>
                    <span class="char-count" id="charCount">0 / 500</span>
                    @error('description') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- ===== COMPTE PRINCIPAL (RH_ADMIN) ===== --}}
                <div class="section-label">Compte administrateur</div>
                <p style="font-size:13px;color:#64748B;margin-bottom:16px;">
                    Ces identifiants serviront à vous connecter en tant qu'administrateur principal de l'entreprise.
                </p>

                <div class="input-group">
                    <label for="display_name">Comment souhaitez-vous que nous vous appelions ? <span class="required">*</span></label>
                    <input type="text" id="display_name" name="display_name"
                        placeholder="Ex : Mohamed, Sarah..."
                        value="{{ old('display_name') }}" required>
                    @error('display_name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="input-group">
                    <label for="password">Mot de passe <span class="required">*</span></label>
                    <div class="password-box">
                        <input type="password" id="password" name="password"
                            placeholder="Minimum 8 caractères" required autocomplete="new-password">
                        <button type="button" id="togglePassword" aria-label="Afficher/masquer">👁</button>
                    </div>
                    <div class="strength-bar" aria-hidden="true">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="strength-label" id="strengthLabel"></span>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Confirmation --}}
                <div class="input-group">
                    <label for="password_confirmation">Confirmer le mot de passe <span class="required">*</span></label>
                    <div class="password-box">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Répétez votre mot de passe" required autocomplete="new-password">
                        <button type="button" id="toggleConfirm" aria-label="Afficher/masquer">👁</button>
                    </div>
                    @error('password_confirmation') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="login-btn" id="submitBtn">
                    Créer l'espace entreprise →
                </button>
            </form>

            <p class="register-text" style="margin-top:25px;">
                Déjà inscrit ? <a href="/connexion">Se connecter</a>
            </p>
            <a href="/choix-profil" class="back-home">← Changer de profil</a>

        </div>
    </div>

</div>
<script src="{{ asset('js/inscription_rh.js') }}"></script>
</body>
</html>
