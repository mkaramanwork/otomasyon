<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');           // örn: "Numune Kabul", "Analiz Sonucu"
            $table->text('description');        // detaylı açıklama
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sample_acceptance_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_logs');
    }
};
