<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
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
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    // TODO : Remove on production
    public function continue(): void
    {
        // Mark the user as verified for testing purposes
        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::user()->markEmailAsVerified();
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}
