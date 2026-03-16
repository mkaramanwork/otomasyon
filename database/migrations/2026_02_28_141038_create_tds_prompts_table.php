<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tds_prompts', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->longText('answer')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Eğer user tablosu ile ilişki kurmak istersen:
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tds_prompts');
    }
};