<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_no'); 
            $table->unsignedBigInteger('customer_id');
            $table->string('title')->nullable();
            $table->text('text')->nullable(); 
            $table->date('offer_date');
            $table->integer('sample_count')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->string('approved_by')->nullable();
            $table->string('approved_pdf')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
