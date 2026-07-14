<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    protected $fillable = [
        'entreprise_id',
        'r_h_id',
        'titre',
        'description',
        'type_stage',
        'duree',
        'filiere_cible',
        'competences_requises',
        'lieu',
        'date_debut',
        'date_fin_candidature',
        'statut',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function rh()
    {
        return $this->belongsTo(RH::class, 'r_h_id');
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }
}