<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\RegisterForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Inscription')]
class Register extends Component
{
    public RegisterForm $form;

    public bool $showGeneralCondition = false;

    public function register(): void
    {
        $this->validate();

        $this->form->register();

        if (config('app.email_verification_enabled', true)) {
            $this->redirectRoute('verification.notice');
        } else {
            $this->redirectRoute('onboarding.family');
        }
    }

    public function showConditions(): void
    {
        $this->showGeneralCondition = true;
    }
}
