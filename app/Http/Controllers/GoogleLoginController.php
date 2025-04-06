<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        Log::info('Google callback initiated.');

        $googleUser = Socialite::driver('google')->stateless()->user();
        Log::info('Google user retrieved.', ['googleUser' => $googleUser]);

        $user = User::where('email', $googleUser->email)->first();
        Log::info('User lookup completed.', ['user' => $user]);

        if (! $user) {
            Log::info('No user found, creating a new user.');
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(rand(100000, 999999)),
                'avatar' => null,
            ]);
            Log::info('New user created.', ['user' => $user]);
        }

        Auth::login($user);
        Log::info('User logged in.', ['user' => $user]);

        return redirect(RouteServiceProvider::HOME);
    }
}
