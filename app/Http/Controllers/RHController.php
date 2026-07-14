<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Entreprise;
use App\Models\Entretien;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Offre;
use App\Models\RH;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\TwoFactorAuthenticator;

class RHController extends Controller
{
    public function dashboard(TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        $user = Auth::user();
        $rh = RH::where('user_id', $user->id)->first();

        if (!$rh || !in_array($user->role, ['rh_admin', 'assistant', 'stagiaire', 'rh_user'])) {
            return redirect()->route('dashboard.candidat')
                ->withErrors(['error' => 'Acces reserve aux recruteurs.']);
        }

        if (blank($user->display_name)) {
            return redirect()->route('display-name.edit');
        }

        $entreprise = Entreprise::find($rh->entreprise_id);

        // Donnees principales affichees dans le dashboard RH.
        $offres = $entreprise
            ? Offre::where('entreprise_id', $entreprise->id)->withCount('candidatures')->latest()->get()
            : collect();

        $offresCount = $entreprise
            ? Offre::where('entreprise_id', $entreprise->id)->where('statut', 'active')->count()
            : 0;

        $toutesCandidatures = $entreprise
            ? Candidature::where('entreprise_id', $entreprise->id)
                ->with(['candidat.user', 'offre', 'dernierEntretien.recruteur'])
                ->latest()
                ->get()
            : collect();

        $entretiens = $entreprise
            ? Entretien::whereHas('candidature', fn($query) => $query->where('entreprise_id', $entreprise->id))
                ->with(['candidature.candidat.user', 'candidature.offre', 'recruteur'])
                ->latest('date_entretien')
                ->get()
            : collect();

        $candidats = $entreprise
            ? User::where('role', 'candidat')
                ->whereHas('candidat.candidatures', fn($query) => $query->where('entreprise_id', $entreprise->id))
                ->orderBy('prenom')
                ->get()
            : collect();

        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        $teamUsers = $entreprise
            ? RH::where('entreprise_id', $entreprise->id)->with('user')->get()
            : collect();

        // Compteurs utilises par les cartes et les badges.
        $dernieresCandidatures = $toutesCandidatures->take(5);
        $candidaturesCount = $toutesCandidatures->count();
        $lastSeenCandidaturesAt = data_get($user->settings, 'seen.candidatures_at');
        $newCandidaturesCount = $lastSeenCandidaturesAt
            ? $toutesCandidatures->where('created_at', '>', $lastSeenCandidaturesAt)->count()
            : $toutesCandidatures->count();
        $entretiensCount = $entretiens->count();

        // On fabrique une liste unique pour afficher les dernieres activites.
        $notifications = collect($toutesCandidatures->map(fn($candidature) => (object) [
                'type' => 'candidature',
                'message' => 'Candidature : ' . ($candidature->candidat->user->prenom ?? 'Un candidat') . ' - ' . ($candidature->offre->titre ?? 'candidature spontanee') . ' (' . $candidature->statut . ')',
                'lu' => false,
                'date_envoi' => $candidature->updated_at,
            ]))
            ->merge($offres->map(fn($offre) => (object) [
                'type' => 'offre', 
                'message' => 'Offre : ' . $offre->titre . ' - statut ' . $offre->statut,
                'lu' => false,
                'date_envoi' => $offre->updated_at,
            ]))
            ->merge($entretiens->map(fn($entretien) => (object) [
                'type' => 'entretien',
                'message' => 'Entretien : ' . ($entretien->candidature->candidat->user->prenom ?? 'Candidat') . ' le ' . $entretien->date_entretien->format('d/m/Y') . ' a ' . substr($entretien->heure, 0, 5),
                'lu' => false,
                'date_envoi' => $entretien->updated_at,
            ]))
            ->merge($messages->map(fn($message) => (object) [
                'type' => 'message',
                'message' => 'Message : ' . ($message->sender_id === $user->id ? 'envoye a ' . ($message->receiver->prenom ?? 'un utilisateur') : 'recu de ' . ($message->sender->prenom ?? 'un utilisateur')),
                'lu' => (bool) $message->read_at,
                'date_envoi' => $message->created_at,
            ]))
            ->sortByDesc('date_envoi')
            ->take(20)
            ->values();

        return view('dashboard-rh', [
            'user' => $user,
            'rh' => $rh,
            'entreprise' => $entreprise,
            'offres' => $offres,
            'offresCount' => $offresCount,
            'candidaturesCount' => $candidaturesCount,
            'newCandidaturesCount' => $newCandidaturesCount,
            'entretiensCount' => $entretiensCount,
            'dernieresCandidatures' => $dernieresCandidatures,
            'toutesCandidatures' => $toutesCandidatures,
            'notifications' => $notifications,
            'entretiens' => $entretiens,
            'candidats' => $candidats,
            'messages' => $messages,
            'teamUsers' => $teamUsers,
            'twoFactorAppSetup' => $twoFactorAuthenticator->setupData($user),
        ]);
    }

