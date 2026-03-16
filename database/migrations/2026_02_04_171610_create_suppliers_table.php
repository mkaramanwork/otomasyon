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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Firma adı
            $table->string('contact')->nullable(); // Telefon/E-posta
            $table->string('address')->nullable();
            $table->string('accreditation')->nullable(); // Sertifika/Akreditasyon
            $table->enum('status',['Aktif','Pasif'])->default('Aktif');
            $table->integer('performance_score')->nullable(); // Değerlendirme puanı
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
