<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
    Schema::create('external_documents', function (Blueprint $table) {
        $table->id();
        $table->integer('sequence_no'); // sırano
        $table->string('document_code'); // doküman kodu
        $table->string('title'); // doküman adı
        $table->string('institution'); // kurum
        $table->string('revision_no'); // revizyon numarası
        $table->date('publish_date'); // yayın tarihi
        $table->date('review_date')->nullable(); // gözden geçirilme tarihi
        $table->string('url')->nullable(); // kaynak url
        $table->string('file_path'); // pdf, word, ppt dosya yolu
        $table->unsignedBigInteger('created_by'); // kayıt eden kişi
        $table->timestamps();

        $table->foreign('created_by')->references('id')->on('users');
    });

           
    }

    public function down(): void
    {
        Schema::dropIfExists('external_documents');
    }
};