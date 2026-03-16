<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('denetims', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Doküman adı
            $table->string('category'); // Başlık adı
            $table->string('file_path'); // PDF dosya yolu
            $table->text('description')->nullable(); // Tanım
            $table->date('date'); // Tarih
            $table->unsignedBigInteger('uploaded_by'); // Ekleyen kullanıcı
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denetims');
    }
};