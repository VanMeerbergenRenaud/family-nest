<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Actions\Logout;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Vérifier l\'email')]
class VerifyEmail extends Component
{
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {

            if (! Auth::user()->family()) {
                $this->redirectRoute('onboarding.family');

                return;
            }

            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        try {
            Auth::user()->sendEmailVerificationNotification();
            Session::flash('status', 'verification-link-sent');
        } catch (Exception $e) {
            // Check if the error is related to MailerSend limits
            if (str_contains($e->getMessage(), 'reached trial account unique recipients limit') ||
                str_contains($e->getMessage(), '#MS42225')) {

                // Log the error for administrators (me)
                Log::warning('MailerSend limit reached. Auto-verifying user: '.Auth::user()->email);

                // Automatically verify the user as a temporary solution
                DB::table('users')
                    ->where('id', Auth::user()->id)
                    ->update(['email_verified_at' => now()]);

                Session::flash('status', 'Votre email a été automatiquement vérifié en raison de limitations techniques temporaires.');

                if (! Auth::user()->family()) {
                    $this->redirectRoute('onboarding.family');
                } else {
                    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
                }
            } else {
                Log::error('Email verification error: '.$e->getMessage());
                Session::flash('status', 'Une erreur s\'est produite. Veuillez réessayer plus tard.');
            }
        }
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}
