<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'candidat_id',
        'candidature_id',
        'nom_fichier',
        'type_document',
        'chemin',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class);
    }

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }
}
