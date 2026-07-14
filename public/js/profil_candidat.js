/* =========================================
   PROFIL CANDIDAT — JavaScript
   1. Preview photo de profil
   2. Upload CV avec nom du fichier
   3. Tags pour les compétences
   4. Compteur expériences
   5. Drag & drop
   6. Anti double-clic
========================================= */

document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       1. PREVIEW PHOTO DE PROFIL
    ========================================= */

    const photoZone    = document.getElementById('photoZone');
    const photoInput   = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');

    const previewImg   = document.createElement('img');
    previewImg.id      = 'photoPreviewImg';
    previewImg.alt     = 'Photo de profil';
    photoPreview.prepend(previewImg);

    const photoText = photoPreview.querySelector('.upload-text');

    if (photoInput) {
        photoInput.addEventListener('change', () => {
            const file = photoInput.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                photoZone.classList.add('has-file');
                photoText.textContent = file.name;
            };
            reader.readAsDataURL(file);
        });
    }


    /* =========================================
       2. UPLOAD CV — affichage nom du fichier
    ========================================= */

    const cvInput   = document.getElementById('cv');
    const cvPreview = document.getElementById('cvPreview');
    const cvZone    = document.getElementById('cvZone');

    if (cvInput && cvPreview) {
        const cvText = cvPreview.querySelector('.upload-text');
        const cvIcon = cvPreview.querySelector('.upload-icon');

        cvInput.addEventListener('change', () => {
            const file = cvInput.files[0];
            if (!file) return;
            cvZone.classList.add('has-file');
            cvIcon.textContent = '✅';
            cvText.textContent = file.name;
            cvText.style.color = '#1E88FF';
        });
    }


    /* =========================================
       3. TAGS COMPÉTENCES
    ========================================= */

    const tagsContainer = document.getElementById('tagsContainer');
    const tagsInput     = document.getElementById('tagsInput');
    const hiddenInput   = document.getElementById('competences');

    let tags = hiddenInput.value
        ? hiddenInput.value.split(',').map(t => t.trim()).filter(Boolean)
        : [];

    function renderTags() {
        tagsContainer.innerHTML = '';
        tags.forEach((tag, i) => {
            const el = document.createElement('span');
            el.className = 'tag';
            el.innerHTML = tag + '<span class="tag-remove" data-index="' + i + '">×</span>';
            tagsContainer.appendChild(el);
        });
        hiddenInput.value = tags.join(',');
    }

    if (tagsInput) {
        tagsInput.addEventListener('keydown', (e) => {
            if ((e.key === 'Enter' || e.key === ',') && tagsInput.value.trim()) {
                e.preventDefault();
                const val = tagsInput.value.trim().replace(/,$/, '');
                if (val && !tags.includes(val)) {
                    tags.push(val);
                    renderTags();
                }
                tagsInput.value = '';
            }
            // Supprimer dernier tag avec Backspace
            if (e.key === 'Backspace' && !tagsInput.value && tags.length) {
                tags.pop();
                renderTags();
            }
        });
    }

    if (tagsContainer) {
        tagsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('tag-remove')) {
                const i = parseInt(e.target.getAttribute('data-index'));
                tags.splice(i, 1);
                renderTags();
            }
        });
    }

    renderTags();


    /* =========================================
       4. COMPTEUR EXPÉRIENCES
    ========================================= */

    const expTextarea = document.getElementById('experiences');
    const expCount    = document.getElementById('expCount');
    const MAX_EXP     = 500;

    if (expTextarea && expCount) {
        expTextarea.addEventListener('input', () => {
            const len = expTextarea.value.length;
            expCount.textContent = len + ' / ' + MAX_EXP;
            if (len >= MAX_EXP) {
                expCount.classList.add('limit');
                expTextarea.value = expTextarea.value.substring(0, MAX_EXP);
            } else {
                expCount.classList.remove('limit');
            }
        });
    }


    /* =========================================
       5. DRAG & DROP PHOTO ET CV
    ========================================= */

    [
        { zone: photoZone, input: photoInput, type: 'image' },
        { zone: cvZone,    input: cvInput,    type: 'pdf'   },
    ].forEach(({ zone, input }) => {
        if (!zone || !input) return;

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
    });


    /* =========================================
       6. ANTI DOUBLE-CLIC
    ========================================= */

    const form      = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled    = true;
            submitBtn.textContent = 'Enregistrement…';
        });
    }

});