<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('iqc_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
        $table->date('date');
        $table->string('material');
        $table->string('value');
        $table->string('rule_status'); // OK / Fail
        $table->text('note')->nullable();
        $table->timestamps();
    });


    }

    public function down(): void {
        Schema::dropIfExists('iqc_records');
    }
};


