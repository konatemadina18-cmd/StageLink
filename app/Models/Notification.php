<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'candidat_id',
        'message',
        'date_envoi',
        'lu'
    ];

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }
}