<?php

namespace App\Http\Controllers;

use App\Models\FamilyInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FamilyInvitationController extends Controller
{
    /**
     * Affiche la page d'invitation
     */
    public function showInvitation(string $token)
    {
        $invitation = FamilyInvitation::where('token', $token)->first();

        if (! $invitation || $invitation->isExpired()) {
            return view('invitation.expired');
        }

        // Si l'utilisateur est déjà connecté
        if (Auth::check()) {
            $user = Auth::user();

            // Si l'email de l'utilisateur correspond à l'invitation
            if ($user->email === $invitation->email) {
                return view('invitation.accept', [
                    'invitation' => $invitation,
                    'user' => $user,
                    'registered' => true,
                ]);
            }

            // Si l'utilisateur est connecté avec un autre compte
            return view('invitation.wrong-account', [
                'invitation' => $invitation,
            ]);
        }

        // L'utilisateur n'est pas connecté
        return view('invitation.register', [
            'invitation' => $invitation,
            'email' => $invitation->email,
        ]);
    }

    /**
     * Accepte une invitation et crée un compte si nécessaire
     */
    public function acceptInvitation(Request $request, string $token)
    {
        // Récupérer l'invitation
        $invitation = FamilyInvitation::where('token', $token)->first();

        if (! $invitation || $invitation->isExpired()) {
            return redirect()->route('home')->with('error', 'Cette invitation n\'est plus valide.');
        }

        Log::info('Tentative d\'acceptation d\'invitation', [
            'token' => $token,
            'invitation' => $invitation->toArray(),
        ]);

        // Si l'utilisateur n'est pas connecté, création d'un compte
        if (! Auth::check()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);
        } else {
            $user = Auth::user();

            // Vérifier que l'email correspond
            if ($user->email !== $invitation->email) {
                return back()->with('error', 'Vous devez vous connecter avec le compte associé à cette invitation.');
            }
        }

        // S'assurer que les valeurs ne sont pas null
        $permission = $invitation->permission ?? 'viewer';
        $relation = $invitation->relation ?? 'member';
        $isAdmin = $invitation->is_admin ?? false;

        Log::info('Données d\'invitation', [
            'permission' => $permission,
            'relation' => $relation,
            'is_admin' => $isAdmin,
        ]);

        try {
            // Ajouter l'utilisateur à la famille
            $invitation->family->users()->attach($user->id, [
                'permission' => $permission,
                'relation' => $relation,
                'is_admin' => $isAdmin,
            ]);

            // Supprimer l'invitation
            $invitation->delete();

            return redirect()->route('family')
                ->with('success', 'Vous avez rejoint la famille avec succès!');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout à la famille', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }
}
