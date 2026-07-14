<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard RH | StageLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-rh.css') }}">
</head>
<body>
<div class="dashboard-container">
    @php
        $currentRole = Auth::user()->role;
        $canManageTeam = $currentRole === 'rh_admin';
        $canManageOffers = $currentRole === 'rh_admin';
        $canValidateCandidatures = in_array($currentRole, ['rh_admin', 'assistant', 'rh_user']);
        $canManageInterviews = $currentRole === 'rh_admin';
        $canUseMessages = $currentRole === 'rh_admin';
        $roleLabel = ['rh_admin' => 'RH', 'assistant' => 'Assistant(e)', 'stagiaire' => 'Stagiaire', 'rh_user' => 'Assistant(e)'][$currentRole] ?? 'RH';
    @endphp

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar">
        <div class="logo-box">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo StageLink">
            <span class="logo-name">StageLink</span>
        </div>

        <nav>
            <ul>
                <li class="menu-item active" data-section="dashboard">
                    <span class="menu-icon"><i class="fas fa-th-large"></i></span>
                    <span>{{ __('Dashboard') }}</span>
                </li>
                <li class="menu-item" data-section="entreprise">
                    <span class="menu-icon"><i class="fas fa-building"></i></span>
                    <span>{{ __('My company') }}</span>
                </li>
                <li class="menu-item" data-section="offres">
                    <span class="menu-icon"><i class="fas fa-briefcase"></i></span>
                    <span>{{ __('Internship offers') }}</span>
                </li>
                <li class="menu-item" data-section="candidatures">
                    <span class="menu-icon"><i class="fas fa-users"></i></span>
                    <span>{{ __('Applications') }}</span>
                    @if(isset($newCandidaturesCount) && $newCandidaturesCount > 0)
                        <span class="menu-badge">{{ $newCandidaturesCount }}</span>
                    @endif
                </li>
                {{-- AJOUT DANS LA SIDEBAR --}}
                @if($canManageTeam)
                <li class="menu-item" data-section="equipe">
                    <span class="menu-icon"><i class="fas fa-user-plus"></i></span>
                    <span>{{ __('Team management') }}</span>
                </li>
                @endif
                @if($canUseMessages)
                <li class="menu-item" data-section="messages-modern-rh">
                    <span class="menu-icon"><i class="fas fa-envelope"></i></span>
                    <span>{{ __('Messages') }}</span>
                    @if(isset($messages) && $messages->whereNull('read_at')->where('receiver_id', $user->id)->count() > 0)
                        <span class="menu-badge">{{ $messages->whereNull('read_at')->where('receiver_id', $user->id)->count() }}</span>
                    @endif
                </li>
                @endif
                <li class="menu-item" data-section="entretiens">
                    <span class="menu-icon"><i class="fas fa-calendar-check"></i></span>
                    <span>{{ __('Interviews') }}</span>
                </li>
                <li class="menu-item" data-section="notifications-modern-rh">
                    <span class="menu-icon"><i class="fas fa-bell"></i></span>
                    <span>{{ __('Notifications') }}</span>
                </li>
                <li class="menu-item" data-section="parametres-modern-rh">
                    <span class="menu-icon"><i class="fas fa-cog"></i></span>
                    <span>{{ __('Settings') }}</span>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-avatar">@if($user->photo)<img src="{{ asset('storage/'.$user->photo) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else{{ mb_substr($user->prenom ?? 'U', 0, 1) }}@endif</div>
            <div class="sidebar-user-info">
                <strong>{{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</strong>
                <span>{{ $roleLabel }}</span>
            </div>
            <a href="#" class="logout-btn"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               title="Déconnexion">
                <i class="fas fa-sign-out-alt"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </aside>

    {{-- ===== MAIN ===== --}}
    <main class="main-content">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="topbar-left">
                <p class="topbar-date">{{ now()->translatedFormat('l d F Y') }}</p>
                <h1 class="topbar-name">{{ __('Hello') }} {{ $user->display_name ?: ($user->prenom ?? 'Recruteur') }} </h1>
            </div>
            <div class="topbar-right">
                <button class="icon-btn notif-btn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-dot"></span>
                </button>
                <button class="avatar-btn" type="button" onclick="switchSection('parametres-modern-rh')">@if($user->photo)<img src="{{ asset('storage/'.$user->photo) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else{{ mb_substr($user->prenom ?? 'U', 0, 1) }}@endif</button>
            </div>
        </header>

        {{-- ===== SECTION DASHBOARD ===== --}}
        <section id="dashboard" class="content-section active-section">

            @if(session('success'))
                <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-success" style="background:#FEF2F2;color:#991B1B;border-color:#FECACA;"><i class="fas fa-triangle-exclamation"></i> {{ $errors->first() }}</div>
            @endif

            {{-- STATS --}}
            <div class="stats-grid">
                <div class="stat-card stat-blue">
                    <div class="stat-head">
                        <span class="stat-label">Offres actives</span>
                        <span class="stat-icon-wrap blue-wrap"><i class="fas fa-briefcase"></i></span>
                    </div>
                    <div class="stat-value">{{ $offresCount ?? 0 }}</div>
                    <div class="stat-bar"><div class="stat-bar-fill blue-fill" style="width: {{ min(($offresCount ?? 0) * 10, 100) }}%"></div></div>
                </div>
                <div class="stat-card stat-cyan">
                    <div class="stat-head">
                        <span class="stat-label">Candidatures</span>
                        <span class="stat-icon-wrap cyan-wrap"><i class="fas fa-users"></i></span>
                    </div>
                    <div class="stat-value">{{ $candidaturesCount ?? 0 }}</div>
                    <div class="stat-bar"><div class="stat-bar-fill cyan-fill" style="width: {{ min(($candidaturesCount ?? 0) * 5, 100) }}%"></div></div>
                </div>
                <div class="stat-card stat-green">
                    <div class="stat-head">
                        <span class="stat-label">Entretiens</span>
                        <span class="stat-icon-wrap green-wrap"><i class="fas fa-calendar-check"></i></span>
                    </div>
                    <div class="stat-value">{{ $entretiensCount ?? 0 }}</div>
                    <div class="stat-bar"><div class="stat-bar-fill green-fill" style="width: {{ min(($entretiensCount ?? 0) * 10, 100) }}%"></div></div>
                </div>
            </div>

            {{-- DERNIÈRES CANDIDATURES --}}
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-users"></i> Dernières candidatures</h2>
                    <button class="btn-outline btn-sm" onclick="switchSection('candidatures')">
                        Tout voir <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                @if(isset($dernieresCandidatures) && $dernieresCandidatures->count() > 0)
                    <div class="cand-list">
                        @foreach($dernieresCandidatures as $candidature)
                            <div class="cand-item">
                                <div class="cand-avatar">{{ mb_substr($candidature->candidat->user->prenom ?? 'C', 0, 1) }}</div>
                                <div class="cand-info">
                                    <strong>{{ $candidature->candidat->user->prenom ?? 'Candidat' }} {{ $candidature->candidat->user->nom ?? '' }}</strong>
                                    <span>{{ $candidature->offre->titre ?? 'Candidature spontanée' }} — {{ $candidature->duree ?? '' }}</span>
                                </div>
                                <span class="status-badge status-{{ \Illuminate\Support\Str::slug($candidature->statut) }}">
                                    {{ $candidature->statut }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <span>Aucune candidature pour le moment.</span>
                    </div>
                @endif
            </div>
        </section>
        {{-- ===== SECTION GESTION D'ÉQUIPE (Étape 3) ===== --}}
        @if($canManageTeam)
        <section id="equipe" class="content-section">
            <div class="team-management-section">
                <h2><i class="fas fa-user-plus"></i> Gestion de l'équipe RH</h2>
                <p>Ajoutez des collaborateurs qui pourront consulter les offres et candidatures.</p>

                <form action="{{ route('rh.users.store') }}" method="POST" class="team-form-grid team-card" autocomplete="off">
                    @csrf
                    <div class="team-form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" required placeholder="Nom">
                    </div>
                    <div class="team-form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" required placeholder="Prénom">
                    </div>
                    <div class="team-form-group">
                        <label>Email professionnel</label>
                        <input type="email" name="collaborator_email" required placeholder="email@professionel.com" autocomplete="off">
                    </div>
                    <div class="team-form-group">
                        <label>Téléphone</label>
                        <input type="text" name="telephone" required placeholder="Téléphone">
                    </div>
                    <div class="team-form-group">
                        <label>Rôle</label>
                        <select name="role" required><option value="assistant">Assistant(e)</option><option value="stagiaire">Stagiaire</option></select>
                    </div>
                    <div class="team-form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="collaborator_password" required placeholder="Mot de passe temporaire" autocomplete="new-password">
                    </div>
                    <div class="full-width">
                        <button type="submit" class="team-submit-btn">
                            <i class="fas fa-plus"></i> Ajouter le collaborateur
                        </button>
                    </div>
                </form>

                @if(isset($teamUsers) && $teamUsers->count())
                    <div class="team-members-grid">
                        @foreach($teamUsers as $member)
                            @if($member->user && $member->user->id !== $user->id)
                                <div class="team-member-card">
                                    <div class="team-member-main">
                                        <div class="team-member-avatar">{{ mb_substr($member->user->prenom ?? 'U', 0, 1) }}</div>
                                        <div class="setting-info">
                                        <strong>{{ $member->user->prenom ?? '' }} {{ $member->user->nom ?? '' }}</strong>
                                        <span>{{ ['assistant'=>'Assistant(e)','stagiaire'=>'Stagiaire','rh_user'=>'Assistant(e)'][$member->user->role] ?? 'RH' }}</span>
                                        </div>
                                    </div>
                                    @if($member->user->role !== 'rh_admin')
                                        <form action="{{ route('rh.users.role', $member->user) }}" method="POST" class="team-role-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" required>
                                                <option value="assistant" {{ in_array($member->user->role, ['assistant', 'rh_user']) ? 'selected' : '' }}>Assistant(e)</option>
                                                <option value="stagiaire" {{ $member->user->role === 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
                                            </select>
                                            <button class="btn-outline btn-sm" type="submit">Approuver</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
        @endif
        {{-- ===== SECTION ENTREPRISE ===== --}}
        <section id="entreprise" class="content-section">
            <div class="dash-card">
                <div class="card-head company-head">
                    <h2><i class="fas fa-building"></i> Mon entreprise</h2>
                    <span class="offres-count">{{ $entreprise?->secteur_activite ?? 'Profil entreprise' }}</span>
                </div>
                <div class="company-box company-box-modern">
                    <div class="company-logo-wrap">
                        @if($entreprise && $entreprise->logo)
                            <img src="{{ asset('storage/' . $entreprise->logo) }}" class="company-logo-img" alt="Logo">
                        @else
                            <div class="company-logo-placeholder"><i class="fas fa-building"></i></div>
                        @endif
                    </div>
                    <div class="company-info">
                        <h3>{{ $entreprise->nom ?? 'Nom non renseigné' }}</h3>
                        <p class="company-desc">{{ $entreprise->description ?? 'Aucune description.' }}</p>
                        <div class="company-meta">
                            <span><i class="fas fa-map-marker-alt"></i> {{ $entreprise->adresse ?? 'Adresse non renseignée' }}</span>
                            <span><i class="fas fa-envelope"></i> {{ $entreprise->email ?? '' }}</span>
                            <span><i class="fas fa-phone"></i> {{ $entreprise->telephone ?? '' }}</span>
                            @if($entreprise && $entreprise->site_web)
                                <span><i class="fas fa-globe"></i> <a href="{{ $entreprise->site_web }}" target="_blank">{{ $entreprise->site_web }}</a></span>
                            @endif
                        </div>
                    </div>
                </div>
                @if(Auth::user()->role === 'rh_admin' && $entreprise)
                    <form action="{{ route('rh.entreprise.update') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                        @csrf @method('PUT')
                        <div class="form-grid">
                            <div class="form-group"><label>{{ $entreprise->logo ? 'Modifier le logo' : 'Ajouter un logo' }}</label><input type="file" name="logo" accept="image/*"></div>
                            <div class="form-group"><label>Nom de l'entreprise</label><input name="nom" value="{{ old('nom', $entreprise->nom) }}" required></div>
                            <div class="form-group"><label>Adresse</label><input name="adresse" value="{{ old('adresse', $entreprise->adresse) }}" required></div>
                            <div class="form-group"><label>Telephone</label><input name="telephone" value="{{ old('telephone', $entreprise->telephone) }}" required></div>
                            <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $entreprise->email) }}" required></div>
                            <div class="form-group"><label>Site web</label><input name="site_web" value="{{ old('site_web', $entreprise->site_web) }}"></div>
                            <div class="form-group"><label>Secteur d'activite</label><input name="secteur_activite" value="{{ old('secteur_activite', $entreprise->secteur_activite) }}"></div>
                            <div class="form-group"><label>Taille de l'entreprise</label><input name="taille" value="{{ old('taille', $entreprise->taille) }}" placeholder="Ex: 11-50 employes"></div>
                            <div class="form-group form-full"><label>Description</label><textarea name="description" rows="4">{{ old('description', $entreprise->description) }}</textarea></div>
                        </div>
                        <button class="btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer l'entreprise</button>
                    </form>
                @endif
            </div>
        </section>

        {{-- ===== SECTION OFFRES ===== --}}
        <section id="offres" class="content-section">
            @if($canManageOffers)
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-plus-circle"></i> Publier une nouvelle offre</h2>
                    <button class="btn-outline btn-sm" id="toggleOffreForm">
                        <i class="fas fa-chevron-down" id="toggleOffreIcon"></i> Afficher le formulaire
                    </button>
                </div>
                <div id="offreFormWrap" style="display:none;">
                    <form method="POST" action="{{ route('offres.store') }}" class="offre-form">
                        @csrf
                        <div class="form-grid">
                            <div class="form-group form-full">
                                <label>Titre de l'offre <span class="required">*</span></label>
                                <input type="text" name="titre" value="{{ old('titre') }}" placeholder="Ex : Développeur Web Junior…" required>
                                @error('titre') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Type de stage <span class="required">*</span></label>
                                <div class="select-wrap">
                                    <select name="type_stage" required>
                                        <option value="" disabled {{ old('type_stage') ? '' : 'selected' }}>Choisir</option>
                                        <option value="Stage académique"     {{ old('type_stage') == 'Stage académique'     ? 'selected' : '' }}>Stage académique</option>
                                        <option value="Stage de fin d'étude" {{ old('type_stage') == "Stage de fin d'étude" ? 'selected' : '' }}>Stage de fin d'étude</option>
                                        <option value="Stage d'observation"  {{ old('type_stage') == "Stage d'observation"  ? 'selected' : '' }}>Stage d'observation</option>
                                        <option value="Stage professionnel"  {{ old('type_stage') == 'Stage professionnel'  ? 'selected' : '' }}>Stage professionnel</option>
                                    </select>
                                    <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Durée <span class="required">*</span></label>
                                <div class="select-wrap">
                                    <select name="duree" required>
                                        <option value="" disabled {{ old('duree') ? '' : 'selected' }}>Choisir</option>
                                        @foreach(['1 mois','2 mois','3 mois','4 mois','5 mois','6 mois'] as $d)
                                            <option value="{{ $d }}" {{ old('duree') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                    <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Filière cible</label>
                                <input type="text" name="filiere_cible" value="{{ old('filiere_cible') }}" placeholder="Ex : Informatique…">
                            </div>
                            <div class="form-group">
                                <label>Lieu</label>
                                <input type="text" name="lieu" value="{{ old('lieu') }}" placeholder="Ex : Plateau, Abidjan">
                            </div>
                            <div class="form-group">
                                <label>Date de début</label>
                                <input type="date" name="date_debut" value="{{ old('date_debut') }}">
                            </div>
                            <div class="form-group">
                                <label>Date limite de candidature</label>
                                <input type="date" name="date_fin_candidature" value="{{ old('date_fin_candidature') }}">
                            </div>
                            <div class="form-group form-full">
                                <label>Compétences requises</label>
                                <input type="text" name="competences_requises" value="{{ old('competences_requises') }}" placeholder="Ex : JavaScript, Excel…">
                            </div>
                            <div class="form-group form-full">
                                <label>Description <span class="required">*</span></label>
                                <textarea name="description" rows="5" placeholder="Décrivez le poste, les missions…" required>{{ old('description') }}</textarea>
                                <span class="char-count" id="descCount">0 / 1000</span>
                                @error('description') <span class="field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Publier l'offre
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-briefcase"></i> Mes offres publiées</h2>
                    <span class="offres-count">{{ isset($offres) ? $offres->count() : 0 }} offre(s)</span>
                </div>
                @if(isset($offres) && $offres->count() > 0)
                    <div class="offres-list">
                        @foreach($offres as $offre)
                            <div class="offre-item">
                                <div class="offre-item-left">
                                    <div class="offre-item-head">
                                        <h4>{{ $offre->titre }}</h4>
                                        <span class="offre-statut {{ $offre->statut == 'active' ? 'statut-active' : 'statut-fermee' }}">
                                            {{ ucfirst($offre->statut) }}
                                        </span>
                                    </div>
                                    <div class="offre-item-meta">
                                        <span><i class="fas fa-tag"></i> {{ $offre->type_stage }}</span>
                                        <span><i class="fas fa-clock"></i> {{ $offre->duree }}</span>
                                        @if($offre->lieu)<span><i class="fas fa-map-marker-alt"></i> {{ $offre->lieu }}</span>@endif
                                        @if($offre->filiere_cible)<span><i class="fas fa-graduation-cap"></i> {{ $offre->filiere_cible }}</span>@endif
                                        <span><i class="fas fa-users"></i> {{ $offre->candidatures->count() }} candidature(s)</span>
                                    </div>
                                </div>
                                @if($canManageOffers)
                                    <div class="offre-item-actions">
                                        <form method="POST" action="{{ route('offres.toggle', $offre->id) }}" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-outline btn-sm">
                                                <i class="fas {{ $offre->statut == 'active' ? 'fa-pause' : 'fa-play' }}"></i>
                                                {{ $offre->statut == 'active' ? 'Fermer' : 'Rouvrir' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('offres.destroy', $offre->id) }}" class="delete-offre-form" style="display:inline;"
                                              data-offre-title="{{ $offre->titre }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm" title="Supprimer l'offre" aria-label="Supprimer l'offre">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <span>Aucune offre publiée. Créez votre première offre ci-dessus !</span>
                    </div>
                @endif
            </div>
        </section>

        {{-- ===== SECTION CANDIDATURES (COMPLÈTE AVEC STATUTS) ===== --}}
        <section id="candidatures" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-users"></i> Toutes les candidatures</h2>
                    <span class="offres-count">{{ isset($toutesCandidatures) ? $toutesCandidatures->count() : 0 }} candidature(s)</span>
                </div>

                @if(session('success_statut'))
                    <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success_statut') }}</div>
                @endif

                @if(isset($toutesCandidatures) && $toutesCandidatures->count() > 0)
                    <div class="candidatures-table">
                        @foreach($toutesCandidatures as $candidature)
                            <div class="cand-row">

                                {{-- Avatar + Infos candidat --}}
                                <div class="cand-row-main">
                                    <div class="cand-avatar">{{ mb_substr($candidature->candidat->user->prenom ?? 'C', 0, 1) }}</div>
                                    <div class="cand-row-info">
                                        <strong>{{ $candidature->candidat->user->prenom ?? 'Candidat' }} {{ $candidature->candidat->user->nom ?? '' }}</strong>
                                        <span class="cand-row-sub">
                                            {{ $candidature->candidat->filiere ?? '' }}
                                            {{ $candidature->candidat->niveau ? '— '.$candidature->candidat->niveau : '' }}
                                        </span>
                                        <span class="cand-row-offre">
                                            <i class="fas fa-briefcase"></i>
                                            {{ $candidature->offre->titre ?? 'Candidature spontanée' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Meta --}}
                                <div class="cand-row-meta">
                                    <span><i class="fas fa-tag"></i> {{ $candidature->type_stage }}</span>
                                    <span><i class="fas fa-clock"></i> {{ $candidature->duree }}</span>
                                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($candidature->date_candidature)->format('d/m/Y') }}</span>
                                    @if($candidature->score)
                                        <span class="score-badge"><i class="fas fa-chart-line"></i> {{ $candidature->score }}/100</span>
                                    @endif
                                </div>

                                {{-- Documents --}}
                                <div class="cand-row-docs">
                                    @if($candidature->cv)
                                        <a href="{{ asset('storage/'.$candidature->cv) }}" target="_blank" class="doc-link">
                                            <i class="fas fa-file-pdf"></i> CV
                                        </a>
                                    @endif
                                    @if($candidature->lettre_motivation)
                                        <a href="{{ asset('storage/'.$candidature->lettre_motivation) }}" target="_blank" class="doc-link">
                                            <i class="fas fa-envelope-open-text"></i> Lettre
                                        </a>
                                    @endif
                                    @if($candidature->lettre_recommandation)
                                        <a href="{{ asset('storage/'.$candidature->lettre_recommandation) }}" target="_blank" class="doc-link" style="color:var(--green);background:#F0FDF4;">
                                            <i class="fas fa-award"></i> Reco.
                                        </a>
                                    @endif
                                    @if($candidature->linkedin)
                                        <a href="{{ $candidature->linkedin }}" target="_blank" class="doc-link">
                                            <i class="fab fa-linkedin"></i> LinkedIn
                                        </a>
                                    @endif
                                    @if($candidature->portfolio)
                                        <a href="{{ $candidature->portfolio }}" target="_blank" class="doc-link">
                                            <i class="fas fa-globe"></i> Portfolio
                                        </a>
                                    @endif
                                </div>

                                {{-- Statut actuel + Select pour changer --}}
                                <div class="cand-row-actions">
                                    <span class="status-badge status-{{ \Illuminate\Support\Str::slug($candidature->statut) }}">
                                        {{ $candidature->statut }}
                                    </span>

                                    @if($canValidateCandidatures)
                                        <form method="POST" action="{{ route('candidatures.statut', $candidature->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="select-wrap select-sm">
                                                <select name="statut" onchange="this.form.submit()" title="Changer le statut">
                                                    <option value="En attente" {{ $candidature->statut == 'En attente' ? 'selected' : '' }}>En attente</option>
                                                    <option value="Preselectionnee" {{ in_array($candidature->statut, ['Preselectionnee', 'Présélectionnée']) ? 'selected' : '' }}>Présélectionnée</option>
                                                    <option value="Entretien programme" {{ in_array($candidature->statut, ['Entretien programme', 'Entretien programmé']) ? 'selected' : '' }}>Entretien programmé</option>
                                                    <option value="Acceptee" {{ in_array($candidature->statut, ['Acceptee', 'Acceptée']) ? 'selected' : '' }}>Acceptée</option>
                                                    <option value="Refusee" {{ in_array($candidature->statut, ['Refusee', 'Refusée']) ? 'selected' : '' }}>Refusée</option>
                                                </select>
                                                <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                                            </div>
                                        </form>

                                        @if($canManageInterviews)
                                            <button type="button" class="btn-outline btn-sm"
                                                onclick="ouvrirModalEntretien({{ $candidature->id }}, '{{ $candidature->candidat->user->prenom ?? '' }} {{ $candidature->candidat->user->nom ?? '' }}')">
                                                <i class="fas fa-calendar-check"></i> Entretien
                                            </button>
                                        @endif
                                    @endif
                                </div>

                                {{-- Commentaire RH --}}
                                @if($candidature->commentaire_rh)
                                    <div class="cand-row-commentaire">
                                        <i class="fas fa-comment-dots"></i> {{ $candidature->commentaire_rh }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <span>Aucune candidature reçue pour le moment.</span>
                    </div>
                @endif
            </div>
        </section>        {{-- ===== SECTION ENTRETIENS ===== --}}
        <section id="entretiens" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-calendar-check"></i> Entretiens planifies</h2>
                </div>

                @if($entretiens->count() > 0)
                    <div class="candidatures-table">
                        @foreach($entretiens as $entretien)
                            @php $candidature = $entretien->candidature; @endphp
                            <div class="cand-row">
                                <div class="cand-row-main">
                                    <div class="cand-avatar">@if($candidature?->candidat?->user?->photo)<img src="{{ asset('storage/'.$candidature->candidat->user->photo) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else{{ mb_substr($candidature->candidat->user->prenom ?? 'C', 0, 1) }}@endif</div>
                                    <div class="cand-row-info">
                                        <strong>{{ $candidature->candidat->user->prenom ?? '' }} {{ $candidature->candidat->user->nom ?? '' }}</strong>
                                        <span class="cand-row-offre"><i class="fas fa-briefcase"></i> {{ $candidature->offre->titre ?? 'Candidature spontanee' }}</span>
                                    </div>
                                </div>
                                <div class="entretien-box" style="flex:1;">
                                    <i class="fas fa-calendar-check"></i>
                                    <div>
                                        <strong>{{ $entretien->date_entretien->format('d/m/Y') }} a {{ substr($entretien->heure, 0, 5) }} - {{ $entretien->statut }}</strong>
                                        <span>Recruteur : {{ $entretien->recruteur->prenom ?? '' }} {{ $entretien->recruteur->nom ?? '' }}</span>
                                        <span>Lieu/lien : {{ $entretien->lieu }} - {{ $entretien->type }}</span>
                                        @if($entretien->commentaires)<span>Commentaires : {{ $entretien->commentaires }}</span>@endif
                                    </div>
                                </div>
                                @if($canManageInterviews)
                                    <div class="cand-row-actions">
                                        <button type="button" class="btn-outline btn-sm" onclick="ouvrirModalEntretien({{ $candidature->id }}, '{{ $candidature->candidat->user->prenom ?? '' }}')">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state"><i class="fas fa-calendar"></i><span>Aucun entretien planifie pour le moment.</span></div>
                @endif
            </div>
        </section>
@if($canUseMessages)
        <section id="messages-modern-rh" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-envelope"></i> Messages candidats</h2>
                    <span class="offres-count">{{ $messages->whereNull('read_at')->where('receiver_id', $user->id)->count() }} non lu(s)</span>
                </div>
                <div class="messages-layout">
                    <div class="conversation-list">
                        @forelse($messages as $message)
                            @php $other = $message->sender_id === $user->id ? $message->receiver : $message->sender; @endphp
                            <div class="conversation-item {{ !$message->read_at && $message->receiver_id === $user->id ? 'is-unread' : '' }}">
                                <strong>{{ $other->prenom ?? 'Candidat' }} {{ $other->nom ?? '' }}</strong>
                                <span>{{ Str::limit($message->body, 120) }}</span>
                                <small>{{ $message->created_at->format('d/m/Y H:i') }}</small>
                                @if($message->attachment)<a href="{{ asset('storage/'.$message->attachment) }}" target="_blank">Piece jointe</a>@endif
                                <button type="button" class="btn-outline btn-sm reply-message-btn" data-receiver-id="{{ $other->id }}" data-receiver-name="{{ $other->prenom ?? 'Candidat' }} {{ $other->nom ?? '' }}">Répondre</button>
                            </div>
                        @empty
                            <div class="empty-state"><i class="fas fa-envelope-open"></i><span>Aucune conversation.</span></div>
                        @endforelse
                    </div>
                    <form action="{{ route('rh.messages.send') }}" method="POST" enctype="multipart/form-data" class="message-compose">
                        @csrf
                        <div class="form-group">
                            <label>Candidat</label>
                            <select name="receiver_id" required>
                                <option value="">Choisir</option>
                                @foreach($candidats as $candidatUser)
                                    <option value="{{ $candidatUser->id }}">{{ $candidatUser->prenom }} {{ $candidatUser->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label>Message</label><textarea name="body" rows="6" required></textarea></div>
                        <div class="form-group"><label>Piece jointe</label><input type="file" name="attachment"></div>
                        <button class="btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
                    </form>
                </div>
            </div>
        </section>
        @endif

        {{-- ===== SECTIONS MODERNES RH ===== --}}
        <section id="notifications-modern-rh" class="content-section">
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-bell"></i> Notifications</h2></div>
                <div class="notification-list">
                    @forelse($notifications as $notification)
                        @php
                            $notificationIcon = ['candidature'=>'fa-file-lines','offre'=>'fa-briefcase','entretien'=>'fa-calendar-check','message'=>'fa-envelope'][$notification->type] ?? 'fa-bell';
                        @endphp
                        <div class="notification-item notification-card is-unread" data-type="{{ $notification->type }}">
                            <div class="notification-icon notification-{{ $notification->type }}"><i class="fas {{ $notificationIcon }}"></i></div>
                            <div class="notification-content">
                                <strong>{{ ucfirst($notification->type) }}</strong>
                                <span>{{ $notification->message }}</span>
                            </div>
                            <time>{{ \Carbon\Carbon::parse($notification->date_envoi)->diffForHumans() }}</time>
                        </div>
                    @empty
                        <div class="empty-state"><i class="fas fa-bell-slash"></i><span>Aucune notification.</span></div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="parametres-modern-rh" class="content-section">
            @php $settings = $user->settings ?? []; $notifs = $settings['notifications'] ?? []; $access = $settings['access'] ?? []; $privacy = $settings['privacy'] ?? []; @endphp
            <div class="settings-shell">
                <aside class="settings-nav">
                    @foreach(['infos'=>'Personal information','securite'=>'Security','password'=>'Password','notifications'=>'Notifications','preferences'=>'Preferences','apparence'=>'Appearance','langue'=>'Language','access'=>'Access management','confidentialite'=>'Privacy','entreprise'=>'My company','danger'=>'Delete account'] as $key => $label)
                        <button type="button" class="settings-tab {{ $loop->first ? 'active' : '' }}" data-settings-tab="{{ $key }}">{{ __($label) }}</button>
                    @endforeach
                </aside>
                <div class="settings-panels">
                    <form class="settings-panel active settings-form" data-settings-panel="infos" action="{{ route('rh.profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <h2>{{ __('Personal information') }}</h2>
                        <div class="profile-editor">
                            <div class="profile-photo-box">
                                @if($user->photo)<img src="{{ asset('storage/'.$user->photo) }}" alt="Photo">@else<span>{{ mb_substr($user->prenom ?? 'R', 0, 1) }}</span>@endif
                                <label class="btn-outline btn-sm"><i class="fas fa-camera"></i> Photo<input type="file" name="photo" accept="image/*" hidden></label>
                            </div>
                            <div class="form-grid">
                                <div class="form-group"><label>Nom</label><input name="nom" value="{{ old('nom', $user->nom) }}" required></div>
                                <div class="form-group"><label>Prenom</label><input name="prenom" value="{{ old('prenom', $user->prenom) }}" required></div>
                                <div class="form-group"><label>Telephone</label><input name="telephone" value="{{ old('telephone', $user->telephone) }}" required></div>
                                <div class="form-group"><label>Rôle actuel</label><input value="{{ $roleLabel }}" disabled></div>
                            </div>
                        </div>
                        <button class="btn-primary">{{ __('Save') }}</button>
                    </form>
                    <div class="settings-panel settings-form" data-settings-panel="securite">
                        <h2>{{ __('Security') }}</h2>
                        <form action="{{ route('rh.profil.update') }}" method="POST" class="settings-form">
                            @csrf @method('PUT')
                            <input type="hidden" name="nom" value="{{ $user->nom }}"><input type="hidden" name="prenom" value="{{ $user->prenom }}"><input type="hidden" name="telephone" value="{{ $user->telephone }}">
                            <label class="toggle-row"><input type="checkbox" name="two_factor_enabled" value="1" {{ $user->two_factor_enabled_at ? 'checked' : '' }}> {{ __('Two-factor authentication') }}</label>
                            <div class="settings-list">
                                <label class="setting-item">
                                    <span><strong>{{ __('Email') }}</strong><br><small>{{ $user->email }}</small></span>
                                    <input type="radio" name="two_factor_method" value="email" {{ ($user->two_factor_method ?? 'email') === 'email' ? 'checked' : '' }}>
                                </label>
                                <div class="setting-item disabled-option"><span><strong>SMS</strong><br><small>{{ __('Coming soon') }}</small></span></div>
                                <label class="setting-item"><span><strong>Application d'authentification</strong><br><small>Google Authenticator, Microsoft Authenticator, Authy</small></span><input type="radio" name="two_factor_method" value="app" {{ $user->two_factor_method === 'app' || $user->two_factor_pending_secret ? 'checked' : '' }}></label>
                            </div>
                            <button class="btn-primary">{{ __('Update') }}</button>
                        </form>
                        @if($twoFactorAppSetup)
                            <div class="totp-setup-box">
                                <div class="totp-qr">{!! $twoFactorAppSetup['qr_svg'] !!}</div>
                                <div class="totp-details">
                                    <h3>Configurer l'application</h3>
                                    <p>Scannez ce QR Code avec Google Authenticator, Microsoft Authenticator ou Authy, puis saisissez le code a 6 chiffres.</p>
                                    <label>Cle secrete</label>
                                    <code>{{ $twoFactorAppSetup['secret'] }}</code>
                                    <form action="{{ route('two-factor.app.confirm') }}" method="POST" class="settings-form">
                                        @csrf
                                        <div class="form-group"><label>Code de validation</label><input name="code" inputmode="numeric" maxlength="6" required></div>
                                        <button class="btn-primary" type="submit"><i class="fas fa-shield-halved"></i> Activer l'application</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                        @if($user->two_factor_enabled_at)
                            <form action="{{ route('two-factor.disable') }}" method="POST" class="settings-form">
                                @csrf @method('DELETE')
                                <button class="btn-outline" type="submit"><i class="fas fa-lock-open"></i> Desactiver la double authentification</button>
                            </form>
                        @endif
                        <div class="settings-list"><div class="setting-item"><div class="setting-info"><strong>Historique des connexions</strong><span>Derniere session active : {{ now()->format('d/m/Y H:i') }}</span></div></div></div>
                    </div>
                    <div class="settings-panel" data-settings-panel="password">
                        <h2>{{ __('Password') }}</h2>
                        <form action="{{ route('rh.password.update') }}" method="POST" class="form-grid">@csrf @method('PUT')<div class="form-group"><label>{{ __('Current password') }}</label><input type="password" name="current_password" required></div><div class="form-group"><label>{{ __('New password') }}</label><input type="password" name="password" required></div><div class="form-group"><label>{{ __('Confirmation') }}</label><input type="password" name="password_confirmation" required></div><button class="btn-primary">{{ __('Change') }}</button></form>
                    </div>
                    <form class="settings-panel settings-form" data-settings-panel="notifications" action="{{ route('rh.preferences.update') }}" method="POST">
                        @csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}">
                        <h2>{{ __('Notifications') }}</h2>
                        <label class="toggle-row"><input type="checkbox" name="notif_candidatures" value="1" {{ ($notifs['candidatures'] ?? true) ? 'checked' : '' }}> {{ __('New applications') }}</label>
                        <label class="toggle-row"><input type="checkbox" name="notif_entretiens" value="1" {{ ($notifs['entretiens'] ?? true) ? 'checked' : '' }}> {{ __('New interviews') }}</label>
                        <label class="toggle-row"><input type="checkbox" name="notif_statuts" value="1" {{ ($notifs['statuts'] ?? true) ? 'checked' : '' }}> {{ __('Status changes') }}</label>
                        <button class="btn-primary">{{ __('Save') }}</button>
                    </form>
                    <form class="settings-panel settings-form" data-settings-panel="preferences" action="{{ route('rh.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}"><h2>{{ __('Preferences') }}</h2><label class="toggle-row"><input type="checkbox" name="access_team_visible" value="1" {{ ($access['team_visible'] ?? true) ? 'checked' : '' }}> {{ __('Show access management') }}</label><button class="btn-primary">{{ __('Save') }}</button></form>
                    <form class="settings-panel settings-form" data-settings-panel="apparence" action="{{ route('rh.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}"><h2>{{ __('Appearance') }}</h2><div class="segmented"><label><input type="radio" name="theme" value="light" {{ ($settings['theme'] ?? 'light') === 'light' ? 'checked' : '' }}> {{ __('Light mode') }}</label><label><input type="radio" name="theme" value="dark" {{ ($settings['theme'] ?? '') === 'dark' ? 'checked' : '' }}> {{ __('Dark mode') }}</label></div><button class="btn-primary">{{ __('Apply') }}</button></form>
                    <form class="settings-panel settings-form" data-settings-panel="langue" action="{{ route('rh.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><h2>{{ __('Language') }}</h2><div class="segmented"><label><input type="radio" name="language" value="fr" onchange="this.form.submit()" {{ ($settings['language'] ?? app()->getLocale()) === 'fr' ? 'checked' : '' }}> Français</label><label><input type="radio" name="language" value="en" onchange="this.form.submit()" {{ ($settings['language'] ?? app()->getLocale()) === 'en' ? 'checked' : '' }}> English</label></div><button class="btn-primary">{{ __('Apply') }}</button></form>
                    <div class="settings-panel" data-settings-panel="access"><h2>Gestion des acces</h2><button class="btn-primary" type="button" onclick="switchSection('equipe')"><i class="fas fa-user-plus"></i> Gerer l'equipe RH</button></div>
                    <form class="settings-panel settings-form" data-settings-panel="confidentialite" action="{{ route('rh.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}"><h2>{{ __('Privacy') }}</h2><label class="toggle-row"><input type="checkbox" name="privacy_activity" value="1" {{ ($privacy['activity_visible'] ?? true) ? 'checked' : '' }}> {{ __('Show activity to RH collaborators') }}</label><button class="btn-primary">{{ __('Save') }}</button></form>
                    <div class="settings-panel" data-settings-panel="entreprise"><h2>Mon entreprise</h2><button class="btn-primary" type="button" onclick="switchSection('entreprise')"><i class="fas fa-building"></i> Modifier l'entreprise</button></div>
                    <div class="settings-panel danger-zone" data-settings-panel="danger"><h2>Suppression du compte</h2><p>Action sensible reservee a une confirmation administrative.</p><button class="btn-danger" type="button">Demander la suppression</button></div>
                </div>
            </div>
        </section>

        {{-- ===== SECTION NOTIFICATIONS ===== --}}
        <section id="notifications" class="content-section">
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-bell"></i> Notifications</h2></div>
                <div class="empty-state"><i class="fas fa-bell-slash"></i><span>Aucune notification.</span></div>
            </div>
        </section>

        {{-- ===== SECTION PARAMÈTRES ===== --}}
        <section id="parametres" class="content-section">
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-cog"></i> Paramètres du compte</h2></div>
                <div class="settings-list">
                    <div class="setting-item">
                        <div class="setting-info">
                            <strong>Nom complet</strong>
                            <span>{{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</span>
                        </div>
                        <button class="btn-outline btn-sm"><i class="fas fa-edit"></i> Modifier</button>
                    </div>
                    <div class="setting-item">
                        <div class="setting-info">
                            <strong>Email</strong>
                            <span>{{ $user->email ?? '' }}</span>
                        </div>
                        <button class="btn-outline btn-sm"><i class="fas fa-edit"></i> Modifier</button>
                    </div>
                    <div class="setting-item">
                        <div class="setting-info">
                            <strong>Mot de passe</strong>
                            <span>••••••••</span>
                        </div>
                        <button class="btn-outline btn-sm"><i class="fas fa-lock"></i> Changer</button>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>

{{-- ===== MODAL ENTRETIEN ===== --}}
<div id="modalEntretien" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="fas fa-calendar-check"></i> Planifier un entretien</h3>
            <button type="button" onclick="fermerModalEntretien()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="modal-sub" id="modalCandidatNom"></p>

        <form method="POST" id="entretienForm" action="">
            @csrf
            @method('POST')

            <div class="form-grid">
                <div class="form-group">
                    <label>Date <span class="required">*</span></label>
                    <input type="date" name="date_entretien" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                </div>
                <div class="form-group">
                    <label>Heure <span class="required">*</span></label>
                    <input type="time" name="heure" required>
                </div>
                <div class="form-group form-full">
                    <label>Lieu <span class="required">*</span></label>
                    <input type="text" name="lieu" placeholder="Ex : Salle B2, Plateau Abidjan, ou Zoom…" required>
                </div>
                <div class="form-group">
                    <label>Type <span class="required">*</span></label>
                    <div class="select-wrap">
                        <select name="type" required>
                    <option value="Presentiel">Présentiel</option>
                    <option value="Visioconference">Visioconférence</option>
                    <option value="Telephone">Téléphone</option>
                        </select>
                        <span class="select-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Note (optionnel)</label>
                    <input type="text" name="note" placeholder="Ex : Apporter votre CV imprimé…">
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" onclick="fermerModalEntretien()" class="btn-outline btn-sm">Annuler</button>
                <button type="submit" class="btn-primary btn-sm">
                    <i class="fas fa-calendar-check"></i> Confirmer l'entretien
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/dashboard-rh.js') }}"></script>
</body>
</html>



