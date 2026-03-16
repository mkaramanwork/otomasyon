<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
Schema::create('purchase_requests', function (Blueprint $table) {
    $table->id();
    $table->string('request_code')->unique();
    $table->string('status')->default('Beklemede');
    $table->timestamp('ordered_at')->nullable();
    $table->timestamp('accepted_at')->nullable();

    // Onaylayan isimler
    $table->string('lab_manager_name')->nullable();
    $table->string('general_manager_name')->nullable();
    $table->string('procurement_name')->nullable();
    $table->string('order_name')->nullable();
    $table->string('control_name')->nullable();
    $table->string('acceptance_name')->nullable();

    $table->timestamps();
});

           
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};