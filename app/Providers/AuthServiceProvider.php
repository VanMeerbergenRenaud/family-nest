<?php

namespace App\Providers;

use App\Models\Family;
use App\Models\Invoice;
use App\Policies\FamilyPolicy;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
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
        // Define gates to quickly check permissions for family-related actions
        Gate::define('view-family', function ($user, $family) {
            return (new FamilyPolicy)->view($user, $family);
        });

        Gate::define('manage-family', function ($user, $family) {
            return (new FamilyPolicy)->update($user, $family);
        });

        Gate::define('admin-family', function ($user, $family) {
            return (new FamilyPolicy)->delete($user, $family);
        });

        Gate::define('invite-members', function ($user, $family) {
            return (new FamilyPolicy)->inviteMembers($user, $family);
        });

        Gate::define('manage-members', function ($user, $family) {
            return (new FamilyPolicy)->manageMembers($user, $family);
        });

        // Define gates for invoice-related actions
        Gate::define('view-invoice', function ($user, $invoice) {
            return (new InvoicePolicy)->view($user, $invoice);
        });

        Gate::define('create-invoice', function ($user) {
            return (new InvoicePolicy)->create($user);
        });

        Gate::define('update-invoice', function ($user, $invoice) {
            return (new InvoicePolicy)->update($user, $invoice);
        });

        Gate::define('archive-invoice', function ($user, $invoice) {
            return (new InvoicePolicy)->archive($user, $invoice);
        });

        Gate::define('delete-invoice', function ($user, $invoice) {
            return (new InvoicePolicy)->delete($user, $invoice);
        });
    }
}
