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
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->string('relation_type')->nullable()->comment('spouse, child, parent, other');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false)->comment('Primary account holder');
            $table->timestamps();
        });

        // Table pivot pour gérer les répartitions de factures entre membres
        Schema::create('invoice_family', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->decimal('share_amount', 10, 2)->nullable()->comment('Montant fixe de la part');
            $table->decimal('share_percentage', 5, 2)->nullable()->comment('Pourcentage de la part');
            $table->timestamps();

            $table->unique(['invoice_id', 'family_id']);
        });

        // Modification de la table invoices pour ajouter paid_by_id
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('paid_by_id')->nullable()->constrained('families')->nullOnDelete();
            $table->string('paid_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
        Schema::dropIfExists('invoice_family');
    }
};
