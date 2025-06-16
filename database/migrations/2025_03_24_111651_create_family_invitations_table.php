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
        Schema::create('family_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->string('email');
            $table->string('token')->unique();
            $table->string('permission')->default('viewer');
            $table->string('relation')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // These columns are used to track the status of the email sending process
            $table->boolean('send_failed')->default(false);
            $table->text('send_error')->nullable();

            // An invitation can only be sent once to a family member
            $table->unique(['family_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_invitations');
    }
};
