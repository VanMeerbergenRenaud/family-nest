<?php

namespace App\Providers;

use App\Livewire\Breadcrumb;
use App\Services\FamilyRoleService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Livewire strict locked all the properties by default
        // LivewireStrict::lockProperties(); // globally

        /**
         * Registers the FamilyRoleService as a singleton ensures that
         * only one instance of the service will be created and reused
         * throughout the entire application, conserving resources.
         *
         * The service handles roles and permissions within the family
         * context of the application, and will be automatically
         * injected into classes that require it via dependency injection.
         */
        $this->app->singleton(FamilyRoleService::class, function ($app) {
            return new FamilyRoleService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Livewire::component('breadcrumb', Breadcrumb::class);
    }
}
