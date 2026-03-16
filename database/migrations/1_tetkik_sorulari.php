<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tetkik_sorulari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tetkik_plan_id')->constrained('tetkik_planlari')->onDelete('cascade');
            $table->string('standart_maddesi');
            $table->text('soru');
            $table->text('delil')->nullable();
            $table->boolean('uygunsuzluk_var')->default(false);
            $table->text('uygunsuzluk_aciklama')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tetkik_sorulari');
    }
};