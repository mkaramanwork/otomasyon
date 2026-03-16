<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('orientation_activities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('orientation_plan_id')->constrained()->onDelete('cascade');
        $table->string('activity');           // Faaliyet
        $table->string('trainer');            // Eğitimi veren
        $table->date('start_date');
        $table->date('end_date');
        $table->string('result')->nullable();
        $table->timestamps();
    });


    }

    public function down(): void {
        Schema::dropIfExists('orientation_activities');
    }
};


