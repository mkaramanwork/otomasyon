<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('equipment', function (Blueprint $table) {
        $table->id();
        $table->string('tag')->unique(); // cihaz kodu
        $table->string('name');
        $table->string('model')->nullable();
        $table->string('serial_no')->nullable();
        $table->date('purchase_date')->nullable();
        $table->date('calibration_due_at')->nullable();
        $table->string('status')->default('active'); // active, inactive, out_of_service
        $table->timestamps();
    });

    }

    public function down(): void {
        Schema::dropIfExists('equipment');
    }
};


