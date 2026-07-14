/* =========================================
   À PROPOS — JavaScript
   1. Animations reveal au scroll
   2. Menu mobile toggle
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

    const toggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (toggle && navLinks) {
        toggle.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            const expanded = navLinks.classList.contains('open');
            toggle.setAttribute('aria-expanded', expanded);
        });
    }

});