    public function storeRHUser(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'collaborator_email' => 'required|email|unique:users,email',
            'telephone' => 'required|string|max:20',
            'role' => 'required|in:assistant,stagiaire',
            'collaborator_password' => 'required|string|min:8',
        ]);

        abort_unless(Auth::user()->role === 'rh_admin', 403);

        $adminRH = RH::where('user_id', Auth::id())->first();

        if (!$adminRH) {
            return back()->withErrors(['error' => 'Action non autorisee : profil RH administrateur introuvable.']);
        }

        $roleLabel = $request->role === 'assistant' ? 'Assistant(e)' : 'Stagiaire';

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->collaborator_email,
            'telephone' => $request->telephone,
            'fonction' => $roleLabel,
            'role' => $request->role,
            'password' => Hash::make($request->collaborator_password),
        ]);

        RH::create([
            'user_id' => $user->id,
            'entreprise_id' => $adminRH->entreprise_id,
            'fonction' => $roleLabel,
            'is_admin' => false,
        ]);

        return back()->with('success', 'Collaborateur ajoute avec succes a votre equipe !');
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:assistant,stagiaire',
        ]);

        abort_unless(Auth::user()->role === 'rh_admin', 403);

        $adminRH = RH::where('user_id', Auth::id())->firstOrFail();
        $memberRH = RH::where('user_id', $user->id)->firstOrFail();

        abort_unless($memberRH->entreprise_id === $adminRH->entreprise_id, 403);
        abort_if($user->role === 'rh_admin' || $memberRH->is_admin, 403);

        $roleLabel = $request->role === 'assistant' ? 'Assistant(e)' : 'Stagiaire';

        $user->update([
            'role' => $request->role,
            'fonction' => $roleLabel,
        ]);

        $memberRH->update(['fonction' => $roleLabel]);

        return back()->with('success', 'Role du collaborateur mis a jour.');
    }

    public function updateProfil(Request $request, TwoFactorAuthenticator $twoFactorAuthenticator)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'telephone' => 'required|string|max:20',
            'fonction' => 'nullable|string|max:150',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'two_factor_enabled' => 'nullable|boolean',
            'two_factor_method' => 'nullable|in:email,app',
        ]);

        $user = Auth::user();
        $photoPath = $user->photo;

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        if (!$request->boolean('two_factor_enabled')) {
            $twoFactorAuthenticator->disable($user);
        } elseif ($request->two_factor_method === 'app') {
            if (!$user->two_factor_secret || $user->two_factor_method !== 'app') {
                $twoFactorAuthenticator->startSetup($user);

                return back()->with('success', 'Scannez le QR Code puis validez le code a 6 chiffres.');
            }
        }

        $payload = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'fonction' => $request->input('fonction', $user->fonction),
            'photo' => $photoPath,
            'two_factor_enabled_at' => $request->boolean('two_factor_enabled') ? ($user->two_factor_enabled_at ?? now()) : null,
            'two_factor_method' => $request->boolean('two_factor_enabled') ? ($request->two_factor_method ?? 'email') : null,
            'two_factor_secret' => !$request->boolean('two_factor_enabled') || $request->two_factor_method === 'email' ? null : $user->two_factor_secret,
            'two_factor_pending_secret' => !$request->boolean('two_factor_enabled') || $request->two_factor_method === 'email' ? null : $user->two_factor_pending_secret,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ];

        $user->update($payload);

        return back()->with('success', 'Profil RH mis a jour.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe modifie.');
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
            'language' => 'nullable|in:fr,en',
            'notif_candidatures' => 'nullable|boolean',
            'notif_entretiens' => 'nullable|boolean',
            'notif_statuts' => 'nullable|boolean',
            'access_team_visible' => 'nullable|boolean',
            'privacy_activity' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $currentSettings = $user->settings ?? [];
        $currentNotifications = $currentSettings['notifications'] ?? [];
        $currentAccess = $currentSettings['access'] ?? [];
        $currentPrivacy = $currentSettings['privacy'] ?? [];
        $language = $request->input('language', $currentSettings['language'] ?? app()->getLocale());
        $user->update(['settings' => array_merge($user->settings ?? [], [
            'theme' => $request->theme,
            'language' => $language,
            'notifications' => [
                'candidatures' => $request->has('notif_candidatures') ? $request->boolean('notif_candidatures') : ($currentNotifications['candidatures'] ?? true),
                'entretiens' => $request->has('notif_entretiens') ? $request->boolean('notif_entretiens') : ($currentNotifications['entretiens'] ?? true),
                'statuts' => $request->has('notif_statuts') ? $request->boolean('notif_statuts') : ($currentNotifications['statuts'] ?? true),
            ],
            'access' => [
                'team_visible' => $request->has('access_team_visible') ? $request->boolean('access_team_visible') : ($currentAccess['team_visible'] ?? true),
            ],
            'privacy' => [
                'activity_visible' => $request->has('privacy_activity') ? $request->boolean('privacy_activity') : ($currentPrivacy['activity_visible'] ?? true),
            ],
        ])]);
        session(['locale' => $language]);

        return back()->with('success', 'Parametres RH enregistres.');
    }

    public function updateEntreprise(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email',
            'site_web' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1500',
            'secteur_activite' => 'nullable|string|max:255',
            'taille' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $rh = RH::where('user_id', Auth::id())->firstOrFail();
        abort_unless(Auth::user()->role === 'rh_admin', 403);

        $entreprise = Entreprise::findOrFail($rh->entreprise_id);
        $logoPath = $entreprise->logo;

        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $entreprise->update([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'site_web' => $request->site_web,
            'description' => $request->description,
            'secteur_activite' => $request->secteur_activite,
            'taille' => $request->taille,
            'logo' => $logoPath,
        ]);

        return back()->with('success', 'Entreprise mise a jour.');
    }

    public function sendMessage(Request $request)
    {
        abort_unless(Auth::user()->role === 'rh_admin', 403);

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $receiver = User::with('candidat')->findOrFail($request->receiver_id);
        abort_unless($receiver->role === 'candidat', 403);

        $attachment = $request->hasFile('attachment')
            ? $request->file('attachment')->store('message_attachments', 'public')
            : null;

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'body' => $request->body,
            'attachment' => $attachment,
        ]);

        if ($receiver->candidat) {
            Notification::create([
                'candidat_id' => $receiver->candidat->id,
                'message' => 'Nouveau message recu de ' . (Auth::user()->prenom ?? 'RH') . '.',
                'date_envoi' => now(),
                'lu' => false,
            ]);
        }

        return back()->with('success', 'Message envoye au candidat.');
    }

    public function markSectionRead(Request $request)
    {
        $request->validate([
            'section' => 'required|in:messages,candidatures',
        ]);

        $user = Auth::user();

        if ($request->section === 'messages') {
            Message::where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $settings = $user->settings ?? [];
        data_set($settings, 'seen.' . $request->section . '_at', now()->toDateTimeString());
        $user->update(['settings' => $settings]);

        return response()->json(['ok' => true]);
    }

    public function toggleNotification($notification)
    {
        return back()->with('success', 'Notification mise a jour.');
    }
}
