// Elements qui contiennent les chiffres animes.
const counters = document.querySelectorAll('.counter');

// Elements qui apparaissent progressivement au scroll.
const revealElements = document.querySelectorAll('.reveal');

// Elements du menu mobile.
const menuToggle = document.querySelector('.menu-toggle');
const navLinks = document.querySelector('.nav-links');

// Sert a reduire les animations si l'utilisateur le demande dans son systeme.
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Anime les chiffres de 0 jusqu'a leur valeur finale.
function animateCounter(counter) {
    // data-target contient la valeur finale du compteur.
    const target = Number(counter.dataset.target || 0);
    const duration = prefersReducedMotion ? 0 : 1400;
    const startTime = performance.now();

    function update(now) {
        // Easing: rend l'animation plus douce a la fin.
        const progress = duration === 0 ? 1 : Math.min((now - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);

        counter.textContent = Math.round(target * eased).toLocaleString('fr-FR');

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

// Fait apparaitre les elements quand ils entrent dans l'ecran.
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        // On ignore les elements qui ne sont pas encore visibles.
        if (!entry.isIntersecting) {
            return;
        }

        // La classe is-visible lance la transition CSS.
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
    });
}, {
    threshold: 0.18,
    rootMargin: '0px 0px -40px 0px'
});

// Lance les compteurs seulement quand la section stats est visible.
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        // Le compteur ne demarre que lorsqu'il entre dans l'ecran.
        if (!entry.isIntersecting) {
            return;
        }

        animateCounter(entry.target);
        counterObserver.unobserve(entry.target);
    });
}, {
    threshold: 0.55
});

// Ajoute un petit decalage entre les animations pour un effet plus fluide.
revealElements.forEach((element, index) => {
    element.style.transitionDelay = `${Math.min(index * 70, 420)}ms`;
    revealObserver.observe(element);
});

counters.forEach((counter) => counterObserver.observe(counter));

// Ouvre et ferme le menu mobile.
if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', () => {
        const isOpen = navLinks.classList.toggle('is-open');

        menuToggle.classList.toggle('is-open', isOpen);
        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
    });

    navLinks.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('is-open');
            menuToggle.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Ouvrir le menu');
        });
    });
}
