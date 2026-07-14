document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       1. NAVIGATION ENTRE SECTIONS
    ========================================= */
    const menuItems = document.querySelectorAll('.menu-item');
    const sections  = document.querySelectorAll('.content-section');

    function markSectionRead(target) {
        const map = { 'messages-modern-rh': 'messages', 'candidatures': 'candidatures' };
        const section = map[target];
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!section || !token) return;

        fetch('/rh/sections/read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ section })
        }).then(() => {
            document.querySelector(`.menu-item[data-section="${target}"] .menu-badge`)?.remove();
            const unread = document.querySelector(`#${target} .offres-count`);
            if (target === 'messages-modern-rh' && unread) unread.textContent = '0 non lu(s)';
        }).catch(() => {});
    }

    function activateSection(target) {
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
        item.addEventListener('click', () => activateSection(item.getAttribute('data-section')));
    });

    window.switchSection = activateSection;

    document.addEventListener('click', event => {
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

    document.querySelectorAll('input[name="theme"]').forEach(input => {
        input.addEventListener('change', () => {
            if (input.checked) document.body.classList.toggle('dark-mode', input.value === 'dark');
        });
        if (input.checked && input.value === 'dark') document.body.classList.add('dark-mode');
    });

    document.addEventListener('click', event => {
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

    setInterval(() => {
        const messagesSection = document.getElementById('messages-modern-rh');
        const composeHasFocus = document.activeElement?.closest?.('.message-compose');
        if (messagesSection?.classList.contains('active-section') && !composeHasFocus) {
            window.location.reload();
        }
    }, 15000);


    /* =========================================
       2. TOGGLE FORMULAIRE OFFRE
    ========================================= */
    const toggleBtn  = document.getElementById('toggleOffreForm');
    const formWrap   = document.getElementById('offreFormWrap');

    if (toggleBtn && formWrap) {
        toggleBtn.addEventListener('click', () => {
            const isOpen = formWrap.style.display !== 'none';
            formWrap.style.display = isOpen ? 'none' : 'block';
            toggleBtn.innerHTML = isOpen
                ? '<i class="fas fa-chevron-down"></i> Afficher le formulaire'
                : '<i class="fas fa-chevron-up"></i> Masquer le formulaire';
        });
    }

    // Ouvrir auto si erreur de validation
    const hasErrors = document.querySelectorAll('.field-error').length > 0;
    if (hasErrors && formWrap) {
        formWrap.style.display = 'block';
        activateSection('offres');
        if (toggleBtn) toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Masquer le formulaire';
    }

    /* =========================================
       2.b CONFIRMATION SUPPRESSION OFFRE
    ========================================= */
    let pendingDeleteForm = null;
    const deleteModal = document.createElement('div');
    deleteModal.className = 'confirm-modal-overlay';
    deleteModal.innerHTML = `
        <div class="confirm-modal-box" role="dialog" aria-modal="true" aria-labelledby="deleteOffreTitle">
            <div class="confirm-modal-icon danger"><i class="fas fa-trash"></i></div>
            <div class="confirm-modal-content">
                <h3 id="deleteOffreTitle">Supprimer cette offre ?</h3>
                <p id="deleteOffreText">Cette action supprimera l'offre selectionnee.</p>
            </div>
            <div class="confirm-modal-actions">
                <button type="button" class="btn-outline btn-sm" data-confirm-cancel>Annuler</button>
                <button type="button" class="btn-danger btn-sm" data-confirm-delete>
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(deleteModal);

    function closeDeleteModal() {
        pendingDeleteForm = null;
        deleteModal.classList.remove('is-visible');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.delete-offre-form').forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            pendingDeleteForm = form;
            const title = form.dataset.offreTitle || 'cette offre';
            const text = deleteModal.querySelector('#deleteOffreText');
            if (text) text.textContent = `L'offre "${title}" sera supprimee definitivement.`;
            deleteModal.classList.add('is-visible');
            document.body.style.overflow = 'hidden';
        });
    });

    deleteModal.querySelector('[data-confirm-cancel]')?.addEventListener('click', closeDeleteModal);
    deleteModal.querySelector('[data-confirm-delete]')?.addEventListener('click', () => {
        const form = pendingDeleteForm;
        pendingDeleteForm = null;
        closeDeleteModal();
        form?.submit();
    });
    deleteModal.addEventListener('click', event => {
        if (event.target === deleteModal) closeDeleteModal();
    });


    /* =========================================
       3. COMPTEUR DESCRIPTION OFFRE
    ========================================= */
    const descTextarea = document.querySelector('textarea[name="description"]');
    const descCount    = document.getElementById('descCount');

    if (descTextarea && descCount) {
        descTextarea.addEventListener('input', () => {
            const len = descTextarea.value.length;
            descCount.textContent = len + ' / 1000';
            descCount.style.color = len >= 1000 ? '#EF4444' : '';
            if (len >= 1000) descTextarea.value = descTextarea.value.substring(0, 1000);
        });
    }


    /* =========================================
       4. MODAL ENTRETIEN
    ========================================= */
    const modal        = document.getElementById('modalEntretien');
    const entretienForm = document.getElementById('entretienForm');
    const modalNomEl   = document.getElementById('modalCandidatNom');

    window.ouvrirModalEntretien = function(candidatureId, nomCandidat) {
        if (!modal) return;
        // Met à jour l'action du formulaire
        entretienForm.action = '/candidatures/' + candidatureId + '/entretien';
        // Met à jour le nom
        if (modalNomEl) modalNomEl.textContent = 'Candidat : ' + nomCandidat;
        // Affiche le modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.fermerModalEntretien = function() {
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = '';
        if (entretienForm) entretienForm.reset();
    };

    // Fermer en cliquant sur l'overlay
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) fermerModalEntretien();
        });
    }

    // Fermer avec Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            fermerModalEntretien();
            closeDeleteModal();
        }
    });

});
