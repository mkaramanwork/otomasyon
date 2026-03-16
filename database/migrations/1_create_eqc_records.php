<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('eqc_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
        $table->string('provider');
        $table->string('round_no');
        $table->string('result');
        $table->string('z_score')->nullable();
        $table->text('action_plan')->nullable();
        $table->timestamps();
    });


    }

    public function down(): void {
        Schema::dropIfExists('eqc_records');
    }
};


