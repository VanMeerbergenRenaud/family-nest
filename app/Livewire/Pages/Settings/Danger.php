<?php

namespace App\Livewire\Pages\Settings;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Réglages de suppression')]
class Danger extends Component
{
    public $password = '';

    public $showModal = false;

    protected $rules = [
        'password' => ['required', 'string', 'current_password'],
    ];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate();

        tap(Auth::user(), function ($user) use ($logout) {
            $logout();
            $user->delete();
        });

        $this->redirectRoute('welcome');
    }

    public function render()
    {
        return view('livewire.pages.settings.danger')
            ->layout('layouts.app-sidebar');
    }
}
