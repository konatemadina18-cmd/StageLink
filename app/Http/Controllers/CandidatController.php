<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\CandidateDocument;
use App\Models\Entreprise;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Offre;
use App\Models\RH;
use App\Services\TwoFactorAuthenticator;
use App\Services\CloudinaryUploadService;

class CandidatController extends Controller
{
    /* =========================================
       DASHBOARD PRINCIPAL
    ========================================= */
    public function dashboard(TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        $user     = Auth::user();

        if ($user->role !== 'candidat') {
            return redirect()->route('dashboard.rh');
        }

        $candidat = Candidat::where('user_id', $user->id)->first();

        // On calcule le pourcentage de profil rempli pour la carte du dashboard.
        $champs = [
            $candidat?->photo,
            $candidat?->cv,
            $candidat?->adresse,
            $candidat?->linkedin,
            $candidat?->competences,
            $candidat?->experiences,
            $candidat?->langues,
        ];
        $remplis    = count(array_filter($champs, fn($v) => !empty($v)));
        $completude = round(($remplis / count($champs)) * 100);

        // On recupere toutes les candidatures du candidat avec les infos utiles.
        $mesCandidatures = $candidat
            ? Candidature::where('candidat_id', $candidat->id)
                ->with(['offre', 'entreprise', 'dernierEntretien.recruteur'])
                ->latest()
                ->get()
            : collect();

        // On garde seulement les dernieres candidatures pour l'activite recente.
        $dernieresCandidatures = $mesCandidatures->take(5);

        // Cette liste sert a empecher le candidat de postuler deux fois.
        $offresDejaCandidatees = $mesCandidatures
            ->pluck('offre_id')
            ->filter()
            ->toArray();

        // On affiche seulement les offres encore actives.
        $offresDisponibles = Offre::where('statut', 'active')
            ->with('entreprise')
            ->latest()
            ->get();

        $entreprises = Entreprise::whereHas('rhs')->orderBy('nom')->get();

        // On limite les offres recommandees sur l'accueil du dashboard.
        $offresRecommandees = $offresDisponibles->take(3);
        $documents = $candidat
            ? CandidateDocument::where('candidat_id', $candidat->id)->latest()->get()
            : collect();
        $notifications = $candidat
            ? Notification::where('candidat_id', $candidat->id)->latest('date_envoi')->get()
            : collect();
        $recruteurs = $mesCandidatures
            ->pluck('entreprise_id')
            ->filter()
            ->unique()
            ->flatMap(fn($entrepriseId) => RH::where('entreprise_id', $entrepriseId)->with('user')->get()->pluck('user'))
            ->filter()
            ->where('role', 'rh_admin')
            ->unique('id');
        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        // Ces compteurs alimentent les badges rouges du menu candidat.
        $lastSeenCandidaturesAt = data_get($user->settings, 'seen.candidatures_at');
        $newCandidaturesCount = $lastSeenCandidaturesAt
            ? $mesCandidatures->where('updated_at', '>', $lastSeenCandidaturesAt)->count()
            : $mesCandidatures->count();
        $unreadMessagesCount = $messages->whereNull('read_at')->where('receiver_id', $user->id)->count();
        $unreadNotificationsCount = $notifications->where('lu', false)->count();

        return view('dashboard-candidat', [
            'user'                  => $user,
            'candidat'              => $candidat,
            'completude'            => $completude,
            'candidaturesCount'     => $mesCandidatures->count(),
            'newCandidaturesCount'  => $newCandidaturesCount,
            'unreadMessagesCount'   => $unreadMessagesCount,
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'profilVues'            => 0,
            'entretiensCount'       => $mesCandidatures->where(fn($candidature) => $candidature->dernierEntretien !== null)->count(),
            'offresCount'           => $offresDisponibles->count(),
            'dernieresCandidatures' => $dernieresCandidatures,
            'mesCandidatures'       => $mesCandidatures,
            'offresRecommandees'    => $offresRecommandees,
            'offresDisponibles'     => $offresDisponibles,
            'entreprises'           => $entreprises,
            'offresDejaCandidatees' => $offresDejaCandidatees,
            'documents'             => $documents,
            'notifications'         => $notifications,
            'recruteurs'            => $recruteurs,
            'messages'              => $messages,
            'twoFactorAppSetup'     => $twoFactorAuthenticator->setupData($user),
        ]);
    }

