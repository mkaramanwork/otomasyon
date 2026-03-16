<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
    Schema::create('external_document_revisions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('external_document_id');
        $table->string('revision_no');
        $table->date('publish_date');
        $table->string('file_path');
        $table->timestamps();

        $table->foreign('external_document_id')
            ->references('id')->on('external_documents')
            ->onDelete('cascade');
    });

           
    }

    public function down(): void
    {
        Schema::dropIfExists('external_document_revisions');
    }
};