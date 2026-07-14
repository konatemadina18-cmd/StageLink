document.addEventListener('DOMContentLoaded', () => {

    const menuItems = document.querySelectorAll('.menu-item');
    const sections  = document.querySelectorAll('.content-section');

    function markSectionRead(target) {
        // Quand une section est ouverte, on previens Laravel pour enlever les badges.
        const map = { 'messages-modern': 'messages', 'notifications-modern': 'notifications', 'candidatures': 'candidatures' };
        const section = map[target];
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!section || !token) return;

        fetch('/candidat/sections/read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ section })
        }).then(() => {
            document.querySelector(`.menu-item[data-section="${target}"] .menu-badge`)?.remove();
            const unread = document.querySelector(`#${target} .offres-count`);
            if (target === 'messages-modern' && unread) unread.textContent = '0 non lu(s)';
            if (target === 'notifications-modern') document.querySelector('.notif-dot')?.remove();
            if (target === 'candidatures') document.querySelector('.stat-card[data-target-section="candidatures"] .stat-badge')?.remove();
        }).catch(() => {});
    }

    function activateSection(target) {
        // Change la section visible sans recharger toute la page.
        menuItems.forEach(i => i.classList.remove('active'));
        sections.forEach(s => s.classList.remove('active-section'));
        const activeItem    = document.querySelector(`.menu-item[data-section="${target}"]`);
        const activeSection = document.getElementById(target);
        if (activeItem)    activeItem.classList.add('active');
        if (activeSection) activeSection.classList.add('active-section');
        if (activeSection) {
            markSectionRead(target);
            history.replaceState(null, '', '#' + target);
        }
    }

    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            activateSection(item.getAttribute('data-section'));
        });
    });

    document.querySelector('.notif-btn')?.addEventListener('click', () => activateSection('notifications-modern'));

    document.querySelectorAll('.dashboard-shortcut').forEach(card => {
        // Les cartes du dashboard deviennent des raccourcis cliquables.
        const openTarget = () => activateSection(card.dataset.targetSection);
        card.addEventListener('click', openTarget);
        card.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openTarget();
            }
        });
    });

    window.switchSection = activateSection;

    document.addEventListener('click', event => {
        // Gestion des onglets dans les parametres.
        const tab = event.target.closest('.settings-tab');
        if (!tab) return;
        const key = tab.dataset.settingsTab;
        const shell = tab.closest('.settings-shell');
        if (!shell) return;
        shell.querySelectorAll('.settings-tab').forEach(item => item.classList.remove('active'));
        shell.querySelectorAll('.settings-panel').forEach(panel => {
            panel.classList.remove('active');
            panel.style.display = 'none';
        });
        tab.classList.add('active');
        const panel = shell.querySelector(`[data-settings-panel="${key}"]`);
        if (panel) {
            panel.classList.add('active');
            panel.style.display = 'flex';
        }
    });

    if (window.location.hash && document.getElementById(window.location.hash.slice(1))) {
        activateSection(window.location.hash.slice(1));
    }

    document.querySelectorAll('.filter-pills').forEach(group => {
        // Filtre simple pour afficher certaines notifications seulement.
        group.addEventListener('click', event => {
            const button = event.target.closest('.filter-pill');
            if (!button) return;
            const filter = button.dataset.filter;
            group.querySelectorAll('.filter-pill').forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            group.closest('.dash-card')?.querySelectorAll('[data-type]').forEach(item => {
                item.style.display = filter === 'all' || item.dataset.type === filter ? '' : 'none';
            });
        });
    });

    document.addEventListener('click', event => {
        // Remplit automatiquement le formulaire quand on clique sur Repondre.
        const button = event.target.closest('.reply-message-btn');
        if (!button) return;
        const form = button.closest('.dash-card')?.querySelector('.message-compose');
        const select = form?.querySelector('select[name="receiver_id"]');
        const textarea = form?.querySelector('textarea[name="body"]');
        if (select) select.value = button.dataset.receiverId;
        if (textarea) {
            textarea.placeholder = `Répondre à ${button.dataset.receiverName}`;
            textarea.focus();
        }
        form?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    document.querySelectorAll('input[name="theme"]').forEach(input => {
        input.addEventListener('change', () => {
            if (input.checked) document.body.classList.toggle('dark-mode', input.value === 'dark');
        });
        if (input.checked && input.value === 'dark') document.body.classList.add('dark-mode');
    });

});
