<?php

namespace App\Providers;

use App\Livewire\Pages\Settings\StyleCustomizer;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class StyleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enregistrer le composant Livewire
        Livewire::component('components.style-customizer', StyleCustomizer::class);
    }
}
