<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('maintenances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
        $table->string('type'); // periyodik, arıza vb.
        $table->date('performed_at');
        $table->text('checklist')->nullable();
        $table->string('result')->nullable();
        $table->timestamps();
    });


    }

    public function down(): void {
        Schema::dropIfExists('maintenances');
    }
};


