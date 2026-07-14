document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       AUTO-REMPLISSAGE TYPE/DURÉE SELON L'OFFRE
       (candidature spontanée → liste d'offres)
    ========================================= */
    const offreSelect = document.getElementById('offreSelect');
    const typeInput   = document.getElementById('typeStageInput');
    const dureeInput  = document.getElementById('dureeInput');
    const entrepriseSelect = document.getElementById('entrepriseSelect');
    const entrepriseHint = document.getElementById('entrepriseHint');

    if (offreSelect) {
        offreSelect.addEventListener('change', () => {
            const opt   = offreSelect.options[offreSelect.selectedIndex];
            const type  = opt.getAttribute('data-type');
            const duree = opt.getAttribute('data-duree');
            const entrepriseId = opt.getAttribute('data-entreprise-id');
            const entrepriseNom = opt.getAttribute('data-entreprise-nom');

            if (type && typeInput) {
                [...typeInput.options].forEach(o => { o.selected = (o.value === type); });
            }
            if (duree && dureeInput) {
                [...dureeInput.options].forEach(o => { o.selected = (o.value === duree); });
            }

            if (entrepriseSelect) {
                if (entrepriseId) {
                    entrepriseSelect.value = entrepriseId;
                    entrepriseSelect.required = false;
                    entrepriseSelect.disabled = true;
                    if (entrepriseHint) {
                        entrepriseHint.textContent = `Entreprise recuperee depuis l'offre : ${entrepriseNom || 'Entreprise'}.`;
                    }
                } else {
                    entrepriseSelect.disabled = false;
                    entrepriseSelect.required = true;
                    if (entrepriseHint) {
                        entrepriseHint.textContent = 'Obligatoire uniquement pour une candidature spontanee.';
                    }
                }
            }
        });
    }


    /* =========================================
       UPLOAD + PREVIEW + DRAG & DROP
       (CV, lettre de motivation, lettre de recommandation)
    ========================================= */
    function setupFileZone(zoneId, inputId, previewId) {
        const zone    = document.getElementById(zoneId);
        const input   = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!zone || !input || !preview) return;

        const text = preview.querySelector('.upload-text');
        const ic   = preview.querySelector('.upload-icon');
        const originalText = text.textContent;
        const originalIcon = ic.textContent;

        function showFile(file) {
            if (!file) return;
            zone.classList.add('has-file');
            ic.textContent   = '✅';
            text.textContent = file.name;
        }

        function resetZone() {
            zone.classList.remove('has-file');
            ic.textContent   = originalIcon;
            text.textContent = originalText;
        }

        input.addEventListener('change', () => showFile(input.files[0]));

        // Drag & drop
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            zone.classList.add('dragover');
        });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }

    setupFileZone('cvZone', 'cvInput', 'cvPreview');
    setupFileZone('lettreZone', 'lettreInput', 'lettrePreview');
    setupFileZone('recoZone', 'recoInput', 'recoPreview');


    /* =========================================
       ANTI DOUBLE-CLIC SUR ENVOI
    ========================================= */
    const form = document.getElementById('candidatureForm');
    const btn  = document.getElementById('submitBtn');

    if (form && btn) {
        form.addEventListener('submit', () => {
            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours…';
        });
    }

});
