<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('lab_product_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_product_id')->constrained('lab_products')->onDelete('cascade');
            $table->foreignId('parameter_id')->constrained('lab_parameters')->onDelete('cascade');
            $table->string('min_value')->default('-');
            $table->string('max_value')->default('-');
            $table->string('net_value')->default('-');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_product_parameters');
    }
};
