<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tetkik_planlari', function (Blueprint $table) {
            $table->id();
            $table->string('tetkik_no')->unique();
            $table->date('tetkik_tarih');
            $table->string('denetci');
            $table->string('bolum');
            $table->text('amac')->nullable();
            $table->text('kapsam')->nullable();
            $table->string('durum')->default('Planlandı');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tetkik_planlari');
    }
};