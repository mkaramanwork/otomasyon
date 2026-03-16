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
        Schema::create('production_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Hat adı
            $table->enum('type', ['harmanlama', 'dolum']); // Hat tipi
            $table->integer('capacity_litre')->nullable(); // Kapasite (litre)
            $table->text('description')->nullable(); // Teknik açıklama
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete(); // Ekleyen kullanıcı
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_lines');
    }
};