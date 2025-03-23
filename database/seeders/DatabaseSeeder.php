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
        User::factory()->create([
            'name' => 'Toon Van Den Bos',
            'email' => 'toon.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/toon_van_den_bos.jpeg'),
        ]);

        User::factory()->create([
            'name' => 'Dominique Vilain',
            'email' => 'dominique.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/dominique_vilain.jpeg'),
        ]);

        User::factory()->create([
            'name' => 'Myriam Dupont',
            'email' => 'myriam.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/myriam_dupont.jpeg'),
        ]);

        User::factory()->create([
            'name' => 'Daniel Schreurs',
            'email' => 'daniel.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/daniel_schreurs.jpeg'),
        ]);

        User::factory()->create([
            'name' => 'François Parmentier',
            'email' => 'francois.test@gmail.com',
            'password' => bcrypt('password_1'),
            'avatar' => asset('img/users/teachers/francois_parmentier.jpeg'),
        ]);

        User::factory()->create([
            'name' => 'Cédric Muller',
            'email' => 'cedric.test@gmail.com',
            'password' => bcrypt('password_1'),
        ]);

        /* Invoices pour l'utilisateur 1 avec leurs fichiers associés */
        $invoices = Invoice::factory(20)->create([
            'user_id' => 1,
        ]);

        // Créer un fichier pour chaque facture
        foreach ($invoices as $invoice) {
            InvoiceFile::factory()->create([
                'invoice_id' => $invoice->id,
            ]);
        }

        // Membres de la famille
        Family::factory(12)->create([
            'user_id' => $mainUser->id,
            'is_primary' => false,
        ]);
    }
}
