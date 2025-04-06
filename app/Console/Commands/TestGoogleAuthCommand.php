<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestGoogleAuthCommand extends Command
{
    protected $signature = 'auth:test-google';

    protected $description = 'Test la configuration de l\'authentification Google';

    public function handle(): int
    {
        $this->info('Vérification de la configuration Google OAuth...');

        // Verify environment variables
        if (! env('GOOGLE_CLIENT_ID')) {
            $this->error('❌ GOOGLE_CLIENT_ID est manquant dans le fichier .env');

            return 1;
        } else {
            $this->info('✓ GOOGLE_CLIENT_ID est configuré');
        }

        if (! env('GOOGLE_CLIENT_SECRET')) {
            $this->error('❌ GOOGLE_CLIENT_SECRET est manquant dans le fichier .env');

            return 1;
        } else {
            $this->info('✓ GOOGLE_CLIENT_SECRET est configuré');
        }

        if (! env('GOOGLE_REDIRECT_URI')) {
            $this->warn('⚠️ GOOGLE_REDIRECT_URI est manquant, utilisation de la valeur par défaut');
        } else {
            $this->info('✓ GOOGLE_REDIRECT_URI est configuré: '.env('GOOGLE_REDIRECT_URI'));
        }

        // Verify Google API credentials
        $services = config('services.google');
        if (! $services) {
            $this->error('❌ Configuration Google manquante dans config/services.php');

            return 1;
        } else {
            $this->info('✓ Configuration services.php vérifiée');
        }

        // Verify Google API+ activation
        $logChannel = config('logging.channels.google_auth');
        if (! $logChannel) {
            $this->warn('⚠️ Canal de log google_auth non configuré dans config/logging.php');
        } else {
            $this->info('✓ Canal de log google_auth configuré');

            try {
                Log::channel('google_auth')->info('Test de log depuis la commande Artisan');
                $this->info('✓ Écriture dans les logs réussie');
            } catch (\Exception $e) {
                $this->error('❌ Erreur lors de l\'écriture dans les logs: '.$e->getMessage());
            }
        }

        // Verifying routes
        $routes = app('router')->getRoutes();
        $googleRedirectRoute = $routes->getByName('google.redirect');
        $googleCallbackRoute = $routes->getByName('google.callback');

        if (! $googleRedirectRoute) {
            $this->error('❌ Route google.redirect non trouvée');
        } else {
            $this->info('✓ Route google.redirect configurée: '.$googleRedirectRoute->uri());
        }

        if (! $googleCallbackRoute) {
            $this->error('❌ Route google.callback non trouvée');
        } else {
            $this->info('✓ Route google.callback configurée: '.$googleCallbackRoute->uri());
        }

        // Inform the user about the redirection URL
        $this->info("\nRésumé de la vérification:");
        $this->info('Pour fonctionner correctement, assurez-vous que:');
        $this->info('1. Les identifiants Google sont corrects');
        $this->info("2. L'URL de redirection configurée dans la console Google Cloud correspond à: ".url('/auth/google/callback'));
        $this->info("3. L'API Google+ est activée dans la console Google");

        return 0;
    }
}
