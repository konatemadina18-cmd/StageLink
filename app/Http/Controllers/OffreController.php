<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\RH;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OffreController extends Controller
{
    public function index()
    {
        // On affiche seulement les offres de l'entreprise du RH connecte.
        $rh = RH::where('user_id', Auth::id())->first();

        $offres = $rh
            ? Offre::where('entreprise_id', $rh->entreprise_id)->latest()->get()
            : collect();

        return view('offres.index', compact('offres', 'rh'));
    }

    public function create()
    {
        return view('offres.create');
    }

    public function disponibles()
    {
        return redirect()->route('dashboard.candidat');
    }

    public function store(Request $request)
    {
        // Validation du formulaire avant de publier l'offre.
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'type_stage' => 'required|string',
            'duree' => 'required|string',
            'filiere_cible' => 'nullable|string|max:255',
            'competences_requises' => 'nullable|string|max:500',
            'lieu' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin_candidature' => 'nullable|date',
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'type_stage.required' => 'Le type de stage est obligatoire.',
            'duree.required' => 'La duree est obligatoire.',
        ]);

        $rh = RH::where('user_id', Auth::id())->first();

        // Seul le RH administrateur peut creer une nouvelle offre.
        if (!$rh || Auth::user()->role !== 'rh_admin') {
            return back()->withErrors(['error' => 'Action reservee au RH administrateur.']);
        }

        Offre::create([
            'entreprise_id' => $rh->entreprise_id,
            'r_h_id' => $rh->id,
            'titre' => $request->titre,
            'description' => $request->description,
            'type_stage' => $request->type_stage,
            'duree' => $request->duree,
            'filiere_cible' => $request->filiere_cible,
            'competences_requises' => $request->competences_requises,
            'lieu' => $request->lieu,
            'date_debut' => $request->date_debut,
            'date_fin_candidature' => $request->date_fin_candidature,
            'statut' => 'active',
        ]);

        return redirect()->route('dashboard.rh')
            ->with('success', 'Offre publiee avec succes !');
    }

    public function toggleStatut(Offre $offre)
    {
        // Permet de fermer ou rouvrir une offre.
        $this->authorizeRhAdminFor($offre);

        $offre->update([
            'statut' => $offre->statut === 'active' ? 'fermee' : 'active',
        ]);

        return back()->with('success', 'Statut de l\'offre mis a jour.');
    }

    public function destroy(Offre $offre)
    {
        // Suppression d'une offre apres verification des droits.
        $this->authorizeRhAdminFor($offre);

        $offre->delete();

        return back()->with('success', 'Offre supprimee.');
    }

    private function authorizeRhAdminFor(Offre $offre): void
    {
        // Securite : le RH ne peut agir que sur les offres de son entreprise.
        $rh = RH::where('user_id', Auth::id())->first();

        if (!$rh || Auth::user()->role !== 'rh_admin' || $offre->entreprise_id !== $rh->entreprise_id) {
            abort(403);
        }
    }
}
