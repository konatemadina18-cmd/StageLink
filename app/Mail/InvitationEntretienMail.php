<?php

namespace App\Mail;

use App\Models\Candidature;
use App\Models\Entretien;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationEntretienMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Candidature $candidature,
        public Entretien $entretien,
        public User $recruteur
    ) {
        $this->candidature->loadMissing(['candidat.user', 'entreprise', 'offre']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation a un entretien - StageLink',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation_entretien',
        );
    }
}
