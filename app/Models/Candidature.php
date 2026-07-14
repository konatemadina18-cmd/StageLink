<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = [
    'candidat_id',
    'entreprise_id',
    'r_h_id',
    'offre_id',
    'date_candidature',
    'type_stage',
    'duree',
    'statut',
    'score',
    'commentaire_rh',
    'lettre_motivation',
    'cv',
    'lettre_recommandation', // ← ajouté
    'linkedin',
    'portfolio',     // ← ajouté
    ];

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }
    public function offre()
    {
    return $this->belongsTo(Offre::class);
    }
    public function rh()
    {
        return $this->belongsTo(RH::class, 'r_h_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function entretiens()
    {
        return $this->hasMany(Entretien::class);
    }

    public function dernierEntretien()
    {
        return $this->hasOne(Entretien::class)->latestOfMany();
    }
}
