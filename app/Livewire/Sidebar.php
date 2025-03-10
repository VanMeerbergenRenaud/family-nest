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

        $storedExpanded = $this->js('localStorage.getItem("sidebarExpanded")');

        if ($storedExpanded) {
            $this->expanded = $storedExpanded === 'true';
        }
    }

    public function toggleSidebar()
    {
        $this->expanded = ! $this->expanded;
        $this->dispatch('sidebar-toggled', expanded: $this->expanded);
        $expandedStringForJS = $this->expanded ? 'true' : 'false';
        $this->js("localStorage.setItem('sidebarExpanded', '".$expandedStringForJS."')");
    }

    public function seeProfile()
    {
        $this->redirectRoute('profile');
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
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}
