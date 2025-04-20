<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RedirectFirstLogin
{
    /**
     * Redirects the user to the onboarding process on their first login,
     * or if they haven't set up their family yet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $newRegistration = Session::has('new_registration');

            if ((! $user->hasFamily() || $newRegistration) && ! $request->routeIs('onboarding*')) {
                if ($newRegistration) {
                    Session::forget('new_registration');
                }

                return redirect()->route('onboarding.family');
            }
        }

        return $next($request);
    }
}
