<?php

namespace App\Jobs;

use App\Mail\FamilyInvitation;
use App\Models\Family;
use App\Models\FamilyInvitation as FamilyInvitationModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendFamilyInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // The number of attempts for the job.
    public int $tries = 3;

    // The delay between attempts in seconds.
    public int $backoff = 10;

    public function __construct(
        public FamilyInvitationModel $invitation,
        public Family $family,
        public User $inviter
    ) {
        //
    }

    public function handle(): void
    {
        try {
            // Check if the invitation still exists in the database
            if (! FamilyInvitationModel::find($this->invitation->id)) {
                Log::info('Invitation already deleted, skipping email sending', [
                    'invitation_id' => $this->invitation->id,
                ]);

                return;
            }

            Mail::to($this->invitation->email)->send(new FamilyInvitation(
                $this->family,
                $this->inviter,
                $this->invitation->token,
                $this->invitation->permission,
                $this->invitation->relation
            ));

            // Mark as successfully sent
            $this->invitation->update([
                'send_failed' => false,
                'send_error' => null,
                'sent_at' => now(),
            ]);

            // Update the cache to inform the user interface
            Cache::put('invitation_status_'.$this->invitation->id, 'success', now()->addMinutes(10));

            // Log success
            Log::info('Family invitation sent successfully', [
                'invitation_id' => $this->invitation->id,
                'email' => $this->invitation->email,
                'family' => $this->family->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send family invitation email', [
                'invitation_id' => $this->invitation->id,
                'email' => $this->invitation->email,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Update the cache to inform the user interface
            Cache::put('invitation_status_'.$this->invitation->id, 'error:'.$e->getMessage(), now()->addMinutes(30));

            // If this is the last attempt, mark the error in the database
            if ($this->attempts() >= $this->tries) {
                $this->invitation->update([
                    'send_failed' => true,
                    'send_error' => $e->getMessage(),
                ]);
            }

            Log::error('Family invitation job failed', [
                'invitation_id' => $this->invitation->id,
                'email' => $this->invitation->email,
                'attempt' => $this->attempts(),
            ]);
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Family invitation job failed after all attempts', [
            'invitation_id' => $this->invitation->id,
            'email' => $this->invitation->email,
            'error' => $exception->getMessage(),
        ]);

        // Update the database
        $this->invitation->update([
            'send_failed' => true,
            'send_error' => $exception->getMessage(),
        ]);

        // Update the cache with final status
        Cache::put('invitation_status_'.$this->invitation->id, 'failed:'.$exception->getMessage(), now()->addHours(1));
    }
}
