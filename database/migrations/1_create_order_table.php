<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->enum('type', ['sipariş', 'teklif']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('total_price', 12, 2)->default(0);      // TL toplam
            $table->decimal('total_price_usd', 12, 2)->default(0);  // USD toplam
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ekleyen kişi
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orders');
    }
};