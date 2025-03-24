<?php

namespace App\Mail;

use App\Models\Family;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FamilyInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Family $family,
        public User $inviter,
        public string $token,
        public string $role,
        public string $relation
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->inviter->name} vous invite à rejoindre sa famille sur FamilyFinance",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $roleLabels = [
            'admin' => 'Administrateur',
            'editor' => 'Éditeur',
            'viewer' => 'Lecteur',
        ];

        $relationLabels = [
            'spouse' => 'Conjoint(e)',
            'father' => 'Père',
            'mother' => 'Mère',
            'brother' => 'Frère',
            'sister' => 'Sœur',
            'son' => 'Fils',
            'daughter' => 'Fille',
            'colleague' => 'Collègue',
            'colocataire' => 'Colocataire',
            'friend' => 'Ami(e)',
            'other' => 'Autre',
        ];

        $url = route('family.invitation', ['token' => $this->token]);

        return new Content(
            markdown: 'emails.family.invitation',
            with: [
                'family' => $this->family,
                'inviter' => $this->inviter,
                'role' => $roleLabels[$this->role] ?? ucfirst($this->role),
                'relation' => $relationLabels[$this->relation] ?? ucfirst($this->relation),
                'url' => $url,
            ],
        );
    }
}
