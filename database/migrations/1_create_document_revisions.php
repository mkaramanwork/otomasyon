<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('document_revisions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('document_id')->constrained()->onDelete('cascade');
        $table->integer('revision_no');             // Revizyon numarası
        $table->date('revision_date');              // Revizyon tarihi
        $table->date('review_date')->nullable();    // Gözden geçirme tarihi
        $table->text('change_note')->nullable();    // Revizyon nedeni
        $table->string('file_path');                // Dosya yolu
        $table->string('updated_by');               // Güncelleyen
        $table->timestamps();
    });

    }

    public function down(): void {
        Schema::dropIfExists('document_revisions');
    }
};


