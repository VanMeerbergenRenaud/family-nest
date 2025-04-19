<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RedirectFirstLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if this is a new registration
            $newRegistration = Session::has('new_registration');

            // Check if the user has a family
            if ((! $user->family() || $newRegistration) && ! $request->routeIs('onboarding*')) {

                // Remove the session flag once it's been used
                if ($newRegistration) {
                    Session::forget('new_registration');
                }

                return redirect()->route('onboarding.family');
            }
        }

        return $next($request);
    }
}
