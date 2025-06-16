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
        Schema::table('invoice_files', function (Blueprint $table) {
            $table->string('compression_status')->nullable()->default(null); // null, pending, completed, failed
            $table->bigInteger('original_size')->nullable(); // taille originale en octets
            $table->decimal('compression_rate', 5, 2)->nullable(); // taux de compression en pourcentage
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_files', function (Blueprint $table) {
            $table->dropColumn('compression_status');
            $table->dropColumn('original_size');
            $table->dropColumn('compression_rate');
        });
    }
};
