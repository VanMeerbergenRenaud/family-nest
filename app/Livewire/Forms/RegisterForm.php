<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterForm extends Form
{
    #[Validate]
    public string $name = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public string $password = '';

    #[Validate]
    public bool $general_conditions = true;

    public bool $redirect_to_onboarding = true;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', Rules\Password::defaults()],
            'general_conditions' => ['required', 'boolean', 'accepted'],
        ];
    }

    public function register(): void
    {
        $validated = $this->validate();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Utiliser le service de vérification email
        if (config('app.email_verification_enabled', true)) {
            $emailVerificationService = app(EmailVerificationService::class);
            $emailVerificationService->sendVerificationEmail($user, false);
        }

        Auth::login($user);

        Session::regenerate();

        if ($this->redirect_to_onboarding) {
            Session::put('new_registration', true);
        }
    }
}
