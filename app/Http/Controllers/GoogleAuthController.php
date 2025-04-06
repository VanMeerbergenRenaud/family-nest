<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\GoogleAuthErrorHandler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    use GoogleAuthErrorHandler;

    /**
     * Redirection vers Google pour l'authentification
     *
     * @return RedirectResponse
     */
    public function redirect()
    {
        Log::channel('google_auth')->info('Redirection vers Google OAuth', [
            'time' => now()->toDateTimeString(),
        ]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Récupération des informations utilisateur depuis Google
     *
     * @return RedirectResponse
     */
    public function callback()
    {
        try {
            Log::channel('google_auth')->info('Début du callback Google OAuth', [
                'time' => now()->toDateTimeString(),
            ]);

            $googleUser = Socialite::driver('google')->user();

            Log::channel('google_auth')->info('Données Google récupérées', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'id' => $googleUser->getId(),
            ]);

            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                Log::channel('google_auth')->info('Création d\'un nouvel utilisateur', [
                    'email' => $googleUser->getEmail(),
                ]);

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Mot de passe aléatoire
                ]);

                event(new Registered($user));
            } else {
                Log::channel('google_auth')->info('Utilisateur existant trouvé', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }

            Auth::login($user);
            Session::regenerate();

            Log::channel('google_auth')->info('Authentification réussie', [
                'user_id' => $user->id,
                'time' => now()->toDateTimeString(),
            ]);

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            $errorMessage = $this->handleGoogleAuthError($e, 'callback');

            return redirect()->route('login')->with('google_auth_error', $errorMessage);
        }
    }
}
