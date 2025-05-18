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
            }

            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}
