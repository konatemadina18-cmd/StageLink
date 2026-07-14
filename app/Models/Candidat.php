<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Candidat extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'cv',
        'adresse',
        'date_naissance',
        'telephone',
        'filiere',
        'niveau',
        'linkedin',
        'github',
        'portfolio',
        'portfolio_github',
        'competences',
        'experiences',
        'langues',
        'certifications',
        'use_default_cv',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function documents()
    {
        return $this->hasMany(CandidateDocument::class);
    }
}
