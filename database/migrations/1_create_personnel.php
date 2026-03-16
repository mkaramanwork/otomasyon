<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('personnel', function (Blueprint $table) {
        $table->id();
        $table->string('name');          // Personel adı
        $table->string('title');         // Ünvanı
        $table->string('duty');          // Görevi
        $table->date('start_date');      // İşe başlama tarihi
        $table->string('deputy')->nullable(); // Vekili
        $table->timestamps();
    });


    }

    public function down(): void {
        Schema::dropIfExists('personnel');
    }
};


