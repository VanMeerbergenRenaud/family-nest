<?php

namespace App\Jobs;

use App\Mail\FamilyInvitation;
use App\Models\Family;
use App\Models\FamilyInvitation as FamilyInvitationModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendFamilyInvitation implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public FamilyInvitationModel $invitation,
        public Family $family,
        public User $inviter
    ) {}

    public function handle(): void
    {
        try {
            if (! $this->invitationStillExists()) {
                Log::info('Invitation deleted before sending email', [
                    'invitation_id' => $this->invitation->id,
                    'email' => $this->invitation->email,
                ]);

                return;
            }

            $this->markAsSending();

            Mail::to($this->invitation->email)
                ->send(new FamilyInvitation(
                    $this->invitation,
                    $this->family,
                    $this->inviter,
                    route('family.invitation', ['token' => $this->invitation->token])
                ));

            $this->markAsSent();

            Log::info('Family invitation sent successfully', [
                'invitation_id' => $this->invitation->id,
                'email' => $this->invitation->email,
                'family_name' => $this->family->name,
                'inviter_name' => $this->inviter->name,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'opération : '.$e->getMessage());
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Family invitation job failed after all attempts', [
            'invitation_id' => $this->invitation->id,
            'email' => $this->invitation->email,
            'family_name' => $this->family->name,
            'error' => $exception->getMessage(),
        ]);
    }

    private function invitationStillExists(): bool
    {
        return FamilyInvitationModel::where('id', $this->invitation->id)->exists();
    }

    private function markAsSending(): void
    {
        $this->invitation->update([
            'send_failed' => false,
            'send_error' => null,
            'updated_at' => now(),
        ]);
    }

    private function markAsSent(): void
    {
        $this->invitation->update([
            'send_failed' => false,
            'send_error' => null,
            'updated_at' => now(),
        ]);
    }

    public function backoff(): array
    {
        return [10, 30, 60];
    }
}
