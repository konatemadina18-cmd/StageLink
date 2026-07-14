<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Trouvez le stage qui vous ressemble</title>
    <meta name="description" content="StageLink connecte les étudiants, les entreprises et les recruteurs autour d'une gestion de stages simple, moderne et intelligente.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/accueil.css') }}">
</head>

<body>
    <div class="page-shell">
        {{-- Header principal avec logo, menu desktop et bouton mobile --}}
        <header class="site-header">
            <nav class="navbar" aria-label="Navigation principale">
                <a class="logo" href="/" aria-label="StageLink - Accueil">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="">
                    <span>StageLink</span>
                </a>

                <button class="menu-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <ul class="nav-links">
                    <li><a href="/" class="active">Accueil</a></li>
                    <li><a href="#about">Pourquoi StageLink</a></li>
                    <li><a href="/a-propos">À propos</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/connexion">Connexion</a></li> 
                    <li><a href="/choix-profil" class="nav-cta">S'inscrire</a></li>
                </ul>
            </nav>
        </header>

        <main>
            {{-- Hero: premiere zone visible avec message principal et apercu visuel --}}
            <section class="hero" aria-labelledby="hero-title">
                <div class="hero-glow hero-glow-one"></div>
                <div class="hero-glow hero-glow-two"></div>

                <div class="hero-content reveal">
                    <span class="badge">Plateforme intelligente de gestion des stages</span>
                    <h1 id="hero-title">Connecter les talents, créer l'avenir.</h1>
                    <p>
                        StageLink facilite la recherche de stage: un profil clair, des offres adaptées
                        et un suivi simple des candidatures.
                    </p>

                    <div class="buttons" aria-label="Actions principales">
                        <a href="/choix-profil" class="btn btn-primary">Commencer maintenant</a>
                        <a href="#about" class="btn btn-secondary">Découvrir</a>
                    </div>

                    <div class="trust-row" aria-label="Points forts">
                        <span>Profils vérifiés</span>
                        <span>Suivi en temps réel</span>
                        <span>Matching intelligent</span>
                    </div>
                </div>

                <div class="hero-panel reveal" aria-label="Aperçu de candidature StageLink">
                    {{-- Carte decorative qui simule un tableau de matching --}}
                    <div class="panel-top">
                        <span class="panel-dot"></span>
                        <span class="panel-dot"></span>
                        <span class="panel-dot"></span>
                    </div>
                    <div class="profile-card">
                        <div>
                            <span class="profile-label">Candidature recommandée</span>
                            <h2>Assistant Développeur Web</h2>
                            <p>Score de compatibilité élevé avec le profil étudiant.</p>
                        </div>
                        <strong>94%</strong>
                    </div>
                    <div class="progress-list">
                        <div class="progress-item">
                            <span>CV analysé</span>
                            <div><i style="width: 92%"></i></div>
                        </div>
                        <div class="progress-item">
                            <span>Compétences alignées</span>
                            <div><i style="width: 84%"></i></div>
                        </div>
                        <div class="progress-item">
                            <span>Disponibilité confirmée</span>
                            <div><i style="width: 76%"></i></div>
                        </div>
                    </div>
                    <div class="floating-note note-one">Entretien proposé</div>
                    <div class="floating-note note-two">Profil prioritaire</div>
                </div>
            </section>

            {{-- Chiffres cles animes par accueil.js --}}
            <section class="stats" aria-label="Statistiques StageLink">
                <div class="stat-box reveal">
                    <h2><span class="counter" data-target="0">0</span>+</h2>
                    <p>Étudiants accompagnés</p>
                </div>
                <div class="stat-box reveal">
                    <h2><span class="counter" data-target="0">0</span>+</h2>
                    <p>Entreprises partenaires</p>
                </div>
                <div class="stat-box reveal">
                    <h2><span class="counter" data-target="0">0</span>+</h2>
                    <p>Candidatures suivies</p>
                </div>
            </section>

            {{-- Section compacte: pourquoi choisir StageLink et comment ca marche --}}
            <section id="about" class="about section-band">
                <div class="section-heading reveal">
                    <span class="eyebrow">Pourquoi StageLink ?</span>
                    <h2>Une plateforme simple pour passer du profil à l'opportunité.</h2>
                    <p>
                        Créez votre profil, trouvez des offres adaptées et suivez vos candidatures au même endroit.
                    </p>
                </div>

                <div id="features" class="cards">
                    {{-- Chaque carte a 2 faces: devant = pourquoi, derriere = comment ca marche --}}
                    <article class="flip-card reveal" tabindex="0">
                        <div class="flip-card-inner">
                            <div class="card card-front">
                                <span class="card-kicker">Pourquoi StageLink ?</span>
                                <span class="card-icon">01</span>
                                <h3>Un profil valorisé</h3>
                                <p>Vos compétences, expériences et disponibilités sont présentées clairement.</p>
                            </div>
                            <div class="card card-back">
                                <span class="card-kicker">Comment ça marche ?</span>
                                <span class="card-icon">01</span>
                                <h3>Créez votre profil</h3>
                                <p>Ajoutez vos informations essentielles pour permettre aux recruteurs de mieux vous comprendre.</p>
                            </div>
                        </div>
                    </article>

                    <article class="flip-card reveal" tabindex="0">
                        <div class="flip-card-inner">
                            <div class="card card-front">
                                <span class="card-kicker">Pourquoi StageLink ?</span>
                                <span class="card-icon">02</span>
                                <h3>Des offres plus pertinentes</h3>
                                <p>La plateforme aide à rapprocher votre parcours des stages qui vous correspondent.</p>
                            </div>
                            <div class="card card-back">
                                <span class="card-kicker">Comment ça marche ?</span>
                                <span class="card-icon">02</span>
                                <h3>Trouvez les bonnes offres</h3>
                                <p>Explorez les opportunités adaptées et postulez plus rapidement aux stages intéressants.</p>
                            </div>
                        </div>
                    </article>

                    <article class="flip-card reveal" tabindex="0">
                        <div class="flip-card-inner">
                            <div class="card card-front">
                                <span class="card-kicker">Pourquoi StageLink ?</span>
                                <span class="card-icon">03</span>
                                <h3>Un suivi plus clair</h3>
                                <p>Vous gardez une vision simple de vos candidatures, réponses et prochaines étapes.</p>
                            </div>
                            <div class="card card-back">
                                <span class="card-kicker">Comment ça marche ?</span>
                                <span class="card-icon">03</span>
                                <h3>Suivez vos candidatures</h3>
                                <p>Consultez l'état de vos demandes et avancez sans perdre le fil du processus.</p>
                            </div>
                        </div>
                    </article>
                </div>
            </section>
            {{-- Dernier appel a l'action avant le footer --}}
            <section class="cta reveal">
                <div>
                    <span class="eyebrow">Prêt à avancer ?</span>
                    <h2>Votre prochain stage peut commencer ici.</h2>
                    <p>Rejoignez StageLink et donnez plus de visibilité à votre profil dès aujourd'hui.</p>
                </div>
                <a href="/choix-profil" class="btn btn-light">Créer mon compte</a>
            </section>
        </main>

        <footer>
            <div class="footer-inner">
                <div>
                    <strong>StageLink</strong>
                    <p>Connecter les talents aux bonnes opportunités.</p>
                </div>
                <div class="footer-links">
                    <a href="/">Accueil</a>
                    <a href="#about">Pourquoi StageLink</a>
                    <a href="#apropos">À propos</a>
                    <a href="#contact">Contact</a>
                    <a href="/connexion">Connexion</a>
                </div>
            </div>
            <p class="footer-copy">© 2026 StageLink - Tous droits réservés</p>
        </footer>
    </div>

    <script src="{{ asset('js/accueil.js') }}"></script>
</body>

</html>