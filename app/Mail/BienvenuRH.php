<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Entreprise;

class BienvenuRH extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Entreprise $entreprise
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur StageLink, ' . $this->user->prenom . ' !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenu_rh',
        );
    }
}