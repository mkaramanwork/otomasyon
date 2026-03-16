<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_acceptance_parameter', function (Blueprint $table) {
            $table->id();

            // Numune kabul kaydı
            $table->foreignId('sample_acceptance_id')
                  ->constrained('sample_acceptances')
                  ->onDelete('cascade');

            // Parametre kaydı
            $table->foreignId('lab_parameter_id')
                  ->constrained('lab_parameters')
                  ->onDelete('cascade');

            // ✅ Sonuç alanları
            $table->string('result')->nullable();        // ölçüm sonucu
            $table->timestamp('result_date')->nullable(); // ölçüm tarihi
            $table->text('description')->nullable();     // açıklama

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_acceptance_parameter');
    }
};