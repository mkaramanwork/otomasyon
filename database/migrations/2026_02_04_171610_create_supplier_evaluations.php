<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // değerlendirmeyi yapan
            $table->date('evaluation_date');
            $table->string('title')->nullable(); // unvan

            // Sorular
            $table->enum('certification',['Var','Yok','Aşamada']);
            $table->string('certification_detail')->nullable();
            $table->enum('traceability',['Evet','Hayır']);
            $table->enum('complaint_response',['Üç gün altında','Üç gün üzerinde']);
            $table->enum('pricing_policy',['Evet','Hayır']);
            $table->enum('technical_support',['Evet','Hayır']);

            // Puan ve durum
            $table->integer('score');
            $table->string('status');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_evaluations');
    }
};