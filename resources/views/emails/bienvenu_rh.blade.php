<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; background: #F8FAFC; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #0A1F44, #1E88FF); padding: 40px; text-align: center; color: white; }
        .header h1 { font-size: 28px; margin: 0 0 8px; }
        .header p { margin: 0; opacity: 0.85; font-size: 15px; }
        .body { padding: 40px; }
        .body h2 { color: #0A1F44; font-size: 22px; margin-bottom: 16px; }
        .body p { color: #64748B; line-height: 1.8; font-size: 15px; margin-bottom: 16px; }
        .info-box { background: #EFF6FF; border-left: 4px solid #1E88FF; border-radius: 8px; padding: 16px 20px; margin: 24px 0; }
        .info-box p { margin: 4px 0; color: #1E293B; font-size: 14px; }
        .info-box strong { color: #0A1F44; }
        .btn { display: inline-block; background: linear-gradient(135deg, #1E88FF, #0A1F44); color: white; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 15px; margin: 8px 0; }
        .footer { background: #F8FAFC; padding: 24px 40px; text-align: center; color: #94A3B8; font-size: 13px; border-top: 1px solid #E8EEF7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur StageLink !</h1>
            <p>Votre espace recruteur est prêt</p>
        </div>

        <div class="body">
            <h2>Bonjour {{ $user->prenom }} {{ $user->nom }},</h2>

            <p>
                Votre compte recruteur ainsi que le profil de votre entreprise ont été créés avec succès sur <strong>StageLink</strong>.
                Vous pouvez dès maintenant publier vos offres de stage et accéder aux meilleurs profils de candidats.
            </p>

            <div class="info-box">
                <p><strong>Entreprise :</strong> {{ $entreprise->nom }}</p>
                <p><strong>Email entreprise :</strong> {{ $entreprise->email }}</p>
                <p><strong>Adresse :</strong> {{ $entreprise->adresse }}</p>
                <p><strong>Votre fonction :</strong> {{ $user->fonction }}</p>
            </div>

            <p>Accédez à votre dashboard pour commencer à publier vos offres :</p>

            <a href="{{ url('/dashboard/rh') }}" class="btn">
                Accéder à mon dashboard →
            </a>

            <p style="margin-top: 24px;">
                Si vous avez des questions, n'hésitez pas à nous contacter.<br>
                L'équipe StageLink est là pour vous accompagner.
            </p>
        </div>

        <div class="footer">
            <p>© 2026 StageLink — Tous droits réservés</p>
            <p>Cet email a été envoyé à {{ $entreprise->email }}</p>
        </div>
    </div>
</body>
</html>