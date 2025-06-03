<?php

namespace App\Services;

use App\Models\User;
use Masmerise\Toaster\Toaster;

class EmailVerificationService
{
    /**
     * Envoie un email de vérification à l'utilisateur si nécessaire
     *
     * @param  User  $user  L'utilisateur auquel envoyer l'email
     * @param  bool  $showToasterNotification  Si true, affiche une notification toaster
     * @param  bool  $emailChanged  Si true, considère que l'email a été modifié
     * @return bool Si l'email a été envoyé avec succès
     */
    public function sendVerificationEmail(User $user, bool $showToasterNotification = true, bool $emailChanged = false): bool
    {
        // Vérifie si l'utilisateur a déjà vérifié son email
        if ($user->hasVerifiedEmail()) {
            if ($showToasterNotification) {
                Toaster::info('Votre adresse e-mail est déjà vérifiée.');
            }

            return false;
        }

        try {
            // Envoie l'email de vérification
            $user->sendEmailVerificationNotification();

            // Affiche une notification si demandé
            if ($showToasterNotification) {
                if ($emailChanged) {
                    Toaster::success('Profil mis à jour !::Un e-mail de vérification a été envoyé à votre nouvelle adresse.');
                } else {
                    Toaster::success('Un e-mail de vérification a été envoyé à votre adresse e-mail.');
                }
            }

            return true;
        } catch (\Exception $e) {
            if ($showToasterNotification) {
                Toaster::error('Erreur lors de l\'envoi du mail de vérification');
            }

            \Log::error('Erreur lors de l\'envoi du mail de vérification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
