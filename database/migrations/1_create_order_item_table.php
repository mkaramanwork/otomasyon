<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('sales_product_id')->constrained('sales_products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->decimal('discount', 5, 2)->default(0); // yüzde indirim
            $table->enum('status', ['beklemede','tamamlandı'])->default('beklemede');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('order_items');
    }
};


