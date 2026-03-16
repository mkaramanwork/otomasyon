<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tds_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_group_id')->nullable()->constrained('product_groups');

            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();

            $table->text('description')->nullable();

            $table->json('features')->nullable();     
            $table->json('parameters')->nullable();  

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tds_documents');
    }
};