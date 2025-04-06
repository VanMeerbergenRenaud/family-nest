<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                $avatarPath = null;
                if ($avatarUrl = $googleUser->getAvatar()) {
                    try {
                        $avatarPath = 'avatars/'.Str::uuid().'.jpg';

                        $avatarContent = file_get_contents($avatarUrl);

                        if ($avatarContent) {
                            Storage::disk('s3')->put($avatarPath, $avatarContent);
                            Log::channel('google_auth')->info('Avatar téléchargé avec succès', [
                                'path' => $avatarPath,
                            ]);
                        }
                    } catch (\Exception $e) {
                        $avatarPath = null;
                        Log::channel('google_auth')->error('Erreur lors du téléchargement de l\'avatar', [
                            'message' => $e->getMessage(),
                        ]);
                    }
                }

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Random password
                    'avatar' => $googleUser->getAvatar(),
                ]);

                event(new Registered($user));
            }

            if (! $user->avatar && ($avatarUrl = $googleUser->getAvatar())) {
                try {
                    $avatarPath = 'avatars/'.Str::uuid().'.jpg';

                    $avatarContent = file_get_contents($avatarUrl);

                    if ($avatarContent) {
                        if ($user->avatar && Storage::disk('s3')->exists($user->avatar)) {
                            try {
                                Storage::disk('s3')->delete($user->avatar);
                                Log::channel('google_auth')->info('Ancien avatar supprimé', [
                                    'user_id' => $user->id,
                                    'path' => $user->avatar,
                                ]);
                            } catch (\Exception $e) {
                                Log::channel('google_auth')->error('Erreur lors de la suppression de l\'ancien avatar', [
                                    'message' => $e->getMessage(),
                                    'user_id' => $user->id,
                                ]);
                            }
                        }

                        Storage::disk('s3')->put($avatarPath, $avatarContent);

                        $user->update(['avatar' => $avatarPath]);

                        Log::channel('google_auth')->info('Avatar mis à jour pour l\'utilisateur existant', [
                            'user_id' => $user->id,
                            'path' => $avatarPath,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::channel('google_auth')->error('Erreur lors de la mise à jour de l\'avatar', [
                        'message' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                }
            }

            Auth::login($user);

            Session::regenerate();

            return redirect()->intended(route('dashboard'));

        } catch (\Exception) {
            Toaster::error('Erreur lors de l\'authentification avec Google::Veuillez réessayer à nouveau.');

            return redirect()->route('login');
        }
    }
}
