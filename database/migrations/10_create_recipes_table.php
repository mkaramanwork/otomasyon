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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('total_kg', 10, 2);
            $table->string('batch_number')->unique();
            $table->timestamp('production_datetime');
            $table->enum('status', ['taslak', 'onaylandı', 'üretimde', 'tamamlandı'])->default('taslak');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->foreignId('formula_id')->nullable()->constrained()->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
