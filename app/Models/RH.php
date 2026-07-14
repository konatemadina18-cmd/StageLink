<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RH extends Model
{
    protected $fillable = [
        'user_id',
        'entreprise_id',
        'fonction',
        'is_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'r_h_id');
    }
}