    /* =========================================
       METTRE À JOUR LE PROFIL
    ========================================= */
    public function updateProfil(Request $request, CloudinaryUploadService $cloudinary)
    {
        $user     = Auth::user();
        $candidat = Candidat::where('user_id', $user->id)->first();

        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'telephone'      => 'required|string|max:20',
            'date_naissance' => 'nullable|date',
            'filiere'        => 'nullable|string|max:150',
            'niveau'         => 'nullable|string|max:150',
            'adresse'        => 'nullable|string|max:255',
            'linkedin'       => 'nullable|string|max:255',
            'github'         => 'nullable|string|max:255',
            'portfolio'      => 'nullable|string|max:255',
            'competences'    => 'nullable|string',
            'experiences'    => 'nullable|string|max:500',
            'langues'        => 'nullable|string',
            'certifications' => 'nullable|string',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'cv'             => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $user->update([
            'nom'       => $request->nom,
            'prenom'    => $request->prenom,
            'telephone' => $request->telephone,
            'date_naissance' => $request->date_naissance,
            'filiere' => $request->filiere,
            'niveau' => $request->niveau,
        ]);

        $photoPath = $candidat?->photo ?? $user->photo;
        if ($request->hasFile('photo')) {
            // Supprime l'ancien fichier (Cloudinary si URL complète, sinon disque local historique).
            if ($photoPath) {
                str_starts_with($photoPath, 'http')
                    ? $cloudinary->delete($photoPath, 'image')
                    : Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $cloudinary->upload($request->file('photo'), 'photos');
        }
        $user->update(['photo' => $photoPath]);

        $cvPath = $candidat?->cv;
        if ($request->hasFile('cv')) {
            if ($cvPath) {
                str_starts_with($cvPath, 'http')
                    ? $cloudinary->delete($cvPath, 'raw')
                    : Storage::disk('public')->delete($cvPath);
            }
            $cvPath = $cloudinary->upload($request->file('cv'), 'cvs');
        }

        $profilData = [
            'user_id'        => $user->id,
            'adresse'        => $request->adresse,
            'date_naissance' => $request->date_naissance ?: ($candidat->date_naissance ?? now()->toDateString()),
            'telephone'      => $request->telephone,
            'filiere'        => $request->filiere ?: ($candidat->filiere ?? $user->filiere ?? 'Non renseignee'),
            'niveau'         => $request->niveau ?: ($candidat->niveau ?? $user->niveau ?? 'Non renseigne'),
            'linkedin'       => $request->linkedin,
            'github'         => $request->github,
            'portfolio'      => $request->portfolio,
            'competences'    => $request->competences,
            'experiences'    => $request->experiences,
            'langues'        => $request->langues,
            'certifications' => $request->certifications,
            'photo'          => $photoPath,
            'cv'             => $cvPath,
        ];

        Candidat::updateOrCreate(['user_id' => $user->id], $profilData);

        return redirect()->route('dashboard.candidat')
                         ->with('success', 'Profil mis à jour avec succès !');
    }

    /* =========================================
       CHANGER LE MOT DE PASSE
    ========================================= */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.min'              => 'Le nouveau mot de passe doit faire au moins 8 caractères.',
            'password.confirmed'        => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('dashboard.candidat')
                         ->with('success', 'Mot de passe modifié avec succès !');
    }

