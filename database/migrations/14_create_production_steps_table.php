<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formula_id')->constrained('formulas');
            $table->foreignId('production_order_id')->constrained();
            $table->string('step');
            $table->string('status')->default('bekliyor');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedBigInteger('executed_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_steps');
    }
};