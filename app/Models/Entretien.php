<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    protected $fillable = [
        'candidature_id',
        'recruteur_id',
        'date_entretien',
        'heure',
        'lieu',
        'type',
        'statut',
        'commentaires',
    ];

    protected $casts = [
        'date_entretien' => 'date',
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class);
    }

    public function recruteur()
    {
        return $this->belongsTo(User::class, 'recruteur_id');
    }
}
