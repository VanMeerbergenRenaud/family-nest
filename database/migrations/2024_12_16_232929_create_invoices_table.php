<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            /* Step upload on table invoice_file */
            /* Step 1 */
            $table->string('name');
            $table->string('reference')->nullable(); // Référence de la facture
            $table->string('type')->nullable(); // Type de facture (abonnement, loyer, etc.)
            $table->string('category')->nullable(); // Catégorie de facture (téléphonique, logement, etc.)
            $table->string('issuer_name')->nullable();  // Émetteur de la facture
            $table->string('issuer_website')->nullable(); // Site web de l'émetteur
            /* Step 2 */
            $table->decimal('amount', 10, 2); // Montant de la facture
            $table->string('currency', 3)->default('EUR'); // Devise de la facture
            /* Step 3 */
            $table->date('issued_date')->nullable(); // Date d'émission de la facture
            $table->date('payment_due_date')->nullable(); // Date d'échéance du paiement
            $table->string('payment_reminder')->nullable(); // Rappel de paiement (1 jour, 1 semaine, 2 semaines)
            $table->string('payment_frequency')->nullable(); // Fréquence de paiement (mensuel, trimestriel, annuel)
            /* Step 4 */
            $table->string('payment_status')->nullable(); // Stocké en tant que chaîne pour plus de flexibilité
            $table->string('payment_method')->nullable(); // Méthode de paiement (carte, espèces, etc.)
            $table->string('priority')->nullable(); // Priorité de paiement (haute, moyenne, basse)
            /* Step 5 */
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->json('tags')->nullable(); // JSON format for tags
            /* States */
            $table->boolean('is_archived')->default(false); // Facture archivée
            $table->boolean('is_favorite')->default(false);
            // Foreign keys
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Time stamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
