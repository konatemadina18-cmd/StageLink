<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Invitation a un entretien StageLink</title>
</head>
<body style="font-family:Arial,sans-serif;background:#f6f8fb;padding:24px;color:#1f2937;">
    <div style="max-width:640px;margin:auto;background:white;border-radius:14px;padding:28px;border:1px solid #e5e7eb;">
        <h1 style="font-size:22px;margin:0 0 12px;color:#0f172a;">Invitation a un entretien</h1>
        <p>Bonjour {{ $candidature->candidat->user->prenom ?? 'candidat' }},</p>
        <p>
            Votre candidature a ete retenue pour un entretien avec
            <strong>{{ $candidature->entreprise->nom ?? 'l’entreprise' }}</strong>.
        </p>

        <div style="background:#eff6ff;border-radius:12px;padding:18px;margin:22px 0;border:1px solid #bfdbfe;">
            <p style="margin:0 0 10px;"><strong>Entreprise :</strong> {{ $candidature->entreprise->nom ?? 'Non renseignee' }}</p>
            <p style="margin:0 0 10px;"><strong>Candidat :</strong> {{ $candidature->candidat->user->prenom ?? '' }} {{ $candidature->candidat->user->nom ?? '' }}</p>
            <p style="margin:0 0 10px;"><strong>Offre :</strong> {{ $candidature->offre->titre ?? 'Candidature spontanee' }}</p>
            <p style="margin:0 0 10px;"><strong>Date :</strong> {{ $entretien->date_entretien->format('d/m/Y') }}</p>
            <p style="margin:0 0 10px;"><strong>Heure :</strong> {{ substr($entretien->heure, 0, 5) }}</p>
            <p style="margin:0 0 10px;"><strong>Mode :</strong> {{ $entretien->type }}</p>
            <p style="margin:0 0 10px;"><strong>Adresse ou lien :</strong> {{ $entretien->lieu }}</p>
            <p style="margin:0;"><strong>Recruteur :</strong> {{ $recruteur->prenom ?? '' }} {{ $recruteur->nom ?? '' }}</p>
        </div>

        @if($entretien->commentaires)
            <div style="background:#f8fafc;border-radius:12px;padding:16px;margin-bottom:22px;border:1px solid #e2e8f0;">
                <strong>Message du RH</strong>
                <p style="margin:8px 0 0;line-height:1.55;">{{ $entretien->commentaires }}</p>
            </div>
        @endif

        <p>Merci de vous presenter a l'heure indiquee. En cas d'empechement, contactez le recruteur depuis votre messagerie StageLink.</p>
        <p style="font-size:12px;color:#64748b;margin-top:28px;">StageLink</p>
    </div>
</body>
</html>
