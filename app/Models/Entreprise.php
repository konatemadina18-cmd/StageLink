<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $fillable = [
        'user_id',
        'nom',
        'adresse',
        'telephone',
        'email',
        'logo',
        'site_web',
        'description',
        'secteur_activite',
        'taille',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rhs()
    {
        return $this->hasMany(RH::class);
    }

    public function offres()
    {
        return $this->hasMany(Offre::class);
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }
}
