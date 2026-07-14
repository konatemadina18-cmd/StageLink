/* =========================================
   CONTACT — JavaScript
   1. Animations reveal au scroll
   2. Menu mobile toggle
   3. FAQ accordion
   4. Compteur de caractères message
   5. Désactiver bouton pendant l'envoi
========================================= */

document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       1. ANIMATIONS REVEAL AU SCROLL
    ========================================= */

    const reveals = document.querySelectorAll('.reveal');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    reveals.forEach(el => observer.observe(el));


    /* =========================================
       2. MENU MOBILE TOGGLE
    ========================================= */

    const toggle   = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (toggle && navLinks) {
        toggle.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            toggle.setAttribute('aria-expanded', navLinks.classList.contains('open'));
        });
    }


    /* =========================================
       3. FAQ ACCORDION
    ========================================= */

    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const btn = item.querySelector('.faq-question');

        btn.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Ferme tous les autres
            faqItems.forEach(other => {
                other.classList.remove('open');
                other.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            // Ouvre celui cliqué si il était fermé
            if (!isOpen) {
                item.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });


    /* =========================================
       4. COMPTEUR DE CARACTÈRES MESSAGE
    ========================================= */

    const textarea  = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const MAX_CHARS = 1000;

    if (textarea && charCount) {
        textarea.addEventListener('input', () => {
            const len = textarea.value.length;
            charCount.textContent = len + ' / ' + MAX_CHARS;

            if (len >= MAX_CHARS) {
                charCount.classList.add('limit');
                textarea.value = textarea.value.substring(0, MAX_CHARS);
            } else {
                charCount.classList.remove('limit');
            }
        });
    }


    /* =========================================
       5. DÉSACTIVER BOUTON PENDANT L'ENVOI
    ========================================= */

    const form      = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled    = true;
            submitBtn.textContent = 'Envoi en cours…';
        });
    }

});