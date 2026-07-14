<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family:Arial,sans-serif; background:#F8FAFC; margin:0; padding:0; }
        .container { max-width:600px; margin:40px auto; background:white; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.08); }
        .header { background:linear-gradient(135deg,#0A1F44,#1E88FF); padding:40px; text-align:center; color:white; }
        .header h1 { font-size:26px; margin:0 0 8px; }
        .header p  { margin:0; opacity:.85; font-size:14px; }
        .body { padding:40px; }
        .body h2 { color:#0A1F44; font-size:20px; margin-bottom:16px; }
        .body p  { color:#64748B; line-height:1.8; font-size:15px; margin-bottom:16px; }
        .steps { margin:24px 0; }
        .step  { display:flex; align-items:flex-start; gap:14px; margin-bottom:16px; }
        .step-num { background:#1E88FF; color:white; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; margin-top:2px; }
        .step-text strong { display:block; color:#0A1F44; font-size:14px; margin-bottom:2px; }
        .step-text span { color:#64748B; font-size:13px; }
        .btn { display:inline-block; background:linear-gradient(135deg,#1E88FF,#0A1F44); color:white; padding:14px 32px; border-radius:12px; text-decoration:none; font-weight:700; font-size:15px; margin:8px 0; }
        .footer { background:#F8FAFC; padding:24px 40px; text-align:center; color:#94A3B8; font-size:13px; border-top:1px solid #E8EEF7; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1> Bienvenue sur StageLink !</h1>
        <p>Votre espace candidat est prêt</p>
    </div>
    <div class="body">
        <h2>Bonjour {{ $user->prenom }} {{ $user->nom }},</h2>
        <p>Votre compte candidat a été créé avec succès. Vous pouvez dès maintenant explorer les offres de stage et postuler aux opportunités qui correspondent à votre profil.</p>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-text">
                    <strong>Complétez votre profil</strong>
                    <span>Ajoutez votre CV, vos compétences et vos disponibilités.</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-text">
                    <strong>Explorez les offres</strong>
                    <span>Parcourez les stages adaptés à votre domaine d'études.</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-text">
                    <strong>Postulez et suivez</strong>
                    <span>Candidatez en un clic et suivez vos candidatures en temps réel.</span>
                </div>
            </div>
        </div>
        <a href="{{ url('/dashboard/candidat') }}" class="btn">Accéder à mon espace →</a>
    </div>
    <div class="footer">
        <p>© 2026 StageLink — Tous droits réservés</p>
        <p>Cet email a été envoyé à {{ $user->email }}</p>
    </div>
</div>
</body>
</html>