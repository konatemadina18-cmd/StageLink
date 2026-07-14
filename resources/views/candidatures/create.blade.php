<!DOCTYPE html>
<html lang="fr" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Postuler</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/candidature-form.css') }}">
</head>
<body>
<div class="candidature-page">

    {{-- TOPBAR --}}
    <header class="cf-topbar">
        <a href="{{ route('dashboard.candidat') }}" class="cf-back">
            <i class="fas fa-arrow-left"></i> Retour au dashboard
        </a>
        <div class="logo-box">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo">
            <span class="logo-name" style="color:var(--blue-dark);">StageLink</span>
        </div>
    </header>

    <div class="cf-wrap">

        {{-- TITRE --}}
        <div class="cf-header">
            <h1>
                @if($offre)
                    Postuler — {{ $offre->titre }}
                @else
                    Candidature spontanée
                @endif
            </h1>
            <p>Remplissez ce formulaire pour envoyer votre candidature à {{ $offre->entreprise->nom ?? 'l\'entreprise' }}.</p>
        </div>

        {{-- RÉCAP OFFRE (si candidature sur offre) --}}
        @if($offre)
            <div class="dash-card offre-recap">
                <div class="offre-recap-head">
                    <h3>{{ $offre->titre }}</h3>
                    <span class="offre-statut statut-active">{{ ucfirst($offre->statut) }}</span>
                </div>
                <p class="offre-recap-desc">{{ Str::limit($offre->description, 180) }}</p>
                <div class="offre-item-meta">
                    <span><i class="fas fa-tag"></i> {{ $offre->type_stage }}</span>
                    <span><i class="fas fa-clock"></i> {{ $offre->duree }}</span>
                    @if($offre->lieu)<span><i class="fas fa-map-marker-alt"></i> {{ $offre->lieu }}</span>@endif
                    @if($offre->filiere_cible)<span><i class="fas fa-graduation-cap"></i> {{ $offre->filiere_cible }}</span>@endif
                </div>
            </div>
        @endif

        {{-- ERREURS --}}
        @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <span style="display:block;">{{ $error }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- FORMULAIRE --}}
        <form method="POST" action="{{ route('candidatures.store') }}" enctype="multipart/form-data" id="candidatureForm">
            @csrf

            @if($offre)
                <input type="hidden" name="offre_id" value="{{ $offre->id }}">
            @endif

            {{-- CHOIX OFFRE (si candidature spontanée) --}}
            @if(!$offre)
                <div class="dash-card">
                    <div class="card-head"><h2><i class="fas fa-bullseye"></i> Concerne</h2></div>
                    <div class="form-group">
                        <label>Choisissez une offre ou postulez spontanément</label>
                        <div class="select-wrap">
                            <select name="offre_id" id="offreSelect">
                                <option value="">— Candidature spontanée (sans offre) —</option>
                                @foreach($offres as $o)
                                    <option value="{{ $o->id }}"
                                        data-type="{{ $o->type_stage }}"
                                        data-duree="{{ $o->duree }}"
                                        data-entreprise-id="{{ $o->entreprise_id }}"
                                        data-entreprise-nom="{{ $o->entreprise->nom ?? 'Entreprise' }}">
                                        {{ $o->titre }} — {{ $o->entreprise->nom ?? 'Entreprise' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                        </div>
                    </div>

                    {{-- Entreprise cible pour une candidature spontanée --}}
                    <div class="form-group" style="margin-top:14px;">
                        <label>Entreprise <span class="required">*</span></label>
                        <div class="select-wrap">
                            <select name="entreprise_id" id="entrepriseSelect" required>
                                <option value="" disabled {{ old('entreprise_id') ? '' : 'selected' }}>Choisir une entreprise</option>
                                @foreach($entreprises as $entreprise)
                                    <option value="{{ $entreprise->id }}" {{ old('entreprise_id') == $entreprise->id ? 'selected' : '' }}>
                                        {{ $entreprise->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                        </div>
                        <span class="field-hint" id="entrepriseHint">Obligatoire uniquement pour une candidature spontanée.</span>
                        @error('entreprise_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            {{-- INFOS DE LA CANDIDATURE --}}
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-info-circle"></i> Détails de votre candidature</h2></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Type de stage <span class="required">*</span></label>
                        <div class="select-wrap">
                            <select name="type_stage" id="typeStageInput" required {{ $offre ? 'disabled' : '' }}>
                                <option value="" disabled {{ old('type_stage', $offre->type_stage ?? '') ? '' : 'selected' }}>Choisir</option>
                                <option value="Stage académique"     {{ old('type_stage', $offre->type_stage ?? '') == 'Stage académique'     ? 'selected' : '' }}>Stage académique</option>
                                <option value="Stage de fin d'étude" {{ old('type_stage', $offre->type_stage ?? '') == "Stage de fin d'étude" ? 'selected' : '' }}>Stage de fin d'étude</option>
                                <option value="Stage d'observation"  {{ old('type_stage', $offre->type_stage ?? '') == "Stage d'observation"  ? 'selected' : '' }}>Stage d'observation</option>
                                <option value="Stage professionnel"  {{ old('type_stage', $offre->type_stage ?? '') == 'Stage professionnel'  ? 'selected' : '' }}>Stage professionnel</option>
                            </select>
                            <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                        </div>
                        @if($offre)
                            {{-- Champ caché car select désactivé (les champs disabled ne sont pas envoyés) --}}
                            <input type="hidden" name="type_stage" value="{{ $offre->type_stage }}">
                            <span class="field-hint">Récupéré automatiquement depuis l'offre.</span>
                        @endif
                        @error('type_stage') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label>Durée souhaitée <span class="required">*</span></label>
                        <div class="select-wrap">
                            <select name="duree" id="dureeInput" required {{ $offre ? 'disabled' : '' }}>
                                <option value="" disabled {{ old('duree', $offre->duree ?? '') ? '' : 'selected' }}>Choisir</option>
                                @foreach(['1 mois','2 mois','3 mois','4 mois','5 mois','6 mois'] as $d)
                                    <option value="{{ $d }}" {{ old('duree', $offre->duree ?? '') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                            <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                        </div>
                        @if($offre)
                            <input type="hidden" name="duree" value="{{ $offre->duree }}">
                            <span class="field-hint">Récupérée automatiquement depuis l'offre.</span>
                        @endif
                        @error('duree') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- DOCUMENTS --}}
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-file-alt"></i> Documents</h2></div>

                <div class="form-grid">
                    {{-- CV --}}
                    <div class="form-group form-full">
                        <label>CV <span class="required">*</span></label>
                        @if($candidat && $candidat->cv)
                            <div class="cv-current">
                                <i class="fas fa-file-pdf" style="color:#EF4444; font-size:22px;"></i>
                                <div>
                                    <strong>Utiliser mon CV de profil</strong>
                                    <a href="{{ asset('storage/'.$candidat->cv) }}" target="_blank" class="cv-link">Voir le CV actuel</a>
                                </div>
                            </div>
                        @endif
                        <div class="upload-zone" id="cvZone">
                            <div id="cvPreview">
                                <span class="upload-icon">📄</span>
                                <span class="upload-text">Cliquez ou glissez votre CV (PDF)</span>
                                <span class="upload-hint">PDF — max 5 Mo</span>
                            </div>
                            <input type="file" name="cv" id="cvInput" accept=".pdf" class="upload-input">
                        </div>
                        @error('cv') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- LETTRE DE MOTIVATION --}}
                    <div class="form-group form-full">
                        <label>Lettre de motivation <span class="required">*</span></label>
                        <div class="upload-zone" id="lettreZone">
                            <div id="lettrePreview">
                                <span class="upload-icon">✉️</span>
                                <span class="upload-text">Cliquez ou glissez votre lettre (PDF)</span>
                                <span class="upload-hint">PDF — max 5 Mo</span>
                            </div>
                            <input type="file" name="lettre_motivation" id="lettreInput" accept=".pdf" class="upload-input">
                        </div>
                        @error('lettre_motivation') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- LETTRE DE RECOMMANDATION (optionnelle) --}}
                    <div class="form-group form-full">
                        <label>Lettre de recommandation <span class="field-hint">(optionnel)</span></label>
                        <div class="upload-zone" id="recoZone">
                            <div id="recoPreview">
                                <span class="upload-icon">📝</span>
                                <span class="upload-text">Cliquez ou glissez une lettre de recommandation (PDF)</span>
                                <span class="upload-hint">PDF — max 5 Mo</span>
                            </div>
                            <input type="file" name="lettre_recommandation" id="recoInput" accept=".pdf" class="upload-input">
                        </div>
                        @error('lettre_recommandation') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- LIENS OPTIONNELS --}}
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-link"></i> Liens supplémentaires <span class="field-hint">(optionnel)</span></h2></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fab fa-linkedin" style="color:#0A66C2"></i> LinkedIn</label>
                        <input type="url" name="linkedin"
                            value="{{ old('linkedin', $candidat->linkedin ?? '') }}"
                            placeholder="https://linkedin.com/in/…">
                        @error('linkedin') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-globe" style="color:#1E88FF"></i> Portfolio / GitHub</label>
                        <input type="url" name="portfolio"
                            value="{{ old('portfolio', $candidat->portfolio ?? $candidat->github ?? '') }}"
                            placeholder="https://github.com/… ou https://monportfolio.com">
                        @error('portfolio') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <p class="field-hint" style="margin-top:-6px;">
                    Conseillé pour les profils informatiques / techniques.
                </p>
            </div>

            <button type="submit" class="btn-primary btn-save" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Envoyer ma candidature
            </button>

        </form>

    </div>
</div>

<script src="{{ asset('js/candidature-form.js') }}"></script>
</body>
</html>
