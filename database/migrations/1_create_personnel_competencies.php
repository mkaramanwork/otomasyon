<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('personnel_competencies', function (Blueprint $table) {
        $table->id();
        $table->foreignId('personnel_id')->constrained('personnel')->onDelete('cascade');
        $table->foreignId('competency_id')->constrained()->onDelete('cascade');
        $table->date('granted_at');      // Yetki verildiği tarih
        $table->string('level')->nullable(); // Yetki seviyesi
        $table->timestamps();
    });


    }

    public function down(): void {
        Schema::dropIfExists('personnel_competencies');
    }
};


