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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('goal_type');
            $table->string('period_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_family_goal')->default(false);
            $table->decimal('target_amount', 10);
            $table->json('categories')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('family_id');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->decimal('current_amount', 10)->default(0);
            $table->decimal('completion_percentage', 5)->default(0);
            $table->timestamps();
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('family_id')->references('id')->on('families')->onDelete('cascade');
        });

        Schema::create('goal_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goal_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_admin')->default(false);
            $table->timestamps();

            $table->foreign('goal_id')->references('id')->on('goals')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
        Schema::dropIfExists('goal_user');
    }
};
