<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Masmerise\Toaster\Toaster;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $avatarUrl = $googleUser->getAvatar();
            $user = User::where('email', $googleUser->getEmail())->first();
            $isNewUser = false;

            // Création d'un nouvel utilisateur
            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(32)),
                    'auth_provider' => 'google',
                ]);
                $isNewUser = true;
            }

            // Pour les nouveaux utilisateurs ou ceux sans avatar, on récupère l'avatar Google
            if (($isNewUser || ! $user->avatar) && $avatarUrl) {
                $this->updateUserAvatar($user, $avatarUrl);
            }

            $user->markEmailAsVerified();

            Auth::login($user);

            Session::regenerate();

            if (!$user->family()) {
                return redirect()->route('onboarding.family');
            }

            return redirect()->intended(route('dashboard'));
        } catch (\Exception) {
            Toaster::error('Erreur lors de l\'authentification avec Google::Veuillez réessayer à nouveau.');

            return redirect()->route('login');
        }
    }

    private function updateUserAvatar(User $user, string $avatarUrl): void
    {
        try {
            $avatarDir = 'avatars/user_'.$user->id;
            $filename = Str::random(40).'.jpeg';
            $avatarPath = $avatarDir.'/'.$filename;
            $avatarContent = file_get_contents($avatarUrl);

            // Suppression de l'ancien avatar si existant
            if ($user->avatar && Storage::disk('s3')->exists($user->avatar)) {
                try {
                    Storage::disk('s3')->delete($user->avatar);
                } catch (\Exception $e) {
                    Log::channel('google_auth')->error('Erreur lors de la suppression de l\'ancien avatar', [
                        'message' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                }
            }

            if ($avatarContent) {
                Storage::disk('s3')->put($avatarPath, $avatarContent);
                $user->update(['avatar' => $avatarPath]);
            }
        } catch (\Exception $e) {
            Log::channel('google_auth')->error('Erreur lors de la gestion de l\'avatar', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
        }
    }
}
