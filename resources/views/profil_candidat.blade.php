<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Compléter mon profil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/profil_candidat.css') }}">
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
            <h1>Votre profil,<br>votre carte de visite.</h1>
            <p>Un profil complet multiplie vos chances d'être remarqué par les recruteurs et d'obtenir des entretiens.</p>

            {{-- Indicateur étapes --}}
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
                        <strong>Compléter le profil</strong>
                        <span>En cours</span>
                    </div>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-num">3</span>
                    <div class="step-text">
                        <strong>Dashboard Candidat</strong>
                        <span>Accéder aux offres</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PARTIE DROITE ===== --}}
    <div class="right-panel">
        <div class="login-card">
            <h2>Mon profil</h2>
            <p class="subtitle">Étape 2 sur 2 — Complétez votre profil candidat</p>

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST"
                  action="{{ route('profil.candidat.store') }}"
                  enctype="multipart/form-data"
                  novalidate>
                @csrf

                {{-- ===== PHOTO DE PROFIL ===== --}}
                <div class="input-group">
                    <label>Photo de profil</label>
                    <div class="upload-zone photo-zone" id="photoZone">
                        <div class="photo-preview" id="photoPreview">
                            <span class="upload-icon">👤</span>
                            <span class="upload-text">Cliquez pour ajouter une photo</span>
                            <span class="upload-hint">JPG, PNG — max 2 Mo</span>
                        </div>
                        <input type="file" id="photo" name="photo"
                            accept="image/*" class="upload-input">
                    </div>
                    @error('photo') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- ===== CV ===== --}}
                <div class="input-group">
                    <label>CV <span class="required">*</span></label>
                    <div class="upload-zone cv-zone" id="cvZone">
                        <div class="cv-preview" id="cvPreview">
                            <span class="upload-icon">📄</span>
                            <span class="upload-text">Cliquez ou glissez votre CV ici</span>
                            <span class="upload-hint">PDF — max 5 Mo</span>
                        </div>
                        <input type="file" id="cv" name="cv"
                            accept=".pdf" class="upload-input">
                    </div>
                    @error('cv') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- ===== ADRESSE ===== --}}
                <div class="input-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse"
                        placeholder="Ex : Cocody, Abidjan, Côte d'Ivoire"
                        value="{{ old('adresse') }}">
                    @error('adresse') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- ===== LIENS PROFESSIONNELS ===== --}}
                <div class="section-label">Liens professionnels</div>

                <div class="input-group">
                    <label for="linkedin">LinkedIn</label>
                    <div class="input-icon-wrap">
                        <span class="input-icon">in</span>
                        <input type="url" id="linkedin" name="linkedin"
                            placeholder="https://linkedin.com/in/votrenom"
                            value="{{ old('linkedin') }}">
                    </div>
                    @error('linkedin') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="row-2">
                    <div class="input-group">
                        <label for="github">GitHub</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">GH</span>
                            <input type="url" id="github" name="github"
                                placeholder="https://github.com/votrenom"
                                value="{{ old('github') }}">
                        </div>
                        @error('github') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-group">
                        <label for="portfolio">Portfolio</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">🌐</span>
                            <input type="url" id="portfolio" name="portfolio"
                                placeholder="https://votresite.com"
                                value="{{ old('portfolio') }}">
                        </div>
                        @error('portfolio') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- ===== COMPÉTENCES ===== --}}
                <div class="section-label">Compétences & Expériences</div>

                <div class="input-group">
                    <label for="competences">Compétences</label>
                    <div class="tags-input-wrap">
                        <div class="tags-container" id="tagsContainer"></div>
                        <input type="text" id="tagsInput"
                            placeholder="Ex: JavaScript, Python… (Entrée pour ajouter)">
                        <input type="hidden" id="competences" name="competences"
                            value="{{ old('competences') }}">
                    </div>
                    @error('competences') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="input-group">
                    <label for="experiences">Expériences</label>
                    <textarea id="experiences" name="experiences" rows="3"
                        placeholder="Ex : Stage chez TechCorp (3 mois) — Développement web…">{{ old('experiences') }}</textarea>
                    <span class="char-count" id="expCount">0 / 500</span>
                    @error('experiences') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                {{-- ===== LANGUES & CERTIFICATIONS ===== --}}
                <div class="section-label">Langues & Certifications</div>

                <div class="row-2">
                    <div class="input-group">
                        <label for="langues">Langues</label>
                        <input type="text" id="langues" name="langues"
                            placeholder="Ex : Français, Anglais, Dioula"
                            value="{{ old('langues') }}">
                        @error('langues') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-group">
                        <label for="certifications">Certifications</label>
                        <input type="text" id="certifications" name="certifications"
                            placeholder="Ex : AWS, TOEIC, CCNA"
                            value="{{ old('certifications') }}">
                        @error('certifications') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- ===== BOUTON ===== --}}
                <button type="submit" class="login-btn" id="submitBtn">
                    Accéder à mon dashboard →
                </button>

            </form>

            {{-- Passer cette étape --}}
            <a href="{{ route('dashboard.candidat') }}" class="skip-link">
                Passer cette étape pour l'instant
            </a>

        </div>
    </div>

</div>
<script src="{{ asset('js/profil_candidat.js') }}"></script>
</body>
</html>