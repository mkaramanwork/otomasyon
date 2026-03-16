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
    Schema::create('product_relations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->string('relation_type'); // chemical, physical, tds, label, barcode, box, cap
        $table->unsignedBigInteger('relation_id'); // ilgili tabloya foreign key
        $table->enum('status', ['aktif','revize','kullanim_disi'])->default('aktif');
        $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_relations');
    }
};
