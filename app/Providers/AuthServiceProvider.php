<?php

namespace App\Providers;

use App\Models\Family;
use App\Models\Invoice;
use App\Policies\FamilyPolicy;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        Family::class => FamilyPolicy::class,
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /*
         * For example, to delete my brother's invoice, I can define a gate like this :

         Gate::define('delete-invoice-of-my-brother', function ($user, $invoice) {
             return app(InvoicePolicy::class)->delete($user, $invoice);
         });

         * Then I can use it with @can('delete-invoice-of-my-brother', $invoice) in my Blade template.
        */
    }
}
