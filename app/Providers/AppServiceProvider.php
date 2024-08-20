<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use WireElements\LivewireStrict\LivewireStrict;

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
        //
    }
}
