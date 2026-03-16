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
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->enum('material_type', ['ham_madde', 'katık']);
            $table->unsignedBigInteger('material_id');
            $table->decimal('percentage', 5, 2);
            $table->decimal('calculated_kg', 10, 2);
            $table->foreignId('added_by')->nullable()->constrained('users');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
