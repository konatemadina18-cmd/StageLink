/* =========================================
   INSCRIPTION RH — JavaScript
   1. Toggle afficher / masquer mot de passe
   2. Barre de force du mot de passe
   3. Validation live confirmation mot de passe
========================================= */

document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       1. TOGGLE MOT DE PASSE
    ========================================= */

    function toggleVisibility(btnId, inputId) {
        const btn   = document.getElementById(btnId);
        const input = document.getElementById(inputId);

        if (!btn || !input) return;

        btn.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        });
    }

    toggleVisibility('togglePassword', 'password');
    toggleVisibility('toggleConfirm',  'password_confirmation');


    /* =========================================
       2. BARRE DE FORCE DU MOT DE PASSE
    ========================================= */

    const passwordInput  = document.getElementById('password');
    const strengthFill   = document.getElementById('strengthFill');
    const strengthLabel  = document.getElementById('strengthLabel');

    function getStrength(pwd) {
        let score = 0;
        if (pwd.length >= 8)                          score++;
        if (pwd.length >= 12)                         score++;
        if (/[A-Z]/.test(pwd))                        score++;
        if (/[0-9]/.test(pwd))                        score++;
        if (/[^A-Za-z0-9]/.test(pwd))                score++;
        return score; // 0 à 5
    }

    const levels = [
        { label: '',            color: 'transparent', width: '0%'   },
        { label: 'Très faible', color: '#EF4444',     width: '20%'  },
        { label: 'Faible',      color: '#F97316',     width: '40%'  },
        { label: 'Moyen',       color: '#EAB308',     width: '60%'  },
        { label: 'Fort',        color: '#22C55E',     width: '80%'  },
        { label: 'Très fort',   color: '#16A34A',     width: '100%' },
    ];

    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            const score = getStrength(passwordInput.value);
            const lvl   = levels[score];

            strengthFill.style.width      = lvl.width;
            strengthFill.style.background = lvl.color;
            strengthLabel.textContent     = lvl.label;
            strengthLabel.style.color     = lvl.color;
        });
    }


    /* =========================================
       3. VALIDATION LIVE CONFIRMATION
       Bordure rouge si les mots de passe
       ne correspondent pas
    ========================================= */

    const confirmInput = document.getElementById('password_confirmation');

    if (confirmInput && passwordInput) {
        confirmInput.addEventListener('input', () => {
            if (confirmInput.value && confirmInput.value !== passwordInput.value) {
                confirmInput.classList.add('error');
            } else {
                confirmInput.classList.remove('error');
            }
        });
    }


    /* =========================================
       4. DÉSACTIVER LE BOUTON PENDANT L'ENVOI
       Évite le double clic sur le formulaire
    ========================================= */

    const form = document.querySelector('form');
    const btn  = document.querySelector('.login-btn');

    if (form && btn) {
        form.addEventListener('submit', () => {
            btn.disabled     = true;
            btn.textContent  = 'Création en cours…';
        });
    }

});