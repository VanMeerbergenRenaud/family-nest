<?php

namespace Database\Seeders;

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
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
        /* Main user */
        $renaud = User::factory()->create([
            'name' => 'Renaud Vmb',
            'email' => 'renaud.vmb@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $france = User::factory()->create([
            'name' => 'France Mélon',
            'email' => 'france.test@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $martin = User::factory()->create([
            'name' => 'Martin Van Meerbergen',
            'email' => 'martin.test@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $mamy = User::factory()->create([
            'name' => 'Mamy',
            'email' => 'mamy.test@gmail.com',
            'password' => bcrypt('password'),
        ]);

        // Create a family for the main user
        $renaudFamily = Family::factory()->create([
            'name' => 'Famille Van Meerbergen',
        ]);

        // Attach other users to the family of the main user
        $renaudFamily->users()->attach($renaud->id, [
            'permission' => FamilyPermissionEnum::Admin->value,
            'relation' => FamilyRelationEnum::Self->value,
            'is_admin' => true,
        ]);

        // Add relationships for the main user
        $renaudFamily->users()->attach([
            $france->id => [
                'permission' => FamilyPermissionEnum::Editor->value,
                'relation' => FamilyRelationEnum::Parent->value,
            ],
            $martin->id => [
                'permission' => FamilyPermissionEnum::Viewer->value,
                'relation' => FamilyRelationEnum::Brother->value,
            ],
            $mamy->id => [
                'permission' => FamilyPermissionEnum::Viewer->value,
                'relation' => FamilyRelationEnum::Grandparent->value,
            ],
        ]);

        // Create invoices for users
        $invoiceRenaud = Invoice::factory(20)->create([
            'user_id' => $renaud->id,
        ]);

        $invoiceFrance = Invoice::factory(5)->create([
            'user_id' => $france->id,
        ]);

        // Create invoice files for users
        foreach ($invoiceRenaud as $invoice) {
            InvoiceFile::factory()->create([
                'invoice_id' => $invoice->id,
            ]);
        }

        foreach ($invoiceFrance as $invoice) {
            InvoiceFile::factory()->create([
                'invoice_id' => $invoice->id,
            ]);
        }
    }
}
