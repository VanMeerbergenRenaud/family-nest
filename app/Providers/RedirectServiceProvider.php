<?php

namespace App\Providers;

use App\Http\Middleware\RedirectFirstLogin;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class RedirectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * This method adds the RedirectFirstLogin middleware to the global middleware stack
     * of the application. This middleware will be executed on every HTTP request and
     * will automatically redirect users on their first login to a specific page (likely
     * the onboarding page).
     */
    public function boot(Kernel $kernel): void
    {
        $kernel->pushMiddleware(RedirectFirstLogin::class);
    }
}
