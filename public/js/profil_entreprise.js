/* =========================================
   PROFIL ENTREPRISE — JavaScript
   1. Preview logo avant upload
   2. Drag & drop sur la zone logo
   3. Compteur de caractères description
   4. Désactiver bouton pendant l'envoi
========================================= */

document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       1 & 2. PREVIEW LOGO + DRAG & DROP
    ========================================= */

    const uploadZone    = document.getElementById('uploadZone');
    const logoInput     = document.getElementById('logo');
    const uploadPreview = document.getElementById('uploadPreview');

    // Crée l'image de prévisualisation
    const previewImg = document.createElement('img');
    previewImg.id = 'logoPreviewImg';
    previewImg.alt = 'Aperçu du logo';
    uploadPreview.prepend(previewImg);

    // Texte de remplacement une fois l'image choisie
    const uploadText = uploadPreview.querySelector('.upload-text');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            uploadZone.classList.add('has-image');
            uploadText.textContent = file.name;
        };
        reader.readAsDataURL(file);
    }

    // Clic sur la zone → input file
    if (logoInput) {
        logoInput.addEventListener('change', () => {
            if (logoInput.files.length > 0) {
                showPreview(logoInput.files[0]);
            }
        });
    }

    // Drag & drop
    if (uploadZone) {

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');

            const file = e.dataTransfer.files[0];
            if (file) {
                // Injecte le fichier dans l'input
                const dt = new DataTransfer();
                dt.items.add(file);
                logoInput.files = dt.files;
                showPreview(file);
            }
        });
    }


    /* =========================================
       3. COMPTEUR DE CARACTÈRES DESCRIPTION
    ========================================= */

    const textarea  = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const MAX_CHARS = 500;

    if (textarea && charCount) {

        function updateCount() {
            const len = textarea.value.length;
            charCount.textContent = len + ' / ' + MAX_CHARS;

            if (len >= MAX_CHARS) {
                charCount.classList.add('limit');
                textarea.value = textarea.value.substring(0, MAX_CHARS);
            } else {
                charCount.classList.remove('limit');
            }
        }

        textarea.addEventListener('input', updateCount);
        updateCount(); // initialisation
    }


    /* =========================================
       4. DÉSACTIVER LE BOUTON PENDANT L'ENVOI
    ========================================= */

    const form = document.querySelector('form');
    const btn  = document.querySelector('.login-btn');

    if (form && btn) {
        form.addEventListener('submit', () => {
            btn.disabled    = true;
            btn.textContent = 'Enregistrement…';
        });
    }

});