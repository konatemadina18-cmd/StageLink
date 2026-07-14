<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Candidat | StageLink</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-candidat.css') }}">
</head>
<body>
<div class="dashboard-container">
    @php $avatarPath = $user->photo ?? ($candidat->photo ?? null); @endphp

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar">
        <div class="logo-box">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo">
            <span class="logo-name">StageLink</span>
        </div>

        <nav>
            <ul>
                <li class="menu-item active" data-section="dashboard">
                    <span class="menu-icon"><i class="fas fa-th-large"></i></span>
                    <span>{{ __('Dashboard') }}</span>
                </li>
                <li class="menu-item" data-section="offres">
                    <span class="menu-icon"><i class="fas fa-search"></i></span>
                    <span>{{ __('Available offers') }}</span>
                </li>
                <li class="menu-item" data-section="postuler">
                    <span class="menu-icon"><i class="fas fa-paper-plane"></i></span>
                    <span>{{ __('Apply') }}</span>
                </li>
                <li class="menu-item" data-section="candidatures">
                    <span class="menu-icon"><i class="fas fa-file-alt"></i></span>
                    <span>{{ __('Applications') }}</span>
                    @if(($newCandidaturesCount ?? 0) > 0)
                        <span class="menu-badge">{{ $newCandidaturesCount }}</span>
                    @endif
                </li>
                <li class="menu-item" data-section="entretiens">
                    <span class="menu-icon"><i class="fas fa-calendar-check"></i></span>
                    <span>{{ __('Interviews') }}</span>
                </li>
                <li class="menu-item" data-section="profil-modern">
                    <span class="menu-icon"><i class="fas fa-user"></i></span>
                    <span>{{ __('My profile') }}</span>
                </li>
                <li class="menu-item" data-section="messages-modern">
                    <span class="menu-icon"><i class="fas fa-envelope"></i></span>
                    <span>{{ __('Messages') }}</span>
                    @if(($unreadMessagesCount ?? 0) > 0)
                        <span class="menu-badge">{{ $unreadMessagesCount }}</span>
                    @endif
                </li>
                <li class="menu-item" data-section="notifications-modern">
                    <span class="menu-icon"><i class="fas fa-bell"></i></span>
                    <span>{{ __('Notifications') }}</span>
                    @if(($unreadNotificationsCount ?? 0) > 0)
                        <span class="menu-badge">{{ $unreadNotificationsCount }}</span>
                    @endif
                </li>
                <li class="menu-item" data-section="parametres-modern">
                    <span class="menu-icon"><i class="fas fa-cog"></i></span>
                    <span>{{ __('Settings') }}</span>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-avatar">@if($avatarPath)<img src="{{ asset('storage/'.$avatarPath) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else{{ mb_substr($user->prenom ?? 'C', 0, 1) }}@endif</div>
            <div class="sidebar-user-info">
                <strong>{{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</strong>
                <span>Candidat</span>
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
                <h1 class="topbar-name">{{ __('Hello') }}, {{ $user->display_name ?: ($user->prenom ?? 'Candidat') }} </h1>
            </div>
            <div class="topbar-right">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="{{ __('Search for an internship') }}" id="searchInput">
                </div>
                <button class="icon-btn notif-btn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    @if(($unreadNotificationsCount ?? 0) > 0)
                        <span class="notif-dot"></span>
                    @endif
                </button>
                <button class="avatar-btn" type="button" onclick="switchSection('profil-modern')">@if($avatarPath)<img src="{{ asset('storage/'.$avatarPath) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else{{ mb_substr($user->prenom ?? 'C', 0, 1) }}@endif</button>
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
                <div class="stat-card s-blue dashboard-shortcut" data-target-section="candidatures" role="button" tabindex="0">
                    <div class="stat-icon-wrap"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-body">
                        <span class="stat-label">{{ __('Sent applications') }}</span>
                        <span class="stat-value">{{ $candidaturesCount ?? 0 }}</span>
                    </div>
                    @if(($newCandidaturesCount ?? 0) > 0)<span class="stat-badge">{{ $newCandidaturesCount }}</span>@endif
                </div>
                <div class="stat-card s-cyan dashboard-shortcut" data-target-section="profil-modern" role="button" tabindex="0">
                    <div class="stat-icon-wrap"><i class="fas fa-eye"></i></div>
                    <div class="stat-body">
                        <span class="stat-label">{{ __('Profile views') }}</span>
                        <span class="stat-value">{{ $profilVues ?? 0 }}</span>
                    </div>
                </div>
                <div class="stat-card s-green dashboard-shortcut" data-target-section="entretiens" role="button" tabindex="0">
                    <div class="stat-icon-wrap"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-body">
                        <span class="stat-label">{{ __('Interviews obtained') }}</span>
                        <span class="stat-value">{{ $entretiensCount ?? 0 }}</span>
                    </div>
                </div>
                <div class="stat-card s-orange dashboard-shortcut" data-target-section="offres" role="button" tabindex="0">
                    <div class="stat-icon-wrap"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-body">
                        <span class="stat-label">{{ __('Available offers') }}</span>
                        <span class="stat-value">{{ $offresCount ?? 0 }}</span>
                    </div>
                </div>
            </div>

            {{-- GRILLE : profil + activités récentes --}}
            <div class="main-grid">

                {{-- PROFIL CARD --}}
                <div class="profil-card dash-card dashboard-shortcut" data-target-section="profil-modern" role="button" tabindex="0">
                    <div class="profil-avatar-lg">@if($avatarPath)<img src="{{ asset('storage/'.$avatarPath) }}" alt="Photo" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else{{ mb_substr($user->prenom ?? 'C', 0, 1) }}@endif</div>
                    <h3>{{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</h3>
                    <p>{{ __('Candidate') }}</p>
                    <div class="profil-meta">
                        <span><i class="fas fa-envelope"></i> {{ $user->email ?? '' }}</span>
                        <span><i class="fas fa-phone"></i> {{ $user->telephone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="profil-completude">
                        <div class="completude-head">
                            <span>{{ __('Profile completed') }}</span>
                            <span>{{ $completude ?? 0 }}%</span>
                        </div>
                        <div class="completude-bar">
                            <div class="completude-fill" style="width: {{ $completude ?? 0 }}%"></div>
                        </div>
                    </div>
                    <button class="btn-primary btn-sm" type="button" onclick="event.stopPropagation(); switchSection('profil-modern')">
                        <i class="fas fa-edit"></i> {{ __('Complete my profile') }}
                    </button>
                </div>

                {{-- ACTIVITÉS RÉCENTES --}}
                <div class="dash-card">
                    <div class="card-head">
                        <h2><i class="fas fa-clock"></i> {{ __('Recent activities') }}</h2>
                        <button class="btn-outline btn-sm" onclick="switchSection('candidatures')">
                            {{ __('See all') }} <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    @if(isset($dernieresCandidatures) && $dernieresCandidatures->count() > 0)
                        <div class="cand-list">
                            @foreach($dernieresCandidatures as $cand)
                                <div class="cand-item">
                                    <div class="cand-avatar">
                                        {{ mb_substr($cand->offre->titre ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="cand-info">
                                        <strong>{{ $cand->offre->titre ?? 'Candidature spontanée' }}</strong>
                                        <span>{{ \Carbon\Carbon::parse($cand->date_candidature)->diffForHumans() }}</span>
                                    </div>
                                    <span class="badge-status badge-{{ strtolower(str_replace(' ', '-', $cand->statut)) }}">
                                        {{ $cand->statut }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <span>{{ __('No recent activity.') }}</span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- OFFRES RECOMMANDÉES --}}
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-star"></i> {{ __('Recommended offers') }}</h2>
                    <button class="btn-outline btn-sm" onclick="switchSection('offres')">
                        {{ __('See all') }} <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                @if(isset($offresRecommandees) && $offresRecommandees->count() > 0)
                    <div class="offres-grid">
                        @foreach($offresRecommandees as $offre)
                            <div class="offre-card">
                                <div class="offre-head">
                                    <div class="offre-logo">
                                        @if($offre->entreprise && $offre->entreprise->logo)
                                            <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                                        @else
                                            <i class="fas fa-building"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="offre-entreprise">{{ $offre->entreprise->nom ?? 'Entreprise' }}</span>
                                        <span class="offre-lieu"><i class="fas fa-map-marker-alt"></i> {{ $offre->lieu ?? 'Abidjan' }}</span>
                                    </div>
                                </div>
                                <h4 class="offre-titre">{{ $offre->titre }}</h4>
                                <p class="offre-desc">{{ Str::limit($offre->description, 80) }}</p>
                                <div class="offre-footer">
                                    <span class="offre-badge">{{ $offre->type_stage }}</span>
                                    @if(in_array($offre->id, $offresDejaCandidatees ?? []))
                                        <span class="badge-status badge-acceptee" style="font-size:11px;">
                                            <i class="fas fa-check"></i> Postulé
                                        </span>
                                    @else
                                        <a href="{{ route('candidatures.create', $offre->id) }}" class="btn-primary btn-sm">
                                            {{ __('Apply') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <span>{{ __('No offers available yet.') }}</span>
                    </div>
                @endif
            </div>

        </section>

        {{-- ===== SECTION OFFRES DISPONIBLES ===== --}}
        <section id="offres" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-search"></i> {{ __('Available offers') }}</h2>
                    <a href="{{ route('candidatures.create') }}" class="btn-outline btn-sm">
                        <i class="fas fa-paper-plane"></i> {{ __('Spontaneous application') }}
                    </a>
                </div>

                @if(isset($offresDisponibles) && $offresDisponibles->count() > 0)
                    <div class="offres-grid">
                        @foreach($offresDisponibles as $offre)
                            <div class="offre-card">
                                <div class="offre-head">
                                    <div class="offre-logo">
                                        @if($offre->entreprise && $offre->entreprise->logo)
                                            <img src="{{ asset('storage/'.$offre->entreprise->logo) }}" alt="">
                                        @else
                                            <i class="fas fa-building"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="offre-entreprise">{{ $offre->entreprise->nom ?? 'Entreprise' }}</span>
                                        <span class="offre-lieu"><i class="fas fa-map-marker-alt"></i> {{ $offre->lieu ?? 'Abidjan' }}</span>
                                    </div>
                                </div>
                                <h4 class="offre-titre">{{ $offre->titre }}</h4>
                                <p class="offre-desc">{{ Str::limit($offre->description, 100) }}</p>
                                <div class="offre-tags" style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <span class="offre-badge">{{ $offre->type_stage }}</span>
                                    <span class="offre-badge"><i class="fas fa-clock"></i> {{ $offre->duree }}</span>
                                    @if($offre->filiere_cible)
                                        <span class="offre-badge">{{ $offre->filiere_cible }}</span>
                                    @endif
                                </div>
                                @if($offre->date_fin_candidature)
                                    <p style="font-size:11px;color:var(--muted);">
                                        <i class="fas fa-hourglass-half"></i>
                                        Limite : {{ \Carbon\Carbon::parse($offre->date_fin_candidature)->format('d/m/Y') }}
                                    </p>
                                @endif
                                <div class="offre-footer">
                                    @if(in_array($offre->id, $offresDejaCandidatees ?? []))
                                        <span class="badge-status badge-acceptee" style="font-size:11px;">
                                            <i class="fas fa-check"></i> {{ __('Already applied') }}
                                        </span>
                                    @else
                                        <a href="{{ route('candidatures.create', $offre->id) }}" class="btn-primary btn-sm">
                                            <i class="fas fa-paper-plane"></i> {{ __('Apply') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <span>Aucune offre disponible pour le moment.</span>
                    </div>
                @endif
            </div>
        </section>

        {{-- ===== SECTION POSTULER (candidature spontanée) ===== --}}
        <section id="postuler" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-paper-plane"></i> Candidature spontanée</h2>
                </div>
                <p style="font-size:13.5px;color:var(--muted);margin-top:-8px;">
                    Aucune offre ne correspond à votre profil ? Déposez directement votre dossier auprès de l'entreprise.
                </p>

                {{-- Aperçu des entreprises disponibles --}}
                @if(isset($entreprises) && $entreprises->count() > 0)
                    <div style="display:flex;align-items:center;gap:16px;padding:16px;background:#EFF6FF;border-radius:var(--r-md);border:1.5px solid var(--border);">
                        <div style="width:52px;height:52px;border-radius:12px;background:white;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;border:1px solid var(--border);">
                            <i class="fas fa-building" style="color:var(--blue);font-size:20px;"></i>
                        </div>
                        <div>
                            <strong style="font-size:15px;color:var(--blue-dark);">{{ $entreprises->count() }} entreprise(s) disponible(s)</strong>
                            <p style="font-size:12px;color:var(--muted);margin:3px 0 0;">
                                Vous choisirez l'entreprise cible dans le formulaire.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Ce que le formulaire contiendra --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:1px solid var(--border);">
                        <i class="fas fa-tag" style="color:var(--blue);width:16px;"></i>
                        <span style="font-size:13px;color:var(--text);">Type de stage <span style="color:var(--danger);">*</span></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:1px solid var(--border);">
                        <i class="fas fa-clock" style="color:var(--blue);width:16px;"></i>
                        <span style="font-size:13px;color:var(--text);">Durée souhaitée <span style="color:var(--danger);">*</span></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:1px solid var(--border);">
                        <i class="fas fa-file-pdf" style="color:var(--danger);width:16px;"></i>
                        <span style="font-size:13px;color:var(--text);">CV <span style="color:var(--danger);">*</span></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:1px solid var(--border);">
                        <i class="fas fa-envelope-open-text" style="color:var(--blue);width:16px;"></i>
                        <span style="font-size:13px;color:var(--text);">Lettre de motivation <span style="color:var(--danger);">*</span></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:1px solid var(--border);">
                        <i class="fas fa-award" style="color:var(--green);width:16px;"></i>
                        <span style="font-size:13px;color:var(--muted);">Lettre de recommandation <em>(optionnel)</em></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:1px solid var(--border);">
                        <i class="fab fa-linkedin" style="color:#0A66C2;width:16px;"></i>
                        <span style="font-size:13px;color:var(--muted);">LinkedIn <em>(optionnel)</em></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--bg);border-radius:var(--r-sm);border:1px solid var(--border);grid-column:1/-1;">
                        <i class="fas fa-globe" style="color:var(--blue);width:16px;"></i>
                        <span style="font-size:13px;color:var(--muted);">Portfolio / GitHub <em>(optionnel)</em></span>
                    </div>
                </div>

                <a href="{{ route('candidatures.create') }}" class="btn-primary" style="align-self:flex-start;">
                    <i class="fas fa-paper-plane"></i> Remplir le formulaire de candidature
                </a>
            </div>
        </section>

        {{-- ===== SECTION MES CANDIDATURES ===== --}}
        <section id="candidatures" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-file-alt"></i> {{ __('My applications') }}</h2>
                    <span style="font-size:12px;color:var(--muted);background:#F3F6FB;padding:4px 12px;border-radius:20px;border:1px solid var(--border);">
                        {{ isset($mesCandidatures) ? $mesCandidatures->count() : 0 }} {{ __('application(s)') }}
                    </span>
                </div>

                @if(isset($mesCandidatures) && $mesCandidatures->count() > 0)
                    <div class="cand-list">
                        @foreach($mesCandidatures as $cand)
                            <div class="cand-item" style="padding:16px 0;flex-wrap:wrap;gap:10px;">
                                {{-- Avatar --}}
                                <div class="cand-avatar">
                                    {{ mb_substr($cand->offre->titre ?? 'S', 0, 1) }}
                                </div>

                                {{-- Infos principales --}}
                                <div class="cand-info" style="flex:1;min-width:180px;">
                                    <strong>{{ $cand->offre->titre ?? 'Candidature spontanée' }}</strong>
                                    <span>{{ $cand->type_stage }} — {{ $cand->duree }}</span>
                                    <span style="font-size:11px;color:var(--muted);">
                                        <i class="fas fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($cand->date_candidature)->format('d/m/Y') }}
                                    </span>
                                </div>

                                {{-- Score --}}
                                @if($cand->score)
                                    <div style="text-align:center;flex-shrink:0;">
                                        <span style="display:block;font-size:18px;font-weight:800;color:var(--blue);">
                                            {{ $cand->score }}<small style="font-size:11px;">/100</small>
                                        </span>
                                        <span style="font-size:10px;color:var(--muted);">Score IA</span>
                                    </div>
                                @endif

                                {{-- Statut --}}
                                <span class="badge-status badge-{{ strtolower(str_replace([' ', 'é', 'è'], ['-', 'e', 'e'], $cand->statut)) }}">
                                    {{ $cand->statut }}
                                </span>

                                {{-- Documents --}}
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    @if($cand->cv)
                                        <a href="{{ asset('storage/'.$cand->cv) }}" target="_blank"
                                           style="font-size:12px;color:var(--blue);text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#EFF6FF;border-radius:8px;">
                                            <i class="fas fa-file-pdf"></i> CV
                                        </a>
                                    @endif
                                    @if($cand->lettre_motivation)
                                        <a href="{{ asset('storage/'.$cand->lettre_motivation) }}" target="_blank"
                                           style="font-size:12px;color:var(--blue);text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#EFF6FF;border-radius:8px;">
                                            <i class="fas fa-envelope-open-text"></i> Lettre
                                        </a>
                                    @endif
                                    @if($cand->lettre_recommandation)
                                        <a href="{{ asset('storage/'.$cand->lettre_recommandation) }}" target="_blank"
                                           style="font-size:12px;color:var(--green);text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#F0FDF4;border-radius:8px;">
                                            <i class="fas fa-award"></i> Reco.
                                        </a>
                                    @endif
                                </div>

                                {{-- Commentaire RH / Entretien --}}
                                @if($cand->commentaire_rh)
                                    <div style="width:100%;background:#EFF6FF;border-radius:10px;padding:10px 14px;font-size:12.5px;color:var(--text);display:flex;gap:8px;align-items:flex-start;">
                                        <i class="fas fa-comment-dots" style="color:var(--blue);margin-top:2px;"></i>
                                        <span>{{ $cand->commentaire_rh }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <span>{{ __('No application sent yet.') }}</span>
                        <a href="{{ route('candidatures.create') }}" class="btn-primary btn-sm" style="margin-top:10px;">
                            <i class="fas fa-paper-plane"></i> {{ __('Apply now') }}
                        </a>
                    </div>
                @endif
            </div>
        </section>

        <section id="entretiens" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-calendar-check"></i> {{ __('My interviews') }}</h2>
                    <span class="offres-count">{{ $entretiensCount ?? 0 }} {{ __('interview(s)') }}</span>
                </div>
                @php $candidaturesAvecEntretien = ($mesCandidatures ?? collect())->filter(fn($cand) => $cand->dernierEntretien); @endphp
                @if($candidaturesAvecEntretien->count() > 0)
                    <div class="cand-list">
                        @foreach($candidaturesAvecEntretien as $cand)
                            @php $entretien = $cand->dernierEntretien; @endphp
                            <div class="cand-item" style="padding:16px 0;flex-wrap:wrap;gap:10px;">
                                <div class="cand-avatar"><i class="fas fa-calendar-check"></i></div>
                                <div class="cand-info" style="flex:1;min-width:220px;">
                                    <strong>{{ $cand->offre->titre ?? 'Candidature spontanée' }}</strong>
                                    <span>{{ $cand->entreprise->nom ?? 'Entreprise' }}</span>
                                    <span style="font-size:11px;color:var(--muted);">
                                        <i class="fas fa-clock"></i>
                                        {{ $entretien->date_entretien->format('d/m/Y') }} a {{ substr($entretien->heure, 0, 5) }}
                                    </span>
                                </div>
                                <span class="badge-status badge-entretien">{{ $entretien->type }}</span>
                                <div style="width:100%;background:#EFF6FF;border-radius:10px;padding:10px 14px;font-size:12.5px;color:var(--text);display:flex;gap:8px;align-items:flex-start;">
                                    <i class="fas fa-location-dot" style="color:var(--blue);margin-top:2px;"></i>
                                    <span>{{ $entretien->lieu }}@if($entretien->commentaires) - {{ $entretien->commentaires }}@endif</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state"><i class="fas fa-calendar"></i><span>{{ __('No interview scheduled yet.') }}</span></div>
                @endif
            </div>
        </section>

        {{-- ===== SECTIONS MODERNES CANDIDAT ===== --}}
        <section id="profil-modern" class="content-section">
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-user"></i> {{ __('My profile') }}</h2></div>
                <form action="{{ route('candidat.profil.update') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf @method('PUT')
                    <div class="profile-editor">
                        <div class="profile-photo-box">
                            @if(($candidat && $candidat->photo) || $user->photo)
                                <img src="{{ asset('storage/' . ($candidat->photo ?? $user->photo)) }}" alt="Photo">
                            @else
                                <span>{{ mb_substr($user->prenom ?? 'C', 0, 1) }}</span>
                            @endif
                            <label class="btn-outline btn-sm"><i class="fas fa-camera"></i> Modifier photo<input type="file" name="photo" accept="image/*" hidden></label>
                        </div>
                        <div class="form-grid">
                            <div class="form-group"><label>Nom</label><input name="nom" value="{{ old('nom', $user->nom) }}" required></div>
                            <div class="form-group"><label>Prenom</label><input name="prenom" value="{{ old('prenom', $user->prenom) }}" required></div>
                            <div class="form-group"><label>Telephone</label><input name="telephone" value="{{ old('telephone', $user->telephone ?? $candidat->telephone ?? '') }}" required></div>
                            <div class="form-group"><label>Date de naissance</label><input type="date" name="date_naissance" value="{{ old('date_naissance', $candidat?->date_naissance ? \Carbon\Carbon::parse($candidat->date_naissance)->format('Y-m-d') : $user->date_naissance) }}"></div>
                            <div class="form-group"><label>Filiere</label><input name="filiere" value="{{ old('filiere', $candidat->filiere ?? $user->filiere ?? '') }}"></div>
                            <div class="form-group"><label>Niveau</label><input name="niveau" value="{{ old('niveau', $candidat->niveau ?? $user->niveau ?? '') }}"></div>
                            <div class="form-group"><label>Adresse</label><input name="adresse" value="{{ old('adresse', $candidat->adresse ?? '') }}"></div>
                            <div class="form-group"><label>LinkedIn</label><input name="linkedin" value="{{ old('linkedin', $candidat->linkedin ?? '') }}"></div>
                            <div class="form-group"><label>GitHub</label><input name="github" value="{{ old('github', $candidat->github ?? '') }}"></div>
                            <div class="form-group"><label>Portfolio</label><input name="portfolio" value="{{ old('portfolio', $candidat->portfolio ?? '') }}"></div>
                            <div class="form-group"><label>CV principal</label><input type="file" name="cv" accept="application/pdf"></div>
                            <div class="form-group form-full"><label>Competences</label><textarea name="competences" rows="3">{{ old('competences', $candidat->competences ?? '') }}</textarea></div>
                            <div class="form-group form-full"><label>Experiences</label><textarea name="experiences" rows="3">{{ old('experiences', $candidat->experiences ?? '') }}</textarea></div>
                            <div class="form-group"><label>Langues</label><input name="langues" value="{{ old('langues', $candidat->langues ?? '') }}"></div>
                            <div class="form-group"><label>Certifications</label><input name="certifications" value="{{ old('certifications', $candidat->certifications ?? '') }}"></div>
                        </div>
                    </div>
                    <button class="btn-primary" type="submit"><i class="fas fa-save"></i> Enregistrer</button>
                </form>
            </div>
        </section>

        <section id="messages-modern" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-envelope"></i> Messages</h2>
                    <span class="offres-count">{{ $unreadMessagesCount ?? 0 }} {{ __('unread') }}</span>
                </div>
                <div class="messages-layout">
                    <div class="conversation-list">
                        @forelse($messages as $message)
                            @php $other = $message->sender_id === $user->id ? $message->receiver : $message->sender; @endphp
                            <div class="conversation-item {{ !$message->read_at && $message->receiver_id === $user->id ? 'is-unread' : '' }}">
                                <strong>{{ $other->prenom ?? 'Recruteur' }} {{ $other->nom ?? '' }}</strong>
                                <span>{{ Str::limit($message->body, 90) }}</span>
                                <small>{{ $message->created_at->format('d/m/Y H:i') }}</small>
                                @if($message->attachment)<a href="{{ asset('storage/'.$message->attachment) }}" target="_blank">Piece jointe</a>@endif
                                <button type="button" class="btn-outline btn-sm reply-message-btn" data-receiver-id="{{ $other->id }}" data-receiver-name="{{ $other->prenom ?? 'Recruteur' }} {{ $other->nom ?? '' }}">Répondre</button>
                            </div>
                        @empty
                            <div class="empty-state"><i class="fas fa-envelope-open"></i><span>{{ __('No conversation.') }}</span></div>
                        @endforelse
                    </div>
                    <form action="{{ route('candidat.messages.send') }}" method="POST" enctype="multipart/form-data" class="message-compose">
                        @csrf
                        <div class="form-group"><label>Recruteur</label><select name="receiver_id" required><option value="">Choisir</option>@foreach($recruteurs as $recruteur)<option value="{{ $recruteur->id }}">{{ $recruteur->prenom }} {{ $recruteur->nom }}</option>@endforeach</select></div>
                        <div class="form-group"><label>Message</label><textarea name="body" rows="6" required></textarea></div>
                        <div class="form-group"><label>Piece jointe</label><input type="file" name="attachment"></div>
                        <button class="btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
                    </form>
                </div>
            </div>
        </section>

        <section id="notifications-modern" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-bell"></i> Notifications</h2>
                    <div class="filter-pills" data-filter-group="notifications">
                        <button class="filter-pill active" type="button" data-filter="all">Toutes</button>
                        <button class="filter-pill" type="button" data-filter="candidature">Candidatures</button>
                        <button class="filter-pill" type="button" data-filter="entretien">Entretiens</button>
                        <button class="filter-pill" type="button" data-filter="message">Messages</button>
                    </div>
                </div>
                <div class="notification-list">
                    @forelse($notifications as $notification)
                        @php
                            $notificationType = Str::contains(Str::lower($notification->message), 'entretien') ? 'entretien' : (Str::contains(Str::lower($notification->message), 'message') ? 'message' : 'candidature');
                            $notificationIcon = ['candidature'=>'fa-file-lines','entretien'=>'fa-calendar-check','message'=>'fa-envelope'][$notificationType] ?? 'fa-bell';
                        @endphp
                        <div class="notification-item notification-card {{ $notification->lu ? '' : 'is-unread' }}" data-type="{{ $notificationType }}">
                            <div class="notification-icon notification-{{ $notificationType }}"><i class="fas {{ $notificationIcon }}"></i></div>
                            <div class="notification-content">
                                <strong>{{ $notification->lu ? 'Notification lue' : 'Nouvelle notification' }}</strong>
                                <span>{{ $notification->message }}</span>
                                <time>{{ \Carbon\Carbon::parse($notification->date_envoi)->diffForHumans() }}</time>
                            </div>
                            <form action="{{ route('candidat.notifications.toggle', $notification->id) }}" method="POST" class="notification-action">@csrf @method('PATCH')<button class="btn-outline btn-sm">{{ $notification->lu ? 'Marquer non lue' : 'Marquer lue' }}</button></form>
                        </div>
                    @empty
                        <div class="empty-state"><i class="fas fa-bell-slash"></i><span>Aucune notification.</span></div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="parametres-modern" class="content-section">
            @php $settings = $user->settings ?? []; $notifs = $settings['notifications'] ?? []; $privacy = $settings['privacy'] ?? []; @endphp
            <div class="settings-shell">
                <aside class="settings-nav">
                    @foreach(['compte'=>'My account','profil'=>'My profile','cv'=>'Default resume','documents'=>'Documents','securite'=>'Security','notifications'=>'Notifications','apparence'=>'Appearance','langue'=>'Language','confidentialite'=>'Privacy','export'=>'Export my data','danger'=>'Delete account'] as $key => $label)
                        <button type="button" class="settings-tab {{ $loop->first ? 'active' : '' }}" data-settings-tab="{{ $key }}">{{ __($label) }}</button>
                    @endforeach
                </aside>
                <div class="settings-panels">
                    <div class="settings-panel active" data-settings-panel="compte"><h2>{{ __('My account') }}</h2><div class="setting-item"><div class="setting-info"><strong>{{ __('Email') }}</strong><span>{{ $user->email }}</span></div></div></div>
                    <div class="settings-panel" data-settings-panel="profil"><h2>{{ __('My profile') }}</h2><button class="btn-primary" type="button" onclick="switchSection('profil-modern')"><i class="fas fa-user-edit"></i> {{ __('Edit my profile') }}</button></div>
                    <div class="settings-panel" data-settings-panel="cv"><h2>{{ __('Default resume') }}</h2><form action="{{ route('candidat.preferences.update') }}" method="POST" class="settings-form">@csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}"><label class="toggle-row"><input type="checkbox" name="use_default_cv" value="1" {{ $candidat?->use_default_cv ? 'checked' : '' }}> {{ __('Use this resume automatically when applying') }}</label><button class="btn-primary">{{ __('Save') }}</button></form></div>
                    <div class="settings-panel" data-settings-panel="documents"><h2>Documents</h2><form action="{{ route('candidat.documents.store') }}" method="POST" enctype="multipart/form-data" class="document-upload">@csrf<select name="type_document" required><option value="cv">CV</option><option value="lettre_motivation">Lettre de motivation</option><option value="lettre_recommandation">Lettre de recommandation</option></select><input type="file" name="document" required><label><input type="checkbox" name="is_default" value="1"> CV principal</label><button class="btn-primary btn-sm">Ajouter</button></form><div class="document-list">@foreach($documents as $document)<div class="document-item"><span><i class="fas fa-file-alt"></i> {{ $document->nom_fichier }} {{ $document->is_default ? '- CV principal' : '' }}</span><div>@if($document->type_document === 'cv' && !$document->is_default)<form action="{{ route('candidat.documents.default', $document) }}" method="POST">@csrf @method('PATCH')<button class="btn-outline btn-sm">Definir</button></form>@endif<a href="{{ asset('storage/'.$document->chemin) }}" target="_blank" class="btn-outline btn-sm">Voir</a><form action="{{ route('candidat.documents.destroy', $document) }}" method="POST">@csrf @method('DELETE')<button class="btn-danger btn-sm">Supprimer</button></form></div></div>@endforeach</div></div>
                    <div class="settings-panel" data-settings-panel="securite">
                        <h2>{{ __('Security') }}</h2>
                        <form action="{{ route('candidat.password.update') }}" method="POST" class="form-grid">@csrf @method('PUT')<div class="form-group"><label>{{ __('Current password') }}</label><input type="password" name="current_password" required></div><div class="form-group"><label>{{ __('New password') }}</label><input type="password" name="password" required></div><div class="form-group"><label>{{ __('Confirmation') }}</label><input type="password" name="password_confirmation" required></div><button class="btn-primary">{{ __('Change') }}</button></form>
                        <form action="{{ route('candidat.preferences.update') }}" method="POST" class="settings-form">
                            @csrf @method('PUT')
                            <input type="hidden" name="two_factor_form" value="1">
                            <input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}">
                            <input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}">
                            <h2>{{ __('Two-factor authentication') }}</h2>
                            <label class="toggle-row"><input type="checkbox" name="two_factor_enabled" value="1" {{ $user->two_factor_enabled_at ? 'checked' : '' }}> {{ __('Enable two-factor authentication') }}</label>
                            <div class="settings-list">
                                <label class="setting-item"><span><strong>{{ __('Email') }}</strong><br><small>{{ $user->email }}</small></span><input type="radio" name="two_factor_method" value="email" {{ ($user->two_factor_method ?? 'email') === 'email' ? 'checked' : '' }}></label>
                                <div class="setting-item disabled-option"><span><strong>SMS</strong><br><small>{{ __('Coming soon') }}</small></span></div>
                                <label class="setting-item"><span><strong>Application d'authentification</strong><br><small>Google Authenticator, Microsoft Authenticator, Authy</small></span><input type="radio" name="two_factor_method" value="app" {{ $user->two_factor_method === 'app' || $user->two_factor_pending_secret ? 'checked' : '' }}></label>
                            </div>
                            <button class="btn-primary">{{ __('Save 2FA') }}</button>
                        </form>
                        @if($twoFactorAppSetup)
                            <div class="totp-setup-box">
                                <div class="totp-qr">{!! $twoFactorAppSetup['qr_svg'] !!}</div>
                                <div class="totp-details">
                                    <h3>{{ __('Configure the application') }}</h3>
                                    <p>{{ __('Scan this QR Code with Google Authenticator, Microsoft Authenticator or Authy, then enter the 6-digit code.') }}</p>
                                    <label>{{ __('Secret key') }}</label>
                                    <code>{{ $twoFactorAppSetup['secret'] }}</code>
                                    <form action="{{ route('two-factor.app.confirm') }}" method="POST" class="settings-form">
                                        @csrf
                                        <div class="form-group"><label>{{ __('Validation code') }}</label><input name="code" inputmode="numeric" maxlength="6" required></div>
                                        <button class="btn-primary" type="submit"><i class="fas fa-shield-halved"></i> {{ __('Enable the application') }}</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                        @if($user->two_factor_enabled_at)
                            <form action="{{ route('two-factor.disable') }}" method="POST" class="settings-form">
                                @csrf @method('DELETE')
                                <button class="btn-outline" type="submit"><i class="fas fa-lock-open"></i> {{ __('Disable two-factor authentication') }}</button>
                            </form>
                        @endif
                    </div>
                    <form class="settings-panel settings-form" data-settings-panel="notifications" action="{{ route('candidat.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}"><h2>{{ __('Notifications') }}</h2><label class="toggle-row"><input type="checkbox" name="notif_candidature_acceptee" value="1" {{ ($notifs['candidature_acceptee'] ?? true) ? 'checked' : '' }}> {{ __('Application accepted') }}</label><label class="toggle-row"><input type="checkbox" name="notif_candidature_refusee" value="1" {{ ($notifs['candidature_refusee'] ?? true) ? 'checked' : '' }}> {{ __('Application rejected') }}</label><label class="toggle-row"><input type="checkbox" name="notif_entretien" value="1" {{ ($notifs['entretien'] ?? true) ? 'checked' : '' }}> {{ __('Interview scheduled') }}</label><label class="toggle-row"><input type="checkbox" name="notif_message" value="1" {{ ($notifs['message'] ?? true) ? 'checked' : '' }}> {{ __('New message') }}</label><button class="btn-primary">{{ __('Save') }}</button></form>
                    <form class="settings-panel settings-form" data-settings-panel="apparence" action="{{ route('candidat.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}"><h2>{{ __('Appearance') }}</h2><div class="segmented"><label><input type="radio" name="theme" value="light" {{ ($settings['theme'] ?? 'light') === 'light' ? 'checked' : '' }}> {{ __('Light mode') }}</label><label><input type="radio" name="theme" value="dark" {{ ($settings['theme'] ?? '') === 'dark' ? 'checked' : '' }}> {{ __('Dark mode') }}</label></div><button class="btn-primary">{{ __('Apply') }}</button></form>
                    <form class="settings-panel settings-form" data-settings-panel="langue" action="{{ route('candidat.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><h2>{{ __('Language') }}</h2><div class="segmented"><label><input type="radio" name="language" value="fr" onchange="this.form.submit()" {{ ($settings['language'] ?? app()->getLocale()) === 'fr' ? 'checked' : '' }}> Français</label><label><input type="radio" name="language" value="en" onchange="this.form.submit()" {{ ($settings['language'] ?? app()->getLocale()) === 'en' ? 'checked' : '' }}> English</label></div><button class="btn-primary">{{ __('Apply') }}</button></form>
                    <form class="settings-panel settings-form" data-settings-panel="confidentialite" action="{{ route('candidat.preferences.update') }}" method="POST">@csrf @method('PUT')<input type="hidden" name="theme" value="{{ $settings['theme'] ?? 'light' }}"><input type="hidden" name="language" value="{{ $settings['language'] ?? app()->getLocale() }}"><h2>{{ __('Privacy') }}</h2><label class="toggle-row"><input type="checkbox" name="profile_public" value="1" {{ ($privacy['profile_public'] ?? true) ? 'checked' : '' }}> {{ __('Profile visible to recruiters') }}</label><button class="btn-primary">{{ __('Save') }}</button></form>
                    <div class="settings-panel" data-settings-panel="export"><h2>Exporter mes donnees</h2><a class="btn-outline" href="{{ route('candidatures.mes') }}">Exporter mes candidatures</a></div>
                    <div class="settings-panel danger-zone" data-settings-panel="danger"><h2>Supprimer mon compte</h2><p>Action sensible reservee a une confirmation administrative.</p><button class="btn-danger" type="button">Demander la suppression</button></div>
                </div>
            </div>
        </section>

        {{-- ===== SECTION PROFIL ===== --}}
        <section id="profil" class="content-section">
            <div class="dash-card">
                <div class="card-head">
                    <h2><i class="fas fa-user"></i> Mon profil</h2>
                </div>
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
                            <strong>Téléphone</strong>
                            <span>{{ $user->telephone ?? 'Non renseigné' }}</span>
                        </div>
                        <button class="btn-outline btn-sm"><i class="fas fa-edit"></i> Modifier</button>
                    </div>
                    <div class="setting-item">
                        <div class="setting-info">
                            <strong>Filière</strong>
                            <span>{{ $candidat->filiere ?? 'Non renseigné' }}</span>
                        </div>
                    </div>
                    <div class="setting-item">
                        <div class="setting-info">
                            <strong>Niveau</strong>
                            <span>{{ $candidat->niveau ?? 'Non renseigné' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== SECTIONS VIDES ===== --}}
        <section id="messages" class="content-section">
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-envelope"></i> Messages</h2></div>
                <div class="empty-state"><i class="fas fa-envelope-open"></i><span>Aucun message pour le moment.</span></div>
            </div>
        </section>

        <section id="notifications" class="content-section">
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-bell"></i> Notifications</h2></div>
                <div class="empty-state"><i class="fas fa-bell-slash"></i><span>Aucune notification.</span></div>
            </div>
        </section>

        <section id="parametres" class="content-section">
            <div class="dash-card">
                <div class="card-head"><h2><i class="fas fa-cog"></i> Paramètres</h2></div>
                <div class="settings-list">
                    <div class="setting-item">
                        <div class="setting-info"><strong>Mot de passe</strong><span>••••••••</span></div>
                        <button class="btn-outline btn-sm"><i class="fas fa-lock"></i> Changer</button>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>
<script src="{{ asset('js/dashboard-candidat.js') }}"></script>
</body>
</html>