    /* =========================================
       CHANGER LES PRÉFÉRENCES
    ========================================= */
    public function updatePreferences(Request $request, TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
            'language' => 'nullable|in:fr,en',
            'notif_candidature_acceptee' => 'nullable|boolean',
            'notif_candidature_refusee' => 'nullable|boolean',
            'notif_entretien' => 'nullable|boolean',
            'notif_message' => 'nullable|boolean',
            'profile_public' => 'nullable|boolean',
            'use_default_cv' => 'nullable|boolean',
            'two_factor_enabled' => 'nullable|boolean',
            'two_factor_method' => 'nullable|in:email,app',
        ]);

        $user = Auth::user();
        if ($request->has('two_factor_form')) {
            // On gere ici le choix de double authentification : email ou application.
            if (!$request->boolean('two_factor_enabled')) {
                $twoFactorAuthenticator->disable($user);
            } elseif ($request->two_factor_method === 'app') {
                if (!$user->two_factor_secret || $user->two_factor_method !== 'app') {
                    $twoFactorAuthenticator->startSetup($user);

                    return redirect()->route('dashboard.candidat', [], 303)
                        ->with('success', 'Scannez le QR Code puis validez le code a 6 chiffres.');
                }
            } else {
                $user->forceFill([
                    'two_factor_enabled_at' => $user->two_factor_enabled_at ?? now(),
                    'two_factor_method' => 'email',
                    'two_factor_secret' => null,
                    'two_factor_pending_secret' => null,
                    'two_factor_code' => null,
                    'two_factor_expires_at' => null,
                ])->save();
            }
        }

        $currentSettings = $user->settings ?? [];
        $currentNotifications = $currentSettings['notifications'] ?? [];
        $currentPrivacy = $currentSettings['privacy'] ?? [];
        $language = $request->input('language', $currentSettings['language'] ?? app()->getLocale());
        $user->update(['settings' => array_merge($user->settings ?? [], [
            'theme' => $request->theme,
            'language' => $language,
            'notifications' => [
                'candidature_acceptee' => $request->has('notif_candidature_acceptee') ? $request->boolean('notif_candidature_acceptee') : ($currentNotifications['candidature_acceptee'] ?? true),
                'candidature_refusee' => $request->has('notif_candidature_refusee') ? $request->boolean('notif_candidature_refusee') : ($currentNotifications['candidature_refusee'] ?? true),
                'entretien' => $request->has('notif_entretien') ? $request->boolean('notif_entretien') : ($currentNotifications['entretien'] ?? true),
                'message' => $request->has('notif_message') ? $request->boolean('notif_message') : ($currentNotifications['message'] ?? true),
            ],
            'privacy' => [
                'profile_public' => $request->has('profile_public') ? $request->boolean('profile_public') : ($currentPrivacy['profile_public'] ?? true),
            ],
        ])]);

        if ($request->has('use_default_cv')) {
            // Si cette option est cochee, ce CV sera utilise automatiquement.
            Candidat::where('user_id', $user->id)->update([
                'use_default_cv' => $request->boolean('use_default_cv'),
            ]);
        }
        session(['locale' => $language]);

        return redirect()->route('dashboard.candidat')
                         ->with('success', 'Préférences enregistrées !');
    }

    public function storeDocument(Request $request, CloudinaryUploadService $cloudinary)
    {
        $request->validate([
            'type_document' => 'required|in:cv,lettre_motivation,lettre_recommandation',
            'document' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'is_default' => 'nullable|boolean',
        ]);

        $candidat = Candidat::where('user_id', Auth::id())->firstOrFail();
        $path = $cloudinary->upload($request->file('document'), 'documents_candidat');

        if ($request->boolean('is_default') && $request->type_document === 'cv') {
            CandidateDocument::where('candidat_id', $candidat->id)->where('type_document', 'cv')->update(['is_default' => false]);
            $candidat->update(['cv' => $path, 'use_default_cv' => true]);
        }

        CandidateDocument::create([
            'candidat_id' => $candidat->id,
            'nom_fichier' => $request->file('document')->getClientOriginalName(),
            'type_document' => $request->type_document,
            'chemin' => $path,
            'is_default' => $request->boolean('is_default') && $request->type_document === 'cv',
        ]);

        return back()->with('success', 'Document ajoute avec succes.');
    }

    public function setDefaultDocument(CandidateDocument $document)
    {
        $candidat = Candidat::where('user_id', Auth::id())->firstOrFail();
        abort_unless($document->candidat_id === $candidat->id && $document->type_document === 'cv', 403);

        CandidateDocument::where('candidat_id', $candidat->id)->where('type_document', 'cv')->update(['is_default' => false]);
        $document->update(['is_default' => true]);
        $candidat->update(['cv' => $document->chemin, 'use_default_cv' => true]);

        return back()->with('success', 'CV principal mis a jour.');
    }

    public function destroyDocument(CandidateDocument $document, CloudinaryUploadService $cloudinary)
    {
        $candidat = Candidat::where('user_id', Auth::id())->firstOrFail();
        abort_unless($document->candidat_id === $candidat->id, 403);

        str_starts_with($document->chemin, 'http')
            ? $cloudinary->delete($document->chemin, 'raw')
            : Storage::disk('public')->delete($document->chemin);

        $document->delete();

        return back()->with('success', 'Document supprime.');
    }

    public function sendMessage(Request $request, CloudinaryUploadService $cloudinary)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $attachment = $request->hasFile('attachment')
            ? $cloudinary->upload($request->file('attachment'), 'message_attachments')
            : null;

        // On enregistre le message et sa piece jointe si elle existe.
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body' => $request->body,
            'attachment' => $attachment,
        ]);

        if ($message->receiver?->role === 'candidat' && $message->receiver->candidat) {
            Notification::create([
                'candidat_id' => $message->receiver->candidat->id,
                'message' => 'Nouveau message recu de ' . (Auth::user()->prenom ?? 'un utilisateur') . '.',
                'date_envoi' => now(),
                'lu' => false,
            ]);
        }

        return back()->with('success', 'Message envoye.');
    }

    public function toggleNotification(Notification $notification)
    {
        $candidat = Candidat::where('user_id', Auth::id())->firstOrFail();
        abort_unless($notification->candidat_id === $candidat->id, 403);

        $notification->update(['lu' => !$notification->lu]);

        return back()->with('success', 'Notification mise a jour.');
    }

    public function markSectionRead(Request $request)
    {
        $request->validate([
            'section' => 'required|in:messages,notifications,candidatures',
        ]);

        $user = Auth::user();

        if ($request->section === 'messages') {
            // Quand le candidat ouvre les messages, ils sont consideres comme lus.
            Message::where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        if ($request->section === 'notifications') {
            // Quand il ouvre les notifications, le badge revient a zero.
            $candidat = Candidat::where('user_id', $user->id)->first();
            if ($candidat) {
                Notification::where('candidat_id', $candidat->id)->update(['lu' => true]);
            }
        }

        if ($request->section === 'candidatures') {
            // On memorise la date de lecture des candidatures.
            $settings = $user->settings ?? [];
            data_set($settings, 'seen.candidatures_at', now()->toDateTimeString());
            $user->update(['settings' => $settings]);
        }

        return response()->json(['ok' => true]);
    }
}