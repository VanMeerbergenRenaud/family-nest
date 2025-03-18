<?php

namespace App\Providers;

use App\Livewire\Breadcrumb;
use App\Livewire\BreadcrumbDynamic;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Livewire strict locked all the properties by default */
        // LivewireStrict::lockProperties(); // globally
        // LivewireStrict::lockProperties(shouldLockProperties: app()->isLocal()); // locally only
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer les composants Livewire
        Livewire::component('breadcrumb', Breadcrumb::class);
    }
}
