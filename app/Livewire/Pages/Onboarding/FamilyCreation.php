<?php

namespace App\Livewire\Pages\Onboarding;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Jobs\SendFamilyInvitation;
use App\Models\Family as FamilyModel;
use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Title('Créer votre famille')]
class FamilyCreation extends Component
{
    public int $step = 1;

    #[Validate('required|min:2|max:255')]
    public string $familyName = '';

    public array $members = [];

    public function mount()
    {
        if (Auth::user()->family()) {
            $this->redirectRoute('dashboard');
        }

        $this->addMember();
    }

    public function addMember(): void
    {
        $this->members[] = [
            'email' => '',
            'permission' => FamilyPermissionEnum::Editor->value,
            'relation' => FamilyRelationEnum::Member->value,
            'valid' => true,
            'error' => '',
        ];
    }

    public function removeMember($index): void
    {
        if (isset($this->members[$index])) {
            unset($this->members[$index]);
            $this->members = array_values($this->members);
        }
    }

    public function validateMemberEmail($index): void
    {
        if (! isset($this->members[$index])) {
            return;
        }

        $email = $this->members[$index]['email'];

        if (empty($email)) {
            $this->members[$index]['valid'] = true;
            $this->members[$index]['error'] = '';

            return;
        }

        try {
            $this->validateOnly("members.$index.email", [
                "members.$index.email" => [
                    'email',
                    function ($attribute, $value, $fail) use ($email) {
                        if ($email === Auth::user()->email) {
                            $fail('Vous ne pouvez pas vous inviter vous-même');
                        }

                        $count = 0;
                        foreach ($this->members as $member) {
                            if ($member['email'] === $email) {
                                $count++;
                            }
                        }

                        if ($count > 1) {
                            $fail('Cet email est déjà dans la liste');
                        }
                    },
                ],
            ]);

            $this->members[$index]['valid'] = true;
            $this->members[$index]['error'] = '';

        } catch (ValidationException $e) {
            $this->members[$index]['valid'] = false;
            $this->members[$index]['error'] = $e->validator->errors()->first("members.$index.email");
            Toaster::error('Veuillez corriger l\'erreur dans l\'email');
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'familyName' => 'required|min:2|max:255',
            ]);
        } elseif ($this->step === 2) {
            $allValid = true;

            foreach ($this->members as $index => $member) {
                if (! empty($member['email'])) {
                    $this->validateMemberEmail($index);
                    if (! $member['valid']) {
                        $allValid = false;
                    }
                }
            }

            if (! $allValid) {
                Toaster::error('Veuillez corriger les erreurs dans les emails');

                return;
            }
        }

        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submitForm(): void
    {
        try {
            $family = FamilyModel::create([
                'name' => $this->familyName,
            ]);

            // Attach the authenticated user as the family admin
            $family->users()->attach(Auth::id(), [
                'permission' => FamilyPermissionEnum::Admin->value,
                'relation' => FamilyRelationEnum::Self->value,
                'is_admin' => true,
            ]);

            $invitationCount = 0;

            foreach ($this->members as $member) {
                if (empty($member['email']) || ! $member['valid']) {
                    continue;
                }

                $token = Str::uuid();
                $permission = FamilyPermissionEnum::tryFrom($member['permission']);
                $isAdmin = $permission && $permission->isAdmin();

                $invitation = FamilyInvitation::create([
                    'family_id' => $family->id,
                    'invited_by' => Auth::id(),
                    'email' => $member['email'],
                    'token' => $token,
                    'permission' => $member['permission'],
                    'relation' => $member['relation'],
                    'is_admin' => $isAdmin,
                    'expires_at' => now()->addDays(7),
                    'send_failed' => false,
                ]);

                SendFamilyInvitation::dispatch(
                    $invitation,
                    $family,
                    Auth::user()
                );

                $invitationCount++;
            }

            $this->step = 4;

            if ($invitationCount > 0) {
                Toaster::success("Votre famille a été créée et $invitationCount invitation(s) ont été envoyées!");
            } else {
                Toaster::success('Votre famille a été créée avec succès!');
            }
        } catch (\Exception $e) {
            Toaster::error('Une erreur est survenue lors de la création de la famille');
            Log::error('Erreur lors de la création de la famille: '.$e->getMessage());
        }
    }

    public function skipInvitations(): void
    {
        $this->members = [];
        $this->submitForm();
    }

    public function finishOnboarding(): void
    {
        $this->redirectRoute('dashboard');
    }

    public function hasInvitedMembers(): bool
    {
        return count(array_filter($this->members, fn ($member) => ! empty($member['email']))) > 0;
    }

    public function render()
    {
        $availablePermissions = FamilyPermissionEnum::getPermissionOptions();
        $availableRelations = FamilyRelationEnum::getRelationOptions();

        return view('livewire.pages.onboarding.family-creation', [
            'availablePermissions' => $availablePermissions,
            'availableRelations' => $availableRelations,
        ])->layout('layouts.onboarding');
    }
}
