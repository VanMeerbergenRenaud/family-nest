<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Livewire\Component;

class Sidebar extends Component
{
    public $user;

    public bool $expanded = true;

    public function mount()
    {
        $this->user = auth()->user();
        // Utiliser une valeur par défaut de true
        $this->expanded = session('sidebar_expanded', true);
    }

    public function toggleSidebar()
    {
        $this->expanded = ! $this->expanded;

        // Stocker l'état dans la session
        session(['sidebar_expanded' => $this->expanded]);

        // Émettre l'événement avec l'état mis à jour
        $this->dispatch('sidebar-toggled', expanded: $this->expanded);
    }

    public function seeProfile()
    {
        $this->redirectRoute('settings.profile');
    }

    public function seeShortcut()
    {
        dd('Voir les raccourcis');
    }

    public function inviteMember()
    {
        dd('Inviter un membre');
    }

    public function seeUpdates()
    {
        dd('Voir les mises à jour');
    }

    public function seeSupport()
    {
        dd('Voir le support');
    }

    public function seeArchives()
    {
        $this->redirectRoute('invoices.archived');
    }

    public function logout()
    {
        app(Logout::class)->__invoke();
        $this->redirectRoute('welcome');
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}
