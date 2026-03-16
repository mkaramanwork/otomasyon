<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tetkik_soru_listesi', function (Blueprint $table) {
            $table->id();
            $table->string('standart_maddesi');
            $table->text('soru');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tetkik_soru_listesi');
    }
};