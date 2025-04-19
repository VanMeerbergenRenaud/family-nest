<?php

namespace App\Mail;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
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

    public function __construct(
        public Family $family,
        public User $inviter,
        public string $token,
        public string $role,
        public string $relation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Salut, {$this->inviter->name} vous invite à rejoindre sa famille sur FamilyNest",
        );
    }

    public function content(): Content
    {
        $permission = FamilyPermissionEnum::tryFrom($this->role);
        $relation = FamilyRelationEnum::tryFrom($this->relation);

        $url = route('family.invitation', ['token' => $this->token]);

        return new Content(
            markdown: 'emails.family.invitation',
            with: [
                'family' => $this->family,
                'inviter' => $this->inviter,
                'role' => $permission ? $permission->label() : ucfirst($this->role),
                'relation' => $relation ? $relation->label() : ucfirst($this->relation),
                'url' => $url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
