<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('technical_specifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
        $table->string('spec_title');
        $table->text('spec_detail')->nullable();
        $table->string('standards')->nullable();
        $table->string('compliance')->nullable();
        $table->string('document_code')->nullable(); // ek alan
        $table->timestamps();
    });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_specifications');
    }
};