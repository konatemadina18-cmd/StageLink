<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'nom',
    'prenom',
    'display_name',
    'email',
    'password',
    'role',
    'telephone',
    'fonction',
    'photo',
    'settings',
    'two_factor_enabled_at',
    'two_factor_method',
    'two_factor_secret',
    'two_factor_pending_secret',
    'two_factor_code',
    'two_factor_expires_at',
    'date_naissance',
    'filiere',
    'niveau',
])]
#[Hidden([
    'password',
    'remember_token'
])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
            'two_factor_enabled_at' => 'datetime',
            'two_factor_secret' => 'encrypted',
            'two_factor_pending_secret' => 'encrypted',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    public function candidat()
    {
        return $this->hasOne(Candidat::class);
    }

    public function rh()
    {
        return $this->hasOne(RH::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
