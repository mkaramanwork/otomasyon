<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  
            $table->string('unit')->default('kg');   
            $table->decimal('stock_kg', 10, 2)->default(0); 
            $table->decimal('unit_price_tl', 10, 2)->default(0);
            $table->decimal('unit_price_usd', 10, 2)->default(0);
            $table->decimal('unit_price_eur', 10, 2)->default(0);
            $table->string('currency')->default('TL'); 
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
