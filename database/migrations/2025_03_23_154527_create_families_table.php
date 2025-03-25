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
        // Création de la table familles
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Table pour la relation entre utilisateurs et familles
        Schema::create('family_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('permission')->default('viewer'); // admin, editor, viewer
            $table->string('relation')->default('member'); // spouse, father, mother, brother, sister, son, daughter, colleague, colocataire, friend, other
            $table->boolean('is_admin')->default(false);
            $table->timestamps();

            // Un utilisateur ne peut apparaître qu'une seule fois dans une famille.
            $table->unique(['family_id', 'user_id']);
        });

        // Modification de la table invoices pour référencer la famille plutôt que le membre
        Schema::table('invoices', function (Blueprint $table) {
            // Supprimer la colonne paid_by_id si elle existe déjà
            if (Schema::hasColumn('invoices', 'paid_by_id')) {
                $table->dropForeign(['paid_by_id']);
                $table->dropColumn('paid_by_id');
            }

            // Ajouter la colonne famille
            $table->foreignId('family_id')->nullable()->constrained()->nullOnDelete();
            // Ajouter la colonne pour qui a payé
            $table->foreignId('paid_by_user_id')->nullable()->constrained('users')->nullOnDelete();
        });

        // Table pivot pour gérer les répartitions de factures entre membres
        Schema::create('invoice_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('share_amount', 10, 2)->nullable()->comment('Montant fixe de la part');
            $table->decimal('share_percentage', 5, 2)->nullable()->comment('Pourcentage de la part');
            $table->timestamps();

            $table->unique(['invoice_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_user');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn('family_id');
            $table->dropForeign(['paid_by_user_id']);
            $table->dropColumn('paid_by_user_id');
        });

        Schema::dropIfExists('family_user');
        Schema::dropIfExists('families');
    }
};
