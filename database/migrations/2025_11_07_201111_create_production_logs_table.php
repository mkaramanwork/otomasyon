<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->onDelete('cascade');
            $table->string('event');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('logged_at')->useCurrent(); // otomatik zaman damgası
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_logs');
    }
};
