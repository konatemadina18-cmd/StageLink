<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Entreprise;
use App\Models\Entretien;
use App\Models\Notification;
use App\Models\Offre;
use App\Models\RH;
use App\Mail\InvitationEntretienMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CandidatureController extends Controller
{
    public function create($offreId = null)
    {
        $offre = $offreId ? Offre::with('entreprise')->findOrFail($offreId) : null;
        $offres = Offre::where('statut', 'active')->with('entreprise')->latest()->get();
        $entreprises = Entreprise::whereHas('rhs')->orderBy('nom')->get();
        $candidat = Candidat::where('user_id', Auth::id())->first();

        return view('candidatures.create', compact('offre', 'offres', 'entreprises', 'candidat'));
    }

    public function store(Request $request)
    {
        // Validation des informations envoyees dans le formulaire de candidature.
        $request->validate([
            'type_stage' => 'required|string',
            'duree' => 'required|string',
            'lettre_motivation' => 'required|file|mimes:pdf|max:5120',
            'cv' => 'nullable|file|mimes:pdf|max:5120',
            'lettre_recommandation' => 'nullable|file|mimes:pdf|max:5120',
            'linkedin' => 'nullable|url',
            'portfolio' => 'nullable|url',
            'offre_id' => 'nullable|exists:offres,id',
            'entreprise_id' => 'required_without:offre_id|nullable|exists:entreprises,id',
        ], [
            'type_stage.required' => 'Le type de stage est obligatoire.',
            'duree.required' => 'La duree est obligatoire.',
            'lettre_motivation.required' => 'La lettre de motivation est obligatoire.',
            'lettre_motivation.mimes' => 'La lettre doit etre en PDF.',
            'cv.mimes' => 'Le CV doit etre en PDF.',
            'lettre_recommandation.mimes' => 'La lettre de recommandation doit etre en PDF.',
            'entreprise_id.required_without' => 'Choisissez une entreprise pour une candidature spontanee.',
        ]);

        $user = Auth::user();
        $candidat = Candidat::where('user_id', $user->id)->first();

        if (!$candidat) {
            return back()->withErrors(['error' => 'Profil candidat introuvable.'])->withInput();
        }

        $offre = $request->offre_id
            ? Offre::with('entreprise')->where('statut', 'active')->findOrFail($request->offre_id)
            : null;

        $entreprise = $offre
            ? $offre->entreprise
            : Entreprise::find($request->entreprise_id);

        if (!$entreprise) {
            return back()->withErrors(['error' => 'Entreprise introuvable.'])->withInput();
        }

        $rh = RH::where('entreprise_id', $entreprise->id)
            ->where(function ($query) {
                $query->where('is_admin', true)
                    ->orWhereHas('user', fn($q) => $q->where('role', 'rh_admin'));
            })
            ->first();

        if (!$rh) {
            return back()->withErrors(['error' => 'Aucun RH administrateur configure pour cette entreprise.'])->withInput();
        }

        if ($offre) {
            $dejaCandidate = Candidature::where('candidat_id', $candidat->id)
                ->where('offre_id', $offre->id)
                ->exists();

            if ($dejaCandidate) {
                return back()->withErrors(['error' => 'Vous avez deja postule a cette offre.'])->withInput();
            }
        }

        $lettrePath = $request->file('lettre_motivation')->store('lettres', 'public');
        if (!$request->hasFile('cv') && (!$candidat->use_default_cv || !$candidat->cv)) {
            return back()->withErrors(['cv' => 'Ajoutez un CV ou activez un CV principal dans vos parametres.'])->withInput();
        }

        $cvPath = $request->hasFile('cv')
            ? $request->file('cv')->store('cvs_candidatures', 'public')
            : $candidat->cv;
        $lettreRecoPath = $request->hasFile('lettre_recommandation')
            ? $request->file('lettre_recommandation')->store('lettres_recommandation', 'public')
            : null;

        // Score simple qui aide le RH a comparer les candidatures.
        $score = $this->calculerScore($candidat, $request);

        $candidature = Candidature::create([
            'candidat_id' => $candidat->id,
            'entreprise_id' => $entreprise->id,
            'r_h_id' => $rh->id,
            'offre_id' => $offre?->id,
            'date_candidature' => now()->toDateString(),
            'type_stage' => $request->type_stage,
            'duree' => $request->duree,
            'statut' => 'En attente',
            'score' => $score,
            'lettre_motivation' => $lettrePath,
            'lettre_recommandation' => $lettreRecoPath,
            'cv' => $cvPath,
            'linkedin' => $request->linkedin,
            'portfolio' => $request->portfolio,
        ]);

        Notification::create([
            'candidat_id' => $candidat->id,
            'message' => 'Nouvelle candidature enregistree pour ' . ($offre->titre ?? $entreprise->nom),
            'date_envoi' => now(),
            'lu' => false,
        ]);

        return redirect()->route('dashboard.candidat')
            ->with('success', 'Candidature envoyee avec succes ! Score IA : ' . $score . '/100');
    }

    private function calculerScore(Candidat $candidat, Request $request): float
    {
        $score = 0;

        if ($request->offre_id) {
            $offre = Offre::find($request->offre_id);
            if ($offre && $offre->filiere_cible) {
                similar_text(
                    strtolower($candidat->filiere ?? ''),
                    strtolower($offre->filiere_cible),
                    $pct
                );
                $score += ($pct / 100) * 30;
            } else {
                $score += 15;
            }
        } else {
            $score += 15;
        }

        if ($candidat->competences && $request->offre_id) {
            $offre = Offre::find($request->offre_id);
            if ($offre && $offre->competences_requises) {
                $compsCandidat = array_map('trim', explode(',', strtolower($candidat->competences)));
                $compsOffre = array_map('trim', explode(',', strtolower($offre->competences_requises)));
                $communs = array_intersect($compsCandidat, $compsOffre);
                $matchPct = count($compsOffre) > 0 ? (count($communs) / count($compsOffre)) : 0;
                $score += $matchPct * 30;
            } else {
                $score += 15;
            }
        } else {
            $score += 10;
        }

        if ($request->offre_id) {
            $offre = Offre::find($request->offre_id);
            if ($offre && $offre->duree === $request->duree) {
                $score += 20;
            } else {
                $score += 10;
            }
        } else {
            $score += 15;
        }

        if ($candidat->cv) {
            $score += 5;
        }
        if ($candidat->linkedin) {
            $score += 5;
        }
        if ($candidat->competences) {
            $score += 5;
        }
        if ($candidat->experiences) {
            $score += 5;
        }

        return round(min($score, 100), 2);
    }

    public function mesCandidatures()
    {
        $user = Auth::user();
        $candidat = Candidat::where('user_id', $user->id)->first();

        $candidatures = $candidat
            ? Candidature::where('candidat_id', $candidat->id)
                ->with(['offre', 'entreprise'])
                ->latest()
                ->get()
            : collect();

        return view('candidatures.mes', compact('candidatures'));
    }

    public function updateStatut(Request $request, Candidature $candidature)
    {
        // Le RH change le statut et le candidat recoit une notification.
        $this->authorizeCanValidate($candidature);

        $request->validate([
            'statut' => 'required|in:En attente,Preselectionnee,Entretien programme,Acceptee,Refusee',
            'commentaire_rh' => 'nullable|string|max:500',
        ]);

        $candidature->update([
            'statut' => $request->statut,
            'commentaire_rh' => $request->commentaire_rh,
        ]);

        Notification::create([
            'candidat_id' => $candidature->candidat_id,
            'message' => 'Statut de votre candidature mis a jour : ' . $request->statut,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        return back()->with('success', 'Statut mis a jour : ' . $request->statut);
    }

    public function planifierEntretien(Request $request, Candidature $candidature)
    {
        // Le RH admin programme ou met a jour l'entretien d'une candidature.
        $this->authorizeRhAdminFor($candidature);

        $request->validate([
            'date_entretien' => 'required|date|after:today',
            'heure' => 'required|string',
            'lieu' => 'required|string|max:255',
            'type' => 'required|in:Presentiel,Visioconference,Telephone',
            'note' => 'nullable|string|max:500',
        ]);

        $commentaire = 'Entretien le '
            . Carbon::parse($request->date_entretien)->format('d/m/Y')
            . ' a ' . $request->heure
            . ' - ' . $request->type
            . ' - ' . $request->lieu;

        if ($request->note) {
            $commentaire .= ' - Note : ' . $request->note;
        }

        $candidature->update([
            'statut' => 'Entretien programme',
            'commentaire_rh' => $commentaire,
        ]);

        $entretien = Entretien::updateOrCreate(
            ['candidature_id' => $candidature->id],
            [
                'recruteur_id' => Auth::id(),
                'date_entretien' => $request->date_entretien,
                'heure' => $request->heure,
                'lieu' => $request->lieu,
                'type' => $request->type,
                'statut' => 'Planifie',
                'commentaires' => $request->note,
            ]
        );

        // On envoie l'invitation par email, sans casser la creation si SMTP echoue.
        $candidature->loadMissing(['candidat.user', 'entreprise', 'offre']);
        if ($candidature->candidat?->user?->email) {
            try {
                Mail::to($candidature->candidat->user->email)
                    ->send(new InvitationEntretienMail($candidature, $entretien, Auth::user()));
            } catch (\Throwable $exception) {
                Log::warning('Impossible d envoyer l invitation entretien.', [
                    'candidature_id' => $candidature->id,
                    'email' => $candidature->candidat->user->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        Notification::create([
            'candidat_id' => $candidature->candidat_id,
            'message' => 'Entretien programme le ' . Carbon::parse($request->date_entretien)->format('d/m/Y') . ' a ' . $request->heure,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        return back()->with('success', 'Entretien planifie avec succes !');
    }

    private function authorizeRhAdminFor(Candidature $candidature): void
    {
        $rh = RH::where('user_id', Auth::id())->first();

        if (!$rh || Auth::user()->role !== 'rh_admin' || $rh->entreprise_id !== $candidature->entreprise_id) {
            abort(403);
        }
    }

    private function authorizeCanValidate(Candidature $candidature): void
    {
        $rh = RH::where('user_id', Auth::id())->first();

        if (!$rh || !in_array(Auth::user()->role, ['rh_admin', 'assistant', 'rh_user']) || $rh->entreprise_id !== $candidature->entreprise_id) {
            abort(403);
        }
    }
}
