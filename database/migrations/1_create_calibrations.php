<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('calibrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->string('certificate_no');
            $table->string('provider');
            $table->date('performed_at');
            $table->date('valid_until');
            $table->string('result')->nullable();
            $table->string('certificate_file')->nullable(); // ✅ PDF dosya yolu için alan
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('calibrations');
    }
};


