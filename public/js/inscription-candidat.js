document.addEventListener('DOMContentLoaded', () => {

    // Toggle mot de passe
    function toggleVisibility(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        if (!btn || !input) return;
        btn.addEventListener('click', () => {
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.textContent = input.type === 'password' ? '👁' : '🙈';
        });
    }
    toggleVisibility('togglePassword', 'password');
    toggleVisibility('toggleConfirm', 'password_confirmation');

    // Barre de force
    const passwordInput = document.getElementById('password');
    const strengthFill  = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    const levels = [
        { label: '',           color: 'transparent', width: '0%'   },
        { label: 'Très faible',color: '#EF4444',     width: '20%'  },
        { label: 'Faible',     color: '#F97316',     width: '40%'  },
        { label: 'Moyen',      color: '#EAB308',     width: '60%'  },
        { label: 'Fort',       color: '#22C55E',     width: '80%'  },
        { label: 'Très fort',  color: '#16A34A',     width: '100%' },
    ];
    function getStrength(pwd) {
        let s = 0;
        if (pwd.length >= 8)           s++;
        if (pwd.length >= 12)          s++;
        if (/[A-Z]/.test(pwd))         s++;
        if (/[0-9]/.test(pwd))         s++;
        if (/[^A-Za-z0-9]/.test(pwd))  s++;
        return s;
    }
    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            const lvl = levels[getStrength(passwordInput.value)];
            strengthFill.style.width      = lvl.width;
            strengthFill.style.background = lvl.color;
            strengthLabel.textContent     = lvl.label;
            strengthLabel.style.color     = lvl.color;
        });
    }

    // Validation confirmation
    const confirmInput = document.getElementById('password_confirmation');
    if (confirmInput && passwordInput) {
        confirmInput.addEventListener('input', () => {
            confirmInput.classList.toggle('error', confirmInput.value !== '' && confirmInput.value !== passwordInput.value);
        });
    }

    // Anti double-clic
    const form = document.querySelector('form');
    const btn  = document.getElementById('submitBtn');
    if (form && btn) {
        form.addEventListener('submit', () => {
            btn.disabled    = true;
            btn.textContent = 'Création en cours…';
        });
    }
});