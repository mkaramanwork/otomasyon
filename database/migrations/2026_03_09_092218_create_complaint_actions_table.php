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
        Schema::create('complaint_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaint_id');
            $table->longText('action_description');
            $table->unsignedBigInteger('responsible_person_id');
            $table->date('due_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();
            $table->foreign('complaint_id')->references('id')->on('complaints')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_actions');
    }
};
