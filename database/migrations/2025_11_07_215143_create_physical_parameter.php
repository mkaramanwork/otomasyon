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
        Schema::create('physical_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Örn: “Bidon”, “Etiket”, “Palet”
            $table->string('group')->nullable(); // Örn: “Ambalaj”, “Etiketleme”
            $table->text('description')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_parameters');
    }
};
