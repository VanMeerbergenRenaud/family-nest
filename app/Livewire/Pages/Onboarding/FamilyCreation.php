<?php

namespace App\Livewire\Pages\Onboarding;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Livewire\Forms\FamilyForm;
use App\Services\FamilyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Title('Créer une famille')]
class FamilyCreation extends Component
{
    public FamilyForm $form;

    public int $step = 1;

    public bool $showWelcome = true;

    public array $members = [];

    protected FamilyService $familyService;

    public function boot(FamilyService $familyService)
    {
        $this->familyService = $familyService;
    }

    public function mount()
    {
        // Rediriger si l'utilisateur a déjà une famille
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

    public function removeMember(int $index): void
    {
        if (isset($this->members[$index])) {
            unset($this->members[$index]);
            $this->members = array_values($this->members);
        }
    }

    public function validateMemberEmail(int $index): void
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
                        // Ne pas s'inviter soi-même
                        if ($email === Auth::user()->email) {
                            $fail('Vous ne pouvez pas vous inviter vous-même');
                        }

                        // Pas de doublons dans la liste
                        if (collect($this->members)->pluck('email')->filter()->countBy()->get($email, 0) > 1) {
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

    // Navigate to the first step
    public function firstStep(): void
    {
        $this->showWelcome = false;
        $this->step = 1;
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'form.familyName' => 'required|min:2|max:255',
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
            // Ajouter une majuscule au nom de la famille
            $this->form->familyName = ucfirst($this->form->familyName);

            $family = $this->form->create();

            if (! $family) {
                Toaster::error('Une erreur est survenue lors de la création de la famille');

                return;
            }

            // Filtrer les membres valides
            $validMembers = $this->familyService->prepareInvitationsData($this->members, $family->id);

            // Utiliser le formulaire pour créer les invitations
            $invitationsCount = $this->form->createInvitations($validMembers);

            $this->step = 4;

            if ($invitationsCount > 0) {
                Toaster::success("Votre famille a été créée et $invitationsCount invitation(s) ont été envoyées!");
            } else {
                Toaster::success('Votre famille a été créée avec succès!');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la famille: '.$e->getMessage());
            Toaster::error('Une erreur est survenue lors de la création de la famille');
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
        return collect($this->members)->pluck('email')->filter()->isNotEmpty();
    }

    public function render()
    {
        return view('livewire.pages.onboarding.family-creation', [
            'familyName' => $this->form->familyName,
            'availablePermissions' => FamilyPermissionEnum::getPermissionOptions(),
            'availableRelations' => FamilyRelationEnum::getRelationOptions(),
        ])->layout('layouts.onboarding');
    }
}
