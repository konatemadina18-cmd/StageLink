<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Inscription Candidat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/inscription-candidat.css') }}">
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
            <span class="badge">Espace Candidat</span>
            <h1>Trouvez le stage<br>qui vous ressemble.</h1>
            <p>Créez votre profil, explorez les offres adaptées à votre parcours et suivez vos candidatures en temps réel.</p>

            {{-- Indicateur d'étapes --}}
            <div class="steps">
                <div class="step active">
                    <span class="step-num">1</span>
                    <div class="step-text">
                        <strong>Créer votre compte</strong>
                        <span>Informations personnelles</span>
                    </div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-num">2</span>
                    <div class="step-text">
                        <strong>Dashboard Candidat</strong>
                        <span>Postuler aux offres</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PARTIE DROITE ===== --}}
    <div class="right-panel">
        <div class="login-card">
            <h2>Créer mon compte</h2>
            <p class="subtitle">Étape 1 sur 2 — Vos informations personnelles</p>

            <form method="POST" action="{{ route('inscription.candidat.store') }}" novalidate>
                @csrf

                {{-- Nom / Prénom --}}
                <div class="row-2">
                    <div class="input-group">
                        <label for="nom">Nom <span class="required">*</span></label>
                        <input type="text" id="nom" name="nom"
                            placeholder="Konate"
                            value="{{ old('nom') }}" required>
                        @error('nom') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-group">
                        <label for="prenom">Prénom <span class="required">*</span></label>
                        <input type="text" id="prenom" name="prenom"
                            placeholder="Madina"
                            value="{{ old('prenom') }}" required>
                        @error('prenom') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="input-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email"
                        placeholder="madina.konate@email.com"
                        value="{{ old('email') }}" required>
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Téléphone / Date naissance --}}
                <div class="row-2">
                    <div class="input-group">
                        <label for="telephone">Téléphone <span class="required">*</span></label>
                        <input type="tel" id="telephone" name="telephone"
                        {{--  placeholder="+225 07 00 00 00 00"--}}
                            value="{{ old('telephone') }}" required>
                        @error('telephone') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-group">
                        <label for="date_naissance">Date de naissance <span class="required">*</span></label>
                        <input type="date" id="date_naissance" name="date_naissance"
                            value="{{ old('date_naissance') }}" required>
                        @error('date_naissance') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Filière / Niveau --}}
                <div class="row-2">
                    <div class="input-group">
                        <label for="filiere">Filière <span class="required">*</span></label>
                        <input type="text" id="filiere" name="filiere"
                        {{-- placeholder="Ex : Informatique"--}}
                            value="{{ old('filiere') }}" required>
                        @error('filiere') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-group">
                        <label for="niveau">Niveau <span class="required">*</span></label>
                        <div class="select-wrap">
                            <select id="niveau" name="niveau" required>
                                <option value="" disabled {{ old('niveau') ? '' : 'selected' }}>Choisir</option>
                                <option value="Licence 1" {{ old('niveau') == 'Licence 1' ? 'selected' : '' }}>Licence 1</option>
                                <option value="Licence 2" {{ old('niveau') == 'Licence 2' ? 'selected' : '' }}>Licence 2</option>
                                <option value="Licence 3" {{ old('niveau') == 'Licence 3' ? 'selected' : '' }}>Licence 3</option>
                                <option value="Master 1"  {{ old('niveau') == 'Master 1'  ? 'selected' : '' }}>Master 1</option>
                                <option value="Master 2"  {{ old('niveau') == 'Master 2'  ? 'selected' : '' }}>Master 2</option>
                                <option value="BTS"       {{ old('niveau') == 'BTS'       ? 'selected' : '' }}>BTS</option>
                                <option value="DUT"       {{ old('niveau') == 'DUT'       ? 'selected' : '' }}>DUT</option>
                                <option value="Doctorat"  {{ old('niveau') == 'Doctorat'  ? 'selected' : '' }}>Doctorat</option>
                            </select>
                            <span class="select-arrow" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"/>
                                </svg>
                            </span>
                        </div>
                        @error('niveau') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Mot de passe --}}
                <div class="input-group">
                    <label for="password">Mot de passe <span class="required">*</span></label>
                    <div class="password-box">
                        <input type="password" id="password" name="password"
                            placeholder="Minimum 8 caractères" required>
                            {{-- <button type="button" id="togglePassword" aria-label="Afficher/masquer">👁</button>--}}
                    </div>
                    <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                    <span class="strength-label" id="strengthLabel"></span>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- Confirmation mot de passe --}}
                <div class="input-group">
                    <label for="password_confirmation">Confirmer le mot de passe <span class="required">*</span></label>
                    <div class="password-box">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Répétez votre mot de passe" required>
                            {{-- <button type="button" id="toggleConfirm" aria-label="Afficher/masquer">👁</button>--}}
                    </div>
                    @error('password_confirmation') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="login-btn" id="submitBtn">
                    Créer mon compte →
                </button>
            </form>

            <p class="register-text" style="margin-top:25px;">
                Déjà inscrit ? <a href="/connexion">Se connecter</a>
            </p>
            <a href="/choix-profil" class="back-home">← Changer de profil</a>
        </div>
    </div>

</div>
<script src="{{ asset('js/inscription-candidat.js') }}"></script>
</body>
</html>