<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création de l'utilisateur principal
        $mainUser = User::factory()->create([
            'name' => 'Renaud Vmb',
            'email' => 'renaud.vmb@gmail.com',
            'password' => bcrypt('password'),
            'avatar' => asset('img/users/renaud_vmb.png'),
        ]);

        /* Teachers users */
        $dominique = User::factory()->create([
            'name' => 'Dominique Vilain',
            'email' => 'teachers.test@gmail.com',
            'password' => bcrypt('password'),
            'avatar' => asset('img/users/teachers/dominique_vilain.jpeg'),
        ]);

        $toon = User::factory()->create([
            'name' => 'Toon Van Den Bos',
            'email' => 'toon.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/toon_van_den_bos.jpeg'),
        ]);

        $myriam = User::factory()->create([
            'name' => 'Myriam Dupont',
            'email' => 'myriam.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/myriam_dupont.jpeg'),
        ]);

        $daniel = User::factory()->create([
            'name' => 'Daniel Schreurs',
            'email' => 'daniel.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/daniel_schreurs.jpeg'),
        ]);

        $francois = User::factory()->create([
            'name' => 'François Parmentier',
            'email' => 'francois.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/francois_parmentier.jpeg'),
        ]);

        $cedric = User::factory()->create([
            'name' => 'Cédric Muller',
            'email' => 'cedric.test@gmail.com',
            'password' => bcrypt('password_1'),
        ]);

        // Création de la famille principale
        $mainFamily = Family::factory()->create([
            'name' => 'Famille Vmb',
        ]);

        // Ajouter l'utilisateur principal à sa famille en tant qu'administrateur
        $mainFamily->users()->attach($mainUser->id, [
            'role' => 'admin',
            'relation' => 'self',
            'is_admin' => true,
        ]);

        // Création d'une seconde famille (professeurs)
        $teachersFamily = Family::factory()->create([
            'name' => 'Professeurs HEPL',
        ]);

        // Ajouter les enseignants à leur famille
        $teachersFamily->users()->attach($dominique->id, [
            'role' => 'admin',
            'relation' => 'self',
            'is_admin' => true,
        ]);

        $teachersFamily->users()->attach([
            $toon->id => ['role' => 'editor', 'relation' => 'colleague'],
            $myriam->id => ['role' => 'editor', 'relation' => 'colleague'],
            $daniel->id => ['role' => 'editor', 'relation' => 'colleague'],
            $francois->id => ['role' => 'viewer', 'relation' => 'colleague'],
            $cedric->id => ['role' => 'viewer', 'relation' => 'colleague'],
        ]);

        /* Invoices pour la famille principale avec leurs fichiers associés */
        $invoices = Invoice::factory(20)->create([
            'user_id' => $mainUser->id,
            'family_id' => $mainFamily->id,
            'paid_by_user_id' => $mainUser->id,
        ]);

        // Créer un fichier pour chaque facture
        foreach ($invoices as $invoice) {
            InvoiceFile::factory()->create([
                'invoice_id' => $invoice->id,
            ]);
        }
    }
}
