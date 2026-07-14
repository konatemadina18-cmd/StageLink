<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Code de verification StageLink</title>
</head>
<body style="font-family:Arial,sans-serif;background:#f6f8fb;padding:24px;color:#1f2937;">
    <div style="max-width:560px;margin:auto;background:white;border-radius:14px;padding:28px;border:1px solid #e5e7eb;">
        <h1 style="font-size:22px;margin:0 0 12px;color:#0f172a;">Verification de connexion</h1>
        <p>Bonjour {{ $user->display_name ?: $user->prenom }},</p>
        <p>Voici votre code de verification StageLink :</p>
        <div style="font-size:32px;font-weight:800;letter-spacing:6px;background:#eff6ff;color:#1d4ed8;padding:18px;border-radius:12px;text-align:center;margin:22px 0;">
            {{ $code }}
        </div>
        <p>Ce code expire dans quelques minutes. Si vous n'etes pas a l'origine de cette connexion, ignorez ce message.</p>
        <p style="font-size:12px;color:#64748b;margin-top:28px;">StageLink</p>
    </div>
</body>
</html>
