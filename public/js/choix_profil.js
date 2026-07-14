/* =========================================
   CHOIX PROFIL — JavaScript
   1. Surbrillance avancée au survol
   2. Animation de sélection + loader avant redirection
========================================= */

document.addEventListener('DOMContentLoaded', () => {

    const items = document.querySelectorAll('.profil-item');
    const overlay = document.getElementById('select-overlay');
    const overlayLabel = document.getElementById('overlay-label');


    /* =========================================
       1. SURBRILLANCE AVANCÉE AU SURVOL
       Quand on survole une carte, l'autre
       devient légèrement grisée/rétrécie
    ========================================= */

    items.forEach(item => {

        item.addEventListener('mouseenter', () => {
            items.forEach(other => {
                if (other !== item) {
                    other.classList.add('dimmed');
                }
            });
        });

        item.addEventListener('mouseleave', () => {
            items.forEach(other => {
                other.classList.remove('dimmed');
            });
        });

    });


    /* =========================================
       2. ANIMATION DE SÉLECTION + LOADER
       Au clic : on stoppe la navigation,
       on joue l'animation, puis on redirige
    ========================================= */

    items.forEach(item => {

        item.addEventListener('click', function (e) {

            e.preventDefault();

            const destination = this.getAttribute('href');
            const nom = this.querySelector('.profil-text strong').textContent;

            // Marque la carte cliquée comme "active"
            this.classList.add('selected');

            // Met à jour le texte de l'overlay
            overlayLabel.textContent = 'Chargement de votre espace ' + nom + '…';

            // Affiche l'overlay après un court délai (laisse l'animation de carte se jouer)
            setTimeout(() => {
                overlay.classList.add('visible');
            }, 350);

            // Redirige après que l'overlay soit pleinement visible
            setTimeout(() => {
                window.location.href = destination;
            }, 1800);

        });

    });

});