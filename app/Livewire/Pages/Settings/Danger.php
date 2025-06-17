<?php

namespace App\Livewire\Pages\Settings;

use App\Enums\FamilyPermissionEnum;
use App\Livewire\Actions\Logout;
use App\Services\FamilyRoleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Réglages de suppression')]
class Danger extends Component
{
    public $password = '';

    public $showModal = false;

    protected $familyRoleService;

    public function boot(FamilyRoleService $familyRoleService): void
    {
        $this->familyRoleService = $familyRoleService;
    }

    protected function rules(): array
    {
        if (auth()->user()->auth_provider === 'google') {
            return [
                'password' => ['nullable', 'string'],
            ];
        }

        return [
            'password' => ['required', 'string', 'current_password'],
        ];
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate();

        $user = auth()->user();

        try {
            DB::beginTransaction();

            $this->promoteNewAdminIfNeeded($user);

            // Soft delete des factures
            $user->invoices()->delete();

            // Soft delete du profil utilisateur (modification de l'email pour éviter les conflits)
            $randomString = uniqid();
            $user->email = $user->email.'_'.$randomString.'@deleted.account';
            $user->save();

            $user->delete();

            DB::commit();

            $logout();

            $this->redirectRoute('welcome');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression du compte utilisateur: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.settings.danger')
            ->layout('layouts.app-sidebar');
    }

    /**
     * Promeut un autre membre de la famille au rôle d'administrateur
     * si l'utilisateur actuel est le seul administrateur de la famille
     */
    protected function promoteNewAdminIfNeeded($user): void
    {
        $userFamilies = $user->families()->get();

        foreach ($userFamilies as $family) {
            $familyId = $family->id;

            // Compter le nombre total de membres dans cette famille
            $memberCount = DB::table('family_user')
                ->where('family_id', $familyId)
                ->count();

            // Si l'utilisateur est le seul membre, supprimer la famille
            if ($memberCount === 1) {
                // On supprime la famille et toutes ses données associées
                $family->delete();

                continue; // Passer à la famille suivante
            }

            $userPermission = $family->pivot->permission;

            // Si l'utilisateur est administrateur de cette famille
            if ($userPermission === FamilyPermissionEnum::Admin->value) {
                // Compter le nombre d'administrateurs dans cette famille
                $adminCount = DB::table('family_user')
                    ->where('family_id', $familyId)
                    ->where('permission', FamilyPermissionEnum::Admin->value)
                    ->count();

                // Si c'est le dernier administrateur
                if ($adminCount === 1) {
                    // Chercher d'abord un éditeur pour le promouvoir
                    $nextAdmin = DB::table('family_user')
                        ->where('family_id', $familyId)
                        ->where('user_id', '!=', $user->id)
                        ->where('permission', FamilyPermissionEnum::Editor->value)
                        ->first();

                    // S'il n'y a pas d'éditeur, chercher un lecteur
                    if (! $nextAdmin) {
                        $nextAdmin = DB::table('family_user')
                            ->where('family_id', $familyId)
                            ->where('user_id', '!=', $user->id)
                            ->where('permission', FamilyPermissionEnum::Viewer->value)
                            ->first();
                    }

                    // S'il y a un autre membre, le promouvoir en administrateur
                    if ($nextAdmin) {
                        DB::table('family_user')
                            ->where('family_id', $familyId)
                            ->where('user_id', $nextAdmin->user_id)
                            ->update(['permission' => FamilyPermissionEnum::Admin->value]);
                    }
                }
            }
        }
    }
}
