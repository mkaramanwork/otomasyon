<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
    Schema::create('purchase_request_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
        $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
        $table->integer('quantity');
        $table->string('unit');
        $table->text('description')->nullable();
        $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
        $table->timestamps();
    });

           
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};