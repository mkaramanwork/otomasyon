<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->string('doc_no')->unique();          // Doküman numarası
        $table->string('title');                     // Doküman adı
        $table->integer('revision_no')->default(0);  // Revizyon numarası
        $table->date('publish_date');                // Yayın tarihi
        $table->date('last_revision_date')->nullable(); // Son revizyon tarihi
        $table->date('review_date')->nullable();     // Gözden geçirme tarihi
        $table->text('revision_reason')->nullable(); // Revizyon nedeni
        $table->string('created_by');                // Kayıt eden
        $table->enum('status',['guncel','arsiv'])->default('guncel'); // Durum
        $table->timestamps();
    });

    }

    public function down(): void {
        Schema::dropIfExists('documents');
    }
};


