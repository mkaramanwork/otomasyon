<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bottles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // şişe/ambalaj adı
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // görsel veya pdf
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottles');
    }
};