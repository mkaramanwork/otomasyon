<?php

// database/migrations/2025_11_19_000000_create_sales_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales_products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ürün adı
            $table->string('performance_class')->nullable(); // Performans sınıfı
            $table->decimal('volume', 10, 2)->nullable(); // Hacim
            $table->decimal('price_usd', 10, 2)->default(0); // Dolar fiyatı
            $table->decimal('price_try', 10, 2)->default(0); // TL fiyatı
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sales_products');
    }
};
