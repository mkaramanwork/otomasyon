<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
    Schema::create('exams', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->integer('question_count');
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
           
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};