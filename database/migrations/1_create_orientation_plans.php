<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('orientation_plans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('personnel_id')->constrained('personnel')->onDelete('cascade');
        $table->string('department');
        $table->text('purpose');              // Oryantasyon amacı
        $table->date('evaluation_date')->nullable();
        $table->date('approval_date')->nullable();
        $table->boolean('is_completed')->default(false);
        $table->timestamps();
    });


    }

    public function down(): void {
        Schema::dropIfExists('orientation_plans');
    }
};


