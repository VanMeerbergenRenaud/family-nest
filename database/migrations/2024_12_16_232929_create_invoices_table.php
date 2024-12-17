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
            $table->string('file_path');
            $table->string('name');
            $table->string('issuer');
            $table->string('type');
            $table->string('category')->nullable();
            $table->string('website')->nullable();
            $table->decimal('amount', 8, 2);
            $table->boolean('is_variable')->default(false);
            $table->boolean('is_family_related')->default(false);
            $table->date('issued_date');
            $table->string('payment_reminder')->nullable();
            $table->string('payment_frequency')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'late', 'partially_paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'card', 'mastercard'])->default('card');
            $table->enum('priority', ['high', 'medium', 'low'])->default('low');
            $table->text('notes')->nullable();
            $table->json('tags')->nullable(); // JSON format for tags
            // Foreign keys
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            /*$table->foreignId('invoice_file_id')->constrained()->cascadeOnDelete();*/
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
