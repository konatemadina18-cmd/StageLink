<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateDocument extends Model
{
    protected $fillable = [
        'candidat_id',
        'nom_fichier',
        'type_document',
        'chemin',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }
}
