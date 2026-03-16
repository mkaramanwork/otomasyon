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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            // Giriş / çıkış tipi
            $table->enum('type', ['in', 'out']);

            // Polymorphic malzeme ilişkisi (örneğin: raw_materials, additives)
            $table->unsignedBigInteger('material_id');
            $table->string('material_type');

            // Miktar
            $table->decimal('amount', 10, 2);

            // Üretim emriyle ilişki
            $table->unsignedBigInteger('related_recipe_id')->nullable();
            $table->foreign('related_recipe_id')->references('id')->on('production_orders')->onDelete('cascade');

            // İşlemi tetikleyen kullanıcı
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};