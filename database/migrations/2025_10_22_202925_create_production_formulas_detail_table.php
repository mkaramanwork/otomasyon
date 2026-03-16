<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_formula_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('formula_detail_id')->constrained()->onDelete('cascade');
            $table->decimal('calculated_amount', 10, 2)->default(0); // kg cinsinden hesaplanan miktar
            $table->boolean('is_completed')->default(false); // üretimde işaretlendi mi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_formula_details');
    }
};