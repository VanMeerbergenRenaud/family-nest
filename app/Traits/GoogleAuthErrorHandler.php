<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;

trait GoogleAuthErrorHandler
{
    /**
     * Gérer les exceptions d'authentification Google
     *
     * @return string|void
     */
    protected function handleGoogleAuthError(\Exception $e, string $context = 'général')
    {
        $errorMessage = $e->getMessage();
        $errorCode = $e->getCode();

        // Journaliser l'erreur
        Log::channel('google_auth')->error('Erreur d\'authentification Google', [
            'context' => $context,
            'message' => $errorMessage,
            'code' => $errorCode,
            'trace' => $e->getTraceAsString(),
        ]);

        // Messages d'erreur adaptés selon le type d'erreur
        $userFriendlyMessage = match (true) {
            // Problèmes de configuration
            str_contains($errorMessage, 'invalid_client') => 'Configuration incorrecte de l\'API Google.',
            str_contains($errorMessage, 'invalid_grant') => 'Autorisation expirée ou invalide.',

            // Problèmes d'accès utilisateur
            str_contains($errorMessage, 'access_denied') => 'Accès refusé par l\'utilisateur.',

            // Problèmes réseau
            str_contains($errorMessage, 'timeout') || str_contains($errorMessage, 'connection') => 'Problème de connexion avec les serveurs Google.',

            // Message par défaut
            default => 'Une erreur est survenue lors de l\'authentification avec Google.'
        };

        // Pour les toasts (si disponible)
        if (class_exists('Masmerise\Toaster\Toaster')) {
            Toaster::error($userFriendlyMessage);
        }

        // Retourner le message pour être utilisé ailleurs si nécessaire
        return $userFriendlyMessage;
    }
}
