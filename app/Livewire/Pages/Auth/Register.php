<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\RegisterForm;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Register extends Component
{
    public RegisterForm $form;

    public bool $showGeneralCondition = false;

    public function register(): void
    {
        $this->validate();

        $this->form->register();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function showConditions(): void
    {
        $this->showGeneralCondition = true;
    }
}
