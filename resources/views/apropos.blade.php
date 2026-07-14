<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | À propos</title>
    <meta name="description" content="Découvrez l'histoire, la mission et les valeurs de StageLink.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/apropos.css') }}">
</head>

<body>

    {{-- ===== HEADER ===== --}}
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
                <li><a href="/">Accueil</a></li>
                <li><a href="/#about">Pourquoi StageLink</a></li>
                <li><a href="/a-propos" class="active">À propos</a></li>
                <li><a href="/contact">Contact</a></li>
                <li><a href="/connexion">Connexion</a></li>
                <li><a href="/choix-profil" class="nav-cta">S'inscrire</a></li>
            </ul>
        </nav>
    </header>

    <main>

        {{-- ===== HERO À PROPOS ===== --}}
        <section class="apropos-hero">
            <div class="hero-glow glow-one"></div>
            <div class="hero-glow glow-two"></div>

            <div class="apropos-hero-content reveal">
                <span class="badge">À propos de nous</span>
                <h1>Nous connectons les talents<br>aux opportunités.</h1>
                <p>
                    StageLink est née d'un constat simple : trouver un stage ne devrait pas
                    être un parcours du combattant. Nous avons créé une plateforme humaine,
                    intelligente et accessible pour tous.
                </p>
            </div>
        </section>

        {{-- ===== NOTRE HISTOIRE ===== --}}
        <section class="histoire section-band">
            <div class="histoire-grid reveal">
                <div class="histoire-text">
                    <span class="eyebrow">Notre histoire</span>
                    <h2>Comment StageLink est né</h2>
                    <p>
                        En 2026, face aux difficultés que rencontrent les étudiants pour trouver
                        un stage et les entreprises pour identifier les bons profils, nous avons
                        décidé de créer StageLink.
                    </p>
                    <p>
                        Notre équipe a développé une plateforme simple et efficace qui met en
                        relation les étudiants, les candidats et les recruteurs autour d'une
                        expérience fluide et transparente.
                    </p>
                    <p>
                        Aujourd'hui, StageLink accompagne des étudiants et
                        d'entreprises à travers la Côte d'Ivoire et ambitionne de devenir
                        la référence en Afrique.
                    </p>
                </div>
                <div class="histoire-visuel">
                    <div class="histoire-card">
                        <div class="hcard-stat">
                            <h3>2026</h3>
                            <span>Année de création</span>
                        </div>
                        <div class="hcard-stat">
                            <h3>0+</h3>
                            <span>Utilisateurs actifs</span>
                        </div>
                        <div class="hcard-stat">
                            <h3>0+</h3>
                            <span>Entreprises partenaires</span>
                        </div>
                        <div class="hcard-stat">
                            <h3>🇨🇮</h3>
                            <span>Basé en Côte d'Ivoire</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== MISSION / VISION / VALEURS ===== --}}
        <section class="mvv section-band">
            <div class="section-heading reveal">
                <span class="eyebrow">Ce qui nous guide</span>
                <h2>Mission, Vision & Valeurs</h2>
            </div>

            <div class="mvv-grid">
                <div class="mvv-card reveal">
                    
                    <h3>Notre mission</h3>
                    <p>
                        Connecter les étudiants et candidats aux meilleures opportunités
                        de stage, et aider les entreprises à identifier rapidement
                        les talents dont elles ont besoin.
                    </p>
                </div>
                <div class="mvv-card reveal">
                    
                    <h3>Notre vision</h3>
                    <p>
                        Devenir la plateforme de référence pour la gestion des stages
                        en Afrique, en rendant le processus simple, transparent
                        et accessible à tous.
                    </p>
                </div>
                <div class="mvv-card reveal">
                    
                    <h3>Nos valeurs</h3>
                    <p>
                        Transparence, innovation, bienveillance et inclusion sont
                        au cœur de tout ce que nous construisons pour notre communauté.
                    </p>
                </div>
            </div>
        </section>

        {{-- ===== POURQUOI NOUS CHOISIR ===== --}}
        <section class="pourquoi section-band">
            <div class="section-heading reveal">
                <span class="eyebrow">Nos engagements</span>
                <h2>Pourquoi choisir StageLink ?</h2>
            </div>

            <div class="pourquoi-grid">
                <div class="pourquoi-item reveal">
                    <span class="pourquoi-num">01</span>
                    <div>
                        <h4>Simple et rapide</h4>
                        <p>Créez votre profil en quelques minutes et accédez immédiatement aux opportunités.</p>
                    </div>
                </div>
                <div class="pourquoi-item reveal">
                    <span class="pourquoi-num">02</span>
                    <div>
                        <h4>Profils vérifiés</h4>
                        <p>Chaque compte est vérifié pour garantir des interactions de qualité et de confiance.</p>
                    </div>
                </div>
                <div class="pourquoi-item reveal">
                    <span class="pourquoi-num">03</span>
                    <div>
                        <h4>Suivi en temps réel</h4>
                        <p>Suivez vos candidatures et recevez des notifications à chaque étape.</p>
                    </div>
                </div>
                <div class="pourquoi-item reveal">
                    <span class="pourquoi-num">04</span>
                    <div>
                        <h4>Matching intelligent</h4>
                        <p>Notre système rapproche automatiquement les profils des offres qui leur correspondent.</p>
                    </div>
                </div>
                <div class="pourquoi-item reveal">
                    <span class="pourquoi-num">05</span>
                    <div>
                        <h4>100% gratuit</h4>
                        <p>StageLink est entièrement gratuit pour les candidats, sans abonnement caché.</p>
                    </div>
                </div>
                <div class="pourquoi-item reveal">
                    <span class="pourquoi-num">06</span>
                    <div>
                        <h4>Support dédié</h4>
                        <p>Une équipe disponible pour vous accompagner à chaque étape de votre parcours.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== CTA FINAL ===== --}}
        <section class="cta-apropos reveal">
            <div>
                <span class="eyebrow">Rejoignez-nous</span>
                <h2>Prêt à faire partie de l'aventure ?</h2>
                <p>Créez votre compte gratuitement et commencez dès aujourd'hui.</p>
            </div>
            <div class="cta-buttons">
                <a href="/choix-profil" class="btn btn-primary">Créer mon compte</a>
                <a href="/contact" class="btn btn-outline">Nous contacter</a>
            </div>
        </section>

    </main>

    {{-- ===== FOOTER ===== --}}
    <footer>
        <div class="footer-inner">
            <div>
                <strong>StageLink</strong>
                <p>Connecter les talents aux bonnes opportunités.</p>
            </div>
            <div class="footer-links">
                <a href="/">Accueil</a>
                <a href="/#about">Pourquoi StageLink</a>
                <a href="/a-propos">À propos</a>
                <a href="/contact">Contact</a>
                <a href="/connexion">Connexion</a>
            </div>
        </div>
        <p class="footer-copy">© 2026 StageLink - Tous droits réservés</p>
    </footer>

    <script src="{{ asset('js/apropos.js') }}"></script>
</body>

</html>