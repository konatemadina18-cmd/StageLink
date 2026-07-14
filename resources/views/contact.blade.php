<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageLink | Contact</title>
    <meta name="description" content="Contactez l'équipe StageLink pour toute question ou demande d'information.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
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
                <li><a href="/a-propos">À propos</a></li>
                <li><a href="/contact" class="active">Contact</a></li>
                <li><a href="/connexion">Connexion</a></li>
                <li><a href="/choix-profil" class="nav-cta">S'inscrire</a></li>
            </ul>
        </nav>
    </header>

    <main>

        {{-- ===== HERO CONTACT ===== --}}
        <section class="contact-hero">
            <div class="hero-glow glow-one"></div>
            <div class="hero-glow glow-two"></div>

            <div class="contact-hero-content reveal">
                <span class="badge">Contactez-nous</span>
                <h1>Une question ?<br>On est là pour vous.</h1>
                <p>
                    Notre équipe est disponible pour répondre à toutes vos questions,
                    que vous soyez candidat, recruteur ou entreprise partenaire.
                </p>
            </div>
        </section>

        {{-- ===== INFOS + FORMULAIRE ===== --}}
        <section class="contact-main section-band">
            <div class="contact-grid">

                {{-- Infos de contact --}}
                <div class="contact-infos reveal">
                    <h2>Nos coordonnées</h2>
                    <p class="contact-intro">
                        Vous pouvez nous joindre par email, téléphone ou directement
                        via le formulaire. Nous vous répondons sous 24h.
                    </p>

                    <div class="contact-items">
                        <div class="contact-item">
                            <div class="contact-icon-wrap"><span>📧</span></div>
                            <div class="contact-item-text">
                                <strong>Email</strong>
                                <a href="mailto:contactstagelink@gmail.com">contactstagelink@gmail.com</a>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon-wrap"><span>📞</span></div>
                            <div class="contact-item-text">
                                <strong>Téléphone</strong>
                                <a href="tel:+2250719191974">+225 07 19 19 19 74</a>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon-wrap"><span>📍</span></div>
                            <div class="contact-item-text">
                                <strong>Adresse</strong>
                                <span>Plateau, Abidjan, Côte d'Ivoire</span>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon-wrap"><span>🕐</span></div>
                            <div class="contact-item-text">
                                <strong>Horaires</strong>
                                <span>Lun – Ven, 8h – 18h</span>
                            </div>
                        </div>
                    </div>

                    {{-- Réseaux sociaux --}}
                    <div class="socials">
                        <p>Suivez-nous</p>
                        <div class="social-links">
                            <a href="#" class="social-btn" aria-label="LinkedIn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/>
                                    <circle cx="4" cy="4" r="2"/>
                                </svg>
                                LinkedIn
                            </a>
                            <a href="#" class="social-btn" aria-label="Twitter">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                                </svg>
                                Twitter
                            </a>
                            <a href="#" class="social-btn" aria-label="Instagram">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                                    <circle cx="12" cy="12" r="4"/>
                                    <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                                </svg>
                                Instagram
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Formulaire --}}
                <div class="contact-form-wrap reveal">
                    <div class="contact-card">
                        <h2>Envoyez-nous un message</h2>
                        <p class="form-subtitle">Remplissez le formulaire et nous vous répondrons rapidement.</p>

                        <form id="contactForm" method="POST" action="{{ route('contact.store') }}" novalidate>
                            @csrf

                            {{-- Message succès après envoi --}}
                            @if(session('success'))
                                <div class="success-msg visible">{{ session('success') }}</div>
                            @endif

                            {{-- Nom / Prénom --}}
                            <div class="row-2">
                                <div class="input-group">
                                    <label for="nom">Nom <span class="required">*</span></label>
                                    <input type="text" id="nom" name="nom"
                                        placeholder="Konate"
                                        value="{{ old('nom') }}" required>
                                    @error('nom')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input-group">
                                    <label for="prenom">Prénom <span class="required">*</span></label>
                                    <input type="text" id="prenom" name="prenom"
                                        placeholder="Madina"
                                        value="{{ old('prenom') }}" required>
                                    @error('prenom')
                                        <span class="field-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="input-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email"
                                    placeholder="madina.konate@email.com"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Sujet --}}
                            <div class="input-group">
                                <label for="sujet">Sujet <span class="required">*</span></label>
                                <div class="select-wrap">
                                    <select id="sujet" name="sujet" required>
                                        <option value="" disabled {{ old('sujet') ? '' : 'selected' }}>Choisissez un sujet</option>
                                        <option value="info"        {{ old('sujet') == 'info'        ? 'selected' : '' }}>Demande d'information</option>
                                        <option value="candidat"    {{ old('sujet') == 'candidat'    ? 'selected' : '' }}>Question candidat</option>
                                        <option value="recruteur"   {{ old('sujet') == 'recruteur'   ? 'selected' : '' }}>Question recruteur</option>
                                        <option value="partenariat" {{ old('sujet') == 'partenariat' ? 'selected' : '' }}>Partenariat</option>
                                        <option value="bug"         {{ old('sujet') == 'bug'         ? 'selected' : '' }}>Signaler un problème</option>
                                        <option value="autre"       {{ old('sujet') == 'autre'       ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    <span class="select-arrow" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 9l6 6 6-6"/>
                                        </svg>
                                    </span>
                                </div>
                                @error('sujet')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Message --}}
                            <div class="input-group">
                                <label for="message">Message <span class="required">*</span></label>
                                <textarea id="message" name="message" rows="5"
                                    placeholder="Décrivez votre demande en détail…"
                                    required>{{ old('message') }}</textarea>
                                <span class="char-count" id="charCount">0 / 1000</span>
                                @error('message')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Bouton --}}
                            <button type="submit" class="submit-btn" id="submitBtn">
                                Envoyer le message →
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </section>

        {{-- ===== FAQ RAPIDE ===== --}}
        <section class="faq section-band">
            <div class="section-heading reveal">
                <span class="eyebrow">FAQ</span>
                <h2>Questions fréquentes</h2>
            </div>

            <div class="faq-grid">
                <div class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span>StageLink est-il gratuit pour les candidats ?</span>
                        <span class="faq-arrow">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Oui, StageLink est entièrement gratuit pour tous les candidats. Inscription, recherche d'offres et candidatures sont sans frais.</p>
                    </div>
                </div>

                <div class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span>Comment fonctionne l'inscription recruteur ?</span>
                        <span class="faq-arrow">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Créez votre compte RH, complétez le profil de votre entreprise, puis accédez à votre dashboard pour publier des offres et gérer vos candidatures.</p>
                    </div>
                </div>

                <div class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span>Combien de temps pour recevoir une réponse ?</span>
                        <span class="faq-arrow">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Notre équipe répond à toutes les demandes dans un délai maximum de 24 heures ouvrées, généralement bien plus vite.</p>
                    </div>
                </div>

                <div class="faq-item reveal">
                    <button class="faq-question" aria-expanded="false">
                        <span>Puis-je proposer un partenariat avec StageLink ?</span>
                        <span class="faq-arrow">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Absolument ! Utilisez le formulaire de contact en sélectionnant "Partenariat" comme sujet. Nous étudions toutes les propositions sérieuses.</p>
                    </div>
                </div>
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

    <script src="{{ asset('js/contact.js') }}"></script>
</body>

</html>