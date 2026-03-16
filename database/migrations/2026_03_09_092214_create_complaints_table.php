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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_no')->unique(); // 2026-001 formatı
            $table->date('received_date');
            $table->string('subject'); // şikayet konusu
            $table->longText('description'); // sınırsız tanım
            $table->string('owner'); // firma/kişi
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('received_by');
            $table->unsignedBigInteger('product_id'); // ürün seçimi
            $table->unsignedBigInteger('user_id'); // login olan kullanıcı
            $table->longText('evaluation')->nullable();
            $table->date('evaluation_date')->nullable();
            $table->string('evaluated_by')->nullable();
            $table->longText('result')->nullable();
            $table->enum('dif_opened',['evet','hayir'])->default('hayir');
            $table->longText('dif_description')->nullable();
            $table->longText('feedback')->nullable();
            $table->enum('status',['open','in_progress','closed'])->default('open');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
