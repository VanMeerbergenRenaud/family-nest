<?php

namespace Tests\Feature\Invoices;

use App\Livewire\Pages\Invoices\Create;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use function Pest\Laravel\assertDatabaseHas;

it('permet de créer une facture complète avec succès', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    // Configurer le stockage de fichiers factice
    Storage::fake('public');

    // Créer un fichier PDF factice pour simuler l'upload
    $file = UploadedFile::fake()->image('facture.jpg');

    // Livewire test
    Livewire::test(Create::class)
        // Étape 1: Upload du fichier
        ->set('form.uploadedFile', $file)

        // Étape 2: Informations générales
        ->set('form.name', 'Facture Internet Mars 2024')
        ->set('form.type', 'abonnement')
        ->set('form.category', 'internet')
        ->set('form.issuer_name', 'Orange')
        ->set('form.issuer_website', 'https://www.orange.fr')

        // Étape 3: Détails financiers
        ->set('form.amount', 49.99)
        ->set('form.currency', 'EUR')
        ->set('form.paid_by', $user->name)
        ->set('form.associated_members', $user->name)

        // Étape 4: Dates importantes
        ->set('form.issued_date', '2024-03-01')
        ->set('form.payment_due_date', '2024-03-15')
        ->set('form.payment_reminder', '2024-03-10')
        ->set('form.payment_frequency', 'monthly')

        // Étape 5: Engagement (aucun choisi pour ce test)

        // Étape 6: Statut de paiement
        ->set('form.payment_status', 'unpaid')
        ->set('form.payment_method', 'card')
        ->set('form.priority', 'medium')

        // Étape 7: Notes et tags
        ->set('form.notes', 'Facture mensuelle pour la fibre optique')

        // Ajouter deux tags
        ->set('form.tagInput', 'internet')->call('addTag')
        ->set('form.tagInput', 'eau')->call('addTag')

        // Soumettre le formulaire
        ->call('createInvoice')

        // Vérifier la redirection
        ->assertRedirect(route('invoices'));

    // Vérifier que la facture a été créée en base de données
    assertDatabaseHas('invoices', [
        'user_id' => $user->id,
        'name' => 'Facture Internet Mars 2024',
        'type' => 'abonnement',
        'category' => 'internet',
        'issuer_name' => 'Orange',
        'issuer_website' => 'https://www.orange.fr',
        'amount' => 49.99,
        'currency' => 'EUR',
        'paid_by' => $user->name,
        'payment_status' => 'unpaid',
        'payment_method' => 'card',
        'priority' => 'medium',
    ]);

    // Vérifier que les tags ont été enregistrés correctement
    $invoice = Invoice::where('name', 'Facture Internet Mars 2024')->first();
    expect($invoice->tags)->toContain('internet', 'eau');

    // Vérifier que le fichier a été stocké
    Storage::disk('public')->assertExists('invoices/'.$file->hashName());
});
