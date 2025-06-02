<?php

namespace App\Mail;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Models\Family;
use App\Models\FamilyInvitation as FamilyInvitationModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FamilyInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public FamilyInvitationModel $invitation,
        public Family $family,
        public User $inviter,
        public string $invitationUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            replyTo: config('mail.support.address'),
            subject: "🏠 {$this->inviter->name} vous invite à rejoindre sa famille sur ".config('app.name'),
            tags: ['family-invitation'],
            metadata: [
                'family_id' => $this->family->id,
                'inviter_id' => $this->inviter->id,
                'invitation_id' => $this->invitation->id,
            ],
        );
    }

    public function content(): Content
    {
        $permission = FamilyPermissionEnum::tryFrom($this->invitation->permission);
        $relation = FamilyRelationEnum::tryFrom($this->invitation->relation);

        return new Content(
            markdown: 'emails.views.family-invitation',
            with: [
                'invitation' => $this->invitation,
                'family' => $this->family,
                'inviter' => $this->inviter,
                'invitedEmail' => $this->invitation->email,
                'permissionLabel' => $permission?->label() ?? ucfirst($this->invitation->permission), // Utiliser permissionLabel au lieu de role
                'relationLabel' => $relation?->label() ?? ucfirst($this->invitation->relation), // Utiliser relationLabel au lieu de relation
                'invitationUrl' => $this->invitationUrl, // Utiliser invitationUrl au lieu de url
                'appName' => config('app.name'),
                'expirationDays' => (int) now()->diffInDays($this->invitation->expires_at), // Convertir en entier
                'supportEmail' => config('mail.support.address', 'support@familynest.com'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Family invitation email failed', [
            'invitation_id' => $this->invitation->id,
            'email' => $this->invitation->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
