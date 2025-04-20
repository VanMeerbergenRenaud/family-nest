<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Masmerise\Toaster\Toaster;
use Symfony\Component\HttpFoundation\Response;

class HasFamily
{
    /**
     * Checks if the user has an associated family.
     * Redirects to the family page if not.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->hasFamily()) {
            Toaster::info('Vous devez d\'abord créer ou rejoindre une famille pour accéder à cette fonctionnalité.');

            return redirect()->route('family');
        }

        return $next($request);
    }
}
