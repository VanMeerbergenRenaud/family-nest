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
            $table->string('permission')->default('viewer');
            $table->string('relation')->default('member');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();
            // Foreign key
            $table->unique(['family_id', 'user_id']);
        });

        // Modification de la table invoices pour référencer la famille plutôt que le membre
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
        Schema::dropIfExists('family_user');
    }
};
