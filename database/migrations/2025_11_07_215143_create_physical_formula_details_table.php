<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_formula_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_formula_id')->constrained()->onDelete('cascade');
            $table->string('selection_label'); // Örn: “1. Seçim”, “2. Seçim”
            $table->foreignId('physical_parameter_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 3)->default(1); // Gerekli miktar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_formula_details');
    }
};