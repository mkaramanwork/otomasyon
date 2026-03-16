<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formula_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 3)->default(0);     
            $table->decimal('percentage', 6, 2)->default(0);  
            $table->string('unit', 10)->nullable();           

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_details');
    }
};