<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use App\Models\User;
use Livewire\Component;

class Sidebar extends Component
{
    public User $user;

    public bool $expanded = true;

    // Modal states
    public bool $showShortcutsModal = false;

    protected $listeners = [
        'user-profile-updated' => 'refreshUserData',
    ];

    public function mount()
    {
        $this->user = auth()->user();
        $this->expanded = session('sidebar_expanded', true);
    }

    public function toggleSidebar(): void
    {
        $this->expanded = ! $this->expanded;

        $this->dispatch('sidebar-toggled', expanded: $this->expanded);

        session()->put('sidebar_expanded', $this->expanded);
    }

    public function refreshUserData(): void
    {
        $this->user = auth()->user()->fresh();
    }

    public function seeProfile(): void
    {
        $this->redirectRoute('settings.profile');
    }

    public function seeShortcut(): void
    {
        $this->showShortcutsModal = true;
    }

    public function suppressAccount(): void
    {
        $this->redirectRoute('settings.danger');
    }

    public function logout(): void
    {
        app(Logout::class)->__invoke();
        $this->redirectRoute('welcome');
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}
